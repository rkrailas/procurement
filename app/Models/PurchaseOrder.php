<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Site;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $table = 'po_header';

    public function getPoHeader($id)
    {
        return PurchaseOrder::join('site','site.id','=','po_header.company')->where('po_header.id','=',$id)->first();
    }

   
}
