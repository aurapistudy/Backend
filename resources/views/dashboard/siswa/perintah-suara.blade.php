@extends('dashboard.siswa.layout')

@section('content')
    <h2 class="section-title">Perintah Suara</h2>
    <p class="section-desc">Daftar perintah suara yang bisa kamu gunakan.</p>

    <div class="section-grid" style="margin-top:1rem;">
        <section class="section-card">
            <span class="tag">Navigasi</span>
            <h3 class="section-title">Voice Nav</h3>
            <p class="section-desc">
                Ucapkan:
                <br>• "buka dashboard"
                <br>• "buka mata pelajaran"
                <br>• "buka catatan"
                <br>• "buka riwayat"
                <br>• "buka perintah suara"
                <br>• "buka panduan"
                <br>• "buka rak buku"
            </p>
        </section>

        <section class="section-card">
            <span class="tag">Sesi Baca</span>
            <h3 class="section-title">Kontrol Mata Pelajaran</h3>
            <p class="section-desc">
                Ucapkan:
                <br>• "mulai scroll"
                <br>• "berhenti scroll"
                <br>• "lebih cepat"
                <br>• "lebih pelan"
                <br>• "mulai baca"
                <br>• "stop baca"
            </p>
        </section>

        <section class="section-card">
            <span class="tag">Catatan</span>
            <h3 class="section-title">Mencatat</h3>
            <p class="section-desc">
                Ucapkan:
                <br>• "mulai mencatat"
                <br>• "stop mencatat"
                <br>• "selesai mencatat"
            </p>
        </section>

        <section class="section-card">
            <span class="tag">Kuis</span>
            <h3 class="section-title">Navigasi Soal</h3>
            <p class="section-desc">
                Ucapkan:
                <br>• "selanjutnya"
                <br>• "sebelumnya"
                <br>• "lewati"
                <br>• "nomor 3"
            </p>
        </section>

        <section class="section-card">
            <span class="tag">Kuis</span>
            <h3 class="section-title">Jawaban</h3>
            <p class="section-desc">
                Ucapkan:
                <br>• "jawab A/B/C/D" (pilihan ganda)
                <br>• "jawab ..." (essay)
            </p>
        </section>
    </div>

    <div class="section-card" style="margin-top:1.5rem;">
        <span class="tag">Catatan</span>
        <p class="section-desc">
            Pastikan izin mikrofon aktif. Browser biasanya butuh klik pertama
            sebelum ASR bisa berjalan. Bahasa ASR dapat diatur di menu Pengaturan.
        </p>
    </div>
@endsection
