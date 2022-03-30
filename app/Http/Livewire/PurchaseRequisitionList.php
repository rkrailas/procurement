<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Support\Collection;

class PurchaseRequisitionList extends Component
{
    //for Pagination
    use WithPagination; 
    // protected $paginationTheme = 'bootstrap';

    //for Grid
    public $sortDirection = "desc";
    public $sortBy = "prh.request_date";
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
        return redirect("purchaserequisitiondetails?mode=edit&prno=" . $prno . "&tab=item");
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
            return redirect("purchaserequisitiondetails?mode=create&ordertype=" . $this->selectedOrderType);
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
        // $this->requestdate_from = "01-" . date_format(Carbon::now(),'M-Y');
        // $this->requestdate_to = date('Y-m-t', strtotime(date_format(Carbon::now(),'d-M-Y'))); //หาวันที่สุดท้ายของเดือน
        $this->requestdate_from = date_format(Carbon::now(),'Y-m') . "-01";
        $this->requestdate_to = date('Y-m-t', strtotime(date_format(Carbon::now(),'Y-m-d'))); //หาวันที่สุดท้ายของเดือน
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function mount()
    {
        $this->resetSearch();
    }

    public function loadDropdownList()
    {
        $strsql = "SELECT ordertype, description FROM order_type 
                    WHERE pr = 1 ORDER BY ordertype";
        $this->ordertype_dd = DB::select($strsql);

        $strsql = "SELECT site, site_description FROM site 
                    WHERE company='" . auth()->user()->company . "' AND SUBSTRING(address_id, 7, 2)='EN'";
        $this->site_dd = DB::select($strsql);

        $strsql = "SELECT id, name + ' ' + ISNULL(lastname, '') as fullname, username FROM users 
                    WHERE company='" . auth()->user()->company 
                    . "' ORDER BY users.name";
        $this->requestor_dd = DB::select($strsql);
        $this->requestedfor_dd = DB::select($strsql);

        $strsql = "SELECT a.username, b.name + ' ' + b.lastname AS fullname
                FROM buyer a 
                left join users b ON a.username=b.username";
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

        $xWhere = " WHERE prh.company='" . auth()->user()->company . "' AND ISNULL(prh.deletion_flag, 0) <> 1";

        //ตรวจสอบว่าเป็น Buyer หรือไม่
        $strsql = "SELECT username FROM buyer WHERE username='" . auth()->user()->username . "'";
        $data = DB::select($strsql);
        if (count($data) > 0) {
            
        }else{
            $xWhere = $xWhere . " AND (prh.requestor=" . auth()->user()->id . 
                                    " OR prh.requested_for=" . auth()->user()->id . 
                                    " OR (prh.id IN (SELECT ref_doc_id FROM dec_val_workflow WHERE approver='" . auth()->user()->username . "')
                                            AND prh.status >= '20'
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
                , pr_status.description AS status, prh.request_date, ISNULL(buyername.name,'') + ' ' + ISNULL(buyername.lastname,'') AS buyer
                , pri.total_budget, pri.total_final_price, site.site_description as site
                , ISNULL(req.name,'') + ' ' + ISNULL(req.lastname,'') AS requestor, c.description as item_desc
                FROM pr_header prh
                LEFT JOIN (SELECT prno, SUM(qty * unit_price_local) as total_budget
                            , SUM(final_price_local) as total_final_price 
                            FROM pr_item 
                            WHERE ISNULL(deletion_flag, 0) = 0
                            GROUP BY prno) pri ON pri.prno=prh.prno
                LEFT JOIN order_type ort ON ort.ordertype=prh.ordertype
                LEFT JOIN users req_f ON req_f.id=prh.requested_for
                LEFT JOIN users req ON req.id=prh.requestor
                LEFT JOIN pr_status ON pr_status.status=prh.status
                LEFT JOIN users buyername ON prh.buyer=buyername.username
                LEFT JOIN (SELECT site, site_description FROM site WHERE address_id LIKE '%-EN') site ON site.site=prh.site
                LEFT JOIN (SELECT prno, MIN(id) AS id
                            FROM pr_item
                            WHERE ISNULL(deletion_flag,0)=0
                            GROUP BY prno) b ON b.prno=prh.prno
                LEFT JOIN pr_item c ON c.id=b.id";

        $strsql = $strsql . $xWhere;
        $strsql = $strsql . " GROUP BY prh.prno, ort.description, req_f.name, req_f.lastname, pr_status.description, prh.request_date
                        , buyername.name, buyername.lastname, pri.total_budget, pri.total_final_price, site.site_description, prh.site, prh.status
                        , req.name, req.lastname, c.description";
        $strsql = $strsql . " ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $pr_list = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        $this->resetPage();

        return view('livewire.purchase-requisition-list', 
            [
                'pr_list' => $pr_list
            ]);
    }
}
