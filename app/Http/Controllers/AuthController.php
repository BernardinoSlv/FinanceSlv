<?php

namespace App\Http\Controllers;

use App\Support\Message;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index()
    {
        if (auth()->check())
            return redirect(route("dashboard.index"));
        return view('auth.index');
    }

    public function attempt(Request $request)
    {
        if (auth::check())
            abort(403);

        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'max:256'],
        ]);

        if (! Auth::attempt(
            $data,
            $request->remember ? true : false
        )) {
            return redirect()->route('auth.index')->with(
                Message::danger('E-mail e/ou senha incorretos.')
            )->withInput();
        }

        return redirect()->route('dashboard.index');
    }

    public function create()
    {
        return view('auth.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'min:3', 'max:256', "regex:/\w+ \w+/i"],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'max:256'],
            'confirm_password' => ['required', 'same:password'],
            'terms' => ['required'],
        ]);

        User::query()->create($data);

        return redirect()->route('auth.index')->with(
            Message::success('Cadastro realizado com sucesso.')
        );
    }

    public function logout()
    {
        if (Auth::check()) {
            Auth::logout();
        }

        return redirect()->route('auth.index')->with(
            Message::success('Volte sempre.')
        );
    }
}
