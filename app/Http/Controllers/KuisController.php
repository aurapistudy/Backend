<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersByAssignedMapel;
use App\Exceptions\GeminiCoverException;
use App\Models\Kuis;
use App\Models\KuisHasil;
use App\Models\KuisJawaban;
use App\Models\KuisPertanyaan;
use App\Models\KuisOpsi;
use App\Models\Materi;
use App\Models\MateriBab;
use App\Services\GeminiQuizService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class KuisController extends Controller
{
    use FiltersByAssignedMapel;

    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $kuis = $this->applyMapelFilterToKuis(Kuis::with('materi'))
            ->withCount('pertanyaan')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('id', 'like', "%{$search}%")
                        ->orWhere('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%")
                        ->orWhereHas('materi', function ($materiQuery) use ($search) {
                            $materiQuery->where('judul', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.kuis.index', compact('kuis', 'search'));
    }

    public function create()
    {
        $this->ensureNotSuperAdmin();

        $materiList = $this->applyMapelFilterToMateri(
            Materi::with(['mataPelajaran', 'level', 'bab'])->where('status_aktif', true)
        )
            ->orderBy('judul')
            ->get();

        $prefillMateriId = request('materi_id');
        $prefillMateriBabId = request('materi_bab_id');

        return view('dashboard.kuis.create', compact('materiList', 'prefillMateriId', 'prefillMateriBabId'));
    }

    public function generateFromMateri(Request $request, GeminiQuizService $geminiQuizService)
    {
        $this->ensureNotSuperAdmin();

        $validated = $request->validate([
            'materi_id' => [
                'required',
                Rule::exists('materi', 'id')->where('status_aktif', true),
            ],
            'materi_bab_id' => [
                'nullable',
                Rule::exists('materi_bab', 'id')->where('status_aktif', true),
            ],
            'jumlah_soal' => 'nullable|integer|min:1|max:10',
            'kesulitan' => 'nullable|in:mudah,sedang,sulit',
            'jenis_soal' => 'nullable|in:pilihan,essay',
        ], [
            'materi_id.required' => 'Pilih materi dulu sebelum generate kuis.',
        ]);

        $materi = Materi::with(['mataPelajaran', 'level'])->findOrFail($validated['materi_id']);
        $this->authorizeMateriAccess($materi);
        $bab = !empty($validated['materi_bab_id']) ? MateriBab::findOrFail($validated['materi_bab_id']) : null;
        if ($bab && (int) $bab->materi_id !== (int) $materi->id) {
            return response()->json([
                'message' => 'Bab yang dipilih tidak sesuai dengan materi.',
            ], 422);
        }

        try {
            $draft = $geminiQuizService->generateFromMateri(
                $materi,
                (int) ($validated['jumlah_soal'] ?? 5),
                (string) ($validated['kesulitan'] ?? 'sedang'),
                (string) ($validated['jenis_soal'] ?? 'pilihan'),
                $bab
            );

            return response()->json([
                'message' => 'Draft kuis berhasil dibuat.',
                'data' => $draft,
            ]);
        } catch (GeminiCoverException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status());
        }
    }

    public function store(Request $request)
    {
        $this->ensureNotSuperAdmin();

        $validated = $this->validatePayload($request);
        $relationPayload = $this->resolveMateriRelationPayload($validated);
        $this->authorizeKuisMateriId($relationPayload['materi_id'] ?? null);

        DB::transaction(function () use ($validated, $request, $relationPayload) {
            $kuis = Kuis::create([
                'materi_id' => $relationPayload['materi_id'],
                'materi_bab_id' => $relationPayload['materi_bab_id'],
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'status_aktif' => $request->boolean('status_aktif'),
                'dibuat_oleh' => Auth::id(),
            ]);

            $this->storePertanyaan($kuis->id, $validated['pertanyaan']);
        });

        return redirect()->route('kuis.index')
            ->with('success', 'Kuis berhasil dibuat.');
    }

    public function show(Kuis $kui)
    {
        $kui->load(['materi', 'materiBab', 'pertanyaan.opsi']);
        $this->authorizeKuisAccess($kui);
        return view('dashboard.kuis.show', ['kuis' => $kui]);
    }

    public function edit(Kuis $kui)
    {
        $this->ensureNotSuperAdmin();

        $kui->load('pertanyaan.opsi');
        $this->authorizeKuisAccess($kui);
        $materiList = $this->applyMapelFilterToMateri(
            Materi::with('bab')->where('status_aktif', true)
        )
            ->orderBy('judul')
            ->get();

        return view('dashboard.kuis.edit', ['kuis' => $kui, 'materiList' => $materiList]);
    }

    public function update(Request $request, Kuis $kui)
    {
        $this->ensureNotSuperAdmin();

        $this->authorizeKuisAccess($kui);
        $validated = $this->validatePayload($request, $kui);
        $relationPayload = $this->resolveMateriRelationPayload($validated);
        $this->authorizeKuisMateriId($relationPayload['materi_id'] ?? null);

        DB::transaction(function () use ($kui, $validated, $request, $relationPayload) {
            $kui->update([
                'materi_id' => $relationPayload['materi_id'],
                'materi_bab_id' => $relationPayload['materi_bab_id'],
                'judul' => $validated['judul'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'status_aktif' => $request->boolean('status_aktif'),
            ]);

            KuisPertanyaan::where('kuis_id', $kui->id)->delete();
            $this->storePertanyaan($kui->id, $validated['pertanyaan']);
        });

        return redirect()->route('kuis.index')
            ->with('success', 'Kuis berhasil diperbarui.');
    }

    public function destroy(Kuis $kui)
    {
        $this->ensureNotSuperAdmin();

        $this->authorizeKuisAccess($kui);
        $kui->delete();

        return redirect()->route('kuis.index')
            ->with('success', 'Kuis berhasil dihapus.');
    }

    public function hasilIndex(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $sort = (string) $request->get('sort', 'terakhir');

        $kuisList = $this->applyMapelFilterToKuis(Kuis::query())
            ->with('materi')
            ->whereHas('hasil', function ($hasilQuery) {
                $this->applyMapelFilterToKuisHasil($hasilQuery);
            })
            ->withCount(['hasil as total_pengerjaan' => function ($query) {
                $this->applyMapelFilterToKuisHasil($query);
            }])
            ->withCount(['hasil as siswa_unik_count' => function ($query) {
                $this->applyMapelFilterToKuisHasil($query);
                $query->select(DB::raw('count(distinct pengguna_id)'));
            }])
            ->withCount(['hasil as perlu_koreksi_count' => function ($query) {
                $query->whereHas('jawaban', function ($jawabanQuery) {
                    $jawabanQuery->where('status_koreksi', 'pending');
                });
                $this->applyMapelFilterToKuisHasil($query);
            }])
            ->withMax(['hasil as terakhir_selesai' => function ($query) {
                $this->applyMapelFilterToKuisHasil($query);
            }], 'selesai_at')
            ->withAvg(['hasil as rata_skor' => function ($query) {
                $this->applyMapelFilterToKuisHasil($query);
            }], 'skor')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('id', 'like', "%{$search}%")
                        ->orWhere('judul', 'like', "%{$search}%")
                        ->orWhereHas('materi', function ($materiQuery) use ($search) {
                            $materiQuery->where('judul', 'like', "%{$search}%");
                        });
                });
            })
            ->when($sort === 'peserta', fn ($query) => $query->orderByDesc('siswa_unik_count'))
            ->when($sort === 'skor_tinggi', fn ($query) => $query->orderByDesc('rata_skor'))
            ->when($sort === 'skor_rendah', fn ($query) => $query->orderBy('rata_skor'))
            ->when($sort === 'judul', fn ($query) => $query->orderBy('judul'))
            ->when($sort === 'terakhir' || !in_array($sort, ['peserta', 'skor_tinggi', 'skor_rendah', 'judul'], true), fn ($query) => $query->orderByDesc('terakhir_selesai'))
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.kuis.hasil', compact('kuisList', 'search', 'sort'));
    }

    public function hasilKuis(Request $request, Kuis $kuis)
    {
        $this->authorizeKuisAccess($kuis);
        $kuis->load('materi');

        $search = trim((string) $request->get('search', ''));
        $sort = (string) $request->get('sort', 'skor_desc');

        $hasilQuery = $this->applyMapelFilterToKuisHasil(
            KuisHasil::query()
                ->with(['pengguna.siswa.level'])
                ->withPendingFlag()
                ->where('kuis_id', $kuis->id)
        );

        $hasil = (clone $hasilQuery)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('id', 'like', "%{$search}%")
                        ->orWhere('skor', 'like', "%{$search}%")
                        ->orWhere('total_benar', 'like', "%{$search}%")
                        ->orWhere('total_pertanyaan', 'like', "%{$search}%")
                        ->orWhereHas('pengguna', function ($penggunaQuery) use ($search) {
                            $penggunaQuery->where('nama', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%")
                                ->orWhereHas('siswa.level', function ($levelQuery) use ($search) {
                                    $levelQuery->where('nama', 'like', "%{$search}%");
                                });
                        });
                });
            })
            ->when($sort === 'skor_asc', fn ($query) => $query->orderBy('skor')->orderByDesc('selesai_at'))
            ->when($sort === 'terbaru', fn ($query) => $query->orderByDesc('selesai_at'))
            ->when($sort === 'terlama', fn ($query) => $query->orderBy('selesai_at'))
            ->when($sort === 'nama', function ($query) {
                $query->join('pengguna', 'pengguna.id', '=', 'kuis_hasil.pengguna_id')
                    ->orderBy('pengguna.nama')
                    ->select('kuis_hasil.*');
            })
            ->when($sort === 'skor_desc' || !in_array($sort, ['skor_asc', 'terbaru', 'terlama', 'nama'], true), fn ($query) => $query->orderByDesc('skor')->orderByDesc('selesai_at'))
            ->paginate(10)
            ->withQueryString();

        $ringkasan = [
            'total_pengerjaan' => (clone $hasilQuery)->count(),
            'siswa_unik' => (clone $hasilQuery)->distinct()->count('pengguna_id'),
            'rata_skor' => (clone $hasilQuery)->avg('skor'),
            'perlu_koreksi' => (clone $hasilQuery)->whereHas('jawaban', function ($jawabanQuery) {
                $jawabanQuery->where('status_koreksi', 'pending');
            })->count(),
        ];

        return view('dashboard.kuis.hasil-kuis', compact('kuis', 'hasil', 'search', 'sort', 'ringkasan'));
    }

    public function hasilShow(KuisHasil $hasil)
    {
        $hasil->load(['kuis.materi', 'jawaban.pertanyaan', 'pengguna.siswa.level']);
        $this->authorizeMapelAccess($hasil->kuis?->materi?->mata_pelajaran_id);
        return view('dashboard.kuis.hasil-show', compact('hasil'));
    }

    public function hasilUpdate(Request $request, KuisHasil $hasil)
    {
        $this->ensureNotSuperAdmin();

        $hasil->load(['kuis.materi', 'jawaban.pertanyaan']);
        $this->authorizeMapelAccess($hasil->kuis?->materi?->mata_pelajaran_id);

        $updates = $request->input('koreksi', []);
        foreach ($hasil->jawaban as $jawaban) {
            $p = $jawaban->pertanyaan;
            if (!$p || !in_array($p->tipe, ['essay', 'speaking'], true)) {
                continue;
            }
            $data = $updates[$jawaban->id] ?? null;
            if (!$data) {
                continue;
            }
            $status = $data['status_koreksi'] ?? $jawaban->status_koreksi;
            $skorAuto = isset($data['skor_auto']) ? (int) $data['skor_auto'] : $jawaban->skor_auto;
            $benar = ($status === 'approved') && $skorAuto >= 70;

            $jawaban->update([
                'status_koreksi' => $status,
                'skor_auto' => $skorAuto,
                'benar' => $benar,
            ]);
        }

        $totalPertanyaan = $hasil->jawaban->count();
        $totalBenar = $hasil->jawaban->where('benar', true)->count();
        $skor = $totalPertanyaan > 0 ? (int) round(($totalBenar / $totalPertanyaan) * 100) : 0;
        $hasil->update([
            'total_benar' => $totalBenar,
            'total_pertanyaan' => $totalPertanyaan,
            'skor' => $skor,
        ]);

        return redirect()
            ->route('kuis.hasil.show', $hasil->id)
            ->with('success', 'Koreksi disimpan.');
    }

    private function validatePayload(Request $request, ?Kuis $kuis = null): array
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:200',
            'materi_id' => [
                'nullable',
                Rule::exists('materi', 'id')->where('status_aktif', true),
            ],
            'materi_bab_id' => [
                'nullable',
                Rule::exists('materi_bab', 'id')->where('status_aktif', true),
            ],
            'deskripsi' => 'nullable|string',
            'pertanyaan' => 'required|array|min:1',
            'pertanyaan.*.id' => 'nullable|integer',
            'pertanyaan.*.teks' => 'required|string',
            'pertanyaan.*.tipe' => 'required|in:pilihan,essay',
            'pertanyaan.*.benar' => 'nullable|in:A,B,C,D',
            'pertanyaan.*.opsi' => 'nullable|array',
            'pertanyaan.*.opsi.A' => 'nullable|string',
            'pertanyaan.*.opsi.B' => 'nullable|string',
            'pertanyaan.*.opsi.C' => 'nullable|string',
            'pertanyaan.*.opsi.D' => 'nullable|string',
            'pertanyaan.*.jawaban_teks' => 'nullable|string',
            'pertanyaan.*.keyword' => 'nullable|string|max:255',
            'pertanyaan.*.bahasa' => 'nullable|in:id-ID,en-US',
        ], [
            'judul.required' => 'Judul kuis wajib diisi.',
            'pertanyaan.required' => 'Minimal harus ada 1 pertanyaan.',
            'pertanyaan.*.teks.required' => 'Pertanyaan wajib diisi.',
            'pertanyaan.*.tipe.required' => 'Tipe soal wajib dipilih.',
        ]);

        $validator->after(function ($validator) use ($request) {
            $pertanyaanList = $request->input('pertanyaan', []);

            foreach ($pertanyaanList as $idx => $item) {
                $tipe = $item['tipe'] ?? 'pilihan';

                if ($tipe === 'pilihan') {
                    if (empty($item['benar']) || empty($item['opsi']['A']) || empty($item['opsi']['B']) || empty($item['opsi']['C']) || empty($item['opsi']['D'])) {
                        $validator->errors()->add("pertanyaan.$idx", 'Soal pilihan ganda harus punya opsi A-D dan jawaban benar.');
                    }
                }
                if ($tipe === 'essay') {
                    if (empty($item['jawaban_teks']) || empty($item['keyword'])) {
                        $validator->errors()->add("pertanyaan.$idx", 'Soal essay harus punya jawaban contoh dan keyword.');
                    }
                }
            }

            $materiId = $request->input('materi_id');
            $materiBabId = $request->input('materi_bab_id');
            if ($materiBabId) {
                $bab = MateriBab::find($materiBabId);
                if (!$bab) {
                    $validator->errors()->add('materi_bab_id', 'Bab materi tidak ditemukan.');
                } elseif ($materiId && (int) $bab->materi_id !== (int) $materiId) {
                    $validator->errors()->add('materi_bab_id', 'Bab yang dipilih tidak sesuai dengan materi.');
                }
            }
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    private function storePertanyaan(int $kuisId, array $pertanyaanList): void
    {
        $order = 1;
        foreach ($pertanyaanList as $item) {
            $tipe = $item['tipe'] ?? 'pilihan';

            $pertanyaan = KuisPertanyaan::create([
                'kuis_id' => $kuisId,
                'pertanyaan' => $item['teks'],
                'urutan' => $order,
                'tipe' => $tipe,
                'jawaban_teks' => $item['jawaban_teks'] ?? null,
                'keyword' => $item['keyword'] ?? null,
                'audio_path' => null,
                'audio_text' => null,
                'bahasa' => $item['bahasa'] ?? null,
            ]);

            if ($tipe === 'pilihan') {
                foreach (['A', 'B', 'C', 'D'] as $label) {
                    if (!isset($item['opsi'][$label])) {
                        continue;
                    }
                    KuisOpsi::create([
                        'pertanyaan_id' => $pertanyaan->id,
                        'label' => $label,
                        'teks' => $item['opsi'][$label],
                        'benar' => ($item['benar'] ?? '') === $label,
                    ]);
                }
            }

            $order += 1;
        }
    }

    private function resolveMateriRelationPayload(array $validated): array
    {
        $materiBabId = $validated['materi_bab_id'] ?? null;
        if ($materiBabId) {
            $bab = MateriBab::findOrFail($materiBabId);

            return [
                'materi_id' => $bab->materi_id,
                'materi_bab_id' => $bab->id,
            ];
        }

        return [
            'materi_id' => $validated['materi_id'] ?? null,
            'materi_bab_id' => null,
        ];
    }

    private function authorizeKuisAccess(Kuis $kuis): void
    {
        $kuis->loadMissing('materi');
        $this->authorizeKuisMateriId($kuis->materi_id);
    }

    private function authorizeKuisMateriId(?int $materiId): void
    {
        if (!$materiId) {
            abort(403, 'Kuis ini tidak terhubung ke materi yang dapat Anda kelola.');
        }

        $materi = Materi::findOrFail($materiId);
        $this->authorizeMateriAccess($materi);
    }

    private function ensureNotSuperAdmin(): void
    {
        if (Auth::user()?->isSuperAdmin()) {
            abort(403, 'Super admin hanya dapat melihat data kuis.');
        }
    }
}
