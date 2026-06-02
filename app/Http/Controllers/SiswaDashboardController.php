<?php
namespace App\Http\Controllers;
use App\Models\CatatanSiswa;
use App\Models\Kuis;
use App\Models\KuisHasil;
use App\Models\KuisJawaban;
use App\Models\LogAksesMateri;
use App\Models\Level;
use App\Models\Materi;
use App\Models\RakBuku;
use App\Models\SesiBaca;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class SiswaDashboardController extends Controller
{
    public function __construct()
    {
        $levels = Level::where('status_aktif', true)
            ->orderBy('nama')
            ->get();
        View::share('levels', $levels);
    }

    public function index()
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $pageTitle = 'Dashboard Siswa';

        return view('dashboard.siswa.index', compact('user', 'pageTitle'));
    }

    public function materi()
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $pageTitle = 'Mata Pelajaran';
        $materi = Materi::with(['mataPelajaran', 'level'])
            ->where('status_aktif', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        $rakMateriIds = RakBuku::where('pengguna_id', $user->id)
            ->pluck('materi_id')
            ->toArray();

        return view('dashboard.siswa.materi', compact('user', 'pageTitle', 'materi', 'rakMateriIds'));
    }

    public function showMateri(Materi $materi)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        if ($block = $this->ensureMateriLevelAccess($user, $materi)) {
            return $block;
        }

        if (!$materi->status_aktif) {
            abort(404);
        }

        $pageTitle = 'Detail Mata Pelajaran';
        $materiKuisList = Kuis::withCount('pertanyaan')
            ->where('materi_id', $materi->id)
            ->where('status_aktif', true)
            ->orderByDesc('created_at')
            ->get();
        $hasKuis = $materiKuisList->isNotEmpty();

        $isMateriCompleted = $this->isMateriCompleted($user->id, $materi->id);
        $inRak = RakBuku::where('pengguna_id', $user->id)
            ->where('materi_id', $materi->id)
            ->exists();

        return view('dashboard.siswa.materi-show', compact('user', 'pageTitle', 'materi', 'hasKuis', 'isMateriCompleted', 'inRak', 'materiKuisList'));
    }

    public function readMateri(Materi $materi)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        if ($block = $this->ensureMateriLevelAccess($user, $materi)) {
            return $block;
        }

        if (!$materi->status_aktif) {
            abort(404);
        }

        LogAksesMateri::create([
            'pengguna_id' => $user->id,
            'materi_id' => $materi->id,
            'waktu_akses' => now(),
            'aksi' => 'OPEN',
        ]);

        $sesi = SesiBaca::create([
            'pengguna_id' => $user->id,
            'materi_id' => $materi->id,
            'mulai' => now(),
            'selesai' => null,
            'durasi_detik' => null,
            'halaman_terakhir' => null,
            'progres_persen' => null,
            'gunakan_gaze' => true,
            'gunakan_suara' => false,
        ]);

        $pageTitle = 'Sesi Baca';

        return view('dashboard.siswa.materi-read', compact('user', 'pageTitle', 'materi', 'sesi'));
    }

    public function rakBuku()
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $pageTitle = 'Rak Buku';
        $rak = RakBuku::with(['materi.mataPelajaran', 'materi.level'])
            ->where('pengguna_id', $user->id)
            ->whereHas('materi', function ($query) {
                $query->where('status_aktif', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.siswa.rak-buku', compact('user', 'pageTitle', 'rak'));
    }

    public function addRakBuku(Request $request)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'materi_id' => 'required|exists:materi,id',
        ]);

        $materi = Materi::findOrFail($validated['materi_id']);
        if (!$materi->status_aktif) {
            return redirect()
                ->back()
                ->with('error', 'Mata pelajaran ini sudah tidak tersedia.');
        }

        if ($block = $this->ensureMateriLevelAccess($user, $materi)) {
            return $block;
        }

        RakBuku::firstOrCreate([
            'pengguna_id' => $user->id,
            'materi_id' => $validated['materi_id'],
        ]);

        return redirect()
            ->back()
            ->with('success', 'Mata pelajaran ditambahkan ke rak buku.');
    }

    public function removeRakBuku($materiId)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        RakBuku::where('pengguna_id', $user->id)
            ->where('materi_id', $materiId)
            ->delete();

        return redirect()
            ->back()
            ->with('success', 'Mata pelajaran dihapus dari rak buku.');
    }

    public function catatan()
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $pageTitle = 'Catatan';
        $materiList = Materi::where('status_aktif', true)
            ->orderBy('judul')
            ->get();
        $catatan = CatatanSiswa::with('materi')
            ->where('pengguna_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(8);

        return view('dashboard.siswa.catatan', compact('user', 'pageTitle', 'catatan', 'materiList'));
    }

    public function storeCatatan(Request $request)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'materi_id' => 'nullable|exists:materi,id',
            'isi' => 'required|string|max:5000',
        ], [
            'isi.required' => 'Catatan tidak boleh kosong.',
        ]);

        CatatanSiswa::create([
            'pengguna_id' => $user->id,
            'materi_id' => $validated['materi_id'] ?? null,
            'isi' => $validated['isi'],
        ]);

        return redirect()
            ->route('dashboard.siswa.catatan')
            ->with('success', 'Catatan berhasil disimpan.');
    }

    public function destroyCatatan(CatatanSiswa $catatan)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        if ($catatan->pengguna_id !== $user->id) {
            abort(403);
        }

        $catatan->delete();

        return redirect()
            ->route('dashboard.siswa.catatan')
            ->with('success', 'Catatan dihapus.');
    }

    public function riwayat()
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $pageTitle = 'Riwayat';
        $riwayat = DB::table('sesi_baca')
            ->join('materi', 'materi.id', '=', 'sesi_baca.materi_id')
            ->where('sesi_baca.pengguna_id', $user->id)
            ->select(
                'materi.id as materi_id',
                'materi.judul as judul',
                'materi.deskripsi as deskripsi',
                DB::raw('COUNT(*) as total_baca'),
                DB::raw('MAX(COALESCE(sesi_baca.selesai, sesi_baca.mulai)) as last_access'),
                DB::raw('MAX(sesi_baca.halaman_terakhir) as halaman_terakhir'),
                DB::raw('MAX(sesi_baca.progres_persen) as progres_persen'),
                DB::raw('MAX(sesi_baca.durasi_detik) as durasi_detik')
            )
            ->groupBy('materi.id', 'materi.judul', 'materi.deskripsi')
            ->orderByDesc('last_access')
            ->paginate(10);

        $kuisSort = request()->get('kuis_sort', 'latest');
        $riwayatKuisQuery = DB::table('kuis_hasil')
            ->join('kuis', 'kuis.id', '=', 'kuis_hasil.kuis_id')
            ->leftJoin('materi', 'materi.id', '=', 'kuis.materi_id')
            ->leftJoin('kuis_jawaban', 'kuis_jawaban.kuis_hasil_id', '=', 'kuis_hasil.id')
            ->where('kuis_hasil.pengguna_id', $user->id)
            ->select(
                'kuis_hasil.id as hasil_id',
                'kuis.judul as kuis_judul',
                'kuis.materi_id as materi_id',
                'materi.judul as materi_judul',
                'kuis_hasil.skor',
                'kuis_hasil.total_benar',
                'kuis_hasil.total_pertanyaan',
                'kuis_hasil.selesai_at',
                DB::raw("MAX(CASE WHEN kuis_jawaban.status_koreksi = 'pending' THEN 1 ELSE 0 END) as has_pending")
            )
            ->groupBy('kuis_hasil.id', 'kuis.judul', 'kuis.materi_id', 'materi.judul', 'kuis_hasil.skor', 'kuis_hasil.total_benar', 'kuis_hasil.total_pertanyaan', 'kuis_hasil.selesai_at');

        if ($kuisSort === 'score') {
            $riwayatKuisQuery->orderByDesc('kuis_hasil.skor')->orderByDesc('kuis_hasil.selesai_at');
        } else {
            $riwayatKuisQuery->orderByDesc('kuis_hasil.selesai_at');
        }

        $riwayatKuis = $riwayatKuisQuery->paginate(8, ['*'], 'kuis_page')->withQueryString();

        return view('dashboard.siswa.riwayat', compact('user', 'pageTitle', 'riwayat', 'riwayatKuis', 'kuisSort'));
    }

    public function riwayatKuisShow($hasilId)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $hasil = KuisHasil::with(['kuis.materi', 'jawaban.pertanyaan', 'jawaban.opsi'])
            ->where('id', $hasilId)
            ->where('pengguna_id', $user->id)
            ->firstOrFail();

        if ($hasil->kuis && $hasil->kuis->materi) {
            if ($block = $this->ensureMateriLevelAccess($user, $hasil->kuis->materi)) {
                return $block;
            }
        }

        $pageTitle = 'Detail Kuis';

        return view('dashboard.siswa.riwayat-kuis-show', compact('user', 'pageTitle', 'hasil'));
    }

    public function perintahSuara()
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        return redirect()->route('dashboard.siswa.panduan');
    }

    public function panduan()
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $pageTitle = 'Panduan';

        return view('dashboard.siswa.panduan', compact('user', 'pageTitle'));
    }

    public function pengaturan()
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $pageTitle = 'Pengaturan';

        return view('dashboard.siswa.pengaturan', compact('user', 'pageTitle'));
    }

    public function updatePengaturan(Request $request)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'asr_lang' => 'required|in:id-ID,en-US',
            'tts_lang' => 'required|in:id-ID,en-US',
            'tts_rate' => 'required|numeric|min:0.6|max:1.4',
            'auto_voice_nav' => 'nullable|boolean',
        ]);

        $user->update([
            'asr_lang' => $validated['asr_lang'],
            'tts_lang' => $validated['tts_lang'],
            'tts_rate' => $validated['tts_rate'],
            'auto_voice_nav' => $request->has('auto_voice_nav'),
        ]);

        return redirect()
            ->route('dashboard.siswa.pengaturan')
            ->with('success', 'Pengaturan tersimpan.');
    }

    public function updateKelas(Request $request)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'level_id' => 'required|exists:level,id',
        ]);

        $siswa = $user->siswa;
        if (!$siswa) {
            $siswa = Siswa::create([
                'pengguna_id' => $user->id,
                'nama_sekolah' => null,
                'jenjang' => null,
                'level_id' => $validated['level_id'],
                'catatan' => null,
            ]);
        } else {
            $siswa->update([
                'level_id' => $validated['level_id'],
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Kelas berhasil disimpan.');
    }

    public function kuisMateri(Materi $materi)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        if ($block = $this->ensureMateriLevelAccess($user, $materi)) {
            return $block;
        }

        if (!$materi->status_aktif) {
            abort(404);
        }

        $kuis = Kuis::query()
            ->where('materi_id', $materi->id)
            ->where('status_aktif', true)
            ->orderByDesc('created_at')
            ->first();

        if (!$kuis) {
            return redirect()
                ->route('dashboard.siswa.materi.show', $materi->id)
                ->with('error', 'Kuis untuk mata pelajaran ini belum tersedia.');
        }

        return redirect()->route('dashboard.siswa.materi.kuis.show', [
            'materi' => $materi->id,
            'kuis' => $kuis->id,
        ]);
    }

    public function kuisMateriShow(Materi $materi, Kuis $kuis)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        if ($block = $this->ensureMateriLevelAccess($user, $materi)) {
            return $block;
        }

        if (!$materi->status_aktif) {
            abort(404);
        }

        $kuis = $this->resolveMateriKuis($materi, $kuis);
        $kuis->load('pertanyaan.opsi');

        $pageTitle = 'Kuis Mata Pelajaran';
        $submitRoute = route('dashboard.siswa.materi.kuis.submit', [
            'materi' => $materi->id,
            'kuis' => $kuis->id,
        ]);
        $backUrl = route('dashboard.siswa.materi.show', $materi->id);
        $displayTitle = $kuis->judul ?: $materi->judul;

        return view('dashboard.siswa.kuis', compact('user', 'pageTitle', 'materi', 'kuis', 'submitRoute', 'backUrl', 'displayTitle'));
    }

    public function submitKuisMateri(Request $request, Materi $materi, Kuis $kuis)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        if ($block = $this->ensureMateriLevelAccess($user, $materi)) {
            return $block;
        }

        if (!$materi->status_aktif) {
            abort(404);
        }

        $kuis = $this->resolveMateriKuis($materi, $kuis);
        $kuis->load('pertanyaan.opsi');

        $skor = $this->storeKuisHasil($request, $kuis, $user->id);

        return redirect()
            ->route('dashboard.siswa.materi.kuis.show', [
                'materi' => $materi->id,
                'kuis' => $kuis->id,
            ])
            ->with('success', 'Kuis selesai. Skor kamu: ' . $skor);
    }

    public function kuisIndex()
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        $pageTitle = 'Kuis';

        $kuisAktif = Kuis::with(['materi'])
            ->withCount('pertanyaan')
            ->where('status_aktif', true)
            ->where(function ($query) {
                $query->whereNull('materi_id')
                    ->orWhereHas('materi', function ($materiQuery) {
                        $materiQuery->where('status_aktif', true);
                    });
            })
            ->orderBy('created_at', 'desc')
            ->get();

        $kuisUmum = $kuisAktif->whereNull('materi_id')->values();
        $kuisMateri = $kuisAktif->whereNotNull('materi_id')->values();

        $materiIdsForProgress = $kuisMateri->pluck('materi_id')
            ->filter()
            ->unique()
            ->values();

        $progressRows = SesiBaca::where('pengguna_id', $user->id)
            ->when($materiIdsForProgress->count() > 0, function ($query) use ($materiIdsForProgress) {
                $query->whereIn('materi_id', $materiIdsForProgress->all());
            })
            ->select(
                'materi_id',
                DB::raw('MAX(COALESCE(progres_persen, 0)) as progres_persen'),
                DB::raw('MAX(CASE WHEN selesai IS NULL THEN 0 ELSE 1 END) as selesai_flag')
            )
            ->groupBy('materi_id')
            ->get();

        $progressMap = [];
        $completedMateriIds = [];
        foreach ($progressRows as $row) {
            $progressMap[$row->materi_id] = [
                'progres' => (int) ($row->progres_persen ?? 0),
                'selesai' => (int) ($row->selesai_flag ?? 0),
            ];
            if (($row->selesai_flag ?? 0) || ((int) ($row->progres_persen ?? 0) >= 80)) {
                $completedMateriIds[] = $row->materi_id;
            }
        }

        return view('dashboard.siswa.kuis-index', compact('user', 'pageTitle', 'kuisUmum', 'kuisMateri', 'progressMap', 'completedMateriIds'));
    }

    public function kuisUmumShow(Kuis $kuis)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        if (!$kuis->status_aktif) {
            abort(404);
        }

        if ($kuis->materi_id) {
            $kuis->load('materi');
            if ($kuis->materi && ($block = $this->ensureMateriLevelAccess($user, $kuis->materi))) {
                return $block;
            }
            return redirect()->route('dashboard.siswa.materi.kuis.show', [
                'materi' => $kuis->materi_id,
                'kuis' => $kuis->id,
            ]);
        }

        $kuis->load('pertanyaan.opsi');
        $pageTitle = 'Kuis Umum';
        $submitRoute = route('dashboard.siswa.kuis.submit', $kuis->id);
        $backUrl = route('dashboard.siswa.kuis');
        $displayTitle = $kuis->judul ?? 'Kuis Umum';

        return view('dashboard.siswa.kuis', compact('user', 'pageTitle', 'kuis', 'submitRoute', 'backUrl', 'displayTitle'));
    }

    public function submitKuisUmum(Request $request, Kuis $kuis)
    {
        [$user, $redirect] = $this->requireSiswa();
        if ($redirect) {
            return $redirect;
        }

        if (!$kuis->status_aktif) {
            abort(404);
        }

        if ($kuis->materi_id) {
            return redirect()->route('dashboard.siswa.materi.kuis.show', [
                'materi' => $kuis->materi_id,
                'kuis' => $kuis->id,
            ]);
        }

        $kuis->load('pertanyaan.opsi');
        $skor = $this->storeKuisHasil($request, $kuis, $user->id);

        return redirect()
            ->route('dashboard.siswa.kuis.show', $kuis->id)
            ->with('success', 'Kuis selesai. Skor kamu: ' . $skor);
    }

    private function requireSiswa()
    {
        $user = Auth::user();
        if (!$user || $user->peran !== 'siswa') {
            return [null, redirect('/dashboard')];
        }

        $user->load('siswa');

        return [$user, null];
    }

    private function ensureMateriLevelAccess($user, Materi $materi)
    {
        return null;
    }

    private function isMateriCompleted(int $userId, int $materiId): bool
    {
        $row = SesiBaca::where('pengguna_id', $userId)
            ->where('materi_id', $materiId)
            ->select(
                DB::raw('MAX(COALESCE(progres_persen, 0)) as progres_persen'),
                DB::raw('MAX(CASE WHEN selesai IS NULL THEN 0 ELSE 1 END) as selesai_flag')
            )
            ->first();

        if (!$row) {
            return false;
        }

        return (bool) ($row->selesai_flag ?? 0) || ((int) ($row->progres_persen ?? 0) >= 80);
    }

    private function storeKuisHasil(Request $request, Kuis $kuis, int $userId): int
    {
        $jawaban = $request->input('jawaban', []);
        $jawabanTeks = $request->input('jawaban_teks', []);
        $totalPertanyaan = $kuis->pertanyaan->count();
        $totalBenar = 0;

        $hasil = KuisHasil::create([
            'kuis_id' => $kuis->id,
            'pengguna_id' => $userId,
            'total_pertanyaan' => $totalPertanyaan,
            'total_benar' => 0,
            'skor' => 0,
            'selesai_at' => now(),
        ]);

        $similarity = function (string $a, string $b): int {
            $normalize = function (string $s): string {
                $s = mb_strtolower($s);
                $s = preg_replace('/[^\p{L}\p{N}\s-]/u', ' ', $s);
                $s = preg_replace('/\s+/', ' ', $s);
                return trim($s);
            };
            $a = $normalize($a);
            $b = $normalize($b);
            if ($a === '' || $b === '') {
                return 0;
            }
            $maxLen = max(mb_strlen($a), mb_strlen($b));
            if ($maxLen === 0) {
                return 0;
            }
            $dist = levenshtein($a, $b);
            $score = (1 - ($dist / $maxLen)) * 100;
            return (int) max(0, min(100, round($score)));
        };

        foreach ($kuis->pertanyaan as $pertanyaan) {
            if (in_array($pertanyaan->tipe, ['pilihan', 'listening'], true)) {
                $selectedId = $jawaban[$pertanyaan->id] ?? null;
                $opsi = $pertanyaan->opsi->firstWhere('id', (int) $selectedId);
                $benar = $opsi ? (bool) $opsi->benar : false;

                if ($benar) {
                    $totalBenar += 1;
                }

                KuisJawaban::create([
                    'kuis_hasil_id' => $hasil->id,
                    'pertanyaan_id' => $pertanyaan->id,
                    'opsi_id' => $opsi?->id,
                    'benar' => $benar,
                ]);
                continue;
            }

            $teks = trim((string) ($jawabanTeks[$pertanyaan->id] ?? ''));
            $skorAuto = null;
            $benar = false;

            if ($pertanyaan->tipe === 'essay') {
                if ($pertanyaan->keyword) {
                    $keywords = array_filter(array_map('trim', explode(',', $pertanyaan->keyword)));
                    if (count($keywords) > 0) {
                        $normalized = mb_strtolower($teks);
                        $match = 0;
                        foreach ($keywords as $kw) {
                            if ($kw !== '' && str_contains($normalized, mb_strtolower($kw))) {
                                $match += 1;
                            }
                        }
                        $skorAuto = (int) round(($match / count($keywords)) * 100);
                        $benar = false;
                    }
                }
            } elseif ($pertanyaan->tipe === 'speaking') {
                $target = (string) ($pertanyaan->jawaban_teks ?? '');
                $skorAuto = $similarity($teks, $target);
                $benar = false;
            }

            KuisJawaban::create([
                'kuis_hasil_id' => $hasil->id,
                'pertanyaan_id' => $pertanyaan->id,
                'opsi_id' => null,
                'benar' => $benar,
                'jawaban_teks' => $teks,
                'skor_auto' => $skorAuto,
                'status_koreksi' => 'pending',
            ]);
        }

        $skor = $totalPertanyaan > 0 ? (int) round(($totalBenar / $totalPertanyaan) * 100) : 0;
        $hasil->update([
            'total_benar' => $totalBenar,
            'skor' => $skor,
        ]);

        return $skor;
    }

    private function resolveMateriKuis(Materi $materi, Kuis $kuis): Kuis
    {
        if (
            !$kuis->status_aktif ||
            (int) $kuis->materi_id !== (int) $materi->id
        ) {
            abort(404);
        }

        return $kuis;
    }
}
