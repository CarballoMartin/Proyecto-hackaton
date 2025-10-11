<?php

namespace App\Livewire\Productor;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ProductorPanel extends Component
{
    public function render()
    {
        return view('livewire.productor.productor-panel');
    }
}
