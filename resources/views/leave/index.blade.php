@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('leaves.create') }}" class="btn btn-primary">Criar nova</a>
    </div>

    <h3>{{ date('m/Y') }}</h3>

    @include('includes.alerts')
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
                @foreach ($leaves as $leave)
                    <tr>
                        <td>{{ $leave->id }}</td>
                        <td>{{ $leave->title }}</td>
                        <td>R$ {{ $leave->amount }}</td>
                        <td><span class="badge text-white bg-black">Padrão</span></td>
                        <td>{{ $leave->created_at_formated }}</td>
                        <td align="right">
                            <a class="btn btn-outline-primary btn-sm"
                                href="{{ route('leaves.edit', [
                                    'leave' => $leave->id,
                                ]) }}"><i
                                    class="fas fa-edit"></i></a>
                            <form
                                action="{{ route('leaves.destroy', [
                                    'leave' => $leave->id,
                                ]) }}"
                                method="POST" class="d-inline-block">
                                @csrf
                                @method('DELETE')

                                <button class="btn btn-danger btn-sm"
                                    onclick="return confirm('Deseja remover a entrada #{{ $leave->id }}')">
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
