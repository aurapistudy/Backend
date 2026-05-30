<?php

namespace App\Http\Controllers;

use App\Models\GuruMateri;
use App\Models\Materi;
use App\Models\Pengguna;
use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TahunAkademikController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $tahunAkademik = TahunAkademik::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where('nama', 'like', "%{$search}%");
            })
            ->withCount('penugasanGuru')
            ->orderByDesc('tanggal_mulai')
            ->paginate(10)
            ->withQueryString();

        $tahunAktif = TahunAkademik::active();

        return view('dashboard.tahun-akademik.index', compact('tahunAkademik', 'search', 'tahunAktif'));
    }

    public function create()
    {
        $tahunAktif = TahunAkademik::active();

        return view('dashboard.tahun-akademik.create', compact('tahunAktif'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePayload($request);

        $tahun = TahunAkademik::create($validated);

        if ($validated['status_aktif']) {
            TahunAkademik::ensureSingleActive($tahun);
        }

        return redirect()->route('tahun-akademik.index')
            ->with('success', 'Tahun akademik berhasil ditambahkan!');
    }

    public function penugasan(string $id)
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);

        $guruList = Pengguna::query()
            ->where('peran', 'guru')
            ->where('status_aktif', true)
            ->orderBy('nama')
            ->get(['id', 'nama', 'email']);

        $materiList = Materi::query()
            ->where('status_aktif', true)
            ->orderBy('judul')
            ->get(['id', 'judul']);

        $assignedByGuru = GuruMateri::query()
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->get(['pengguna_id', 'materi_id'])
            ->groupBy('pengguna_id')
            ->map(fn ($rows) => $rows->pluck('materi_id')->map(fn ($materiId) => (int) $materiId)->values()->all());

        return view('dashboard.tahun-akademik.penugasan', compact(
            'tahunAkademik',
            'guruList',
            'materiList',
            'assignedByGuru'
        ));
    }

    public function updatePenugasan(Request $request, string $id)
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);

        $request->validate([
            'penugasan' => 'nullable|array',
            'penugasan.*' => 'nullable|array',
            'penugasan.*.*' => 'integer|exists:materi,id',
        ]);

        $guruList = Pengguna::query()
            ->where('peran', 'guru')
            ->where('status_aktif', true)
            ->get();

        $penugasan = $request->input('penugasan', []);

        foreach ($guruList as $guru) {
            $materiIds = array_map('intval', $penugasan[$guru->id] ?? $penugasan[(string) $guru->id] ?? []);
            $guru->syncMateriAsGuru($materiIds, (int) $tahunAkademik->id);
        }

        return redirect()
            ->route('tahun-akademik.show', $tahunAkademik->id)
            ->with('success', "Penugasan guru untuk {$tahunAkademik->periodeLabel()} berhasil disimpan.");
    }

    public function show(string $id)
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);

        $penugasan = GuruMateri::query()
            ->with(['pengguna:id,nama,email', 'materi:id,judul'])
            ->where('tahun_akademik_id', $tahunAkademik->id)
            ->orderBy('pengguna_id')
            ->get()
            ->groupBy(fn (GuruMateri $row) => $row->pengguna?->nama ?? 'Tidak diketahui');

        return view('dashboard.tahun-akademik.show', compact('tahunAkademik', 'penugasan'));
    }

    public function edit(string $id)
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);

        return view('dashboard.tahun-akademik.edit', compact('tahunAkademik'));
    }

    public function update(Request $request, string $id)
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);
        $validated = $this->validatePayload($request, (int) $tahunAkademik->id);

        $tahunAkademik->update($validated);

        if ($validated['status_aktif']) {
            TahunAkademik::ensureSingleActive($tahunAkademik);
        } else {
            TahunAkademik::clearActiveCache();
        }

        return redirect()->route('tahun-akademik.index')
            ->with('success', 'Tahun akademik berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);

        if ($tahunAkademik->status_aktif) {
            return redirect()->route('tahun-akademik.index')
                ->with('error', 'Tahun akademik yang sedang aktif tidak dapat dihapus.');
        }

        if ($tahunAkademik->penugasanGuru()->exists()) {
            return redirect()->route('tahun-akademik.index')
                ->with('error', 'Tahun akademik masih memiliki riwayat penugasan guru.');
        }

        $tahunAkademik->delete();

        return redirect()->route('tahun-akademik.index')
            ->with('success', 'Tahun akademik berhasil dihapus!');
    }

    public function activate(string $id)
    {
        $tahunAkademik = TahunAkademik::findOrFail($id);

        DB::transaction(function () use ($tahunAkademik) {
            $tahunAkademik->update(['status_aktif' => true]);
            TahunAkademik::ensureSingleActive($tahunAkademik);
        });

        return redirect()->route('tahun-akademik.index')
            ->with('success', "Periode {$tahunAkademik->periodeLabel()} sekarang aktif.");
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('tahun_akademik', 'nama')
                    ->where('semester', $request->input('semester'))
                    ->ignore($ignoreId),
            ],
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status_aktif' => 'boolean',
        ], [
            'nama.required' => 'Nama tahun akademik wajib diisi.',
            'nama.regex' => 'Format nama harus seperti 2025/2026.',
            'nama.unique' => 'Periode tahun akademik dengan semester ini sudah ada.',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester harus Ganjil atau Genap.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif');

        return $validated;
    }
}
