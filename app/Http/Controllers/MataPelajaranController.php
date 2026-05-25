<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\FiltersByAssignedMapel;
use Illuminate\Http\Request;
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

        MataPelajaran::create($validated);

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

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus!');
    }
}
