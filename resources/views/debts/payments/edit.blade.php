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
                    <form method="POST" action="{{ route('debts.update', $debt) }}">
                        @method('PUT')
                        @csrf

                        @include('includes.alerts')

                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <h5 class="mb-2">Identificador</h5>
                                <x-inputs.selects.identifier :identifiers="$identifiers" :selected-id="$debt->identifier_id" />
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Título</h5>
                                <input type="text" name="title" value="{{ old('title', $debt->title) }}"
                                    class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Valor</h5>
                                <input type="text" name="amount" value="{{ old('amount', $debt->amount) }}"
                                    class="form-control" data-js-mask="money1">
                                <div class="form-text"><strong>Ex</strong>: 1.599,00</div>
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Parcelas <small class="text-muted">(Opcional)</small></h5>
                                <input type="number" name="installments"
                                    value="{{ old('installments', $debt->installments) }}" class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Data de vencimento</h5>
                                <input type="date" name="due_date"
                                    value="{{ old('due_date', $debt->due_date->format('Y-m-d')) }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <h5 class="mb-2">Descrição</h5>
                                <textarea name="description" class="form-control">{{ old('description', $debt->description) }}</textarea>
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
