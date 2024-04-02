@extends('master.master')

@section('content')
    @include('includes.alerts')

    <form
        action="{{ route('investiments.entries.update', [
            'investiment' => $investiment,
            'entry' => $entry,
        ]) }}"
        method="POST">
        @method('PUT')
        @csrf

        <div class="row gy-3 mb-4">
            <div class="col-sm-6">
                <label for="" class="form-label">Comprovante</label>
                <input type="file" name="" class="form-control">
            </div>
            <div class="col-6">
                <label for="" class="form-label">Valor</label>
                <input type="text" name="amount" value="{{ old('amount', $entry->amount) }}" class="form-control">
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary">Atualizar</button>
        </div>
    </form>
@endsection
