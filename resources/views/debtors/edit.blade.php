@extends('master.master')

@section('content')
<!--breadcrumb-->
<div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
    <div class="breadcrumb-title pe-3">Operações</div>
    <div class="ps-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 p-0">
                <li class="breadcrumb-item"><a href="">Entradas rápidas</a></li>
                <li class="breadcrumb-item active" aria-current="page">Editar #</li>
            </ol>
        </nav>
    </div>
</div>
<!--end breadcrumb-->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form action="{{ route('debtors.update', $debtor) }}" method="POST">
                    @csrf
                    @method('PUT')

                    @include("includes.alerts")

                    <div class="row gy-3 mb-4">
                        <div class="col-sm-6">
                            <label for="" class="form-label">Identificador</label>
                            <x-inputs.selects.identifier :identifiers="$identifiers" :selected-id="$debtor->identifier_id" />
                        </div>
                        <div class="col-sm-6">
                            <label for="" class="form-label">Título</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $debtor->title) }}">
                            <div class="form-text"></div>
                        </div>
                        <div class="col-sm-6">
                            <label for="" class="form-label">Valor</label>
                            <input type="text" name="amount" class="form-control" value="{{ old('amount', $debtor->amount) }}">
                            <div class="form-text">Ex: 125,50</div>
                        </div>
                        <div class="col-sm-4">
                            <label for="" class="form-label">Data de efetivação</label>
                            <input type="date" name="effetive_at" class="form-control" value="{{ old('effetive_at', $debtor->effetive_at) }}">
                            <div class="form-text"></div>
                        </div>
                        <div></div>
                        <div class="col-12">
                            <label for="" class="form-label">Descrição</label>
                            <textarea name="description" class="form-control" style="height: 140px">{{ old('description', $debtor->description) }}</textarea>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Criar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div><!--end row-->
@endsection