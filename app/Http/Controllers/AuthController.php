<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Repositories\Contracts\UserRepositoryContract;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected UserRepositoryContract $_userRepository;

    public function __construct(UserRepositoryContract $userRepository)
    {
        $this->_userRepository = $userRepository;
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
