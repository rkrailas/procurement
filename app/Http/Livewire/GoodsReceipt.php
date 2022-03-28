<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\MessageList;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Site;
use Illuminate\Support\Facades\Auth;

class GoodsReceipt extends Component
{

    public $purchase_order;
    public $messageList;
    public $goodsReceipt;
    public $poHeaders = [];
    public $poItems = [];
    public $purchaseOrder, $purchaseOrderItem;

    public function __construct()
    {
        $this->messageList = new MessageList();
        $this->purchaseOrder = new PurchaseOrder();
        $this->purchaseOrderItem = new PurchaseOrderItem();
    }

    public function render()
    {
        return view('livewire.goods-receipt');
    }

    public function previewItem()
    {
        if ($this->purchase_order == "" || $this->purchase_order == null) {
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => $this->messageList->getMessageOnly(100),
            ]);
            return false;
        }


        $purchaseOrder = $this->purchaseOrder->getPoHeader($this->purchase_order);
        if ($purchaseOrder == null) {
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => $this->messageList->getMessageOnly(101),
            ]);
        } else {
            if ($purchaseOrder->status == 40 || $purchaseOrder->status == 41) {
                $this->dispatchBrowserEvent('popup-alert', [
                    'title' => $this->messageList->getMessageOnly(103),
                ]);
            }
        }

        $id = null;
        $this->poHeaders = $this->purchaseOrder->getPoHeader($id);
        $this->poItems = $this->purchaseOrderItem->getPoItem($id);
    }
}
