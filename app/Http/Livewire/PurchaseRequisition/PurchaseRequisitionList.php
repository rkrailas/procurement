<?php

namespace App\Http\Livewire\PurchaseRequisition;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Support\Collection;

class PurchaseRequisitionList extends Component
{
    //for Pagination
    use WithPagination; 
    //protected $paginationTheme = 'bootstrap';

    //for Grid
    public $sortDirection = "desc";
    public $sortBy = "prh.prno";
    public $numberOfPage = 10;
    public $searchTerm = null;

    //for Search
    public $prno, $ordertype, $site, $requestdate_from, $requestdate_to, $requestor, $requested_for, $buyer, $status;

    //for Dropdown
    public $ordertype_dd, $site_dd, $requestor_dd, $requestedfor_dd, $buyer_dd, $status_dd;

    //In Modal
    public $selectedOrderType, $workAtCompany;

    public function edit($prno)
    {
        return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $prno . "&tab=item");
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

    public function popupSelectOrderType()
    {
        $this->reset(['selectedOrderType']);
        $this->dispatchBrowserEvent('show-orderTypeModal'); 
    }

    public function createPR()
    {
        if ($this->selectedOrderType) {
            //return redirect(route('pr_detail'));
            return redirect("purchase-requisition/purchaserequisitiondetails?mode=create&ordertype=" . $this->selectedOrderType);
        } else {
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => "Please select Order Type",
            ]);
            $this->dispatchBrowserEvent('hide-orderTypeModal'); 
            $this->dispatchBrowserEvent('show-orderTypeModal'); 
        }        
    }

    public function searchPR()
    {
    }

    public function updated($item){
        //ป้องกันการ Call Back
        if ($item == "requestor" or $item == "requested_for" or $item == "buyer" or $item == "status" or $item == "ordertype" or $item == "site") {
            $this->skipRender();
        }
    }

    public function resetSearch()
    {
        $this->reset(['prno', 'ordertype', 'site', 'requestor', 'requested_for', 'buyer', 'status']);
        $this->requestdate_from = date_format(Carbon::now(),'Y-m-d');
        $this->requestdate_to = date_format(Carbon::now()->addMonth(+3),'Y-m-d');
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function mount()
    {
        $this->resetSearch();
        //หา Company 
        // $strsql = "SELECT usr.company FROM users usr WHERE id=" . config('constants.USER_LOGIN');
        // $data = DB::select($strsql);
        // if (count($data)) {
        //     $this->workAtCompany = $data[0]->company;
        // }
        $this->workAtCompany = auth()->user()->company;
    }

    public function loadDropdownList()
    {
        $strsql = "SELECT ordertype, description FROM order_type 
                    WHERE pr = 1 ORDER BY ordertype";
        $this->ordertype_dd = DB::select($strsql);

        $strsql = "SELECT site FROM site 
                    WHERE company='" . config("constants.USER_COMPANY") 
                . "' GROUP BY site ORDER BY site";
        $this->site_dd = DB::select($strsql);

        $strsql = "SELECT id, name + ' ' + ISNULL(lastname, '') as fullname, username FROM users 
                    WHERE company='" . config("constants.USER_COMPANY") 
                    . "' ORDER BY users.name";
        $this->requestor_dd = DB::select($strsql);
        $this->requestedfor_dd = DB::select($strsql);
        $this->buyer_dd = DB::select($strsql);

        $strsql = "SELECT status, description FROM pr_status 
                    ORDER BY status";
        $this->status_dd = DB::select($strsql);

        $strsql = "SELECT * FROM company";
        $companys =  DB::select($strsql);
    }

    public function render()
    {
        $this->loadDropdownList();
        //แสดงรายการ PR >> $prno, $ordertype, $site, $requestdate_from, $requestdate_to, $requestor, $requested_for, $buyer, $status;

        //Validation
        if ($this->requestdate_from > $this->requestdate_to) {
            $strsql = "SELECT msg_text FROM message_list WHERE msg_no='112'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-alert', [
                    'title' => $data[0]->msg_text,
                ]);
            }
            $this->skipRender();

        }else if ($this->requestdate_from == "" OR $this->requestdate_to == "")  {
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => "Please ensure From Date is not empty",
            ]);
            $this->skipRender();
        }

        $xWhere = " WHERE prh.company='" . $this->workAtCompany . "' AND ISNULL(prh.deletion_flag, 0) <> 1";

        //ตรวจสอบว่าเป็น Buyer หรือไม่
        $strsql = "SELECT username FROM buyer WHERE username='" . auth()->user()->username . "'";
        $data = DB::select($strsql);
        if (count($data) > 0) {
            
        }else{
            $xWhere = $xWhere . " AND (prh.requestor=" . auth()->user()->id . 
                                    " OR prh.requested_for=" . auth()->user()->id . 
                                    " OR (prh.id IN (SELECT ref_doc_id FROM dec_val_workflow WHERE approver='" . auth()->user()->username . "')
                                            AND prh.status>='20'
                                         )
                                )"; //Decider & Validator can view the PR/MR since the PR Released for Sourcing
        }
        
        $xWhere = $xWhere . " AND prh.prno LIKE '%" . $this->prno . "%' 
            AND prh.request_date BETWEEN '" . $this->requestdate_from . "' AND '" . $this->requestdate_to . "'";

        if ($this->ordertype) {
            $xWhere = $xWhere . "AND prh.ordertype IN (" . myWhereIn($this->ordertype) . ")";
        }
        if ($this->site) {
            $xWhere = $xWhere . "AND prh.site IN (" . myWhereIn($this->site) . ")";
        }
        if ($this->requestor) {
            $xWhere = $xWhere . "AND prh.requestor IN (" . myWhereIn($this->requestor) . ")";
        }
        if ($this->requested_for) {
            $xWhere = $xWhere . "AND prh.requested_for IN (" . myWhereIn($this->requested_for) . ")";
        }
        if ($this->buyer) {
            $xWhere = $xWhere . "AND prh.buyer IN (" . myWhereIn($this->buyer) . ")";
        }
        if ($this->status) {
            $xWhere = $xWhere . "AND prh.status IN (" . myWhereIn($this->status) . ")";
        }

        $strsql = "SELECT prh.prno, ort.description AS order_type, ISNULL(req_f.name,'') + ' ' + ISNULL(req_f.lastname,'') AS requested_for
                , pr_status.description AS status, prh.request_date, ISNULL(buyer.name,'') + ' ' + ISNULL(buyer.lastname,'') AS buyer
                , pri.total_budget, pri.total_final_price, site.name as site
                , ISNULL(req.name,'') + ' ' + ISNULL(req.lastname,'') AS requestor
                FROM pr_header prh
                LEFT JOIN (SELECT prno, SUM(qty * unit_price_local) as total_budget
                            , SUM(final_price_local) as total_final_price 
                            FROM pr_item GROUP BY prno) pri ON pri.prno=prh.prno
                LEFT JOIN order_type ort ON ort.ordertype=prh.ordertype
                LEFT JOIN users req_f ON req_f.id=prh.requested_for
                LEFT JOIN users req ON req.id=prh.requestor
                LEFT JOIN pr_status ON pr_status.status=prh.status
                LEFT JOIN users buyer ON buyer.id=prh.buyer
                LEFT JOIN site ON site.site=prh.site";

        $strsql = $strsql . $xWhere;
        $strsql = $strsql . " GROUP BY prh.prno, ort.description, req_f.name, req_f.lastname, pr_status.description, prh.request_date
                        , buyer.name, buyer.lastname, pri.total_budget, pri.total_final_price, site.name, prh.site, prh.status
                        , req.name, req.lastname";
        $strsql = $strsql . " ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $pr_list = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        $this->resetPage();

        return view('livewire.purchase-requisition.purchase-requisition-list', 
            [
                'pr_list' => $pr_list
            ]);
    }
}
