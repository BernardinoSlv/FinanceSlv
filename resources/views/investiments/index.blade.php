@extends('master.master')

@section('content')
    <div class="text-end mb-3">
        <a href="{{ route('investiments.create') }}" class="btn btn-primary">Criar nova</a>
    </div>

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
                @foreach ($investiments as $investiment)
                    <tr>
                        <td>{{ $investiment->id }}</td>
                        <td>{{ $investiment->title }}</td>
                        <td>R$ {{ $investiment->amount }}</td>
                        <td><span class="badge text-white bg-black">Padrão</span></td>
                        <td>{{ $investiment->created_at_formated }}</td>
                        <td>
                            <div class="dropdown">
                                <button class="dropdown-toggle btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item"
                                            href="{{ route('investiments.edit', [
                                                'investiment' => $investiment->id,
                                            ]) }}">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                    </li>
                                    <li>
                                        <form
                                            action="{{ route('investiments.destroy', [
                                                'investiment' => $investiment->id,
                                            ]) }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')

                                            <button class="dropdown-item"
                                                onclick="return confirm('Deseja remover a entrada #{{ $investiment->id }}')">
                                                <i class="fas fa-trash"></i> Remover
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
