@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('debtors.create') }}" class="btn btn-primary">Criar nova</a>
    </div>

    {{-- <h3>{{ date('m/Y') }}</h3> --}}

    @include('includes.alerts')
    <div class="table-responsive">
        <table class="table table-hover" style="min-width: 700px">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Título</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($debtors as $debtor)
                    <tr>
                        <td>{{ $debtor->id }}</td>
                        <td>{{ $debtor->title }}</td>
                        <td>R$ {{ $debtor->amount }}</td>
                        <td><span class="badge bg-success">Ativo</span></td>
                        <td>{{ $debtor->created_at_formated }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="#" class="dropdown-item">
                                            <i class="bi bi-pencil-square"></i>
                                            Editar
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('debtors.payments.index', $debtor) }}" class="dropdown-item">
                                            <i class="bi bi-journal-text"></i>
                                            Pagamentos
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="dropdown-item">
                                            <i class="bi bi-trash"></i>
                                            Remover
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
