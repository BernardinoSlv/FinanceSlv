@extends('master.master')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('debts.index') }}">Dívidas</a></li>
                    <li class="breadcrumb-item">
                        {{ $debt->id }}
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('debts.payments.index', $debt) }}">
                            Pagamentos
                        </a>
                    </li>
                    <li class="breadcrumb-item ">Editar</li>
                    <li class="breadcrumb-item active">{{ $leave->id }}</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form
                        action="{{ route('debts.payments.update', [
                            'debt' => $debt,
                            'leave' => $leave,
                        ]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        @include('includes.alerts')

                        <div class="row gy-3 mb-4">

                            <div class="col-sm-6">
                                <label for="" class="form-label">Comprovante</label>
                                <input type="file" name="images[]" multiple class="form-control" value="">
                                <div class="form-text"></div>
                            </div>
                            <div class="col-sm-6">
                                <label for="" class="form-label">Valor</label>
                                <input type="text" name="amount" class="form-control"
                                    value="{{ old('amount', $leave->amount) }}">
                                <div class="form-text">Ex: 125,50</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Atualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div><!--end row-->
@endsection
