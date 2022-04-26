<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Support\Collection;

class RfqList extends Component
{
    //for Pagination
    use WithPagination; 

    //for Grid
    public $sortDirection = "desc";
    public $sortBy = "a.create_on";
    public $numberOfPage = 30;
    public $searchTerm = null;

    public $myBuyerGroup;

    //DropDown
    public $buyer_dd, $buyergroup_dd, $requestedfor_dd, $requestor_dd, $site_dd, $status_dd;

    //Search
    public $prno, $buyer, $buyer_group, $createon_from, $createon_to, $rfqno, $requested_for, $requester, $site, $status;

    public function edit($rfqno)
    {
        return redirect("rfqdetail?rfqno=" . $rfqno . "&tab=item");
    }

    public function goto_prlist()
    {
        return redirect("purchaserequisitionlist");
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

    public function searchPR()
    {
    }

    public function loadDropdownList()
    {
        $strsql = "SELECT id, name + ' ' + ISNULL(lastname, '') as fullname, username 
            FROM users ORDER BY users.name";
        $this->requestor_dd = DB::select($strsql);
        $this->requestedfor_dd = DB::select($strsql);

        $strsql = "SELECT a.username, b.name + ' ' + b.lastname AS fullname
            FROM buyer a 
            left join users b ON a.username=b.username";
        $this->buyer_dd = DB::select($strsql);

        $strsql = "SELECT buyer_group AS buyer_group_code FROM buyer_group_mapping ORDER BY buyer_group";
        $this->buyergroup_dd = DB::select($strsql);

        $strsql = "SELECT site FROM site GROUP BY site ORDER BY site";
        $this->site_dd = DB::select($strsql);

        $strsql = "SELECT status, description FROM rfq_status 
                    ORDER BY status";
        $this->status_dd = DB::select($strsql);
    }

    public function resetSearch()
    {
        $this->reset(['prno', 'buyer', 'buyer_group', 'createon_from', 'createon_to', 'rfqno', 'requested_for', 'requester', 'site', 'status']);
        $this->createon_from = date_format(Carbon::now(),'Y-m') . "-01";
        $this->createon_to = date('Y-m-t', strtotime(date_format(Carbon::now(),'Y-m-d'))); //หาวันที่สุดท้ายของเดือน
        $this->dispatchBrowserEvent('clear-select2');
    }

    public function mount()
    {
        $this->myBuyerGroup = "";
        $strsql = "SELECT b.buyer_group AS buyer_group_code
                FROM buyer a
                JOIN buyer_group_mapping b ON a.username=b.buyer
                WHERE a.username='" . auth()->user()->username . "'";
        $data = DB::select($strsql);
        if ($data) {
            $this->myBuyerGroup = $data[0]->buyer_group;
        }

        $this->resetSearch();
    }

    public function render()
    {
        $this->loadDropdownList();

        if ($this->createon_from > $this->createon_to) {
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'Please ensure From Date is earlier than To Date',
                ]);
            $this->skipRender();
        }

        //??? 26-04-2022 กำลังแก้ SQL injection
        //แสดงเฉพาะ User ที่เป็น Buyer และอยู่ใน Buyer Group ใน rfq_header เท่านั้น
        $xWhere = " WHERE a.buyer_group='" . $this->myBuyerGroup . "' AND a.prno LIKE '%" . $this->prno . "%' ";
        $xWhere = $xWhere . " AND a.rfqno LIKE '%" . $this->rfqno . "%' ";
        $xWhere = $xWhere . " AND a.create_on BETWEEN '" . $this->createon_from . "' AND '" . $this->createon_to . "'";
        if ($this->buyer) {
            $xWhere = $xWhere . "AND a.buyer IN (" . myWhereIn($this->buyer) . ")";
        }
        if ($this->buyer_group) {
            $xWhere = $xWhere . "AND a.buyer_group IN (" . myWhereIn($this->buyer_group) . ")";
        }
        if ($this->requested_for) {
            $xWhere = $xWhere . "AND c.requested_for IN (" . myWhereIn($this->requested_for) . ")";
        }
        if ($this->requester) {
            $xWhere = $xWhere . "AND c.requester IN (" . myWhereIn($this->requester) . ")";
        }
        if ($this->site) {
            $xWhere = $xWhere . "AND c.site IN (" . myWhereIn($this->site) . ")";
        }
        if ($this->status) {
            $xWhere = $xWhere . "AND a.status IN (" . myWhereIn($this->status) . ")";
        }

        $strsql = "SELECT a.rfqno, d.description AS ordertype, a.prno, e.description AS rfqstatus, c.site, '' AS partno, '' AS part_desc
            , f.name + ' ' + f.lastname AS requested_for, g.name + ' ' + g.lastname AS requestor
            , i.name + ' ' + i.lastname AS buyer, a.create_on
            FROM rfq_header a
            LEFT JOIN pr_header c ON a.prno_id=c.id
            LEFT JOIN order_type d ON c.ordertype=d.ordertype
            LEFT JOIN rfq_status e ON a.status=e.status
            LEFT JOIN users f ON c.requested_for=f.id
            LEFT JOIN users g ON c.requestor=g.id
            LEFT JOIN users i ON c.buyer=i.username";
        $strsql = $strsql . $xWhere;
        $strsql = $strsql . " ORDER BY " . $this->sortBy . " " . $this->sortDirection;

        $rfq_list = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        return view('livewire.rfq-list', 
        [
            'rfq_list' => $rfq_list
        ]);
    }
}
