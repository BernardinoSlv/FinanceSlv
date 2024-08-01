@props(['paginator'])

<nav class="">
    <ul class="pagination pagination-sm justify-content-end">
        <li class="page-item {{ $paginator->previousPageUrl() ?: 'disabled' }}"><a
                href="{{ $paginator->previousPageUrl() }}" class="page-link">Anterior</a></li>
        <li class="page-item {{ $paginator->nextPageUrl() ?: 'disabled' }}"><a href="{{ $paginator->nextPageUrl() }}"
                class="page-link">Próximo</a></li>
    </ul>
</nav>
