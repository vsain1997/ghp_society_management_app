<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CommanModalComponent extends Component
{
    public $modalId;
    public $modalTitle;
    public $togleId;

    /**
     * Create a new component instance.
     */
    public function __construct($modalId, $modalTitle, $togleId = null)
    {
        $this->modalId = $modalId;
        $this->modalTitle = $modalTitle;
        $this->togleId = $togleId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.comman-modal-component');
    }
}
