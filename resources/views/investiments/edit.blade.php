@extends('master.master')

@section('content')
    @include('includes.alerts')

    <form action="{{ route('investiments.update', $investiment) }}" method="POST">
        @method('PUT')
        @csrf
        <div class="row gy-3 mb-4">
            <div class="col-sm-6">
                <label for="" class="form-label">Identificador</label>
                <x-inputs.selects.identifier :identifiers="$identifiers" :selected-id="$investiment->identifier_id" />
            </div>
            <div class="col-sm-6">
                <label for="" class="form-label">Valor</label>
                <input type="text" name="amount" class="form-control" value="{{ old('amount', $investiment->amount) }}">
                <div class="form-text">Ex: 125,50</div>
            </div>
            <div class="col-12">
                <label for="" class="form-label">Descrição</label>
                <textarea name="description" class="form-control" style="height: 140px">{{ old('description', $investiment->description) }}</textarea>
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary">Criar</button>
        </div>
    </form>
@endsection
