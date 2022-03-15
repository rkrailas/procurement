<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;

class PurchaseRequisitionLog extends Model
{
    use HasFactory;
    protected $table = 'pr_history_logs';

    public static function insertLog($data)
    {
      
        try {
            $obj = new PurchaseRequisitionLog();
            $result = $obj->insert($data);
        } catch (Exception $e) {
      
        }
    }
}
