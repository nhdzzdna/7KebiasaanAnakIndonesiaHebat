<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */

    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // CEK EMAIL TERDAFTAR
        $user = \App\Models\User::where(
            'email',
            $this->email
        )->first();

        // VR-01-1: EMAIL TIDAK TERDAFTAR
        if (! $user) {

            RateLimiter::hit(
                $this->throttleKey()
            );

            throw ValidationException::withMessages([

                'email' =>
                    'Email tidak ditemukan'
            ]);
        }

        // VR-01-4: AKUN NONAKTIF
        if ($user->status_akun !== 'aktif') {

            throw ValidationException::withMessages([

                'email' =>
                    'Akun Anda telah dinonaktifkan'
            ]);
        }

        // VR-01-2: PASSWORD SALAH
        if (! Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ], $this->boolean('remember'))) {

            RateLimiter::hit(
                $this->throttleKey()
            );

            throw ValidationException::withMessages([

                'password' =>
                    'Password salah'
            ]);
        }

        RateLimiter::clear(
            $this->throttleKey()
        );
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}