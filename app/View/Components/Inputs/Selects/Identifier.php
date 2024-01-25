<?php

namespace App\View\Components\Inputs\Selects;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class Identifier extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Collection $identifiers,
        public ?int $selectedId = null
    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.inputs.selects.identifier');
    }
}
