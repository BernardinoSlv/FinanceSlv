@extends('master.master')

@section('content')
    @include('includes.alerts')

    <form action="{{ route('debts.update', $debt) }}" method="POST">
        @csrf

        <div class="row gy-3 mb-4">
            <div class="col-sm-8">
                <label for="" class="form-label">Título</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $debt->title) }}">
                <div class="form-text">Ex: Pagamento do fulano</div>
            </div>
            <div class="col-sm-4">
                <label for="" class="form-label">Valor</label>
                <input type="text" name="amount" class="form-control" value="{{ old('amount', $debt->amout) }}">
                <div class="form-text">Ex: 125,50</div>
            </div>
            <div class="col-sm-4">
                <label for="" class="form-label">Data de iniciação</label>
                <input type="text" name="start_at" class="form-control" value="{{ old('start_at', $debt->start_at) }}">
            </div>
            <div></div>
            <div class="col-12">
                <label for="" class="form-label">Descrição</label>
                <textarea name="description" class="form-control" style="height: 140px">{{ old('description', $debt->description) }}</textarea>
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary">Criar</button>
        </div>
    </form>
@endsection
