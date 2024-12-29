<?php

namespace App\Http\Controllers;

use App\Enums\LogTypeEnum;
use App\Support\Message;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function index(): RedirectResponse|View
    {
        if (auth()->check())
            return redirect(route("dashboard.index"));
        return view('auth.index');
    }

    public function attempt(Request $request): RedirectResponse
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

        /** @var User */
        $user = auth()->user();
        $user->logs()->create([
            "type" => LogTypeEnum::INFO->value,
            "description" => "Login realizado.",
        ]);

        return redirect()->route('dashboard.index');
    }

    public function create(): RedirectResponse|View
    {
        if (auth()->check())
            return redirect(route("dashboard.index"));
        return view('auth.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (auth()->check())
            abort(403);

        $data = $request->validate([
            'name' => ['required', 'min:3', 'max:256', "regex:/\w+ \w+/i"],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'max:256', 'confirmed'],
            'terms' => ['required'],
        ]);

        $user = User::query()->create($data);
        $user->logs()->create([
            "user_id" => $user->id,
            "type" => LogTypeEnum::INFO->value,
            "description" => "Cadastro realizado."
        ]);

        return redirect()->route('auth.index')->with(
            Message::success('Cadastro realizado com sucesso.')
        );
    }

    public function logout(): RedirectResponse
    {
        if (!Auth::check())
            return redirect()->route("auth.index");

        auth()->logout();
        return redirect()->route('auth.index')->with(
            Message::primary('Volte sempre.')
        );
    }
}
