<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected UserRepositoryContract $_userRepository;

    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->_userRepository = $userRepository;
    }

    public function index()
    {
        return view("auth.index");
    }

    public function indexStore(Request $request)
    {
        $data = $request->validate([
            "email" => ["required", "email"],
            "password" => ["required", "min:8", "max:256"]
        ]);

        if (!Auth::attempt(
            $data,
            $request->remember ? true : false
        )) {
            return redirect()->route("auth.index")->with(
                Alert::danger("E-mail e/ou senha incorretos.")
            )->withInput();
        }

        return redirect()->route("dashboard.index");
    }


    public function create()
    {
        return view("auth.create");
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => ["required", "min:3", "max:256", "regex:/\w+ \w+/i"],
            "email" => ["required", "email", "unique:users"],
            "password" => ["required", "min:8", "max:256"],
            "confirm_password" => ["required", "same:password"],
            "terms" => ["required"]
        ]);

        $this->_userRepository->create($data);

        return redirect()->route("auth.index")->with(
            Alert::success("Cadastro realizado com sucesso.")
        );
    }
}
