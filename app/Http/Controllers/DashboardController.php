<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengguna;
use App\Models\Materi;
use App\Models\SesiBaca;
use App\Models\LogAksesMateri;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user && $user->peran === 'siswa') {
            return redirect('/dashboard-siswa');
        }

        if ($user && !$user->isStaff()) {
            abort(403);
        }

        $mapelScope = null;
        if ($user && $user->isGuruMapel()) {
            $mapelScope = $user->assignedMataPelajaranIds();
        }

        // Hitung statistik
        $materiQuery = Materi::where('status_aktif', true);
        if ($mapelScope !== null) {
            $materiQuery->whereIn('mata_pelajaran_id', $mapelScope ?: [-1]);
        }
        $totalMateri = $materiQuery->count();
        $totalPenggunaAktif = Pengguna::where('status_aktif', true)->count();
        $totalSesiBaca = SesiBaca::count();
        
        // Total waktu baca (dalam menit)
        $totalWaktuBaca = SesiBaca::sum('durasi_detik') / 60; // Convert to minutes
        
        // Data untuk greeting berdasarkan waktu
        $jam = (int) date('H');
        if ($jam >= 5 && $jam < 12) {
            $greeting = 'Selamat Pagi';
        } elseif ($jam >= 12 && $jam < 15) {
            $greeting = 'Selamat Siang';
        } elseif ($jam >= 15 && $jam < 19) {
            $greeting = 'Selamat Sore';
        } else {
            $greeting = 'Selamat Malam';
        }
        
        // Data untuk grafik waktu baca per hari (7 hari terakhir)
        $waktuBacaPerHari = [];
        $labelsHari = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->format('Y-m-d');
            $labelsHari[] = now()->subDays($i)->format('d M');
            
            $totalDetik = SesiBaca::whereDate('mulai', $tanggal)
                ->sum('durasi_detik');
            $waktuBacaPerHari[] = round($totalDetik / 60, 1); // Convert to minutes
        }
        
        // Data untuk grafik aktivitas membaca per hari (7 hari terakhir)
        $aktivitasPerHari = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->format('Y-m-d');
            $aktivitasPerHari[] = SesiBaca::whereDate('mulai', $tanggal)->count();
        }
        
        // Data untuk grafik materi paling banyak dibaca (top 5)
        $materiPopuler = SesiBaca::selectRaw('materi_id, COUNT(*) as jumlah_baca')
            ->groupBy('materi_id')
            ->orderBy('jumlah_baca', 'desc')
            ->limit(5)
            ->with('materi')
            ->get();
        
        $materiLabels = [];
        $materiData = [];
        foreach ($materiPopuler as $item) {
            if ($item->materi) {
                $materiLabels[] = $item->materi->judul;
                $materiData[] = $item->jumlah_baca;
            }
        }
        
        // Data untuk grafik distribusi waktu baca per kategori (siswa vs guru)
        $waktuBacaSiswa = SesiBaca::whereHas('pengguna', function($query) {
            $query->where('peran', 'siswa');
        })->sum('durasi_detik') / 60;
        
        $waktuBacaGuru = SesiBaca::whereHas('pengguna', function($query) {
            $query->where('peran', 'guru');
        })->sum('durasi_detik') / 60;
        
        return view('dashboard.index', [
            'user' => $user,
            'totalMateri' => $totalMateri,
            'totalPenggunaAktif' => $totalPenggunaAktif,
            'totalSesiBaca' => $totalSesiBaca,
            'totalWaktuBaca' => round($totalWaktuBaca, 1),
            'greeting' => $greeting,
            'waktuBacaPerHari' => $waktuBacaPerHari,
            'labelsHari' => $labelsHari,
            'aktivitasPerHari' => $aktivitasPerHari,
            'materiLabels' => $materiLabels,
            'materiData' => $materiData,
            'waktuBacaSiswa' => round($waktuBacaSiswa, 1),
            'waktuBacaGuru' => round($waktuBacaGuru, 1),
        ]);
    }
}
