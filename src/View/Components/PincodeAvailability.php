<?php

namespace CodeRomeos\BagistoDelhivery\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PincodeAvailability extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('bagistodelhivery::shop.components.pincode-availability');
    }
}
