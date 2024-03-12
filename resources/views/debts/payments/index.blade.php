@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('debts.create') }}" class="btn btn-primary">Criar nova</a>
    </div>

    {{-- <h3>{{ date('m/Y') }}</h3> --}}

    @include('includes.alerts')
    <div>
        <table class="table table-hover" style="min-width: 700px">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Valor</th>
                    <th>Comprovante</th>
                    <th>Data</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($debt->leaves as $leave)
                    <tr>
                        <td>{{ $leave->id }}</td>
                        <td>R$ {{ $leave->amont }}</td>
                        <td>...</td>
                        <td><span class="badge bg-success">Ativo</span></td>
                        <td>{{ $debt->created_at_formated }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>

                                <ul class="dropdown-menu">
                                    {{-- <li>
                                        <a href="#" class="dropdown-item">
                                            <i class="bi bi-pencil-square"></i>
                                            Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item">
                                            <i class="bi bi-journal-text"></i>
                                            Pagamentos
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item">
                                            <i class="bi bi-trash"></i>
                                            Remover
                                        </a>
                                    </li> --}}
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
