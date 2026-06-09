<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    public function handle(
        Request $request,
        Closure $next
    ): Response {

        $user = Auth::user();

        // USER LOGIN TAPI NONAKTIF
        if (
            $user &&
            $user->status_akun !== 'aktif'
        ) {

            Auth::logout();

            return redirect('/login')
                ->withErrors([

                    'email' =>
                        'Akun Anda dinonaktifkan.'
                ]);
        }

        return $next($request);
    }
}