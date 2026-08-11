<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;

class NavbarSearch extends Component
{
    public $search = '';

    public function render()
    {
        $results = [];

        if (mb_strlen($this->search) >= 2){
            $results = Product::where('title', 'like', '%' . $this->search . '%')
            ->take(5)->get();
        }

        return view('livewire.navbar-search', [
            'results' => $results
        ]);
    }
}
