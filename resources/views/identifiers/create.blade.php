@extends('master.master')

@section('content')
    @include('includes.alerts')

    <form action="{{ route('identifiers.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row gy-3 mb-4">
            {{-- <div class="col-sm-6">
                <label for="" class="form-label">Avatar</label>
                <input type="file" name="avatar" class="form-control">
                <div class="form-text"></div>
            </div> --}}
            <div class="col-sm-6">
                <label for="" class="form-label">Nome</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                <div class="form-text"></div>
            </div>
            <div class="col-sm-6">
                <label for="" class="form-label">Telefone</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                <div class="form-text">ex: (10) 98765-4321</div>
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
