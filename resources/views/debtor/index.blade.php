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
                        <td >
                            <a class="btn btn-outline-primary btn-sm"
                                href="{{ route('debtors.edit', [
                                    'debtor' => $debtor->id,
                                ]) }}"><i
                                    class="fas fa-edit"></i></a>
                            <form
                                action="{{ route('debtors.destroy', [
                                    'debtor' => $debtor->id,
                                ]) }}"
                                method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Deseja remover a entrada #{{ $debtor->id }}')">
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
