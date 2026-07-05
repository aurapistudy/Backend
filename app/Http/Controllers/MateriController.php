<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersByAssignedMapel;
use App\Exceptions\GeminiCoverException;
use App\Models\Level;
use App\Models\Materi;
use App\Models\MateriBab;
use App\Models\MataPelajaran;
use App\Models\TahunAkademik;
use App\Services\HuggingFaceCoverService;
use App\Services\PdfCompressionService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MateriController extends Controller
{
    use FiltersByAssignedMapel;

    private const PDF_TARGET_MAX_KB = 10240;
    public function index()
    {
        $search = trim((string) request('search', ''));
        $perPage = (int) request('per_page', 10);
        $perPage = max(1, min($perPage, 100));
        $levelId = request('level_id');
        $mapelId = request('mata_pelajaran_id');

        $materi = $this->applyMapelFilterToMateri(
            Materi::with(['pengguna', 'level', 'mataPelajaran'])->withCount('bab')
        )
            ->when($this->isSiswaApiRequest(), function ($query) {
                $query->where('status_aktif', true);
            })
            ->when(is_numeric($levelId), function ($query) use ($levelId) {
                $query->where('level_id', (int) $levelId);
            })
            ->when(is_numeric($mapelId), function ($query) use ($mapelId) {
                $query->where('mata_pelajaran_id', (int) $mapelId);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('judul', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%")
                        ->orWhere('semester', 'like', "%{$search}%")
                        ->orWhere('tipe_konten', 'like', "%{$search}%")
                        ->when(ctype_digit($search), function ($idQuery) use ($search) {
                            $idQuery->orWhere('id', (int) $search);
                        })
                        ->orWhereHas('mataPelajaran', function ($relation) use ($search) {
                            $relation->where('nama', 'like', "%{$search}%");
                        })
                        ->orWhereHas('level', function ($relation) use ($search) {
                            $relation->where('nama', 'like', "%{$search}%");
                        })
                        ->orWhereHas('pengguna', function ($relation) use ($search) {
                            $relation->where('nama', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json($materi);
        }

        return view('dashboard.materi.materi', compact('materi', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $levels = Level::where('status_aktif', true)->orderBy('nama')->get();
        $mataPelajarans = MataPelajaran::where('status_aktif', true)
            ->orderBy('nama')
            ->get();
        $tahunAktif = TahunAkademik::active();

        return view('dashboard.materi.create', compact('levels', 'mataPelajarans', 'tahunAktif'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $tahunAktif = TahunAkademik::active();
        if (!$tahunAktif || !$tahunAktif->semester) {
            return redirect()->route('materi.create')
                ->withInput()
                ->withErrors([
                    'tahun_akademik' => 'Tidak ada tahun akademik aktif. Admin harus mengaktifkan periode semester terlebih dahulu.',
                ]);
        }

        $maxUploadKb = $this->getServerUploadLimitInKb();

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajaran,id',
            'level_id' => 'nullable|exists:level,id',
            'judul_bab_pertama' => 'required|string|max:200',
            'tipe_konten' => 'required|in:teks,file',
            'konten_teks' => 'nullable|string|required_if:tipe_konten,teks',
            'file_path' => "nullable|file|mimes:pdf,doc,docx|max:{$maxUploadKb}|required_if:tipe_konten,file",
            'pdf_page_selection' => 'nullable|string',
            'bab' => 'nullable|array',
            'bab.*.judul_bab' => 'nullable|string|max:200',
            'bab.*.urutan' => 'nullable|integer|min:1',
            'bab.*.tipe_konten' => 'nullable|in:teks,file',
            'bab.*.konten_teks' => 'nullable|string',
            'bab.*.pdf_page_selection' => 'nullable|string',
            'bab.*.status_aktif' => 'nullable|boolean',
            'bab_files' => 'nullable|array',
            'bab_files.*' => "nullable|file|mimes:pdf,doc,docx|max:{$maxUploadKb}",
            'cover_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'generated_cover_temp_path' => 'nullable|string',
            'use_generated_cover' => 'nullable|boolean',
            'status_aktif' => 'boolean',
        ], [
            'judul.required' => 'Judul wajib diisi',
            'judul_bab_pertama.required' => 'Judul Materi 1 wajib diisi',
            'tipe_konten.required' => 'Tipe konten Materi 1 wajib dipilih',
            'konten_teks.required_if' => 'Konten teks wajib diisi jika tipe konten adalah teks',
            'file_path.required_if' => 'File wajib diupload jika tipe konten adalah file',
            'file_path.file' => 'File materi tidak valid. Pilih file PDF, DOC, atau DOCX.',
            'file_path.mimes' => 'Format file materi harus PDF, DOC, atau DOCX.',
            'file_path.max' => 'Ukuran file materi melebihi batas upload server.',
            'mata_pelajaran_id.exists' => 'Kategori yang dipilih tidak valid',
            'level_id.exists' => 'Level yang dipilih tidak valid',
            'cover_path.image' => 'Cover mata pelajaran harus berupa gambar.',
            'cover_path.mimes' => 'Format cover mata pelajaran harus JPG, JPEG, PNG, atau WEBP.',
            'cover_path.max' => 'Ukuran cover mata pelajaran terlalu besar. Maksimal 5 MB. Silakan kompres atau pilih gambar yang lebih kecil.',
            'generated_cover_temp_path.string' => 'Path cover AI tidak valid.',
            'bab.array' => 'Data materi tidak valid.',
        ]);

        $this->validateBabEntries($request);

        // Guru mapel boleh membuat materi baru; setelah dibuat akan otomatis di-assign ke guru tsb.
        // Admin tetap bisa membuat materi di kategori mana pun.
        if (Auth::user()?->isAdmin()) {
            $this->authorizeMapelAccess(
                !empty($validated['mata_pelajaran_id']) ? (int) $validated['mata_pelajaran_id'] : null
            );
        }

        $firstBabPayload = [
            'judul_bab' => trim((string) $validated['judul_bab_pertama']),
            'urutan' => 1,
            'tipe_konten' => (string) $validated['tipe_konten'],
            'konten_teks' => $validated['tipe_konten'] === 'teks' ? ($validated['konten_teks'] ?? null) : null,
            'file_path' => null,
            'pdf_page_selection' => null,
            'status_aktif' => true,
        ];

        if ($request->hasFile('file_path')) {
            $storedFile = $this->storeMateriFile(
                $request->file('file_path'),
                $request->input('pdf_page_selection')
            );
            $firstBabPayload['file_path'] = $storedFile['path'];
            $firstBabPayload['pdf_page_selection'] = $request->input('pdf_page_selection');
        }

        if ($request->hasFile('cover_path')) {
            $cover = $request->file('cover_path');
            $coverName = time() . '_cover_' . $cover->getClientOriginalName();
            $coverPath = $cover->storeAs('materi/covers', $coverName, 'public');
            $validated['cover_path'] = $coverPath;
        } elseif ($request->boolean('use_generated_cover') && $request->filled('generated_cover_temp_path')) {
            $validated['cover_path'] = $this->moveGeneratedCoverToPermanent(
                (string) $request->input('generated_cover_temp_path')
            );
        } elseif ($request->filled('generated_cover_temp_path')) {
            $this->deleteGeneratedTempCover((string) $request->input('generated_cover_temp_path'));
        }

        $validated['tipe_konten'] = 'bab';
        $validated['konten_teks'] = null;
        $validated['file_path'] = null;
        $validated['pdf_page_selection'] = null;
        $validated['jumlah_halaman'] = null;
        $validated['dibuat_oleh'] = Auth::id();
        $validated['status_aktif'] = $request->has('status_aktif') ? true : false;
        $validated['semester'] = $tahunAktif->semester;

        DB::transaction(function () use ($validated, $request, $firstBabPayload) {
            $materi = Materi::create($validated);

            // Auto-assign materi yang dibuat ke guru mapel pembuatnya.
            $user = Auth::user();
            if ($user && $user->isGuruMapel()) {
                $user->attachMateriAsGuru((int) $materi->id);
            }

            MateriBab::create(array_merge($firstBabPayload, [
                'materi_id' => $materi->id,
            ]));
            $this->storeBabEntries($materi, $request, 2);
        });
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Mata pelajaran berhasil ditambahkan!'], 201);
        }

        return redirect()->route('materi.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    public function generateCoverPreview(
        Request $request,
        HuggingFaceCoverService $huggingFaceCoverService
    ) {
        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'mata_pelajaran' => 'nullable|string|max:200',
            'level' => 'nullable|string|max:200',
            'prompt_tambahan' => 'nullable|string|max:500',
            'previous_temp_path' => 'nullable|string',
        ], [
            'judul.required' => 'Judul wajib diisi sebelum generate cover.',
        ]);

        try {
            $generated = $huggingFaceCoverService->generateBookCover($validated);
            $extension = $this->extensionFromMimeType($generated['mime_type']);
            $fileName = 'temp/covers/' . now()->format('YmdHis') . '_' . Str::uuid() . '.' . $extension;

            Storage::disk('public')->put($fileName, $generated['binary']);

            if (!empty($validated['previous_temp_path'])) {
                $this->deleteGeneratedTempCover((string) $validated['previous_temp_path']);
            }

            return response()->json([
                'message' => 'Preview cover berhasil dibuat.',
                'temp_path' => $fileName,
                'url' => route('media.public.show', ['path' => $fileName], false),
                'prompt' => $generated['prompt'],
            ]);
        } catch (GeminiCoverException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], $exception->status());
        }
    }

    public function discardCoverPreview(Request $request)
    {
        $validated = $request->validate([
            'temp_path' => 'required|string',
        ]);

        $this->deleteGeneratedTempCover((string) $validated['temp_path']);

        return response()->json([
            'message' => 'Preview cover dibuang.',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $materi = Materi::with([
            'pengguna',
            'level',
            'mataPelajaran',
            'bab' => function ($query) {
                $query->with(['kuis' => function ($kuisQuery) {
                    $kuisQuery->orderByDesc('created_at');
                }])->withCount('kuis')->orderBy('urutan');
            },
        ])->findOrFail($id);

        if (!$this->isSiswaApiRequest()) {
            $this->authorizeMateriAccess($materi);
        }

        if ($this->isSiswaApiRequest()) {
            if (!$materi->status_aktif) {
                return response()->json(['message' => 'Mata pelajaran tidak tersedia'], 404);
            }
        }

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json($materi);
        }

        return view('dashboard.materi.show', compact('materi'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $materi = Materi::with('bab')->findOrFail($id);
        $this->authorizeMateriAccess($materi);
        $levels = Level::where('status_aktif', true)->orderBy('nama')->get();
        $mataPelajarans = MataPelajaran::where('status_aktif', true)
            ->orderBy('nama')
            ->get();
        return view('dashboard.materi.edit', compact('materi', 'levels', 'mataPelajarans'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $materi = Materi::findOrFail($id);
        $this->authorizeMateriAccess($materi);

        $validated = $request->validate([
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajaran,id',
            'level_id' => 'nullable|exists:level,id',
            'cover_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'status_aktif' => 'boolean',
        ], [
            'judul.required' => 'Judul wajib diisi',
            'mata_pelajaran_id.exists' => 'Kategori yang dipilih tidak valid',
            'level_id.exists' => 'Level yang dipilih tidak valid',
            'cover_path.image' => 'Cover mata pelajaran harus berupa gambar.',
            'cover_path.mimes' => 'Format cover mata pelajaran harus JPG, JPEG, PNG, atau WEBP.',
            'cover_path.max' => 'Ukuran cover mata pelajaran terlalu besar. Maksimal 5 MB. Silakan kompres atau pilih gambar yang lebih kecil.',
        ]);

        if ($materi->file_path && Storage::disk('public')->exists($materi->file_path)) {
            Storage::disk('public')->delete($materi->file_path);
        }

        if ($request->hasFile('cover_path')) {
            if ($materi->cover_path && Storage::disk('public')->exists($materi->cover_path)) {
                Storage::disk('public')->delete($materi->cover_path);
            }
            $cover = $request->file('cover_path');
            $coverName = time() . '_cover_' . $cover->getClientOriginalName();
            $coverPath = $cover->storeAs('materi/covers', $coverName, 'public');
            $validated['cover_path'] = $coverPath;
        } else {
            $validated['cover_path'] = $materi->cover_path;
        }

        $validated['tipe_konten'] = 'bab';
        $validated['konten_teks'] = null;
        $validated['file_path'] = null;
        $validated['pdf_page_selection'] = null;
        $validated['jumlah_halaman'] = null;
        $validated['status_aktif'] = $request->has('status_aktif') ? true : false;

        if (Auth::user()?->isAdmin()) {
            $this->authorizeMapelAccess(
                isset($validated['mata_pelajaran_id']) ? (int) $validated['mata_pelajaran_id'] : null
            );
        }

        $materi->update($validated);

        $materi = $materi->fresh();
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Mata pelajaran berhasil diperbarui!', 'data' => $materi]);
        }

        return redirect()->route('materi.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $materi = Materi::findOrFail($id);
        $this->authorizeMateriAccess($materi);
        $filePath = $materi->file_path;
        $coverPath = $materi->cover_path;

        DB::transaction(function () use ($materi) {
            $sesiBacaIds = DB::table('sesi_baca')
                ->where('materi_id', $materi->id)
                ->pluck('id');

            DB::table('log_akses_materi')->where('materi_id', $materi->id)->delete();
            DB::table('log_perintah_suara')->whereIn('sesi_id', $sesiBacaIds)->delete();
            DB::table('sesi_baca')->where('materi_id', $materi->id)->delete();
            DB::table('rak_buku')->where('materi_id', $materi->id)->delete();
            DB::table('catatan_siswa')->where('materi_id', $materi->id)->update(['materi_id' => null]);
            DB::table('kuis')->where('materi_id', $materi->id)->update(['materi_id' => null]);

            $materi->delete();
        });

        // Delete files only after the database delete succeeds.
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
        if ($coverPath && Storage::disk('public')->exists($coverPath)) {
            Storage::disk('public')->delete($coverPath);
        }

        if (request()->wantsJson() || request()->is('api/*')) {
            return response()->json(['message' => 'Mata pelajaran berhasil dihapus!']);
        }

        return redirect()->route('materi.index')
            ->with('success', 'Mata pelajaran berhasil dihapus!');
    }

    private function isSiswaApiRequest(): bool
    {
        $user = Auth::user();

        return (request()->wantsJson() || request()->is('api/*'))
            && $user
            && $user->peran === 'siswa';
    }

    private function validateBabEntries(Request $request): void
    {
        $babEntries = $request->input('bab', []);
        if (!is_array($babEntries) || count($babEntries) === 0) {
            return;
        }

        $babFiles = $request->file('bab_files', []);

        foreach ($babEntries as $index => $bab) {
            $tipeKonten = $bab['tipe_konten'] ?? null;
            $judulBab = trim((string) ($bab['judul_bab'] ?? ''));

            if ($judulBab === '') {
                throw ValidationException::withMessages([
                    "bab.$index.judul_bab" => 'Judul materi wajib diisi.',
                ]);
            }

            if (!in_array($tipeKonten, ['teks', 'file'], true)) {
                throw ValidationException::withMessages([
                    "bab.$index.tipe_konten" => 'Tipe konten materi wajib dipilih.',
                ]);
            }

            if ($tipeKonten === 'teks' && trim((string) ($bab['konten_teks'] ?? '')) === '') {
                throw ValidationException::withMessages([
                    "bab.$index.konten_teks" => 'Konten teks materi wajib diisi jika tipe materi adalah teks.',
                ]);
            }

            if ($tipeKonten === 'file' && empty($babFiles[$index])) {
                throw ValidationException::withMessages([
                    "bab_files.$index" => 'File materi wajib diupload jika tipe materi adalah file.',
                ]);
            }
        }
    }

    private function storeBabEntries(Materi $materi, Request $request, int $defaultStartUrutan = 1): void
    {
        $babEntries = $request->input('bab', []);
        $babFiles = $request->file('bab_files', []);

        foreach ($babEntries as $index => $bab) {
            $payload = [
                'materi_id' => $materi->id,
                'judul_bab' => trim((string) ($bab['judul_bab'] ?? '')),
                'urutan' => (int) ($bab['urutan'] ?? ($defaultStartUrutan + $index)),
                'tipe_konten' => (string) ($bab['tipe_konten'] ?? 'teks'),
                'konten_teks' => $bab['tipe_konten'] === 'teks' ? ($bab['konten_teks'] ?? null) : null,
                'pdf_page_selection' => null,
                'status_aktif' => array_key_exists('status_aktif', $bab) ? (bool) $bab['status_aktif'] : true,
            ];

            if (($bab['tipe_konten'] ?? null) === 'file' && !empty($babFiles[$index])) {
                $storedFile = $this->storeMateriFile(
                    $babFiles[$index],
                    $bab['pdf_page_selection'] ?? null
                );
                $payload['file_path'] = $storedFile['path'];
                $payload['pdf_page_selection'] = $bab['pdf_page_selection'] ?? null;
            } else {
                $payload['file_path'] = null;
            }

            MateriBab::create($payload);
        }
    }

    private function moveGeneratedCoverToPermanent(string $tempPath): string
    {
        $normalizedPath = $this->normalizeGeneratedTempPath($tempPath);

        if (!Storage::disk('public')->exists($normalizedPath)) {
            throw ValidationException::withMessages([
                'cover_path' => 'Preview cover AI tidak ditemukan. Silakan generate ulang.',
            ]);
        }

        $extension = pathinfo($normalizedPath, PATHINFO_EXTENSION) ?: 'png';
        $targetPath = 'materi/covers/' . now()->format('YmdHis') . '_' . Str::uuid() . '.' . $extension;

        Storage::disk('public')->copy($normalizedPath, $targetPath);
        Storage::disk('public')->delete($normalizedPath);

        return $targetPath;
    }

    private function deleteGeneratedTempCover(string $tempPath): void
    {
        $normalizedPath = $this->normalizeGeneratedTempPath($tempPath);

        if (Storage::disk('public')->exists($normalizedPath)) {
            Storage::disk('public')->delete($normalizedPath);
        }
    }

    private function normalizeGeneratedTempPath(string $path): string
    {
        $normalizedPath = trim(str_replace('\\', '/', $path), '/');

        if (!str_starts_with($normalizedPath, 'temp/covers/')) {
            throw ValidationException::withMessages([
                'cover_path' => 'Path preview cover AI tidak valid.',
            ]);
        }

        return $normalizedPath;
    }

    private function extensionFromMimeType(string $mimeType): string
    {
        return match (strtolower($mimeType)) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/webp' => 'webp',
            default => 'png',
        };
    }

    private function storeMateriFile($file, $pageSelection = null): array
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(300);
        }

        $extension = strtolower((string) $file->getClientOriginalExtension());
        $originalSize = $file->getSize() ?: 0;
        $maxTargetBytes = self::PDF_TARGET_MAX_KB * 1024;
        $pdfService = app(PdfCompressionService::class);

        if ($extension !== 'pdf' && $originalSize > $maxTargetBytes) {
            throw ValidationException::withMessages([
                'file_path' => 'File DOC/DOCX maksimal 10 MB. Kompres otomatis hanya diterapkan untuk PDF.',
            ]);
        }

        if ($extension !== 'pdf' && $pageSelection) {
            throw ValidationException::withMessages([
                'file_path' => 'Pilihan halaman hanya berlaku untuk file PDF.',
            ]);
        }

        if ($extension !== 'pdf') {
            $fileName = time() . '_' . $file->getClientOriginalName();
            return [
                'path' => $file->storeAs('materi', $fileName, 'public'),
                'page_count' => null,
            ];
        }

        $tempDirectory = storage_path('app/tmp/pdf-compression');
        if (!is_dir($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $baseName) ?: 'materi';
        $uniqueToken = time() . '_' . uniqid();

        $sourcePath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_source.pdf';
        $selectedPath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_selected.pdf';
        $targetPath = $tempDirectory . DIRECTORY_SEPARATOR . $uniqueToken . '_compressed.pdf';
        $bestEffortPath = $sourcePath . '.best';

        $file->move($tempDirectory, basename($sourcePath));

        $workingPath = $sourcePath;
        $pageCount = $pdfService->getPageCount($sourcePath);
        $selectedPages = $this->parseSelectedPages($pageSelection);

        if (!empty($selectedPages)) {
            if ($pageCount === null) {
                @unlink($sourcePath);

                throw ValidationException::withMessages([
                    'file_path' => 'Jumlah halaman PDF tidak bisa dibaca. Pastikan Ghostscript terpasang dengan benar.',
                ]);
            }

            $invalidPage = collect($selectedPages)->first(fn (int $page) => $page < 1 || $page > $pageCount);
            if ($invalidPage !== null) {
                @unlink($sourcePath);

                throw ValidationException::withMessages([
                    'file_path' => "Pilihan halaman tidak valid. Halaman {$invalidPage} berada di luar total {$pageCount} halaman.",
                ]);
            }

            if (count($selectedPages) < $pageCount) {
                $selectionResult = $pdfService->extractSelectedPages(
                    $sourcePath,
                    $selectedPath,
                    $this->buildGhostscriptPageList($selectedPages)
                );

                if (!($selectionResult['success'] ?? false)) {
                    @unlink($sourcePath);
                    @unlink($selectedPath);

                    throw ValidationException::withMessages([
                        'file_path' => 'PDF gagal dipotong sesuai halaman yang dicentang.',
                    ]);
                }

                $workingPath = $selectedPath;
            }

            $pageCount = count($selectedPages);
        }

        $workingSize = filesize($workingPath) ?: 0;

        if ($workingSize > $maxTargetBytes) {
            $result = $pdfService->compressToTarget(
                $workingPath,
                $targetPath,
                $maxTargetBytes
            );

            $finalPath = $result['output_path'] ?? $workingPath;
            $finalSize = $result['final_size'] ?? 0;

            if (!is_file($finalPath) || $finalSize > $maxTargetBytes) {
                @unlink($sourcePath);
                @unlink($selectedPath);
                @unlink($targetPath);
                @unlink($bestEffortPath);

                $message = ($result['tool'] ?? null) === null
                    ? 'PDF di atas 10 MB diterima, tetapi server belum memiliki tool kompres PDF otomatis. Pasang Ghostscript atau upload PDF yang lebih kecil.'
                    : 'PDF gagal dikompres hingga 10 MB. Coba kompres PDF-nya sedikit lagi atau kurangi kualitas gambar di dalam PDF.';

                throw ValidationException::withMessages([
                    'file_path' => $message,
                ]);
            }
        } else {
            $finalPath = $workingPath;
            $finalSize = $workingSize;
        }

        $storedFileName = time() . '_' . $safeBaseName . '.pdf';
        $storedPath = 'materi/' . $storedFileName;

        Storage::disk('public')->put($storedPath, file_get_contents($finalPath));

        @unlink($sourcePath);
        @unlink($selectedPath);
        @unlink($targetPath);
        @unlink($bestEffortPath);

        if ($pageCount === null && is_file($finalPath)) {
            $pageCount = $pdfService->getPageCount($finalPath);
        }

        return [
            'path' => $storedPath,
            'page_count' => $pageCount,
        ];
    }

    private function parseSelectedPages(?string $pageSelection): array
    {
        if ($pageSelection === null || trim($pageSelection) === '') {
            return [];
        }

        $parts = preg_split('/\s*,\s*/', trim($pageSelection)) ?: [];
        $pages = [];

        foreach ($parts as $part) {
            if ($part === '' || !ctype_digit($part)) {
                continue;
            }

            $page = (int) $part;
            if ($page > 0) {
                $pages[] = $page;
            }
        }

        $pages = array_values(array_unique($pages));
        sort($pages);

        return $pages;
    }

    private function buildGhostscriptPageList(array $pages): string
    {
        sort($pages);

        $ranges = [];
        $start = null;
        $previous = null;

        foreach ($pages as $page) {
            if ($start === null) {
                $start = $page;
                $previous = $page;
                continue;
            }

            if ($page === $previous + 1) {
                $previous = $page;
                continue;
            }

            $ranges[] = $start === $previous ? (string) $start : "{$start}-{$previous}";
            $start = $page;
            $previous = $page;
        }

        if ($start !== null) {
            $ranges[] = $start === $previous ? (string) $start : "{$start}-{$previous}";
        }

        return implode(',', $ranges);
    }

    private function getServerUploadLimitInKb(): int
    {
        $uploadMax = $this->convertIniSizeToKb((string) ini_get('upload_max_filesize'));
        $postMax = $this->convertIniSizeToKb((string) ini_get('post_max_size'));
        $effectiveLimit = min($uploadMax, $postMax);

        return $effectiveLimit > 0 ? $effectiveLimit : self::PDF_TARGET_MAX_KB;
    }

    private function convertIniSizeToKb(string $value): int
    {
        $value = trim($value);

        if ($value === '') {
            return self::PDF_TARGET_MAX_KB;
        }

        $unit = strtolower(substr($value, -1));
        $number = (float) $value;

        return match ($unit) {
            'g' => (int) round($number * 1024 * 1024),
            'm' => (int) round($number * 1024),
            'k' => (int) round($number),
            default => (int) round($number / 1024),
        };
    }
}
