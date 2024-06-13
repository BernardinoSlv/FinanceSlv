@php
    use App\Enums\MovementTypeEnum;
@endphp

@extends('master.master')

@section('content')
    <!--breadcrumb-->
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="{{ route('debts.index') }}">Dívidas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('quicks.update', $quick) }}">
                        @method('PUT')
                        @csrf

                        @include('includes.alerts')

                        <div class="row gy-4">
                            <div class="col-sm-6">
                                <h5 class="mb-2">Tipo</h5>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input type="radio" name="type" value="{{ MovementTypeEnum::IN->value }}"
                                            id="in" class="form-check-input" @checked(old('type', $quick->movement?->type) === MovementTypeEnum::IN->value)>
                                        <label for="in">Entrada</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="type" value="{{ MovementTypeEnum::OUT->value }}"
                                            id="out" class="form-check-input" @checked(old('type', $quick->movement?->type) === MovementTypeEnum::OUT->value)>
                                        <label for="out">Saída</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Identificador</h5>
                                <x-inputs.selects.identifier :identifiers="$identifiers" :selected-id="$quick->identifier_id" />
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Título</h5>
                                <input type="text" name="title" value="{{ old('title', $quick->title) }}"
                                    class="form-control">
                                <div class="form-text"><strong>Ex</strong>: hora extra</div>
                            </div>
                            <div class="col-sm-6">
                                <h5 class="mb-2">Valor</h5>
                                <input type="text" name="amount" value="{{ old('amount', $quick->movement?->amount) }}"
                                    class="form-control">
                                <div class="form-text"><strong>Ex</strong>: 1.599,00</div>
                            </div>
                            <div class="col-12">
                                <h5 class="mb-2">Descrição</h5>
                                <textarea name="description" class="form-control">{{ old('description', $quick->description) }}</textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-outline-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div><!--end row-->
@endsection
