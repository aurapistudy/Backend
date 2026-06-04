# Deploy Laravel ke Railway

## 1. Hubungkan repo

- Buat project baru di Railway.
- Pilih `Deploy from GitHub Repo`.
- Pilih repo backend ini.

## 2. Tambahkan database

- Di project Railway, tambahkan service MySQL atau PostgreSQL.
- Setelah database aktif, buka service backend lalu pastikan backend mendapat environment variables database dari Railway.

## 3. Set environment variables backend

Minimal isi:

- `APP_NAME=Ruma`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://domain-backend-kamu.up.railway.app` (wajib pakai **https://**, jangan `http://`)
- `SESSION_SECURE_COOKIE=true` (disarankan di production)
- `APP_KEY=base64:...`
- `LOG_CHANNEL=stack`
- `LOG_LEVEL=error`

Yang disarankan untuk awal deploy:

- `SESSION_DRIVER=file`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=sync`
- `FILESYSTEM_DISK=public`

Jika pakai database Railway:

- `DB_CONNECTION=mysql` atau `pgsql`
- `DB_HOST=...`
- `DB_PORT=...`
- `DB_DATABASE=...`
- `DB_USERNAME=...`
- `DB_PASSWORD=...`

## 4. Build dan start command

Project ini **wajib** dibangun via `Dockerfile` di repo (bukan Nixpacks)
karena fitur poster rangkuman butuh ekstensi PHP `imagick` + `librsvg`.

File `railway.json` di root sudah memaksa builder `DOCKERFILE`:

```json
{
  "build": { "builder": "DOCKERFILE", "dockerfilePath": "Dockerfile" },
  "deploy": { "startCommand": "railway-start" }
}
```

Kalau Railway terlanjur sempat pakai Nixpacks, di service Railway:
buka `Settings` → `Build` → pastikan `Builder = Dockerfile`, lalu Redeploy.

Start command sudah ditangani oleh `railway-start.sh`:

- `php artisan storage:link`
- `php artisan migrate --force`
- `php artisan serve --host=0.0.0.0 --port=$PORT`

## 5. Kalau deploy sukses tapi HTTP 500

Cek urutan ini:

1. `APP_KEY` ada.
2. `APP_URL` sesuai domain Railway.
3. Database variable sudah masuk semua.
4. `php artisan migrate --force` sudah dijalankan.
5. Untuk troubleshooting awal, pakai:
   - `SESSION_DRIVER=file`
   - `CACHE_STORE=file`
   - `QUEUE_CONNECTION=sync`
6. Lihat log runtime Railway, bukan cuma build log.

## 6. Gejala umum

- `No application encryption key has been specified.`  
  `APP_KEY` belum ada.

- `SQLSTATE...`  
  Database belum terhubung atau migration belum dijalankan.

- `table sessions/cache/jobs doesn't exist`  
  Migration belum jalan, atau driver masih `database`.

- `route:cache` gagal  
  Ada bentrok nama route.

- `Poster rangkuman belum bisa dikonversi ke PNG karena ekstensi Imagick belum terpasang di server.`  
  Railway sedang pakai Nixpacks (bukan Dockerfile). Pastikan `railway.json`
  ikut ter-commit, lalu di Railway → Service → `Settings` → `Build` → set
  `Builder = Dockerfile`, kemudian Redeploy.

- `attempt to perform an operation not allowed by the security policy 'SVG'`  
  Imagick sudah aktif tapi ImageMagick mem-blokir SVG. Pastikan Dockerfile
  terbaru sudah di-deploy (sudah mengubah `/etc/ImageMagick-6/policy.xml`).

- Browser: **"The information you're about to submit is not secure"** / tab **Form is not secure**  
  Halaman dibuka lewat HTTPS (Railway), tapi `action` form masih `http://` karena Laravel
  belum mengenali proxy SSL. Pastikan `APP_ENV=production`, `APP_URL` pakai `https://`,
  deploy ulang setelah `trustProxies` di `bootstrap/app.php` aktif. Cek di DevTools →
  Elements → atribut `action` pada `<form>` harus `https://...`, bukan `http://...`.
