@extends('master.master')

@section('content')
    @include('includes.alerts')

    <form action="{{ route('debts.store') }}" method="POST">
        @csrf

        <div class="row gy-3 mb-4">
            <div class="col-sm-6">
                <label for="" class="form-label">Identificador</label>
                <x-inputs.selects.identifier :identifiers="$identifiers" />
            </div>
            <div class="col-sm-6">
                <label for="" class="form-label">Título</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                <div class="form-text"></div>
            </div>
            <div class="col-sm-6">
                <label for="" class="form-label">Valor</label>
                <input type="text" name="amount" class="form-control" value="{{ old('amount') }}">
                <div class="form-text">Ex: 125,50</div>
            </div>
            <div class="col-sm-4">
                <label for="" class="form-label">Data de iniciação</label>
                <input type="date" name="start_at" class="form-control" value="{{ old('start_at') }}">
            </div>
            <div></div>
            <div class="col-12">
                <label for="" class="form-label">Descrição</label>
                <textarea name="description" class="form-control" style="height: 140px">{{ old('description') }}</textarea>
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary">Criar</button>
        </div>
    </form>
@endsection
