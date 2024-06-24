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
                    <li class="breadcrumb-item"><a href="{{ route('debts.payments.index', $debt) }}">Pagamentos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST"
                        action="{{ route('debts.payments.update', [
                            'debt' => $debt,
                            'movement' => $movement,
                        ]) }}">

                        @method('PUT')
                        @csrf

                        @include('includes.alerts')

                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <h5 class="mb-2">Valor</h5>
                                <input type="text" name="amount" value="{{ old('amount', $movement->amount) }}"
                                    class="form-control" data-js-mask="money">
                                <div class="form-text"><strong>Ex</strong>: 1.599,00</div>
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
