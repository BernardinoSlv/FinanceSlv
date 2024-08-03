@extends('master.master')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('identifiers.index') }}">Identificadores</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar #{{ $identifier->id }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <a href="" class="btn btn-sm btn-outline-secondary mb-3 d-sm-none">
        <i class="bi bi-caret-left-fill"></i>
    </a>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('identifiers.update', $identifier) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @include('includes.alerts')

                        <div class="row gy-3 mb-4">
                            {{-- <div class="col-sm-6">
                <img src="{{  }}" alt="">
                        <label for="" class="form-label">Avatar</label>
                        <input type="file" name="avatar" class="form-control">
                        <div class="form-text"></div>
                    </div> --}}
                            <div class="col-sm-6">
                                <label for="" class="form-label">Nome</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $identifier->name) }}">
                                <div class="form-text"></div>
                            </div>
                            <div class="col-sm-6">
                                <label for="" class="form-label">Telefone</label>
                                <input type="text" name="phone" class="form-control"
                                    value="{{ old('phone', $identifier->phone) }}">
                                <div class="form-text">ex: (10) 98765-4321</div>
                            </div>
                            <div class="col-12">
                                <label for="" class="form-label">Descrição</label>
                                <textarea name="description" class="form-control" style="height: 140px">{{ old('description', $identifier->description) }}</textarea>
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
