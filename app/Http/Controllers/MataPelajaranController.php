<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersByAssignedMapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MataPelajaran;

class MataPelajaranController extends Controller
{
    use FiltersByAssignedMapel;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = trim((string) request('search', ''));

        $mataPelajarans = $this->applyMapelFilterToMataPelajaran(MataPelajaran::query())
            ->when($this->isSiswaApiRequest(), function ($query) {
                $query->where('status_aktif', true);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('id', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->paginate(10)
            ->withQueryString();

        if ($this->isApiRequest()) {
            return response()->json($mataPelajarans);
        }

        return view('dashboard.mata-pelajaran.index', compact('mataPelajarans', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.mata-pelajaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:mata_pelajaran,nama',
            'deskripsi' => 'nullable|string',
            'status_aktif' => 'boolean',
        ], [
            'nama.required' => 'Nama mata pelajaran wajib diisi',
            'nama.unique' => 'Nama mata pelajaran sudah ada',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif') ? true : false;

        $mataPelajaran = MataPelajaran::create($validated);

        if ($this->isApiRequest()) {
            return response()->json([
                'message' => 'Mata pelajaran berhasil ditambahkan!',
                'data' => $mataPelajaran,
            ], 201);
        }

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $mataPelajaran = MataPelajaran::findOrFail($id);
        $this->authorizeMapelAccess($mataPelajaran->id);

        if ($this->isApiRequest()) {
            return response()->json($mataPelajaran);
        }

        return view('dashboard.mata-pelajaran.show', compact('mataPelajaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $mataPelajaran = MataPelajaran::findOrFail($id);
        return view('dashboard.mata-pelajaran.edit', compact('mataPelajaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $mataPelajaran = MataPelajaran::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:100|unique:mata_pelajaran,nama,' . $id,
            'deskripsi' => 'nullable|string',
            'status_aktif' => 'boolean',
        ], [
            'nama.required' => 'Nama mata pelajaran wajib diisi',
            'nama.unique' => 'Nama mata pelajaran sudah ada',
        ]);

        $validated['status_aktif'] = $request->has('status_aktif') ? true : false;

        $mataPelajaran->update($validated);

        if ($this->isApiRequest()) {
            return response()->json([
                'message' => 'Mata pelajaran berhasil diperbarui!',
                'data' => $mataPelajaran->fresh(),
            ]);
        }

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $mataPelajaran = MataPelajaran::findOrFail($id);
        $mataPelajaran->delete();

        if ($this->isApiRequest()) {
            return response()->json([
                'message' => 'Mata pelajaran berhasil dihapus!',
            ]);
        }

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus!');
    }

    /**
     * Daftar mata pelajaran aktif (untuk dropdown/filter Flutter).
     */
    public function aktif()
    {
        $mataPelajaranAktif = $this->applyMapelFilterToMataPelajaran(MataPelajaran::query())
            ->where('status_aktif', true)
            ->orderBy('nama')
            ->get();

        return response()->json($mataPelajaranAktif);
    }

    private function isApiRequest(): bool
    {
        return request()->wantsJson() || request()->is('api/*');
    }

    private function isSiswaApiRequest(): bool
    {
        $user = Auth::user();

        return $this->isApiRequest()
            && $user
            && $user->peran === 'siswa';
    }
}
