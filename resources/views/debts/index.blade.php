@extends('master.master')

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Dívidas</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    @include('includes.alerts')

    <div class="product-count d-flex align-items-center gap-3 gap-lg-4 mb-4 fw-bold flex-wrap font-text1">
        <a href="{{ route('debts.index') }}"><span class="me-1">All</span><span
                class="text-secondary">({{ $debts->total() }})</span></a>
        {{-- <a href="javascript:;"><span class="me-1">Published</span><span class="text-secondary">(56242)</span></a>
        <a href="javascript:;"><span class="me-1">Drafts</span><span class="text-secondary">(17)</span></a>
        <a href="javascript:;"><span class="me-1">On Discount</span><span class="text-secondary">(88754)</span></a> --}}
    </div>

    <form action="{{ route('debts.index') }}" method="GET" class="row g-3">
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
                            <label for="radio-order-by-amount-paid" class="form-check-label dropdown-item">
                                <input type="radio" name="order_by" id="radio-order-by-amount-paid" value="amount_paid"
                                    class="form-check-input" @checked(request('order_by') === 'amount_paid')>
                                Valor pago</label>
                        </li>
                        <li class="">
                            <label for="radio-order-by-identifier" class="form-check-label dropdown-item">
                                <input type="radio" name="order_by" id="radio-order-by-identifier" value="identifier"
                                    class="form-check-input" @checked(request('order_by') === 'identifier')>
                                Identificador</label>
                        </li>
                        <li class="">
                            <label for="radio-order-by-due-date" class="form-check-label dropdown-item">
                                <input type="radio" name="order_by" id="radio-order-by-due-date" value="due_date"
                                    class="form-check-input" @checked(request('order_by') === 'due_date')>
                                Vencimento</label>
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
                        Status
                    </button>
                    <ul class="dropdown-menu">
                        <li class="">
                            <label for="radio-status-empty" class="form-check-label dropdown-item">
                                <input type="radio" name="status" id="radio-status-empty" value=""
                                    class="form-check-input" @checked(!request('type'))>
                                Todos</label>
                        </li>
                        <li class="">
                            <label for="radio-status-paid" class="form-check-label dropdown-item">
                                <input type="radio" name="status" id="radio-status-paid" value="paid"
                                    class="form-check-input" @checked(request('status') === 'paid')>
                                Pago</label>
                        </li>
                        <li class="">
                            <label for="radio-status-paying" class="form-check-label dropdown-item">
                                <input type="radio" name="status" id="radio-status-paying" value="paying"
                                    class="form-check-input" @checked(request('status') === 'paying')>
                                Pagando</label>
                        </li>
                        <li class="">
                            <label for="radio-status-no-paying" class="form-check-label dropdown-item">
                                <input type="radio" name="status" id="radio-status-no-paying" value="no-paying"
                                    class="form-check-input" @checked(request('status') === 'no-paying')>
                                Nenhum pagamento</label>
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
                <a class="btn btn-primary px-4" href="{{ route('debts.create') }}"><i
                        class="bi bi-plus-lg me-2"></i>Criar</a>
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
                                <th>Identificador</th>
                                <th>Título</th>
                                <th>Valor</th>
                                <th>Pago</th>
                                <th>Parcelas</th>
                                <th>Vencimento</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($debts as $debt)
                                <tr>
                                    <td>
                                        <strong>{{ $debt->id }}</strong>
                                    </td>
                                    <td>
                                        <a href="javascript:;">{{ $debt->identifier?->name }}</a>
                                    </td>
                                    <td>{{ $debt->title }}</td>
                                    <td>R$ {{ number_format($debt->amount, 2, ',', '.') }}</td>
                                    <td>
                                        R$ {{ number_format($debt->movements_sum_amount, 2, ',', '.') }}
                                        <br>

                                        <div class="progress" style="height: 5px;">
                                            <div @class([
                                                'progress-bar',
                                                'progress-bar-animated',
                                                'bg-success' =>
                                                    floatval($debt->movements_sum_amount) >= floatval($debt->amount),
                                            ]) role="progressbar"
                                                style="width: {{ intval((100 / $debt->amount) * intval($debt->movements_sum_amount)) }}%;"
                                                aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                        <div class="text-end fw-bold">
                                            {{ intval((100 / $debt->amount) * intval($debt->movements_sum_amount)) }}%
                                        </div>
                                    </td>
                                    <td>{{ $debt->installments }}</td>
                                    <td>
                                        {{ $debt->due_date?->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        {{ $debt->created_at->format('d/m/Y') }}
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
                                                    <a class="dropdown-item"
                                                        href="{{ route('debts.edit', $debt) }}">Editar</a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('debts.payments.index', $debt) }}">Pagamentos</a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('debts.destroy', $debt) }}" method="POST"
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
            <nav class="">
                <ul class="pagination pagination-sm justify-content-end">
                    <li class="page-item {{ $debts->previousPageUrl() ?: 'disabled' }}"><a
                            href="{{ $debts->previousPageUrl() }}" class="page-link">Anterior</a></li>
                    <li class="page-item {{ $debts->nextPageUrl() ?: 'disabled' }}"><a
                            href="{{ $debts->nextPageUrl() }}" class="page-link">Próximo</a></li>
                </ul>
            </nav>
        </div>
    </div>
@endsection
