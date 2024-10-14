@extends('master.master')

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Meus</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('projects.index') }}">
                            Projetos
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Itens</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->

    @include('includes.alerts')

    <div class="product-count d-flex align-items-center gap-3 gap-lg-4 mb-4 fw-bold flex-wrap font-text1">
        <a href="{{ route('projects.items.index', $project) }}"><span class="me-1">All</span><span
                class="text-secondary">({{ $projectItems->total() }})</span></a>
        {{-- <a href="javascript:;"><span class="me-1">Published</span><span class="text-secondary">(56242)</span></a>
        <a href="javascript:;"><span class="me-1">Drafts</span><span class="text-secondary">(17)</span></a>
        <a href="javascript:;"><span class="me-1">On Discount</span><span class="text-secondary">(88754)</span></a> --}}
    </div>

    <form class="row g-3">
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
                            <label for="radio-type-empty" class="form-check-label dropdown-item">
                                <input type="radio" name="type" id="radio-type-empty" value=""
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
                <a class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#modal-create"><i
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
                                <th>Nome</th>
                                <th>Valor</th>
                                <th>Identificador</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($projectItems as $projectItem)
                                <tr>
                                    <td>
                                        <strong>{{ $projectItem->id }}</strong>
                                    </td>
                                    <td>{{ $projectItem->name }}</td>
                                    <td>
                                        @if ($projectItem->amount)
                                            @amount($projectItem->amount)
                                        @endif
                                    </td>
                                    <td>
                                        <a href="#">
                                            {{ $projectItem->identifier?->name }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($projectItem->complete)
                                            <p class="badge text-bg-success">Completo</p>
                                        @else
                                            <p class="badge text-bg-warning">Incompleto</p>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 justify-content-end">
                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#modal-edit"
                                                data-config="{{ json_encode([
                                                    'name' => $projectItem->name,
                                                    'description' => $projectItem->description,
                                                    'action' => route('projects.update', $projectItem),
                                                ]) }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('projects.destroy', $projectItem) }}" method="POST">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-danger btn-sm" type="submit"
                                                    onclick="return confirm('Tem certeza que deseja deletar?')">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
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
            <x-pagination :paginator="$projectItems" />
        </div>
    </div>
@endsection

@section('modals')
    {{-- modal create --}}
    <div class="modal" id="modal-create">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-end mb-4">
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('projects.items.store', $project) }}" data-js-component="form-ajax"
                        method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Nome</label>
                            <input type="text" name="name" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Valor <small>(opcional)</small></label>
                            <input type="text" name="amount" class="form-control" data-js-mask="money">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Identificador
                                <small>(opcional)</small></label>
                            <select name="identifier_id" class="form-control">
                                <option value=""></option>
                                @foreach ($identifiers as $identifier)
                                    <option value="{{ $identifier->id }}">{{ $identifier->name }}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Descrição</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- modal edit --}}
    <div class="modal" id="modal-edit">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="text-end mb-4">
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="" data-js-component="form-ajax" method="POST">
                        @method('PUT')
                        @csrf
                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Nome</label>
                            <input type="text" name="name" class="form-control">
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Descrição</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary">Enviar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.addEventListener("load", () => {
            const modalEdit = document.querySelector("#modal-edit");

            modalEdit.addEventListener("show.bs.modal", (event) => {
                config = JSON.parse(event.relatedTarget.getAttribute("data-config"));

                modalEdit.querySelector("form").setAttribute("action", config.action);
                modalEdit.querySelector('input[name=name]').value = config.name;
                modalEdit.querySelector('textarea[name=description]').textContent = config.description;
            });
        });
    </script>
@endsection
