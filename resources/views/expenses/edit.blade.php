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
                    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Despesas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-secondary mb-3 d-sm-none">
        <i class="bi bi-caret-left-fill"></i>
    </a>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('expenses.update', $expense) }}">
                        @csrf
                        @method('PUT')

                        @include('includes.alerts')

                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <h5 class="mb-2">Identificador</h5>
                                <x-inputs.selects.identifier :identifiers="$identifiers" :selected-id="$expense->identifier_id" />
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Título</h5>
                                <input type="text" name="title" value="{{ old('title', $expense->title) }}"
                                    class="form-control">
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Valor</h5>
                                <input type="text" name="amount" value="{{ old('amount', $expense->amount) }}"
                                    class="form-control mb-1" data-js-mask="money">
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Dia de vencimento</h5>
                                <input type="number" name="due_day" min="1" max="31"
                                    value="{{ old('due_date', $expense->due_day) }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <h5 class="mb-2">Descrição</h5>
                                <textarea name="description" class="form-control">{{ old('description', $expense->description) }}</textarea>
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
