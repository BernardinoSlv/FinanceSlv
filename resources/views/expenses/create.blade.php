@php
    use App\Enums\MovementTypeEnum;
@endphp

@extends('master.master')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('debts.index') }}">Dívidas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Criar</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <a href="{{ route('debts.index') }}" class="btn btn-sm btn-outline-secondary mb-3 d-sm-none">
        <i class="bi bi-caret-left-fill"></i>
    </a>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('debts.store') }}">
                        @csrf

                        @include('includes.alerts')

                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <h5 class="mb-2">Identificador</h5>
                                <x-inputs.selects.identifier :identifiers="$identifiers" />
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Título</h5>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Valor</h5>
                                <input type="text" name="amount" value="{{ old('amount') }}" class="form-control mb-1"
                                    data-js-mask="money">
                                <div class="form-check">
                                    <label for="to-balance" class="">Vai para o saldo</label>
                                    <input type="checkbox" id="to-balance" name="to_balance" class="form-check-input"
                                        @checked(old('to_balance') === 'on')>
                                    <div class="d-inline-block px-1 rounded-circle border text-bg-secondary"
                                        data-bs-toggle="tooltip"
                                        data-bs-title="Valor da dívida entrará como saldo na conta."
                                        style="cursor: pointer;">
                                        <span class="material-symbols-outlined">
                                            question_mark
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Parcelas <small class="text-muted">(Opcional)</small></h5>
                                <input type="number" name="installments" value="{{ old('installments') }}"
                                    class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Data de vencimento</h5>
                                <input type="date" name="due_date" value="{{ old('due_date') }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <h5 class="mb-2">Descrição</h5>
                                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-outline-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div><!--end row-->
@endsection

@section('scripts')
    <script>
        window.addEventListener("load", () => {
            new bootstrap.Tooltip('[data-bs-toggle=tooltip]');
        });
    </script>
@endsection
