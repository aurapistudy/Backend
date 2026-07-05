<?php

namespace App\Http\Controllers;

use App\Models\Fiksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FiksiController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $fiksi = Fiksi::with('pengguna')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('id', 'like', "%{$search}%")
                        ->orWhere('judul_buku', 'like', "%{$search}%")
                        ->orWhereHas('pengguna', function ($penggunaQuery) use ($search) {
                            $penggunaQuery->where('nama', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        if (request()->expectsJson()) {
            return response()->json($fiksi);
        }

        return view('dashboard.fiksi.fiksi', compact('fiksi', 'search'));
    }

    public function create()
    {
        return view('dashboard.fiksi.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul_buku' => 'required|string|max:200',
            'cover_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'file_path' => 'required|file|mimes:pdf,doc,docx|max:10240',
            'status_aktif' => 'boolean',
        ], [
            'judul_buku.required' => 'Judul buku wajib diisi',
            'cover_path.image' => 'Cover harus berupa gambar.',
            'cover_path.mimes' => 'Format cover harus JPG, JPEG, PNG, atau WEBP.',
            'cover_path.max' => 'Ukuran cover terlalu besar. Maksimal 5 MB.',
            'file_path.required' => 'File fiksi wajib diunggah.',
            'file_path.file' => 'File fiksi tidak valid. Pilih file PDF, DOC, atau DOCX.',
            'file_path.mimes' => 'Format file fiksi harus PDF, DOC, atau DOCX.',
            'file_path.max' => 'Ukuran file fiksi terlalu besar. Maksimal 10 MB.',
        ]);

        if ($request->hasFile('cover_path')) {
    // User upload cover manual — pakai itu, jangan auto-generate
    $cover = $request->file('cover_path');
    $coverName = time() . '_cover_' . $cover->getClientOriginalName();
    $validated['cover_path'] = $cover->storeAs('fiksi/covers', $coverName, 'public');
}

if ($request->hasFile('file_path')) {
    $file = $request->file('file_path');
    $fileName = time() . '_' . $file->getClientOriginalName();

    // Auto-generate cover dari halaman 1 PDF, hanya kalau cover belum diisi manual
    if (!$request->hasFile('cover_path') && strtolower($file->getClientOriginalExtension()) === 'pdf') {
        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $autoCover = app(\App\Services\CoverGeneratorService::class)
            ->generateFromPdf($file->getRealPath(), $baseName);

        if ($autoCover !== null) {
            $validated['cover_path'] = $autoCover;
        }
    }

    $validated['file_path'] = $file->storeAs('fiksi', $fileName, 'public');
}

        $validated['dibuat_oleh'] = Auth::id();
        $validated['status_aktif'] = $request->has('status_aktif');
        $validated['penulis'] = '';

        $fiksi = Fiksi::create($validated);

        if ($request->expectsJson()) {
            return response()->json($fiksi->load('pengguna'), 201);
        }

        return redirect()->route('fiksi.index')
            ->with('success', 'Fiksi berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        $fiksi = Fiksi::with('pengguna')->findOrFail($id);

        if (request()->expectsJson()) {
            return response()->json($fiksi);
        }

        return view('dashboard.fiksi.show', compact('fiksi'));
    }

    public function edit(string $id)
    {
        $fiksi = Fiksi::findOrFail($id);

        return view('dashboard.fiksi.edit', compact('fiksi'));
    }

    public function update(Request $request, string $id)
    {
        $fiksi = Fiksi::findOrFail($id);

        $validated = $request->validate([
            'judul_buku' => 'required|string|max:200',
            'cover_path' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx|max:10240',
            'status_aktif' => 'boolean',
        ], [
            'judul_buku.required' => 'Judul buku wajib diisi',
            'cover_path.image' => 'Cover harus berupa gambar.',
            'cover_path.mimes' => 'Format cover harus JPG, JPEG, PNG, atau WEBP.',
            'cover_path.max' => 'Ukuran cover terlalu besar. Maksimal 5 MB.',
            'file_path.file' => 'File fiksi tidak valid. Pilih file PDF, DOC, atau DOCX.',
            'file_path.mimes' => 'Format file fiksi harus PDF, DOC, atau DOCX.',
            'file_path.max' => 'Ukuran file fiksi terlalu besar. Maksimal 10 MB.',
        ]);

        if ($request->hasFile('cover_path')) {
            if ($fiksi->cover_path && Storage::disk('public')->exists($fiksi->cover_path)) {
                Storage::disk('public')->delete($fiksi->cover_path);
            }

            $cover = $request->file('cover_path');
            $coverName = time() . '_cover_' . $cover->getClientOriginalName();
            $validated['cover_path'] = $cover->storeAs('fiksi/covers', $coverName, 'public');
        } else {
            $validated['cover_path'] = $fiksi->cover_path;
        }

        if ($request->hasFile('file_path')) {
            if ($fiksi->file_path && Storage::disk('public')->exists($fiksi->file_path)) {
                Storage::disk('public')->delete($fiksi->file_path);
            }

            $file = $request->file('file_path');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $validated['file_path'] = $file->storeAs('fiksi', $fileName, 'public');
        } else {
            $validated['file_path'] = $fiksi->file_path;
        }

        $validated['status_aktif'] = $request->has('status_aktif');

        $fiksi->update($validated);

        if ($request->expectsJson()) {
            return response()->json($fiksi->refresh()->load('pengguna'));
        }

        return redirect()->route('fiksi.index')
            ->with('success', 'Fiksi berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $fiksi = Fiksi::findOrFail($id);

        if ($fiksi->cover_path && Storage::disk('public')->exists($fiksi->cover_path)) {
            Storage::disk('public')->delete($fiksi->cover_path);
        }

        if ($fiksi->file_path && Storage::disk('public')->exists($fiksi->file_path)) {
            Storage::disk('public')->delete($fiksi->file_path);
        }

        $fiksi->delete();

        if (request()->expectsJson()) {
            return response()->noContent();
        }

        return redirect()->route('fiksi.index')
            ->with('success', 'Fiksi berhasil dihapus!');
    }
}
