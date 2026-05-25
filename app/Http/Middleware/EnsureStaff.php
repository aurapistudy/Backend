<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->peran === 'siswa') {
            return redirect()->route('dashboard.siswa');
        }

        if (!in_array($user->peran, ['admin', 'guru'], true)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
