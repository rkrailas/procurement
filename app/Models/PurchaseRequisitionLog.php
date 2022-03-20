<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Illuminate\Support\Facades\DB;

class PurchaseRequisitionLog extends Model
{
    use HasFactory;
    protected $table = 'history_logs';

    public static function insertLog($data)
    {

        try {
            $obj = new PurchaseRequisitionLog();
            $result = $obj->insert($data);
        } catch (Exception $e) {
        }
    }

    public static function showHistroyLog($refdocno)
    {
        $returnData = [];
        try {
            $result = DB::table('history_logs')
                ->select(
                    'history_logs.id',
                    'obj_type',
                    'line_no',
                    'refdocno',
                    'field',
                    'old_value',
                    'new_value',
                    'history_logs.created_by',
                    'history_logs.changed_by',
                    'name',
                    'lastname',
                    'name_th',
                    'lastname_th',
                    'history_logs.changed_on'
                )
                ->join('users', 'history_logs.changed_by', '=', 'users.id')
                ->where([
                    ['refdocno', '=', $refdocno],
                    ['new_value','=',"UPDATE"]
                ])->get();

            $insert = DB::table('history_logs')
                ->select(
                    'history_logs.id',
                    'obj_type',
                    'line_no',
                    'refdocno',
                    'field',
                    'old_value',
                    'new_value',
                    'history_logs.created_by',
                    'history_logs.changed_by',
                    'name',
                    'lastname',
                    'name_th',
                    'lastname_th',
                    'history_logs.changed_on'
                )
                ->join('users', 'history_logs.created_by', '=', 'users.id')
                ->where([
                    ['refdocno', '=', $refdocno],
                    ['new_value','=',"INSERT"]
                ])
                ->Orwhere([
                    ['refdocno', '=', $refdocno],
                    ['new_value','=',"DELETE"]
                ])
                ->get();

                foreach($insert as  $row){
                    $result->push($row);
                }


           
            $returnData = $result->sortBy('id');
         
        } catch (Exception $e) {
            $returnData = [];
        }


        return $returnData;
    }
}
