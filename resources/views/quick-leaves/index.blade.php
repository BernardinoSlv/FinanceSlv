@extends('master.master')

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">eCommerce</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item active" aria-current="page">Products</li>
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

    <div class="row g-3">
        <div class="col-auto">
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
        </div>
        <div class="col-auto">
            <div class="d-flex align-items-center gap-2 justify-content-lg-end">
                <button class="btn btn-light px-4"><i class="bi bi-box-arrow-right me-2"></i>Export</button>
                <a class="btn btn-primary px-4" href="{{ route('quick-entries.create') }}"><i
                        class="bi bi-plus-lg me-2"></i>Criar</a>
            </div>
        </div>
    </div><!--end row-->

    <div class="card mt-4">
        <div class="card-body">
            <div class="product-table">
                <div class="table-responsive white-space-nowrap">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Título</th>
                                <th>Valor</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($quickLeaves as $quickLeave)
                                <tr>
                                    <td>{{ $quickLeave->id }}</td>
                                    <td>{{ $quickLeave->title }}</td>
                                    <td>R$ {{ $quickLeave->amount }}</td>
                                    <td>{{ $quickLeave->created_at_formated }}</td>
                                    <td align="right">
                                        <a class="btn btn-outline-primary btn-sm"
                                            href="{{ route('quick-leaves.edit', [
                                                'quickLeave' => $quickLeave->id,
                                            ]) }}"><i
                                                class="fas fa-edit"></i></a>
                                        <form
                                            action="{{ route('quick-leaves.destroy', [
                                                'quickLeave' => $quickLeave->id,
                                            ]) }}"
                                            method="POST" class="d-inline-block">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-danger btn-sm"
                                                onclick="return confirm('Deseja remover a entrada #{{ $quickLeave->id }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
                    <li class="page-item"><a href="" class="page-link">Anterior</a></li>
                    <li class="page-item"><a href="" class="page-link">Próximo</a></li>
                </ul>
            </nav>
        </div>
    </div>
@endsection
