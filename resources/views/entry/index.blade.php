@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('entry.create') }}" class="btn btn-primary">Criar nova</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover" style="min-width: 700px">
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
                        <td><span class="badge text-white bg-black">Padrão</span></td>
                        <td>{{ $entry->created_at }}</td>
                        <td>
                            <a class="btn btn-outline-primary btn-sm"
                                href="{{ route('entry.edit', [
                                    'entry' => $entry->id,
                                ]) }}"><i class="fas fa-edit"></i></a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
