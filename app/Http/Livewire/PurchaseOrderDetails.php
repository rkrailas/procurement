<?php

namespace App\Http\Livewire;

use Livewire\Component;

class PurchaseOrderDetails extends Component
{
    public $poHeader, $isBlanket, $currentTab;

    public function render()
    {
        $this->poHeader['pono'] = "PO123456789";
        $this->poHeader['ordertypename'] = "XXXXXXX";
        $this->poHeader['statusname'] = "XXXXXXX";
        $this->isBlanket = true;
        $this->currentTab = "item";

        return view('livewire.purchase-order-details');
    }
}
