<?php

namespace App\Http\Controllers;

use App\Models\TahunAkademik;
use Illuminate\Support\Facades\Auth;

class GuruPenugasanController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user || !$user->isGuruMapel()) {
            abort(403, 'Halaman ini hanya untuk guru mata pelajaran.');
        }

        $tahunAkademikAktif = TahunAkademik::active();
        $materiTahunAktif = $user->materiAsGuruAktif();
        $riwayatPenugasan = $user->penugasanRiwayatGrouped();

        return view('dashboard.guru.riwayat-tahun-akademik', compact(
            'user',
            'tahunAkademikAktif',
            'materiTahunAktif',
            'riwayatPenugasan'
        ));
    }
}
