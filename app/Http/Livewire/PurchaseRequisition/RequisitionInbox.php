<?php

namespace App\Http\Livewire\PurchaseRequisition;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Support\Collection;

class RequisitionInbox extends Component
{
    use WithPagination; 

    //Search
    public $doctype_dd, $status_dd, $doctype, $status;

    //Grid
    //for Grid
    public $sortDirection = "desc";
    public $sortBy = "lastupdate";
    public $numberOfPage = 10;

    public function resetSearch()
    {
        first_function();
        $this->reset(['doctype', 'status']);
    }

    public function search()
    {
        //ไม่ต้องใส่คำสั่งอะไร
    }
    
    public function sortBy($sortby)
    {
        $this->sortBy = $sortby;
        if ($this->sortDirection == "asc"){
            $this->sortDirection = "desc";
        }else{
            $this->sortDirection = "asc";
        }
    }

    public function loadDropdownList(){
        $strsql = "SELECT b.doc_type_no, b.description 
                FROM dec_val_workflow a
                JOIN document_type b ON a.ref_doc_type = b.doc_type_no
                WHERE a.approver='" . auth()->user()->username . "'
                GROUP BY b.doc_type_no, b.description";
        $this->doctype_dd = DB::select($strsql);

        $strsql = "SELECT b.status_no, b.description 
                FROM dec_val_workflow a
                JOIN dec_val_status b ON a.status = b.status_no
                WHERE a.approver='" . auth()->user()->username . "'
                GROUP BY b.status_no, b.description
                ORDER BY b.status_no";
        $this->status_dd = DB::select($strsql);
    }

    public function render()
    {
        $this->loadDropdownList();

        $xWhere = " WHERE a.approver='" . auth()->user()->username . "'";

        if ($this->doctype) {
            $xWhere = $xWhere . " AND a.ref_doc_type = '" . $this->doctype . "'";
        }

        if ($this->status) {
            $xWhere = $xWhere . " AND a.status = '" . $this->status . "'";
        }

        $strsql = "SELECT b.description AS doctype, a.ref_doc_no, c.description AS status, a.approval_type
                ,CASE
                    WHEN a.changed_on IS NULL  THEN a.create_on
                    ELSE a.changed_on
                END AS lastupdate
                FROM dec_val_workflow a
                JOIN document_type b ON a.ref_doc_type = b.doc_type_no
                JOIN dec_val_status c ON a.status = c.status_no " 
                . $xWhere . "
                ORDER BY " . $this->sortBy . " " . $this->sortDirection;

                $workflow_list = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        return view('livewire.purchase-requisition.requisition-inbox',[
            'workflow_list' => $workflow_list,
        ]);
    }
}
