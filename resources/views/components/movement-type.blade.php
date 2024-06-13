@php
    use App\Enums\MovementTypeEnum;
@endphp

@props(['movement'])

@if ($movement)
    <div>
        @if ($movement->type === MovementTypeEnum::IN->value)
            <span class="badge text-bg-success">
                <span class="material-symbols-outlined">
                    sync_alt
                </span>
            </span>
            Entrada
        @else
            <span class="badge text-bg-danger">
                <span class="material-symbols-outlined">
                    sync_alt
                </span>
            </span>
            Saída
        @endif
    </div>
@endif
