@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('entry.create') }}" class="btn btn-primary">Criar nova</a>
    </div>

    <table class="table table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Título</th>
                <th>Valor</th>
                <th>Tipo</th>
                <th>Data</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entries as $entry)
                <tr>
                    <td>{{ $entry->id }}</td>
                    <td>{{ $entry->title }}</td>
                    <td>R$ {{ $entry->amount }}</td>
                    <td><span class="badge text-white bg-primary">Pagamento</span></td>
                    <td>{{ $entry->created_at }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
