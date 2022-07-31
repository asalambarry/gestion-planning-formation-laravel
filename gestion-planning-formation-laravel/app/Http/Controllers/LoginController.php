<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Handle an authentication attempt.
     *
     * @param  Request  $request
     * @return  RedirectResponse
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('login', 'mdp');

        $user = User::where('login', $credentials['login'])->first();

        if ($user) {
            if (Hash::check($credentials['mdp'], $user->mdp)) {

                if ($user->type == null) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'login' => 'Compte non activé !!.',
                        ]);
                }

                Auth::login($user);

                $request->session()->regenerate();
                return redirect()->intended('home');
            }
        }

        return back()
            ->withInput()
            ->withErrors([
            'login' => 'Les données fournies sont incorrectes.',
        ]);
    }

    /**
     * Log the user out of the application.
     *
     * @param  Request  $request
     * @return  RedirectResponse|Redirector
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
