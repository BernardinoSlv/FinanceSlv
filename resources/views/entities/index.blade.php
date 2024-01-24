@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('entities.create') }}" class="btn btn-primary">Criar nova</a>
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
                @foreach ($entities as $entity)
                    <tr>
                        <td>{{ $entity->id }}</td>
                        <td>{{ $entity->name }}</td>
                        <td>{{ $entity->phone }}</td>
                        <td><span class="badge bg-success">Ativo</span></td>
                        <td>{{ $entity->created_at_formated }}</td>
                        <td>
                            <a class="btn btn-outline-primary btn-sm"
                                href="{{ route('entities.edit', [
                                    'entity' => $entity->id,
                                ]) }}"><i
                                    class="fas fa-edit"></i></a>
                            <form
                                action="{{ route('entities.destroy', [
                                    'entity' => $entity->id,
                                ]) }}"
                                method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Deseja remover a entrada #{{ $entity->id }}')">
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
