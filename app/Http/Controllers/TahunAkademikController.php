<?php

namespace App\Http\Controllers;

use App\Models\GuruMateri;
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
        return view('dashboard.tahun-akademik.create');
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
            ->with('success', "Tahun akademik {$tahunAkademik->nama} sekarang aktif.");
    }

    private function validatePayload(Request $request, ?int $ignoreId = null): array
    {
        $validated = $request->validate([
            'nama' => [
                'required',
                'string',
                'max:20',
                'regex:/^\d{4}\/\d{4}$/',
                Rule::unique('tahun_akademik', 'nama')->ignore($ignoreId),
            ],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status_aktif' => 'boolean',
        ], [
            'nama.required' => 'Nama tahun akademik wajib diisi.',
            'nama.regex' => 'Format nama harus seperti 2025/2026.',
            'nama.unique' => 'Tahun akademik sudah ada.',
            'tanggal_mulai.required' => 'Tanggal mulai wajib diisi.',
            'tanggal_selesai.required' => 'Tanggal selesai wajib diisi.',
            'tanggal_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif');

        return $validated;
    }
}
