@extends('master.master')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('investiments.index') }}">Investimentos</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ $investiment->id }}
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('investiments.entries.index', $investiment) }}">Retiradas</a>
                    </li>
                    <li class="breadcrumb-item">
                        Editar
                    </li>
                    <li class="breadcrumb-item active">
                        {{ $entry->id }}
                    </li>
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
                                <input type="text" name="amount" value="{{ old('amount', $entry->amount) }}"
                                    class="form-control">
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
