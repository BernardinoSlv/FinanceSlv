@extends('master.master')

@section('content')
    @include('includes.alerts')

    <form action="{{ route('needs.update', $need) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row gy-3 mb-4">
            <div class="col-sm-6">
                <label for="" class="form-label">Identificador</label>
                <select name="identifier_id" class="form-control">
                    <option value="" selected></option>
                    @foreach ($identifiers as $identifier)
                        <option value="{{ $identifier->id }}" @selected(intval(old('identifier_id', $need->identifier_id)) === $identifier->id)>{{ $identifier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-6">
                <label for="" class="form-label">Título</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $need->title) }}">
                <div class="form-text">Ex: Pagamento do fulano</div>
            </div>
            <div class="col-sm-6">
                <label for="" class="form-label">Valor</label>
                <input type="text" name="amount" class="form-control" value="{{ old('amount', $need->amount) }}">
                <div class="form-text">Ex: 125,50</div>
            </div>
            <div class="col-sm-6">
                <label for="" class="form-label">Status</label>
                <select name="completed" id="" class="form-control">
                    <option value="0" selected>Pendente</option>
                    <option value="1" @selected(intval(old('completed', $need->completed)) === 1)>Completo</option>
                </select>
            </div>
            <div class="col-12">
                <label for="" class="form-label">Descrição</label>
                <textarea name="description" class="form-control" style="height: 140px">{{ old('description', $need->description) }}</textarea>
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary">Criar</button>
        </div>
    </form>
@endsection
