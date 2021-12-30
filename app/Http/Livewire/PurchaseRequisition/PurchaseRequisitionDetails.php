<?php

namespace App\Http\Livewire\PurchaseRequisition;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PurchaseRequisitionDetails extends Component
{
    public $headerData, $detailData = [];
    public $requested_for_dd, $delivery_address_dd, $buyer_dd, $cost_center_dd;

    public function updatedHeaderDataRequestedFor()
    {
        $this->getRequestedFor();
    }

    public function getRequestedFor()
    {
        $strsql = "SELECT usr.company, usr.department, usr.site, usr.functions, usr.division, usr.section, usr.email, usr.phone 
                    FROM users usr
                    LEFT JOIN company com ON com.company = usr.company
                    LEFT JOIN cost_center cost ON cost.department = usr.department
                    WHERE usr.id='" . $this->headerData['requested_for'] . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->headerData['company'] = $data[0]->company;
            $this->headerData['site'] = $data[0]->site;
            $this->headerData['functions'] = $data[0]->functions;
            $this->headerData['department'] = $data[0]->department;
            $this->headerData['division'] = $data[0]->division;
            $this->headerData['section'] = $data[0]->section;
            $this->headerData['email'] = $data[0]->email;
            $this->headerData['phone'] = $data[0]->phone;
        }
    }

    public function getPrHeader()
    {
        $strsql = "SELECT description as ordertype 
                    FROM order_type WHERE ordertype='" . $_GET['ordertype'] . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->headerData['ordertype'] = $data[0]->ordertype;
        }

        $strsql = "SELECT description as status 
                    FROM pr_status WHERE status='" . $_GET['status'] . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->headerData['status'] = $data[0]->status;
        }

        $strsql = "SELECT isnull(name,'') + ' ' + isnull(lastname,'') as requestor 
                    FROM users WHERE username='" . config('constants.USER_LOGIN') . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->headerData['requestor'] = $data[0]->requestor;
        }

        //ดึง Company มาก่อนเพื่อเป็นเงื่อนไขตอน loadDropDown
        $strsql = "SELECT usr.company FROM users usr WHERE username='" . $this->headerData['requestor'] . "'";
            $data = DB::select($strsql);
            if (count($data)) {
                $this->headerData['company'] = $data[0]->company;
            }
        
        $this->headerData['requested_for'] = "";
        $this->headerData['delivery_address'] = "";
        $this->headerData['request_date'] = date_format(Carbon::now()->addMonth(+1),'Y-m-d');
        $this->headerData['buyer'] = "";
        $this->headerData['cost_center'] = "";
        $this->headerData['cost_center_dscription'] = "";
        $this->headerData['edecision'] = "";
    }

    public function loadDropdownList()
    {
        //Requested_For & Buyer
        $strsql = "SELECT id, name + ' ' + ISNULL(lastname, '') as fullname, username FROM users 
                    WHERE company='" . $this->headerData['company'] . "' ORDER BY users.name";
        $this->requested_for_dd = DB::select($strsql);
        $this->buyer_dd = DB::select($strsql);

        //Delivery Address
        $strsql = "SELECT address_id, SUBSTRING(address,1,30) as address FROM site 
                    WHERE company = '" . $this->headerData['company'] . "' ORDER BY address_id";
        $this->delivery_address_dd = DB::select($strsql);

        //Cost_Center
        $strsql = "SELECT department, description FROM cost_center 
                    WHERE company = '" . $this->headerData['company'] . "' ORDER BY department";
        $this->cost_center_dd = DB::select($strsql);
    }

    public function mount()
    {
        //เอามาไว้ที่ Mount เพราะต้องการให้ทำงานครั้งเดียว เนื่องจากค่า GET จะส้งมาครั้งแรกครั้งเดียว
        $this->getPrHeader(); 
    }

    public function render()
    {        
        $this->loadDropdownList();
        return view('livewire.purchase-requisition.purchase-requisition-details');
    }
}
