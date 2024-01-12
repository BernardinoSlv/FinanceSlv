@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">Criar nova</a>
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
                    <th>Qnt.</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($expenses as $expense)
                    <tr>
                        <td>{{ $expense->id }}</td>
                        <td>{{ $expense->title }}</td>
                        <td>R$ {{ $expense->amount }}</td>
                        <td>
                            @if ($expense->quantity)
                                {{ $expense->quantity }} vezes
                            @else
                                <span class="fw-bold">
                                    indeterminada
                                </span>
                            @endif
                        </td>
                        <td><span class="badge bg-success">Ativo</span></td>
                        <td>{{ $expense->created_at_formated }}</td>
                        <td >
                            <a class="btn btn-outline-primary btn-sm"
                                href="{{ route('expenses.edit', [
                                    'expense' => $expense->id,
                                ]) }}"><i
                                    class="fas fa-edit"></i></a>
                            <form
                                action="{{ route('expenses.destroy', [
                                    'expense' => $expense->id,
                                ]) }}"
                                method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Deseja remover a entrada #{{ $expense->id }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
