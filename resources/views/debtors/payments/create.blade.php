@extends('master.master')

@section('content')
    @include('includes.alerts')

    <form action="{{ route('debtors.payments.store', $debtor) }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row gy-3 mb-4">

            <div class="col-sm-6">
                <label for="" class="form-label">Comprovante</label>
                <input type="file" name="images[]" multiple class="form-control" value="">
                <div class="form-text"></div>
            </div>
            <div class="col-sm-6">
                <label for="" class="form-label">Valor</label>
                <input type="text" name="amount" class="form-control" value="{{ old('amount') }}">
                <div class="form-text">Ex: 125,50</div>
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-primary">Criar</button>
        </div>
    </form>
@endsection
