@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('debts.create') }}" class="btn btn-primary">Criar nova</a>
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
                @foreach ($debts as $debt)
                    <tr>
                        <td>{{ $debt->id }}</td>
                        <td>{{ $debt->title }}</td>
                        <td>R$ {{ $debt->amount }}</td>
                        <td><span class="badge bg-success">Ativo</span></td>
                        <td>{{ $debt->created_at_formated }}</td>
                        <td >
                            <a class="btn btn-outline-primary btn-sm"
                                href="{{ route('debts.edit', [
                                    'debt' => $debt->id,
                                ]) }}"><i
                                    class="fas fa-edit"></i></a>
                            <form
                                action="{{ route('debts.destroy', [
                                    'debt' => $debt->id,
                                ]) }}"
                                method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Deseja remover a entrada #{{ $debt->id }}')">
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
