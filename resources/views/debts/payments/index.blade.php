@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('debts.payments.create', $debt) }}" class="btn btn-primary">Criar nova</a>
    </div>

    {{-- <h3>{{ date('m/Y') }}</h3> --}}

    @include('includes.alerts')
    <div>
        <table class="table table-hover" style="min-width: 700px">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Valor</th>
                    <th>Data</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($debt->leaves as $leave)
                    <tr>
                        <td>{{ $leave->id }}</td>
                        <td>R$ {{ $leave->amount }}</td>
                        <td>{{ $debt->created_at_formated }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('debts.payments.edit', [
                                            'debt' => $debt,
                                            'leave' => $leave,
                                        ]) }}"
                                            class="dropdown-item">
                                            <i class="bi bi-pencil-square"></i>
                                            Editar
                                        </a>
                                    </li>
                                    <li>
                                        <form
                                            action="{{ route('debts.payments.destroy', [
                                                'debt' => $debt,
                                                'leave' => $leave,
                                            ]) }}"
                                            method="POST">
                                            @method('DELETE')
                                            @csrf

                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-trash"></i>
                                                Remover
                                            </button>
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
@endsection
