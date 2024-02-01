@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('identifiers.create') }}" class="btn btn-primary">Criar nova</a>
    </div>

    {{-- <h3>{{ date('m/Y') }}</h3> --}}

    @include('includes.alerts')
    <div class="table-responsive">
        <table class="table table-hover" style="min-width: 700px">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>telefone</th>
                    <th>Status</th>
                    <th>Data</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($identifiers as $identifier)
                    <tr>
                        <td>{{ $identifier->id }}</td>
                        <td>{{ $identifier->name }}</td>
                        <td>{{ $identifier->phone }}</td>
                        <td><span class="badge bg-success">Ativo</span></td>
                        <td>{{ $identifier->created_at_formated }}</td>
                        <td>
                            <a class="btn btn-outline-primary btn-sm"
                                href="{{ route('identifiers.edit', [
                                    'identifier' => $identifier->id,
                                ]) }}"><i
                                    class="fas fa-edit"></i></a>
                            <form
                                action="{{ route('identifiers.destroy', [
                                    'identifier' => $identifier->id,
                                ]) }}"
                                method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Deseja remover a entrada #{{ $identifier->id }}')">
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
