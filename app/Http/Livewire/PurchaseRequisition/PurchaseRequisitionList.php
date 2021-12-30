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

    public $selectedOrderType;


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
            return redirect("purchase-requisition/purchaserequisitiondetails?ordertype=" . $this->selectedOrderType . "&status=" . "01");
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
                $isValidate = false;
            }
        }else if ($this->requestdate_from == "" or $this->requestdate_to == "")  {
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => "Please ensure From Date is not empty",
            ]);
            $isValidate = false;
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
        $xWhere = " where prh.prno like '%" . $this->prno . "%' 
                    and prh.ordertype like '%" . $this->ordertype . "%'
                    and prh.site like '%" . $this->site . "%'
                    and prh.request_date between '" . $this->requestdate_from . "' and '" . $this->requestdate_to . "'
                    and prh.status like '%" . $this->status . "%'
                    and prh.requestor like '%" . $xRequestor . "%'
                    and prh.requested_for like '%" . $xRequested_for . "%'
                    and prh.buyer like '%" . $xBuyer. "%'
                    order by " . $this->sortBy . " " . $this->sortDirection;

        $strsql = "select prh.prno, ort.description as order_type
                    , isnull(req.name,'') + ' ' + isnull(req.lastname,'') as requested_for
                    ,prh.site, pr_status.description as status, prh.request_date
                    , isnull(buyer.name,'') + ' ' + isnull(buyer.lastname,'') as buyer
                    from pr_header prh
                    left join order_type ort on ort.ordertype=prh.ordertype
                    left join users req on req.id=prh.requestor
                    left join pr_status on pr_status.status=prh.status
                    left join users buyer on buyer.id=prh.buyer";
        $strsql = $strsql . $xWhere;
        $pr_list = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        return view('livewire.purchase-requisition.purchase-requisition-list', [
            'pr_list' => $pr_list
        ]);
    }
}
