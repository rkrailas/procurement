<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Support\Collection;

class PurchaseOrderList extends Component
{
    use WithPagination; 

    //for Grid
    public $sortDirection = "desc";
    public $sortBy = "prh.request_date";
    public $numberOfPage = 10;
    public $searchTerm = null;

    public $selectedOrderType;

    public function popupSelectOrderType()
    {
        $this->reset(['selectedOrderType']);
        $this->dispatchBrowserEvent('show-orderTypeModal'); 
    }

    public function render()
    {
        return view('livewire.purchase-order-list');
    }
}
