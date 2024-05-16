@extends('master.master')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('investiments.index') }}">Investimentos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Criar</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
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
                </div>
            </div>
        </div>

    </div><!--end row-->
@endsection
