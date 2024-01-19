@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('needs.create') }}" class="btn btn-primary">Criar nova</a>
    </div>

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
                @foreach ($needs as $need)
                    <tr>
                        <td>{{ $need->id }}</td>
                        <td>{{ $need->title }}</td>
                        <td>R$ {{ $need->amount }}</td>
                        <td>
                            @if ($need->completed)
                                <span class="badge text-white bg-success">Concluído</span>
                            @else
                                <span class="badge text-white bg-black">Pendente</span>
                            @endif
                        </td>
                        <td>{{ $need->created_at_formated }}</td>
                        <td align="right">
                            <a class="btn btn-outline-primary btn-sm"
                                href="{{ route('needs.edit', [
                                    'need' => $need->id,
                                ]) }}"><i
                                    class="fas fa-edit"></i></a>
                            <form
                                action="{{ route('needs.destroy', [
                                    'need' => $need->id,
                                ]) }}"
                                method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Deseja remover a entrada #{{ $need->id }}')">
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
