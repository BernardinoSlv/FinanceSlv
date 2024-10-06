@extends('master.master')

@section('content')
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 row-cols-xl-4 row-cols-xxl-4">
        <div class="col">
            <div class="card radius-10 border-0 border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1 fw-bold">Balanço</p>
                            <h4 class="mb-0 text-primary">@amount($balance)</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-primary text-white">
                            <i class="bi bi-calculator"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div @class(['col', 'd-none' => !$totalInOpen])>
            <div class="card radius-10 border-0 border-start border-4" style="border-color: orangered !important;">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1 fw-bold">Em aberto</p>
                            <h4 class="mb-0" style="color:orangered;">@amount($totalInOpen)</h4>
                        </div>
                        <div class="ms-auto widget-icon text-white" style="background-color:orangered;">
                            <i class="bi bi-book"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 border-start border-success border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1 fw-bold">Entradas</p>
                            <h4 class="mb-0 text-success">@amount($totalEntry)</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-success text-white">
                            <i class="bi bi-arrow-down-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1 fw-bold">Saída</p>
                            <h4 class="mb-0 text-danger">@amount($totalExit)</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-danger text-white">
                            <i class="bi bi-arrow-down-up"></i>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col">
            <div class="card radius-10 border-0 border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <p class="mb-1 fw-bold">Dívidas</p>
                            <h4 class="mb-0 text-warning">@amount($totalDebts)</h4>
                        </div>
                        <div class="ms-auto widget-icon bg-warning text-white">
                            <i class="bi bi-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end row-->


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <h6 class="mb-0 fw-bold">Entradas/Saídas</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <button type="button" class="btn-option dropdown-toggle dropdown-toggle-nocaret cursor-pointer"
                                data-bs-toggle="dropdown"><i class="bi bi-three-dots fs-4"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Anual</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Mensal</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Semanal</a>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart1" data-config={{ json_encode($dataChart1) }}>
                    </div>
                </div>
            </div>
        </div>
    </div><!--end row-->


    <div class="row">
        <div class="col-12 col-lg-6 col-xl-4 d-flex">
            <div class="card w-100">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <h6 class="mb-0 fw-bold">Mais recebi</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <button type="button" class="btn-option dropdown-toggle dropdown-toggle-nocaret cursor-pointer"
                                data-bs-toggle="dropdown"><i class="bi bi-three-dots fs-4"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Action</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Another action</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="team-list">
                        @foreach ($topIdendifiersEntry as $identifier)
                            @if (!$identifier->movements_sum_amount)
                                @continue
                            @endif
                            <div class="d-flex align-items-center gap-3 ">
                                <div class="">
                                    <img src="assets/images/avatars/01.png" alt="" width="50" height="50"
                                        class="rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $identifier->name }}</h6>
                                    <span
                                        class="badge bg-success bg-success-subtle text-success border border-opacity-25 border-success fw-bold">@amount($identifier->movements_sum_amount)</span>
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-4 d-flex">
            <div class="card w-100">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <h6 class="mb-0 fw-bold">Mais gastei</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <button type="button"
                                class="btn-option dropdown-toggle dropdown-toggle-nocaret cursor-pointer"
                                data-bs-toggle="dropdown"><i class="bi bi-three-dots fs-4"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Action</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Another action</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="team-list">
                        @foreach ($topIdendifiersExit as $identifier)
                            @if (!$identifier->movements_sum_amount)
                                @continue
                            @endif
                            <div class="d-flex align-items-center gap-3 ">
                                <div class="">
                                    <img src="assets/images/avatars/01.png" alt="" width="50" height="50"
                                        class="rounded-circle">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $identifier->name }}</h6>
                                    <span
                                        class="badge bg-danger bg-danger-subtle text-danger border border-opacity-25 border-danger fw-bold">@amount($identifier->movements_sum_amount)</span>
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-12 col-xl-4 d-flex">
            <div class="card w-100">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <h6 class="mb-0 fw-bold">Maiores dívidas</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <button type="button"
                                class="btn-option dropdown-toggle dropdown-toggle-nocaret cursor-pointer"
                                data-bs-toggle="dropdown"><i class="bi bi-three-dots fs-4"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Action</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Another action</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="team-list">
                        @foreach ($topDebts as $debt)
                            <div class="d-flex align-items-center gap-3">
                                <div class="flex-grow-1">
                                    <p class="mb-1 fw-bold text-truncate">{{ $debt->title }}</p>
                                    <p class="mb-1 fw-bold text-truncate text-muted">{{ $debt->identifier?->name }}</p>
                                    <div class="mb-2">
                                        <span class="badge text-bg-danger">@amount($debt->amount)</span>
                                        <span class="badge text-bg-success">@amount($debt->movements_sum_amount)</span>
                                    </div>
                                    <div class="text-end fw-bold">
                                        {{ intval((100 / $debt->amount) * intval($debt->movements_sum_amount)) }}%
                                    </div>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar progress-bar-animated" role="progressbar"
                                            style="width: {{ intval((100 / $debt->amount) * intval($debt->movements_sum_amount)) }}%;"
                                            aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div><!--end row-->


    {{-- <div class="row">
        <div class="col-12 col-lg-12 col-xl-6">
            <div class="card">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <h6 class="mb-0 fw-bold">Monthly Views</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <button type="button"
                                class="btn-option dropdown-toggle dropdown-toggle-nocaret cursor-pointer"
                                data-bs-toggle="dropdown"><i class="bi bi-three-dots fs-4"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Action</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Another action</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart3"></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-12 col-xl-6">
            <div class="card">
                <div class="card-header bg-transparent">
                    <div class="d-flex align-items-center">
                        <div class="">
                            <h6 class="mb-0 fw-bold">Monthly Users</h6>
                        </div>
                        <div class="dropdown ms-auto">
                            <button type="button"
                                class="btn-option dropdown-toggle dropdown-toggle-nocaret cursor-pointer"
                                data-bs-toggle="dropdown"><i class="bi bi-three-dots fs-4"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="javascript:;">Action</a>
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Another action</a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="javascript:;">Something else here</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="chart4"></div>
                </div>
            </div>
        </div>
    </div><!--end row--> --}}


    {{-- <div class="card">
        <div class="card-body">
            <div class="customer-table">
                <div class="table-responsive white-space-nowrap">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input class="form-check-input" type="checkbox">
                                </th>
                                <th>Order Id</th>
                                <th>Price</th>
                                <th>Customer</th>
                                <th>Payment Status</th>
                                <th>Completed Payment</th>
                                <th>Delivery Type</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox">
                                </td>
                                <td>
                                    <a href="javascript:;">#2415</a>
                                </td>
                                <td>$98</td>
                                <td>
                                    <a class="d-flex align-items-center gap-3" href="javascript:;">
                                        <div class="customer-pic">
                                            <img src="assets/images/avatars/01.png" class="rounded-circle" width="40"
                                                height="40" alt="">
                                        </div>
                                        <p class="mb-0 customer-name fw-bold">Andrew Carry</p>
                                    </a>
                                </td>
                                <td><span
                                        class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">Completed<i
                                            class="bi bi-check2 ms-2"></i></span></td>
                                <td><span
                                        class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">Failed<i
                                            class="bi bi-x-lg ms-2"></i></span></td>
                                <td>Cash on delivery</td>
                                <td>Nov 12, 10:45 PM</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox">
                                </td>
                                <td>
                                    <a href="javascript:;">#7845</a>
                                </td>
                                <td>$110</td>
                                <td>
                                    <a class="d-flex align-items-center gap-3" href="javascript:;">
                                        <div class="customer-pic">
                                            <img src="assets/images/avatars/02.png" class="rounded-circle" width="40"
                                                height="40" alt="">
                                        </div>
                                        <p class="mb-0 customer-name fw-bold">Andrew Carry</p>
                                    </a>
                                </td>
                                <td><span
                                        class="lable-table bg-warning-subtle text-warning rounded border border-warning-subtle font-text2 fw-bold">Pending<i
                                            class="bi bi-info-circle ms-2"></i></span></td>
                                <td><span
                                        class="lable-table bg-primary-subtle text-primary rounded border border-primary-subtle font-text2 fw-bold">Completed<i
                                            class="bi bi-check2-all ms-2"></i></span></td>
                                <td>Cash on delivery</td>
                                <td>Nov 12, 10:45 PM</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox">
                                </td>
                                <td>
                                    <a href="javascript:;">#5674</a>
                                </td>
                                <td>$86</td>
                                <td>
                                    <a class="d-flex align-items-center gap-3" href="javascript:;">
                                        <div class="customer-pic">
                                            <img src="assets/images/avatars/03.png" class="rounded-circle" width="40"
                                                height="40" alt="">
                                        </div>
                                        <p class="mb-0 customer-name fw-bold">Andrew Carry</p>
                                    </a>
                                </td>
                                <td><span
                                        class="lable-table bg-primary-subtle text-primary rounded border border-primary-subtle font-text2 fw-bold">Completed<i
                                            class="bi bi-check2-all ms-2"></i></span></td>
                                <td><span
                                        class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">Failed<i
                                            class="bi bi-x-lg ms-2"></i></span></td>
                                <td>Cash on delivery</td>
                                <td>Nov 12, 10:45 PM</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox">
                                </td>
                                <td>
                                    <a href="javascript:;">#6678</a>
                                </td>
                                <td>$78</td>
                                <td>
                                    <a class="d-flex align-items-center gap-3" href="javascript:;">
                                        <div class="customer-pic">
                                            <img src="assets/images/avatars/04.png" class="rounded-circle" width="40"
                                                height="40" alt="">
                                        </div>
                                        <p class="mb-0 customer-name fw-bold">Andrew Carry</p>
                                    </a>
                                </td>
                                <td><span
                                        class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">Paid<i
                                            class="bi bi-check2 ms-2"></i></span></td>
                                <td><span
                                        class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">Failed<i
                                            class="bi bi-x-lg ms-2"></i></span></td>
                                <td>Cash on delivery</td>
                                <td>Nov 12, 10:45 PM</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox">
                                </td>
                                <td>
                                    <a href="javascript:;">#2367</a>
                                </td>
                                <td>$69</td>
                                <td>
                                    <a class="d-flex align-items-center gap-3" href="javascript:;">
                                        <div class="customer-pic">
                                            <img src="assets/images/avatars/05.png" class="rounded-circle" width="40"
                                                height="40" alt="">
                                        </div>
                                        <p class="mb-0 customer-name fw-bold">Andrew Carry</p>
                                    </a>
                                </td>
                                <td><span
                                        class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">Failed<i
                                            class="bi bi-x-lg ms-2"></i></span></td>
                                <td><span
                                        class="lable-table bg-warning-subtle text-warning rounded border border-warning-subtle font-text2 fw-bold">Pending<i
                                            class="bi bi-info-circle ms-2"></i></span></td>
                                <td>Cash on delivery</td>
                                <td>Nov 12, 10:45 PM</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox">
                                </td>
                                <td>
                                    <a href="javascript:;">#9870</a>
                                </td>
                                <td>$49</td>
                                <td>
                                    <a class="d-flex align-items-center gap-3" href="javascript:;">
                                        <div class="customer-pic">
                                            <img src="assets/images/avatars/06.png" class="rounded-circle" width="40"
                                                height="40" alt="">
                                        </div>
                                        <p class="mb-0 customer-name fw-bold">Andrew Carry</p>
                                    </a>
                                </td>
                                <td><span
                                        class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">Failed<i
                                            class="bi bi-x-lg ms-2"></i></span></td>
                                <td><span
                                        class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">Completed<i
                                            class="bi bi-check2 ms-2"></i></span></td>
                                <td>Cash on delivery</td>
                                <td>Nov 12, 10:45 PM</td>
                            </tr>
                            <tr>
                                <td>
                                    <input class="form-check-input" type="checkbox">
                                </td>
                                <td>
                                    <a href="javascript:;">#3456</a>
                                </td>
                                <td>$65</td>
                                <td>
                                    <a class="d-flex align-items-center gap-3" href="javascript:;">
                                        <div class="customer-pic">
                                            <img src="assets/images/avatars/07.png" class="rounded-circle" width="40"
                                                height="40" alt="">
                                        </div>
                                        <p class="mb-0 customer-name fw-bold">Andrew Carry</p>
                                    </a>
                                </td>
                                <td><span
                                        class="lable-table bg-success-subtle text-success rounded border border-success-subtle font-text2 fw-bold">Completed<i
                                            class="bi bi-check2 ms-2"></i></span></td>
                                <td><span
                                        class="lable-table bg-danger-subtle text-danger rounded border border-danger-subtle font-text2 fw-bold">Failed<i
                                            class="bi bi-x-lg ms-2"></i></span></td>
                                <td>Cash on delivery</td>
                                <td>Nov 12, 10:45 PM</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div> --}}
@endsection


@section('js')
    <!-- Dashboar init js-->
    <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>
@endsection
