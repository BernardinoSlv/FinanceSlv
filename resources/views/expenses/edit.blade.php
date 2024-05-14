@extends('master.master')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Despesas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar #{{ $expense->id }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('expenses.update', $expense) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row gy-3 mb-4">
                            <div class="col-sm-6">
                                <label for="" class="form-label">Identificador</label>
                                <x-inputs.selects.identifier :identifiers="$identifiers" :selected-id="$expense->identifier_id" />
                            </div>
                            <div class="col-sm-6">
                                <label for="" class="form-label">Título</label>
                                <input type="text" name="title" class="form-control"
                                    value="{{ old('title', $expense->title) }}">
                                <div class="form-text"></div>
                            </div>
                            <div class="col-sm-6">
                                <label for="" class="form-label">Valor</label>
                                <input type="text" name="amount" class="form-control"
                                    value="{{ old('amount', $expense->amount) }}">
                                <div class="form-text">Ex: 125,50</div>
                            </div>
                            <div class="col-sm-4">
                                <label for="" class="form-label">Quantidade</label>
                                <input type="number" name="quantity" class="form-control"
                                    value="{{ old('quantity', $expense->quantity) }}">
                                <div class="form-text">Vazio para despesas sem fim</div>
                            </div>
                            <div class="col-sm-4">
                                <label for="" class="form-label">Data de início</label>
                                <input type="date" name="effetive_at" class="form-control"
                                    value="{{ old('effetive_at', $expense->effetive_at) }}">
                                <div class="form-text"></div>
                            </div>
                            <div></div>
                            <div class="col-12">
                                <label for="" class="form-label">Descrição</label>
                                <textarea name="description" class="form-control" style="height: 140px">{{ old('description', $expense->description) }}</textarea>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div><!--end row-->
@endsection
