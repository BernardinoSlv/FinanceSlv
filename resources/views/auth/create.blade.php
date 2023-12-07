<!DOCTYPE html>
<html lang="pt-BR" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cadastrar | Finance Slv</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="min-vh-100 container d-flex">
        <div class="m-auto" style="max-width: 600px; width: 100%;">
            <h1 class="fs-4 text-center">Seja bem vindo! <i class="fas fa-door-open"></i></h1>
            <h2 class="fs-5 mb-4 text-muted text-center">Preencha os dados para fazer parte</h2>
            <form action="{{ route('auth.store') }}" method="POST">
                @csrf
                @include('includes.alerts')

                <div class="mb-3 form-floating">
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                        placeholder="">
                    <label for="">Nome</label>
                    <div class="form-text">Nome e sobrenome</div>
                </div>
                <div class="mb-3 form-floating">
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                        placeholder="">
                    <label for="" class="">E-mail</label>
                    <div class="form-text">Seu melhor e-mail</div>
                </div>
                <div class="mb-3 form-floating">
                    <input type="password" name="password" class="form-control" placeholder="">
                    <label for="">Senha</label>
                    <div class="form-text">Deve conter no mínimo 8 caracteres</div>
                </div>
                <div class="mb-3 form-floating">
                    <input type="password" name="confirm_password" class="form-control" placeholder="">
                    <label for="">Confirme sua senha</label>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" name="terms" class="form-check-input" {{ old('terms') ? 'checked' : '' }}
                        id="terms">
                    <label for="terms" class="form-check-label">Aceito os temos e condições</label>
                </div>
                <div class="text-end">
                    <button class="btn btn-primary">Cadastrar</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous">
    </script>
</body>

</html>
