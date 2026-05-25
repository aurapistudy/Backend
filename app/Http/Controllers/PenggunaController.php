<?php

namespace App\Http\Controllers;

use App\Models\MataPelajaran;
use App\Models\Pengguna;
use App\Models\Siswa;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->get('search', ''));

        $pengguna = Pengguna::query()
            ->with(['siswa', 'guru', 'mataPelajaranAsGuru'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('id', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('peran', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard.pengguna.pengguna', compact('pengguna', 'search'));
    }

    public function create()
    {
        $mataPelajarans = MataPelajaran::where('status_aktif', true)->orderBy('nama')->get();

        return view('dashboard.pengguna.create', compact('mataPelajarans'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePenggunaPayload($request);

        $pengguna = Pengguna::create([
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'kata_sandi' => Hash::make($validated['kata_sandi']),
            'peran' => $validated['peran'],
            'status_aktif' => $request->has('status_aktif') ? true : false,
        ]);

        $this->syncPeranRecords($pengguna, $validated, $request);

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil ditambahkan!');
    }

    public function show(string $id)
    {
        $pengguna = Pengguna::with(['siswa', 'guru', 'mataPelajaranAsGuru'])->findOrFail($id);

        return view('dashboard.pengguna.show', compact('pengguna'));
    }

    public function edit(string $id)
    {
        $pengguna = Pengguna::with(['siswa', 'guru', 'mataPelajaranAsGuru'])->findOrFail($id);
        $mataPelajarans = MataPelajaran::where('status_aktif', true)->orderBy('nama')->get();
        $assignedMapelIds = $pengguna->mataPelajaranAsGuru->pluck('id')->all();

        return view('dashboard.pengguna.edit', compact('pengguna', 'mataPelajarans', 'assignedMapelIds'));
    }

    public function update(Request $request, string $id)
    {
        $pengguna = Pengguna::with(['siswa', 'guru', 'mataPelajaranAsGuru'])->findOrFail($id);

        $validated = $this->validatePenggunaPayload($request, $pengguna->id, false);

        $updateData = [
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'peran' => $validated['peran'],
            'status_aktif' => $request->has('status_aktif') ? true : false,
        ];

        if (!empty($validated['kata_sandi'])) {
            $updateData['kata_sandi'] = Hash::make($validated['kata_sandi']);
        }

        $pengguna->update($updateData);
        $this->syncPeranRecords($pengguna, $validated, $request);

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $pengguna = Pengguna::findOrFail($id);

        if ($pengguna->siswa) {
            $pengguna->siswa->delete();
        }
        if ($pengguna->guru) {
            $pengguna->guru->delete();
        }
        $pengguna->mataPelajaranAsGuru()->detach();
        $pengguna->delete();

        return redirect()->route('pengguna.index')
            ->with('success', 'Pengguna berhasil dihapus!');
    }

    private function validatePenggunaPayload(Request $request, ?int $penggunaId = null, bool $requirePassword = true): array
    {
        $passwordRule = $requirePassword ? 'required|string|min:6' : 'nullable|string|min:6';

        return $request->validate([
            'nama' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('pengguna', 'email')->ignore($penggunaId),
            ],
            'kata_sandi' => $passwordRule,
            'peran' => 'required|in:siswa,guru,admin',
            'status_aktif' => 'boolean',
            'nama_sekolah' => 'nullable|string|max:150',
            'jenjang' => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
            'mata_pelajaran_ids' => 'required_if:peran,guru|array|min:1',
            'mata_pelajaran_ids.*' => 'integer|exists:mata_pelajaran,id',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'kata_sandi.required' => 'Kata sandi wajib diisi',
            'kata_sandi.min' => 'Kata sandi minimal 6 karakter',
            'peran.required' => 'Peran wajib dipilih',
            'peran.in' => 'Peran harus siswa, guru mapel, atau administrator',
            'mata_pelajaran_ids.required_if' => 'Pilih minimal satu mata pelajaran untuk guru mapel.',
            'mata_pelajaran_ids.min' => 'Pilih minimal satu mata pelajaran untuk guru mapel.',
        ]);
    }

    private function syncPeranRecords(Pengguna $pengguna, array $validated, Request $request): void
    {
        if ($validated['peran'] === 'siswa') {
            if ($pengguna->guru) {
                $pengguna->guru->delete();
            }
            $pengguna->mataPelajaranAsGuru()->detach();

            if ($pengguna->siswa) {
                $pengguna->siswa->update([
                    'nama_sekolah' => $validated['nama_sekolah'] ?? null,
                    'jenjang' => $validated['jenjang'] ?? null,
                    'catatan' => $validated['catatan'] ?? null,
                ]);
            } else {
                Siswa::create([
                    'pengguna_id' => $pengguna->id,
                    'nama_sekolah' => $validated['nama_sekolah'] ?? null,
                    'jenjang' => $validated['jenjang'] ?? null,
                    'catatan' => $validated['catatan'] ?? null,
                ]);
            }

            return;
        }

        if ($pengguna->siswa) {
            $pengguna->siswa->delete();
        }

        if ($validated['peran'] === 'guru') {
            if ($pengguna->guru) {
                $pengguna->guru->update([
                    'nama_sekolah' => $validated['nama_sekolah'] ?? null,
                ]);
            } else {
                Guru::create([
                    'pengguna_id' => $pengguna->id,
                    'nama_sekolah' => $validated['nama_sekolah'] ?? null,
                ]);
            }

            $mapelIds = array_map('intval', $request->input('mata_pelajaran_ids', []));
            $pengguna->syncMataPelajaranAsGuru($mapelIds);

            return;
        }

        if ($pengguna->guru) {
            $pengguna->guru->delete();
        }
        $pengguna->mataPelajaranAsGuru()->detach();
    }
}
