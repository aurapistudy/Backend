<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Models\Pengguna;
use App\Models\Siswa;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Show the register form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Show the forgot password form.
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'kata_sandi' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'kata_sandi.required' => 'Kata sandi wajib diisi',
        ]);

        // Server-side throttle key per IP+email to mitigate brute-force
        $throttleKey = 'login|' . $request->ip() . '|' . strtolower($request->input('email', ''));
        $maxAttempts = 10;
        $decaySeconds = 60; // lock window

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $available = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$available} detik.",
            ])->withInput($request->only('email'));
        }

        $pengguna = Pengguna::where('email', $request->email)->first();

        if (!$pengguna || !Hash::check($request->kata_sandi, $pengguna->kata_sandi)) {
            // record failed attempt
            RateLimiter::hit($throttleKey, $decaySeconds);
            return back()->withErrors([
                'email' => 'Email atau kata sandi salah.',
            ])->withInput($request->only('email'));
        }

        if (!$pengguna->status_aktif) {
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ])->withInput($request->only('email'));
        }

        // Clear throttle on successful login and login user
        RateLimiter::clear($throttleKey);
        Auth::login($pengguna, $request->boolean('ingat_sandi'));

        $request->session()->regenerate();

        if ($pengguna->peran === 'siswa') {
            return redirect()->intended('/dashboard-siswa');
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Send a verification code to the given email.
     */
    public function sendResetLink(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
        ]);
        $pengguna = Pengguna::where('email', $validated['email'])->first();

        $throttleKey = 'pwdreset|' . $request->ip() . '|' . strtolower($validated['email']);
        $maxAttempts = 5;
        $decaySeconds = 60 * 60;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $available = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Terlalu banyak permintaan reset. Coba lagi dalam {$available} detik.",
            ])->withInput();
        }

        if ($pengguna) {
            $reset = $this->createPasswordResetForUser($pengguna);

            try {
                $this->sendPasswordResetEmail($pengguna, $reset['plain_code']);
            } catch (\Throwable $exception) {
                Log::error('password_reset_mail_failed', [
                    'email' => $pengguna->email,
                    'error' => $exception->getMessage(),
                ]);

                RateLimiter::hit($throttleKey, $decaySeconds);

                return back()
                    ->withInput()
                    ->with('status', 'Kode verifikasi sudah dibuat. Mail server belum aktif, gunakan kode yang ada di log server jika diperlukan.');
            }

            RateLimiter::hit($throttleKey, $decaySeconds);
        }

        return redirect()->route('password.verify')
            ->with('status', 'Jika email terdaftar, kode verifikasi 6 digit telah dikirim.')
            ->with('reset_email', $validated['email']);
    }

    /**
     * Show the verification-code form.
     */
    public function showVerifyResetCodeForm(Request $request)
    {
        return view('auth.verify-reset-code', [
            'email' => (string) $request->query('email', session('reset_email', '')),
        ]);
    }

    /**
     * Verify the reset code sent by email.
     */
    public function verifyResetCode(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'code.required' => 'Kode verifikasi wajib diisi',
            'code.digits' => 'Kode verifikasi harus 6 digit angka',
        ]);

        if (!$this->hasValidResetCode($validated['email'], $validated['code'])) {
            throw ValidationException::withMessages([
                'code' => 'Kode verifikasi tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        return redirect()->route('password.reset', [
            'email' => $validated['email'],
            'code' => $validated['code'],
        ])->with('status', 'Kode verifikasi benar. Silakan buat kata sandi baru.');
    }

    /**
     * Show the reset password form after the code has been verified.
     */
    public function showResetPasswordForm(Request $request)
    {
        $email = (string) $request->query('email', '');
        $code = (string) $request->query('code', '');

        if ($email === '' || $code === '' || !$this->hasValidResetCode($email, $code)) {
            return redirect()->route('password.verify')
                ->withErrors([
                    'email' => 'Silakan verifikasi kode terlebih dahulu.',
                ]);
        }

        return view('auth.reset-password', [
            'email' => $email,
            'code' => $code,
        ]);
    }

    /**
     * Update the user's password using the verified reset code.
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'kata_sandi' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'code.required' => 'Kode verifikasi wajib diisi',
            'code.digits' => 'Kode verifikasi harus 6 digit angka',
            'kata_sandi.required' => 'Kata sandi baru wajib diisi',
            'kata_sandi.min' => 'Kata sandi baru minimal 6 karakter',
            'kata_sandi.confirmed' => 'Konfirmasi kata sandi tidak cocok',
        ]);

        $email = $validated['email'];
        $code = (string) $validated['code'];

        if (!$this->hasValidResetCode($email, $code)) {
            throw ValidationException::withMessages([
                'code' => 'Kode verifikasi tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        $pengguna = Pengguna::where('email', $email)->first();

        if (!$pengguna) {
            throw ValidationException::withMessages([
                'email' => 'Akun tidak ditemukan.',
            ]);
        }

        $pengguna->forceFill([
            'kata_sandi' => Hash::make($validated['kata_sandi']),
            'remember_token' => Str::random(60),
        ])->save();

        $pengguna->tokens()->delete();
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        return redirect()->route('login')
            ->with('success', 'Kata sandi berhasil diubah. Silakan login dengan kata sandi baru.');
    }

    /**
     * Handle register request (web).
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:pengguna,email',
            'kata_sandi' => 'required|string|min:6|confirmed',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'kata_sandi.required' => 'Kata sandi wajib diisi',
            'kata_sandi.min' => 'Kata sandi minimal 6 karakter',
            'kata_sandi.confirmed' => 'Konfirmasi kata sandi tidak cocok',
        ]);

        $pengguna = DB::transaction(function () use ($validated) {
            $user = Pengguna::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'kata_sandi' => Hash::make($validated['kata_sandi']),
                'peran' => 'siswa',
                'status_aktif' => true,
            ]);

            Siswa::create([
                'pengguna_id' => $user->id,
                'nama_sekolah' => null,
                'jenjang' => null,
                'level_id' => null,
                'catatan' => null,
            ]);

            return $user;
        });

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->intended('/dashboard-siswa');
    }

    /**
     * Handle API login request (token-based).
     */
    public function apiLogin(Request $request): JsonResponse
    {
        $payload = [
            'email' => $request->input('email'),
            'kata_sandi' => $request->input('kata_sandi', $request->input('password')),
        ];

        $validated = validator($payload, [
            'email' => 'required|email',
            'kata_sandi' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'kata_sandi.required' => 'Kata sandi wajib diisi',
        ])->validate();

        // Server-side throttle per IP+email for API login
        $throttleKey = 'api_login|' . $request->ip() . '|' . strtolower($validated['email']);
        $maxAttempts = 10;
        $decaySeconds = 60;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $available = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => "Terlalu banyak percobaan login. Coba lagi dalam {$available} detik."
            ], 429);
        }

        $pengguna = Pengguna::where('email', $validated['email'])->first();

        if (!$pengguna || !Hash::check($validated['kata_sandi'], $pengguna->kata_sandi)) {
            RateLimiter::hit($throttleKey, $decaySeconds);
            return response()->json([
                'message' => 'Email atau kata sandi salah.'
            ], 401);
        }

        RateLimiter::clear($throttleKey);

        if (!$pengguna->status_aktif) {
            return response()->json([
                'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
            ], 403);
        }

        $token = $pengguna->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => [
                'id' => $pengguna->id,
                'nama' => $pengguna->nama,
                'email' => $pengguna->email,
                'peran' => $pengguna->peran,
            ]
        ]);
    }

    /**
     * Handle API logout request (token-based).
     */
    public function apiLogout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Return authenticated API user.
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user) {
            $user->load(['siswa', 'guru']);
        }

        return response()->json([
            'user' => $user
        ]);
    }

    /**
     * Handle API register request (default role: siswa).
     */
    public function apiRegister(Request $request): JsonResponse
    {
        $payload = [
            'nama' => $request->input('nama', $request->input('name', $request->input('full_name'))),
            'email' => $request->input('email'),
            'kata_sandi' => $request->input('kata_sandi', $request->input('password')),
            'kata_sandi_konfirmasi' => $request->input(
                'kata_sandi_konfirmasi',
                $request->input(
                    'password_confirmation',
                    $request->input('confirm_password', $request->input('confirmPassword'))
                )
            ),
        ];

        if ($request->boolean('debug')) {
            Log::info('apiRegister payload fields', [
                'nama' => $payload['nama'] !== null && $payload['nama'] !== '',
                'email' => $payload['email'] !== null && $payload['email'] !== '',
                'kata_sandi' => $payload['kata_sandi'] !== null && $payload['kata_sandi'] !== '',
                'kata_sandi_konfirmasi' => $payload['kata_sandi_konfirmasi'] !== null && $payload['kata_sandi_konfirmasi'] !== '',
            ]);
        }

        $validated = validator($payload, [
            'nama' => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:pengguna,email',
            'kata_sandi' => 'required|string|min:6',
            'kata_sandi_konfirmasi' => 'required|string|same:kata_sandi',
        ], [
            'nama.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'kata_sandi.required' => 'Kata sandi wajib diisi',
            'kata_sandi.min' => 'Kata sandi minimal 6 karakter',
            'kata_sandi_konfirmasi.required' => 'Konfirmasi kata sandi wajib diisi',
            'kata_sandi_konfirmasi.same' => 'Konfirmasi kata sandi tidak cocok',
        ])->validate();

        $user = DB::transaction(function () use ($validated) {
            $pengguna = Pengguna::create([
                'nama' => $validated['nama'],
                'email' => $validated['email'],
                'kata_sandi' => Hash::make($validated['kata_sandi']),
                'peran' => 'siswa',
                'status_aktif' => true,
            ]);

            Siswa::create([
                'pengguna_id' => $pengguna->id,
                'nama_sekolah' => null,
                'jenjang' => null,
                'level_id' => null,
                'catatan' => null,
            ]);

            return $pengguna;
        });

        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => [
                'id' => $user->id,
                'nama' => $user->nama,
                'email' => $user->email,
                'peran' => $user->peran,
            ],
        ], 201);
    }

    /**
     * Kirim kode reset kata sandi (API / Flutter).
     */
    public function apiForgotPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
        ]);

        $pengguna = Pengguna::where('email', $validated['email'])->first();

        $throttleKey = 'api_pwdreset|' . $request->ip() . '|' . strtolower($validated['email']);
        $maxAttempts = 5;
        $decaySeconds = 60 * 60;

        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $available = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'message' => "Terlalu banyak permintaan reset. Coba lagi dalam {$available} detik."
            ], 429);
        }

        if ($pengguna) {
            $reset = $this->createPasswordResetForUser($pengguna);

            try {
                $this->sendPasswordResetEmail($pengguna, $reset['plain_code']);
            } catch (\Throwable $exception) {
                Log::error('password_reset_mail_failed', [
                    'email' => $pengguna->email,
                    'error' => $exception->getMessage(),
                ]);

                RateLimiter::hit($throttleKey, $decaySeconds);

                $response = [
                    'message' => 'Gagal mengirim email reset kata sandi. Periksa konfigurasi mail server atau coba lagi nanti.',
                ];

                if (config('app.debug')) {
                    $response['debug'] = [
                        'code' => $reset['plain_code'],
                        'email' => $pengguna->email,
                    ];
                }

                return response()->json($response, 503);
            }

            RateLimiter::hit($throttleKey, $decaySeconds);
        }

        return response()->json([
            'message' => 'Jika email terdaftar, kode verifikasi 6 digit telah dikirim.',
        ]);
    }

    /**
     * Verify the reset code sent to the user (API / Flutter).
     */
    public function apiVerifyResetCode(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'code.required' => 'Kode verifikasi wajib diisi',
            'code.digits' => 'Kode verifikasi harus 6 digit angka',
        ]);

        if (!$this->hasValidResetCode($validated['email'], $validated['code'])) {
            return response()->json([
                'message' => 'Kode verifikasi tidak valid atau sudah kedaluwarsa.',
            ], 422);
        }

        return response()->json([
            'message' => 'Kode verifikasi benar. Anda dapat mengatur kata sandi baru.',
        ]);
    }

    /**
     * Atur ulang kata sandi dengan kode dari email (API / Flutter).
     */
    public function apiResetPassword(Request $request): JsonResponse
    {
        $payload = [
            'email' => $request->input('email'),
            'code' => $request->input('code'),
            'kata_sandi' => $request->input('kata_sandi', $request->input('password')),
            'kata_sandi_konfirmasi' => $request->input(
                'kata_sandi_konfirmasi',
                $request->input(
                    'password_confirmation',
                    $request->input('confirm_password', $request->input('confirmPassword'))
                )
            ),
        ];

        $validated = validator($payload, [
            'email' => 'required|email',
            'code' => 'required|digits:6',
            'kata_sandi' => 'required|string|min:6',
            'kata_sandi_konfirmasi' => 'required|string|same:kata_sandi',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'code.required' => 'Kode verifikasi wajib diisi',
            'code.digits' => 'Kode verifikasi harus 6 digit angka',
            'kata_sandi.required' => 'Kata sandi baru wajib diisi',
            'kata_sandi.min' => 'Kata sandi baru minimal 6 karakter',
            'kata_sandi_konfirmasi.required' => 'Konfirmasi kata sandi wajib diisi',
            'kata_sandi_konfirmasi.same' => 'Konfirmasi kata sandi tidak cocok',
        ])->validate();

        if (!$this->hasValidResetCode($validated['email'], $validated['code'])) {
            return response()->json([
                'message' => 'Kode verifikasi tidak valid atau sudah kedaluwarsa.',
            ], 422);
        }

        $pengguna = Pengguna::where('email', $validated['email'])->first();

        if (!$pengguna) {
            return response()->json([
                'message' => 'Akun tidak ditemukan.',
            ], 404);
        }

        $pengguna->forceFill([
            'kata_sandi' => Hash::make($validated['kata_sandi']),
            'remember_token' => Str::random(60),
        ])->save();

        $pengguna->tokens()->delete();
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return response()->json([
            'message' => 'Kata sandi berhasil diubah. Silakan login dengan kata sandi baru.',
        ]);
    }

    /**
     * @return array{plain_code: string}
     */
    private function createPasswordResetForUser(Pengguna $pengguna): array
    {
        $plainCode = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->where('email', $pengguna->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $pengguna->email,
            'token' => Hash::make($plainCode),
            'created_at' => now(),
        ]);

        return [
            'plain_code' => $plainCode,
        ];
    }

    private function sendPasswordResetEmail(Pengguna $pengguna, string $code): void
    {
        Mail::to($pengguna->email, $pengguna->nama)
            ->send(new PasswordResetMail($pengguna, $code));
    }

    private function hasValidResetCode(string $email, string $code): bool
    {
        if ($email === '' || $code === '') {
            return false;
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();
        if (!$record) {
            return false;
        }

        $expiresAt = Carbon::parse($record->created_at)
            ->addMinutes((int) config('auth.passwords.users.expire', 60));

        if ($expiresAt->isPast()) {
            DB::table('password_reset_tokens')->where('email', $email)->delete();
            return false;
        }

        return Hash::check($code, $record->token);
    }
}
