@extends('master.master')

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Movimentações</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    @include('includes.alerts')

    <div class="product-count d-flex align-items-center gap-3 gap-lg-4 mb-4 fw-bold flex-wrap font-text1">
        <a href="javascript:;"><span class="me-1">All</span><span class="text-secondary">(88754)</span></a>
        <a href="javascript:;"><span class="me-1">Published</span><span class="text-secondary">(56242)</span></a>
        <a href="javascript:;"><span class="me-1">Drafts</span><span class="text-secondary">(17)</span></a>
        <a href="javascript:;"><span class="me-1">On Discount</span><span class="text-secondary">(88754)</span></a>
    </div>

    <form action="{{ route('movements.index') }}" method="GET" class="row g-3">
        <div class="col-auto">
            <div class="position-relative">
                <input class="form-control px-5" type="search" name="text" value="{{ request('text') }}"
                    placeholder="Buscar">
                <span
                    class="material-symbols-outlined position-absolute ms-3 translate-middle-y start-0 top-50 fs-5">search</span>
            </div>
        </div>
        <div class="col-auto flex-grow-1 overflow-auto">
            <div class="btn-group position-static">
                <div class="btn-group position-static">
                    <button type="button" class="btn border btn-light dropdown-toggle px-4" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-expanded="false">
                        Buscar por
                    </button>
                    <ul class="dropdown-menu">
                        <li class="">
                            <h6 class="dropdown-header">Campos</h6>
                        </li>
                        <li class="">
                            <label for="radio-search-by-empty" class="form-check-label dropdown-item">
                                <input type="radio" name="search_by" id="radio-search-by-empty" value=""
                                    class="form-check-input" @checked(!request('search_by'))>
                                Todos</label>
                        </li>
                        <li class="">
                            <label for="radio-search-by-title" class="form-check-label dropdown-item">
                                <input type="radio" name="search_by" id="radio-search-by-title" value="title"
                                    class="form-check-input" @checked(request('search_by') === 'title')>
                                Título</label>
                        </li>
                        <li class="">
                            <label for="radio-search-by-identifier" class="form-check-label dropdown-item">
                                <input type="radio" name="search_by" id="radio-search-by-identifier" value="identifier"
                                    class="form-check-input" @checked(request('search_by') === 'identifier')>
                                Identificador</label>
                        </li>
                    </ul>
                </div>
                <div class="btn-group position-static">
                    <button type="button" class="btn border btn-light dropdown-toggle px-4" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-expanded="false">
                        Ordernar por
                    </button>
                    <ul class="dropdown-menu">
                        <li class="">
                            <h6 class="dropdown-header">Campos</h6>
                        </li>
                        <li class="">
                            <label for="radio-order-by-date" class="form-check-label dropdown-item">
                                <input type="radio" name="order_by" id="radio-order-by-date" value=""
                                    class="form-check-input" @checked(!request('order_by'))>
                                Data</label>
                        </li>
                        <li class="">
                            <label for="radio-order-by-title" class="form-check-label dropdown-item">
                                <input type="radio" name="order_by" id="radio-order-by-title" value="title"
                                    class="form-check-input" @checked(request('order_by') === 'title')>
                                Título</label>
                        </li>
                        <li class="">
                            <label for="radio-order-by-amount" class="form-check-label dropdown-item">
                                <input type="radio" name="order_by" id="radio-order-by-amount" value="amount"
                                    class="form-check-input" @checked(request('order_by') === 'amount')>
                                Valor</label>
                        </li>
                        <li class="">
                            <label for="radio-order-by-identifier" class="form-check-label dropdown-item">
                                <input type="radio" name="order_by" id="radio-order-by-identifier" value="identifier"
                                    class="form-check-input" @checked(request('order_by') === 'identifier')>
                                Identificador</label>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li>
                            <h6 class="dropdown-header">Ordem</h6>
                        </li>
                        <li class="">
                            <label for="radio-order-type-asc" class="form-check-label dropdown-item">
                                <input type="radio" name="order_type" id="radio-order-type-asc" value="a"
                                    class="form-check-input" @checked(request('order_type') === 'a')>
                                Crescenter</label>
                        </li>
                        <li class="">
                            <label for="radio-order-type-desc" class="form-check-label dropdown-item">
                                <input type="radio" name="order_type" id="radio-order-type-desc" value=""
                                    class="form-check-input" @checked(!request('order_type'))>
                                Decrescente</label>
                        </li>
                    </ul>
                </div>
                <div class="btn-group position-static">
                    <button type="button" class="btn border btn-light dropdown-toggle px-4" data-bs-toggle="dropdown"
                        data-bs-auto-close="outside" aria-expanded="false">
                        Tipo
                    </button>
                    <ul class="dropdown-menu">
                        <li class="">
                            <label for="radio-type-all" class="form-check-label dropdown-item">
                                <input type="radio" name="type" id="radio-type-all" value=""
                                    class="form-check-input" @checked(!request('type'))>
                                Todos</label>
                        </li>
                        <li class="">
                            <label for="radio-type-in" class="form-check-label dropdown-item">
                                <input type="radio" name="type" id="radio-type-in" value="in"
                                    class="form-check-input" @checked(request('type') === 'in')>
                                Entrada</label>
                        </li>
                        <li class="">
                            <label for="radio-type-out" class="form-check-label dropdown-item">
                                <input type="radio" name="type" id="radio-type-out" value="out"
                                    class="form-check-input" @checked(request('type') === 'out')>
                                Saída</label>
                        </li>
                        <li class="dropdown-divider"></li>
                        <li>
                            <h4 class="dropdown-header">Operação</h4>
                        </li>
                        <li class="">
                            <label for="radio-operation-type-all" class="form-check-label dropdown-item">
                                <input type="radio" name="operation_type" id="radio-operation-type-all" value=""
                                    class="form-check-input" @checked(!request('operation_type'))>
                                Todas</label>
                        </li>
                        <li class="">
                            <label for="radio-operation-type-quick" class="form-check-label dropdown-item">
                                <input type="radio" name="operation_type" id="radio-operation-type-quick"
                                    value="quick" class="form-check-input" @checked(request('operation_type') === 'quick')>
                                Simples</label>
                        </li>
                        <li class="">
                            <label for="radio-operation-type-debt" class="form-check-label dropdown-item">
                                <input type="radio" name="operation_type" id="radio-operation-type-debt"
                                    value="debt" class="form-check-input" @checked(request('operation_type') === 'debt')>
                                Dívidas</label>
                        </li>
                    </ul>
                </div>
                <button class="btn btn-primary flex-shrink-0">
                    <i class="bi bi-search"></i> Filtrar
                </button>
            </div>
        </div>
        <div class="col-auto">
            <div class="d-flex align-items-center gap-2 justify-content-lg-end">
                <button class="btn btn-light px-4" type="button"><i
                        class="bi bi-box-arrow-right me-2"></i>Export</button>
            </div>
        </div>
    </form><!--end row-->

    <div class="card mt-4">
        <div class="card-body">
            <div class="product-table">
                <div class="table-responsive white-space-nowrap">
                    <table class="table align-middle table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Operação</th>
                                <th>Tipo</th>
                                <th>Título</th>
                                <th>Valor</th>
                                <th>Identificador</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($movements as $movement)
                                @php
                                    $movementableType = \App\Enums\MovementableEnum::from(
                                        get_class($movement->movementable),
                                    );
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $movement->id }}</strong>
                                    </td>
                                    <td>
                                        <span class=" border-bottom">
                                            {{ $movementableType->getLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <x-movement-type :movement="$movement" />
                                    </td>
                                    <td>{{ $movement->movementable->title }}</td>
                                    <td>R$ {{ number_format($movement->amount, '2', ',', '.') }}</td>
                                    <td>
                                        <a href="javascript:;">{{ $movement->identifier?->name }}</a>
                                    </td>
                                    <td>
                                        @if ($movement->effetive_date)
                                            @if ($movement->effetive_date->lt(now()->format('Y-m-d')) && !$movement->closed_date)
                                                <span class="text-danger fw-bold">
                                                    {{ $movement->effetive_date->format('d/m/Y') }}
                                                </span>
                                            @elseif ($movement->effetive_date->gte(now()->format('Y-m-d')))
                                                <span class="text-warning fw-bold">
                                                    {{ $movement->effetive_date->format('d/m/Y') }}
                                                </span>
                                            @else
                                                {{ $movement->effetive_date->format('d/m/Y') }}
                                            @endif
                                        @else
                                            {{ $movement->created_at->format('d/m/Y') }}
                                        @endif
                                        <br>
                                        @if ($movement->effetive_date?->lt(now()->format('Y-m-d')))
                                            <div class="badge text-bg-danger">Atrasada</div>
                                        @elseif (!$movement->closed_date)
                                            <div class="badge text-bg-warning">Em aberto</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="dropdown">
                                            <button
                                                class="btn btn-sm btn-light border dropdown-toggle dropdown-toggle-nocaret"
                                                type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    @if (!$movement->closed_date)
                                                        <button class="dropdown-item"
                                                            data-config="{{ json_encode([
                                                                'url' => route('movements.update', $movement),
                                                            ]) }}"
                                                            data-bs-toggle="modal" data-bs-target="#modal-edit-in-open">
                                                            Atualizar
                                                        </button>
                                                    @endif
                                                </li>
                                                <li>
                                                    <form action="{{ route('movements.destroy', $movement) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('O registro será deletado permanentemente!')">
                                                        @method('DELETE')
                                                        @csrf

                                                        <button type="submit" class="dropdown-item">Remover</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <x-pagination :paginator="$movements" />
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal" id="modal-edit-in-open">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <form action="" method="PUT" data-js-component="form-ajax">
                        <div class="mb-3 text-end">
                            <button class="btn-close" data-dismiss="modal"></button>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Título</label>
                            <input type="text" class="form-control" id="modal-input-title" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Valor</label>
                            <input type="text" class="form-control" id="modal-input-amount" disabled>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Juros</label>
                            <input type="text" class="form-control" name="fees_amount" data-js-mask="money"
                                id="modal-input-fees_amount">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label">Situação</label>
                            <select name="status" class="form-control" id="modal-input-status">
                                <option value="">Em aberto</option>
                                <option value="1">Concluído</option>
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary">Atualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        window.addEventListener('load', () => {
            const modal = document.querySelector("#modal-edit-in-open");

            modal.addEventListener("show.bs.modal", (event) => {
                const config = JSON.parse(event.relatedTarget.getAttribute("data-config"));

                console.log(config);
            });
        });
    </script>
@endsection
