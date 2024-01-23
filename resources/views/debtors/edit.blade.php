@extends('master.master')

@section('content')
    @include('includes.alerts')

    <form action="{{ route('expenses.update', $debtor) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row gy-3 mb-4">
            <div class="col-sm-8">
                <label for="" class="form-label">Título</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $debtor->title) }}">
                <div class="form-text"></div>
            </div>
            <div class="col-sm-4">
                <label for="" class="form-label">Valor</label>
                <input type="text" name="amount" class="form-control" value="{{ old('amount', $debtor->amount) }}">
                <div class="form-text">Ex: 125,50</div>
            </div>
            <div class="col-sm-4">
                <label for="" class="form-label">Data de efetivação</label>
                <input type="date" name="effetive_at" class="form-control"
                    value="{{ old('effetive_at', $debtor->effetive_at) }}">
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
@endsection
