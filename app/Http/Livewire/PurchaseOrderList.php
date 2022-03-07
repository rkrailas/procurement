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

    //Modal
    public $selectedOrderType, $ordertype_dd;

    public function createPO()
    {
        if ($this->selectedOrderType == "30") {
            return redirect("purchaseorderdetails?mode=create&ordertype=" . $this->selectedOrderType);
        }
    }

    public function popupSelectOrderType()
    {
        $this->reset(['selectedOrderType']);
        $this->dispatchBrowserEvent('show-orderTypeModal'); 
    }

    public function loadDropdownList()
    {
        $strsql = "SELECT ordertype, description FROM order_type 
                    WHERE po = 1 AND ordertype='30'";
        $this->ordertype_dd = DB::select($strsql);
    }

    public function render()
    {
        $this->loadDropdownList();

        return view('livewire.purchase-order-list');
    }
}
