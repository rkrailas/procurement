<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;
    protected $table = 'po_item';

    public function getPoItem($id)
    {
        return PurchaseOrderItem::where('id', '=', $id)->get();
    }
}
