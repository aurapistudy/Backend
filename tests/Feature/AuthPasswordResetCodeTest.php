<?php

namespace Tests\Feature;

use App\Mail\PasswordResetMail;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthPasswordResetCodeTest extends TestCase
{
    use WithoutMiddleware;

    public function test_user_can_request_reset_code_and_change_password(): void
    {
        Mail::fake();

        $pengguna = Pengguna::create([
            'nama' => 'Siswa Test',
            'email' => 'siswa-' . Str::random(6) . '@example.com',
            'kata_sandi' => Hash::make('old-password'),
            'peran' => 'siswa',
            'status_aktif' => true,
        ]);

        $sentCode = null;

        $response = $this->post('/forgot-password', [
            'email' => $pengguna->email,
        ]);

        $response->assertRedirect();

        Mail::assertSent(PasswordResetMail::class, function (PasswordResetMail $mail) use ($pengguna, &$sentCode): bool {
            $this->assertSame($pengguna->email, $mail->pengguna->email);
            $this->assertSame(6, strlen($mail->code));
            $this->assertMatchesRegularExpression('/^\d{6}$/', $mail->code);
            $sentCode = $mail->code;

            return true;
        });

        $record = DB::table('password_reset_tokens')->where('email', $pengguna->email)->first();
        $this->assertNotNull($record);
        $this->assertTrue(Hash::check($sentCode, $record->token));

        $this->post('/verify-reset-code', [
            'email' => $pengguna->email,
            'code' => $sentCode,
        ])->assertRedirect();

        $this->post('/reset-password', [
            'email' => $pengguna->email,
            'code' => $sentCode,
            'kata_sandi' => 'new-password-123',
            'kata_sandi_confirmation' => 'new-password-123',
        ])->assertRedirect();

        $pengguna->refresh();
        $this->assertTrue(Hash::check('new-password-123', $pengguna->kata_sandi));
    }
}
