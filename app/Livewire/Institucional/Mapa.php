<?php

namespace App\Livewire\Institucional;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.institucional')]
class Mapa extends Component
{
    public function render()
    {
        return view('livewire.institucional.mapa');
    }
}
