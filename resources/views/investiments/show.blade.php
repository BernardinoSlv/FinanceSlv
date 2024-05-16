@extends('master.master')

@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Operações</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="{{ route('investiments.index') }}">Investimentos</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Visualizar</li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $investiment->id }}</li>
                </ol>
            </nav>
        </div>

    </div>
    <!--end breadcrumb-->

    <div class="row">
        <div class="col-12 col-lg-8 col-xl-9">
            <div class="card overflow-hidden">
                {{-- <div class="profile-cover bg-dark position-relative mb-4">
                    <div class="user-profile-avatar shadow position-absolute top-50 start-0 translate-middle-x">
                        <img src="assets/images/avatars/06.png" alt="...">
                    </div>
                </div> --}}
                <div class="card-body">
                    <div class="">
                        <h3 class="mb-2">{{ $investiment->title }}</h3>
                        <p class="mb-1">{{ $investiment->created_at }}</p>
                        <p>New York, United States</p>
                        <div class="">
                            <span class="badge rounded-pill bg-success">Ativo</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <h4 class="mb-2">Depósitos</h4>
                        <a href="{{ route('investiments.leaves.index', $investiment) }}"
                            class="link-opacity-50 link-opacity-100-hover">Ver todos</a>
                    </div>
                    <ul class="list-group">
                        @foreach ($investiment->leaves as $leave)
                            <li class="list-group-item">
                                <span class="badge text-bg-info">R$ {{ $leave->amount }}</span>
                                {{ $leave->created_at }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between">
                        <h4 class="mb-2">Saques</h4>
                        <a href="{{ route('investiments.entries.index', $investiment) }}"
                            class="link-opacity-50 link-opacity-100-hover">Ver todos</a>
                    </div>
                    <ul class="list-group">
                        @foreach ($investiment->entries as $entry)
                            <li class="list-group-item">
                                <span class="badge text-bg-info">R$ {{ $entry->amount }}</span>
                                {{ $entry->created_at }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Identificador</h5>
                    <p class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>{{ $investiment->identifier_id }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Connect</h5>
                    <p class=""><i class="bi bi-browser-edge me-2"></i>www.example.com</p>
                    <p class=""><i class="bi bi-facebook me-2"></i>Facebook</p>
                    <p class=""><i class="bi bi-twitter me-2"></i>Twitter</p>
                    <p class="mb-0"><i class="bi bi-linkedin me-2"></i>LinkedIn</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Skills</h5>
                    <div class="mb-3">
                        <p class="mb-1">Web Design</p>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 45%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1">HTML5</p>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 55%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1">PHP7</p>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 65%"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <p class="mb-1">CSS3</p>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 75%"></div>
                        </div>
                    </div>
                    <div class="mb-0">
                        <p class="mb-1">Photoshop</p>
                        <div class="progress" style="height: 5px;">
                            <div class="progress-bar" role="progressbar" style="width: 85%"></div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div><!--end row-->
@endsection
