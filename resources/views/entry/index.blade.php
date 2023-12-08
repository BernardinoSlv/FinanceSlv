@extends('master.master')

@section('content')
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
                    <td><span class="badge text-primary">Pagamento</span></td>
                    <td>{{ $entry->created_at }}</td>
                    <td></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
