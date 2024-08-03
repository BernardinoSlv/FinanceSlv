@extends('master.master')

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">

                    <li class="breadcrumb-item" aria-current="page">
                        <a href="{{ route('debts.index') }}">Dívidas</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Pagamentos</li>
                </ol>
            </nav>
        </div>
    </div>
    <!--end breadcrumb-->
    <a href="{{ route('debts.index') }}" class="btn btn-sm btn-outline-secondary mb-3 d-sm-none">
        <i class="bi bi-caret-left-fill"></i>
    </a>

    @include('includes.alerts')

    <div class="row g-3 justify-content-end mb-4">
        {{-- <div class="col-auto">
            <div class="position-relative">
                <input class="form-control px-5" type="search" placeholder="Search Products">
                <span
                    class="material-symbols-outlined position-absolute ms-3 translate-middle-y start-0 top-50 fs-5">search</span>
            </div>
        </div>
        <div class="col-auto flex-grow-1 overflow-auto">
            <div class="btn-group position-static">
                <div class="btn-group position-static">
                    <button type="button" class="btn border btn-light dropdown-toggle px-4" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Category
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                        <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                </div>
                <div class="btn-group position-static">
                    <button type="button" class="btn border btn-light dropdown-toggle px-4" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Vendor
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                        <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                </div>
                <div class="btn-group position-static">
                    <button type="button" class="btn border btn-light dropdown-toggle px-4" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        Collection
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:;">Action</a></li>
                        <li><a class="dropdown-item" href="javascript:;">Another action</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="javascript:;">Something else here</a></li>
                    </ul>
                </div>
            </div>
        </div> --}}
        <div class="col-auto">
            <div class="d-flex align-items-center gap-2 justify-content-lg-end">
                <button class="btn btn-light px-4"><i class="bi bi-box-arrow-right me-2"></i>Export</button>
                <button type="button" class="btn btn-primary px-4" data-config="" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvas-create">
                    <i class="bi bi-plus-lg me-2"></i>Criar
                </button>
            </div>
        </div>
    </div><!--end row-->

    <div class="row align-item-start">
        <div class="col-md-4">
            <div class="card" style="max-width: 400px">
                <div class="card-body">
                    @php
                        $remainingAmount = floatval($debt->amount) - floatval($debt->movements_sum_amount);
                    @endphp
                    <h3>{{ $debt->identifier->name }}</h3>
                    <h5>{{ $debt->title }}</h5>
                    <p class="mb-1">Total: @amount($debt->amount)</p>
                    <p class="mb-1 text-success">Total pago: @amount($debt->movements_sum_amount) </p>
                    <hr>
                    <p @class([
                        'text-danger',
                        'text-decoration-line-through' => !$remainingAmount,
                    ])>Restante: @amount($remainingAmount)</p>
                </div>

            </div>
        </div>
        <div class="col-md-8">
            <div class="card ">
                <div class="card-body">
                    <div class="product-table">
                        <div class="table-responsive white-space-nowrap">
                            <table class="table align-middle table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Valor</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($movements->items() as $movement)
                                        <tr>
                                            <td>
                                                <strong>{{ $movement->id }}</strong>
                                            </td>
                                            <td>R$ @amount($movement->amount)</td>
                                            <td>
                                                {{ $movement->created_at->format('d/m/Y H:i') }}
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

                                                            <button type="button" class="dropdown-item"
                                                                data-config="{{ json_encode([
                                                                    'url' => route('debts.payments.update', [
                                                                        'debt' => $debt,
                                                                        'movement' => $movement,
                                                                    ]),
                                                                    'amount' => number_format($movement->amount,2, ","),
                                                                ]) }}"
                                                                data-bs-toggle="offcanvas"
                                                                data-bs-target="#offcanvas-edit">Editar</button>
                                                        </li>
                                                        <li>
                                                            <form
                                                                action="{{ route('debts.payments.destroy', [
                                                                    'debt' => $debt,
                                                                    'movement' => $movement,
                                                                ]) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('O registro será deletado permanentemente!')">
                                                                @method('DELETE')
                                                                @csrf

                                                                <button type="submit"
                                                                    class="dropdown-item">Remover</button>
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
        </div>
    </div>

    <div class="offcanvas offcanvas-end" id="offcanvas-create">
        <div class="offcanvas-header">
            <h5>Criar pagamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('debts.payments.store', $debt) }}" method="POST" enctype="multipart/form-data"
                data-js-component="form-ajax">
                @csrf

                <div class="mb-3">
                    <label for="input-amount" class="form-label fw-bold">Valor</label>
                    <input type="text" name="amount" class="form-control" id="input-amount" data-js-mask="money">
                    <div class="invalid-feedback"></div>
                    <div class="form-text">ex: 800,00</div>
                </div>
                <div class="mb-3">
                    <label for="input-files" class="form-label fw-bold">Arquivos</label>
                    <input type="file" class="form-control" name="files[]" multiple id="input-files">
                    <div class="form-text">Comprovantes, assinaturas e etc...</div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" id="offcanvas-edit">
        <div class="offcanvas-header">
            <h5>Editar pagamento</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <form action="" method="POST" enctype="multipart/form-data" data-js-component="form-ajax" >
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="input-amount" class="form-label fw-bold">Valor</label>
                    <input type="text" name="amount" class="form-control" id="input-amount" data-js-mask="money">
                    <div class="invalid-feedback"></div>
                    <div class="form-text">ex: 800,00</div>
                </div>
                <div class="mb-3">
                    <label for="input-files" class="form-label fw-bold">Arquivos</label>
                    <input type="file" class="form-control" name="files[]" multiple id="input-files">
                    <div class="form-text">Comprovantes, assinaturas e etc...</div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.addEventListener("load", () => {
            const offcanvas = document.querySelector("#offcanvas-edit");

            offcanvas.addEventListener("show.bs.offcanvas", (event) => {
                const config = JSON.parse(event.relatedTarget.getAttribute("data-config"));
                const form = offcanvas.querySelector("form");
                const inputAmount = offcanvas.querySelector("input[name=amount]");

                form.setAttribute("action", config.url)
                inputAmount.value = config.amount;
            });
        });
    </script>
@endsection
