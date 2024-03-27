@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('debtors.payments.create', $debtor) }}" class="btn btn-primary">Criar nova</a>
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
                @foreach ($debtor->entries as $entry)
                    <tr>
                        <td>{{ $entry->id }}</td>
                        <td>R$ {{ $entry->amount }}</td>
                        <td>{{ $debtor->created_at_formated }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('debtors.payments.edit', [
                                            'debt' => $debt,
                                            'leave' => $entry,
                                        ]) }}"
                                            class="dropdown-item">
                                            <i class="bi bi-pencil-square"></i>
                                            Editar
                                        </a>
                                    </li>
                                    <li>
                                        <form
                                            action="{{ route('debtors.payments.destroy', [
                                                'debt' => $debt,
                                                'leave' => $entry,
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
