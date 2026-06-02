<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        $pengguna = Pengguna::where('email', $request->email)->first();

        if (!$pengguna) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi salah.',
            ])->withInput($request->only('email'));
        }

        if (!Hash::check($request->kata_sandi, $pengguna->kata_sandi)) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi salah.',
            ])->withInput($request->only('email'));
        }

        if (!$pengguna->status_aktif) {
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ])->withInput($request->only('email'));
        }

        // Login user
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
     * Send a password reset link to the given email.
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

        if ($pengguna) {
            $reset = $this->createPasswordResetForUser($pengguna);

            try {
                $this->sendPasswordResetEmail($pengguna, $reset['reset_url']);
            } catch (\Throwable $exception) {
                Log::error('password_reset_mail_failed', [
                    'email' => $pengguna->email,
                    'error' => $exception->getMessage(),
                ]);

                return back()
                    ->withInput()
                    ->with('status', 'Tautan reset sudah dibuat. Mail server belum aktif, gunakan tautan fallback di bawah.')
                    ->with('reset_link_fallback', $reset['reset_url']);
            }
        }

        return back()->with('status', 'Jika email terdaftar, tautan reset kata sandi telah dikirim.');
    }

    /**
     * Show the reset password form for a valid token.
     */
    public function showResetPasswordForm(Request $request, string $token)
    {
        $email = (string) $request->query('email', '');

        if (!$this->hasValidResetToken($email, $token)) {
            return redirect()->route('password.request')
                ->withErrors([
                    'email' => 'Tautan reset kata sandi tidak valid atau sudah kedaluwarsa.',
                ]);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    /**
     * Update the user's password using a reset token.
     */
    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'kata_sandi' => 'required|string|min:6|confirmed',
        ], [
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'kata_sandi.required' => 'Kata sandi baru wajib diisi',
            'kata_sandi.min' => 'Kata sandi baru minimal 6 karakter',
            'kata_sandi.confirmed' => 'Konfirmasi kata sandi tidak cocok',
        ]);

        if (!$this->hasValidResetToken($validated['email'], $validated['token'])) {
            throw ValidationException::withMessages([
                'email' => 'Tautan reset kata sandi tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        $pengguna = Pengguna::where('email', $validated['email'])->first();

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
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

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

        $pengguna = Pengguna::where('email', $validated['email'])->first();

        if (!$pengguna || !Hash::check($validated['kata_sandi'], $pengguna->kata_sandi)) {
            return response()->json([
                'message' => 'Email atau kata sandi salah.'
            ], 401);
        }

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
     * Kirim tautan reset kata sandi (API / Flutter).
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

        if ($pengguna) {
            $reset = $this->createPasswordResetForUser($pengguna);

            try {
                $this->sendPasswordResetEmail($pengguna, $reset['reset_url']);
            } catch (\Throwable $exception) {
                Log::error('password_reset_mail_failed', [
                    'email' => $pengguna->email,
                    'error' => $exception->getMessage(),
                ]);

                $response = [
                    'message' => 'Gagal mengirim email reset kata sandi. Periksa konfigurasi mail server atau coba lagi nanti.',
                ];

                if (config('app.debug')) {
                    $response['debug'] = [
                        'reset_url' => $reset['reset_url'],
                        'token' => $reset['plain_token'],
                        'email' => $pengguna->email,
                    ];
                }

                return response()->json($response, 503);
            }
        }

        return response()->json([
            'message' => 'Jika email terdaftar, tautan reset kata sandi telah dikirim.',
        ]);
    }

    /**
     * Atur ulang kata sandi dengan token dari email (API / Flutter).
     */
    public function apiResetPassword(Request $request): JsonResponse
    {
        $payload = [
            'token' => $request->input('token'),
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

        $validated = validator($payload, [
            'token' => 'required|string',
            'email' => 'required|email',
            'kata_sandi' => 'required|string|min:6',
            'kata_sandi_konfirmasi' => 'required|string|same:kata_sandi',
        ], [
            'token.required' => 'Token reset wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'kata_sandi.required' => 'Kata sandi baru wajib diisi',
            'kata_sandi.min' => 'Kata sandi baru minimal 6 karakter',
            'kata_sandi_konfirmasi.required' => 'Konfirmasi kata sandi wajib diisi',
            'kata_sandi_konfirmasi.same' => 'Konfirmasi kata sandi tidak cocok',
        ])->validate();

        if (!$this->hasValidResetToken($validated['email'], $validated['token'])) {
            throw ValidationException::withMessages([
                'token' => 'Tautan reset kata sandi tidak valid atau sudah kedaluwarsa.',
            ]);
        }

        $pengguna = Pengguna::where('email', $validated['email'])->first();

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
        DB::table('password_reset_tokens')->where('email', $validated['email'])->delete();

        return response()->json([
            'message' => 'Kata sandi berhasil diubah. Silakan login dengan kata sandi baru.',
        ]);
    }

    /**
     * @return array{plain_token: string, reset_url: string}
     */
    private function createPasswordResetForUser(Pengguna $pengguna): array
    {
        $plainToken = Str::random(64);

        DB::table('password_reset_tokens')->where('email', $pengguna->email)->delete();
        DB::table('password_reset_tokens')->insert([
            'email' => $pengguna->email,
            'token' => Hash::make($plainToken),
            'created_at' => now(),
        ]);

        return [
            'plain_token' => $plainToken,
            'reset_url' => $this->buildPasswordResetUrl($plainToken, $pengguna->email),
        ];
    }

    private function buildPasswordResetUrl(string $plainToken, string $email): string
    {
        $template = config('app.mobile_password_reset_url');

        if (is_string($template) && $template !== '') {
            return str_replace(
                ['{token}', '{email}'],
                [rawurlencode($plainToken), rawurlencode($email)],
                $template
            );
        }

        return route('password.reset', [
            'token' => $plainToken,
            'email' => $email,
        ]);
    }

    private function sendPasswordResetEmail(Pengguna $pengguna, string $resetUrl): void
    {
        Mail::raw(
            "Halo {$pengguna->nama},\n\nKlik tautan berikut untuk mengatur ulang kata sandi akun Anda:\n{$resetUrl}\n\nTautan ini berlaku selama 60 menit.\nJika Anda tidak meminta reset kata sandi, abaikan email ini.",
            function ($message) use ($pengguna) {
                $message->to($pengguna->email, $pengguna->nama)
                    ->subject('Reset Kata Sandi');
            }
        );
    }

    private function hasValidResetToken(string $email, string $token): bool
    {
        if ($email === '' || $token === '') {
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

        return Hash::check($token, $record->token);
    }
}
