@extends('master.master')

@section('content')
    @include('includes.alerts')

    <form action="{{ route('investiments.store') }}" method="POST">
        @csrf
        <div class="row gy-3 mb-4">
            <div class="col-sm-6">
                <label for="" class="form-label">Identificador</label>
                <x-inputs.selects.identifier :identifiers="$identifiers" />
            </div>
            <div class="col-sm-6">
                <label for="" class="form-label">Título</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                <div class="form-text">Ex: Pagamento do fulano</div>
            </div>
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
