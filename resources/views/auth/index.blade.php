<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Entrar | Finance Slv</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="min-vh-100 container d-flex">
        <div class="m-auto" style="max-width: 600px; width: 100%;">
            <h1 class="fs-4 text-center">Quase lá! <i class="fas fa-sign-in-alt"></i></h1>
            <h2 class="fs-5 mb-4 text-muted text-center">Basta informar seu e-mail e senha</h2>
            <form action="{{ route('auth.attempt') }}" method="POST">
                @csrf
                @include('includes.alerts')

                <div class="mb-3 form-floating">
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                        placeholder="">
                    <label for="" class="">E-mail</label>
                </div>
                <div class="mb-3 form-floating">
                    <input type="password" name="password" class="form-control" placeholder="">
                    <label for="">Senha</label>
                </div>
                <div class="mb-3 form-check form-switch">
                    <input type="checkbox" name="remember" class="form-check-input"
                        {{ old('remember') ? 'checked' : '' }} id="remember">
                    <label for="remember" class="form-check-label">Lembrar de mim</label>
                </div>
                <div class="text-end">
                    <button class="btn btn-primary">Entrar</button>
                </div>
            </form>
            <p>Ainda não tem conta? <a href="{{ route('auth.create') }}">clique aqui</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>
