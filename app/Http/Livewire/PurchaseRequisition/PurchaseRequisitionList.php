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
    protected $paginationTheme = 'bootstrap'; 

    //for Grid
    public $sortDirection = "desc";
    public $sortBy = "prh.request_date";
    public $numberOfPage = 10;
    public $searchTerm = null;

    //for Search
    public $prno, $ordertype, $site, $requestdate_from, $requestdate_to, $requestor, $requested_for, $buyer, $status;

    //for Dropdown
    public $ordertype_dd, $site_dd, $requestor_dd, $requestedfor_dd, $buyer_dd, $status_dd;

    public $selectedOrderType, $workAtCompany;

    public function edit($prno)
    {
        return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $prno);
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
            return redirect("purchase-requisition/purchaserequisitiondetails?company=" . $this->workAtCompany 
                                . "&mode=create&ordertype=" . $this->selectedOrderType . "&status=01");
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
        if ($item == "requestor" or $item == "requested_for" or $item == "buyer") {
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
        $strsql = "SELECT usr.company FROM users usr WHERE id=" . config('constants.USER_LOGIN');
        $data = DB::select($strsql);
        if (count($data)) {
            $this->workAtCompany = $data[0]->company;
        }
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
        //???ยังไม่รู้ว่าเงื่อนไขตอน On Initialization ต้องเอาข้อมูลอะไรมาแสดงบ้าง
        //Validate
        
        $isValidate = true;
        if ($this->requestdate_from > $this->requestdate_to) {
            $strsql = "select msg_text from message_list where msg_no='112'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-alert', [
                    'title' => $data[0]->msg_text,
                ]);
            }
        }else if ($this->requestdate_from == "" or $this->requestdate_to == "")  {
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => "Please ensure From Date is not empty",
            ]);
        }

        //Condition
        $xRequestor = $this->requestor;
        $xRequested_for = $this->requested_for;
        $xBuyer = $this->buyer;
        if ($xRequestor == " "){
            $xRequestor = "";
        }
        if ($xRequested_for == " "){
            $xRequested_for = "";
        }
        if ($xBuyer == " "){
            $xBuyer = "";
        }

        $xWhere = "";
        $xWhere = " WHERE prh.prno LIKE '%" . $this->prno . "%' 
                    AND prh.ordertype LIKE '%" . $this->ordertype . "%'
                    AND prh.site LIKE '%" . $this->site . "%'
                    AND prh.request_date BETWEEN '" . $this->requestdate_from . "' AND '" . $this->requestdate_to . "'
                    AND prh.status LIKE '%" . $this->status . "%'
                    AND prh.requestor LIKE '%" . $xRequestor . "%'
                    AND prh.requested_for LIKE '%" . $xRequested_for . "%'
                    AND prh.buyer LIKE '%" . $xBuyer. "%'
                    ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $strsql = "SELECT prh.prno, ort.description AS order_type
                    , isnull(req.name,'') + ' ' + isnull(req.lastname,'') AS requested_for
                    ,prh.site, pr_status.description AS status, prh.request_date
                    , isnull(buyer.name,'') + ' ' + isnull(buyer.lastname,'') AS buyer
                    FROM pr_header prh
                    LEFT JOIN order_type ort ON ort.ordertype=prh.ordertype
                    LEFT JOIN users req ON req.id=prh.requested_for
                    LEFT JOIN pr_status ON pr_status.status=prh.status
                    LEFT JOIN users buyer ON buyer.id=prh.buyer";
        $strsql = $strsql . $xWhere;
        $pr_list = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        return view('livewire.purchase-requisition.purchase-requisition-list', [
            'pr_list' => $pr_list
        ]);
    }
}
