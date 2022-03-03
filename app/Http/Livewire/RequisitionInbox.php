<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Support\Collection;

class RequisitionInbox extends Component
{
    use WithPagination; 

    //Search
    public $doctype_dd, $doctype, $status; //8/2/2022 > Hide by CR No.4 $status_dd

    //In grid
    public $sortDirection = "desc";
    public $sortBy = "a.create_on";
    public $numberOfPage = 10;

    public function approvePR($prno)
    {
        return redirect("purchaserequisitiondetails?mode=edit&prno=" . $prno . "&tab=auth");
    }

    public function resetSearch()
    {
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
                JOIN document_file_type b ON a.ref_doc_type = b.doc_type_no
                WHERE a.approver='" . auth()->user()->username . "'
                GROUP BY b.doc_type_no, b.description";
        $this->doctype_dd = DB::select($strsql);

        // 8/2/2022 > Hide by CR No.4
        // $strsql = "SELECT b.status_no, b.description 
        //         FROM dec_val_workflow a
        //         JOIN dec_val_status b ON a.status = b.status_no
        //         WHERE a.approver='" . auth()->user()->username . "'
        //         GROUP BY b.status_no, b.description
        //         ORDER BY b.status_no";
        // $this->status_dd = DB::select($strsql);
    }

    public function render()
    {
        $this->loadDropdownList();

        $xWhere = " WHERE d.status IN ('20', '21') AND a.status='20' AND a.approver='" . auth()->user()->username . "'";

        if ($this->doctype) {
            $xWhere = $xWhere . " AND a.ref_doc_type = '" . $this->doctype . "'";
        }

        // 8/2/2022 > Not use CR No.4
        // if ($this->status) {
        //     $xWhere = $xWhere . " AND a.status = '" . $this->status . "'";
        // }

        $strsql = "SELECT b.description AS doctype, a.ref_doc_no, e.name + ' ' + e.lastname AS requestor, f.name + ' ' + f.lastname AS requested_for 
            , d.company, c.description AS status, a.approval_type, a.create_on 
            FROM dec_val_workflow a 
            LEFT JOIN document_file_type b ON a.ref_doc_type = b.doc_type_no 
            LEFT JOIN dec_val_status c ON a.status = c.status_no 
            LEFT JOIN pr_header d ON a.ref_doc_id = d.id
            LEFT JOIN users e ON d.requestor = e.id 
            LEFT JOIN users f ON d.requested_for = f.id "
            . $xWhere . "
            ORDER BY " . $this->sortBy . " " . $this->sortDirection;

            $workflow_list = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        return view('livewire.requisition-inbox',[
            'workflow_list' => $workflow_list,
        ]);
    }
}
