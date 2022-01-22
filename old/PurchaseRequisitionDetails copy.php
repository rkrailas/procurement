<?php

namespace App\Http\Livewire\PurchaseRequisition;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PurchaseRequisitionDetails extends Component
{
    public $prHeader, $prDetail = [];
    public $requested_for_dd, $delivery_address_dd, $buyer_dd, $cost_center_dd;
    public $isCreateMode, $editPRNo, $workAtCompany;

    public function showAddItem()
    {
        $this->dispatchBrowserEvent('show-modelPartLineItem');
        //$this->dispatchBrowserEvent('show-modelExpenseLineItem');        
    }

    public function setDefaultSelect2()
    {
        //requestedfor-select2
        $xRequested_for_dd = json_decode(json_encode($this->requested_for_dd), true);
        $newOption = "<option value=' '>--- Please Select ---</option>";
        foreach ($xRequested_for_dd as $row) {
            $newOption = $newOption . "<option value='" . $row['id'] . "' ";
            if ($row['id'] == $this->prHeader['requested_for']) {
                $newOption = $newOption . "selected='selected'";
            }
            $newOption = $newOption . ">" . $row['fullname'] . "</option>";
        }
        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#requestedfor-select2']);

        //buyer-select2
        $xBuyer_dd = json_decode(json_encode($this->buyer_dd), true);
        $newOption = "<option value=' '>--- Please Select ---</option>";
        foreach ($xBuyer_dd as $row) {
            $newOption = $newOption . "<option value='" . $row['id'] . "' ";
            if ($row['id'] == $this->prHeader['buyer']) {
                $newOption = $newOption . "selected='selected'";
            }
            $newOption = $newOption . ">" . $row['fullname'] . "</option>";
        }
        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#buyer-select2']);
        $this->skipRender();
    }

    public function editPR()
    {
        $strsql = "SELECT prh.prno, ort.description AS ordertypename, isnull(req.name,'') + ' ' + isnull(req.lastname,'') AS requestor_name
                    , prh.requested_for, prh.delivery_address, FORMAT(prh.request_date,'yyy-MM-dd') AS request_date, prh.company, prh.site, prh.functions
                    , prh.department, prh.division, prh.section, reqf.email, reqf.phone , prh.buyer, prh.cost_center, prh.edecision
                    , pr_status.description AS statusname, FORMAT(prh.valid_until,'yyy-MM-dd') AS valid_until, prh.days_to_notify, prh.notify_below_10
                    , prh.notify_below_25, prh.notify_below_35
                    FROM pr_header prh
                    LEFT JOIN order_type ort ON ort.ordertype=prh.ordertype
                    LEFT JOIN users req ON req.id=prh.requestor
                    LEFT JOIN users reqf ON reqf.id=prh.requested_for
                    LEFT JOIN pr_status ON pr_status.status=prh.status
                    WHERE prh.prno ='" . $this->editPRNo . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader = collect($data[0]);
            if ($this->prHeader['notify_below_10'] == 0) {
                $this->prHeader['notify_below_10'] = false;
            }
            if ($this->prHeader['notify_below_25'] == 0) {
                $this->prHeader['notify_below_25'] = false;
            }
            if ($this->prHeader['notify_below_35'] == 0) {
                $this->prHeader['notify_below_35'] = false;
            }
        }
    }

    public function savePR()
    {
        if ($this->isCreateMode) {
            DB::transaction(function () {
                //pr_header
                DB::statement(
                    "INSERT INTO pr_header(prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                , days_to_notify, notify_below_10, notify_below_25, notify_below_35, create_by, create_on)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [
                        $this->prHeader['prno'], $this->prHeader['ordertype'], $this->prHeader['status'], $this->prHeader['requestor'], $this->prHeader['requested_for'], $this->prHeader['buyer'], $this->prHeader['delivery_address'], $this->prHeader['request_date'], $this->prHeader['company'], $this->prHeader['site'], $this->prHeader['functions'], $this->prHeader['department'], $this->prHeader['division'], $this->prHeader['section'], $this->prHeader['cost_center'], $this->prHeader['edecision'], $this->prHeader['valid_until'], $this->prHeader['days_to_notify'], $this->prHeader['notify_below_10'], $this->prHeader['notify_below_25'], $this->prHeader['notify_below_35'], auth()->user()->id, Carbon::now()
                    ]
                );

                $strsql = "select msg_text from message_list where msg_no='100'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
                    ]);
                }
            });
        } else {
            // DB::transaction(function () {
            //     DB::statement("UPDATE sales SET requestor=?, requested_for=?, buyer=?, delivery_address=?
            //     , request_date=?, company=?, site=?, functions=?, department=?, division=?, section=?, cost_center=?, edecision=?, valid_until=?
            //     , days_to_notify=?, notify_below_10=?, notify_below_25=?, notify_below_35=?, changedby=?, changedon=?
            //     where snumber=?" 
            //     , [$this->prHeader['requestor'], $this->prHeader['requested_for']
            //     , $this->prHeader['buyer'], $this->prHeader['delivery_address'], $this->prHeader['request_date'], $this->prHeader['company'], $this->prHeader['site']
            //     , $this->prHeader['functions'], $this->prHeader['department'], $this->prHeader['division'], $this->prHeader['section'], $this->prHeader['cost_center']
            //     , $this->prHeader['edecision'], $this->prHeader['valid_until'], $this->prHeader['days_to_notify'], $this->prHeader['notify_below_10']
            //     , $this->prHeader['notify_below_25'], $this->prHeader['notify_below_35'], auth()->user()->id, Carbon::now()]);

            //     $strsql = "select msg_text from message_list where msg_no='110'";
            //         $data = DB::select($strsql);
            //         if (count($data) > 0) {
            //             $this->dispatchBrowserEvent('popup-success', [
            //                 'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
            //             ]);
            //         }
            // });
        }

        $this->skipRender();
    }

    public function updatedprHeaderRequestedFor()
    {
        $this->getRequestedFor();
    }

    public function getRequestedFor()
    {
        if ($this->prHeader['requested_for'] != " ") {
            $strsql = "SELECT usr.company, usr.department, usr.site, usr.functions, usr.division, usr.section, usr.email, usr.phone 
                        FROM users usr
                        LEFT JOIN company com ON com.company = usr.company
                        LEFT JOIN cost_center cost ON cost.department = usr.department
                        WHERE usr.id='" . $this->prHeader['requested_for'] . "'";
            $data = DB::select($strsql);
            if (count($data)) {
                $this->prHeader['company'] = $data[0]->company;
                $this->prHeader['site'] = $data[0]->site;
                $this->prHeader['functions'] = $data[0]->functions;
                $this->prHeader['department'] = $data[0]->department;
                $this->prHeader['division'] = $data[0]->division;
                $this->prHeader['section'] = $data[0]->section;
                $this->prHeader['email'] = $data[0]->email;
                $this->prHeader['phone'] = $data[0]->phone;
            }
        } else {
            $this->prHeader['company'] = "";
            $this->prHeader['site'] = "";
            $this->prHeader['functions'] = "";
            $this->prHeader['department'] = "";
            $this->prHeader['division'] = "";
            $this->prHeader['section'] = "";
            $this->prHeader['email'] = "";
            $this->prHeader['phone'] = "";
        }

        $this->skipRender();
    }

    public function getNewPrNo()
    {
        $strsql = "SELECT MAX(prno) as max_prno FROM pr_header WHERE company='" . $this->workAtCompany . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $data2 = explode("-", $data[0]->max_prno);
            $newNo = intval($data2[1]) + 1;
            $newNo = $data2[0] . "-" . sprintf("%05d", $newNo);
            $newPRNo = $newNo;
        }
        return $newPRNo;
    }

    public function createPrHeader()
    {
        //สร้างฟิลด์ใน Array ไว้ก่อน
        $this->prHeader = ([
            'prno' => '', 'ordertype' => '', 'status' => '', 'requestor' => '', 'buyer' => '', 'request_date' => date_format(Carbon::now()->addMonth(+1), 'Y-m-d'), 'company' => '', 'site' => '', 'functions' => '', 'department' => '', 'division' => '', 'section' => '', 'cost_center' => '', 'edecision' => '', 'valid_until' => '', 'days_to_notify' => 0, 'notify_below_10' => false, 'notify_below_25' => false, 'notify_below_35' => false
        ]);

        //สร้างเลขที่ PR ใหม่
        //$this->prHeader['prno'] = $this->getNewPrNo();
        $this->prHeader['prno'] = "";

        $this->prHeader['ordertype'] = $_GET['ordertype'];
        $strsql = "SELECT description as ordertypename 
                    FROM order_type WHERE ordertype='" . $this->prHeader['ordertype'] . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['ordertypename'] = $data[0]->ordertypename;
        }

        $this->prHeader['status'] = $_GET['status'];
        $this->prHeader['statusname'] = "";

        $strsql = "SELECT id as requestor, isnull(name,'') + ' ' + isnull(lastname,'') as requestor_name 
                    FROM users WHERE id=" . config('constants.USER_LOGIN');
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['requestor'] = $data[0]->requestor;
            $this->prHeader['requestor_name'] = $data[0]->requestor_name;
            $this->prHeader['requested_for'] = $data[0]->requestor;
        }

        $strsql = "SELECT id as requestor, isnull(name,'') + ' ' + isnull(lastname,'') as requestor_name 
                    FROM users WHERE id=" . config('constants.USER_LOGIN');
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['requestor'] = $data[0]->requestor;
            $this->prHeader['requestor_name'] = $data[0]->requestor_name;
            $this->prHeader['requested_for'] = $data[0]->requestor;
        }

        , 'delivery_address' => ''

        //dd($this->prHeader['requestor']);
        //Bind Requested For
        // $newOption = "";
        // foreach ($this->requested_for_dd as $row) {
        //     $newOption = $newOption . "<option value=" . $row->id;
        //     if ($row->id == $this->prHeader['requestor']) {
        //         $newOption = $newOption . " selected='selected'";
        //     }
        //     $newOption = $newOption . ">" . $row->fullname . "</option>";
        // }

        // $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#requestedfor-select2']);
    }

    public function loadDropdownList()
    {
        //Requested_For & Buyer
        $strsql = "SELECT id, name + ' ' + ISNULL(lastname, '') as fullname, username FROM users 
                WHERE company='" . $this->workAtCompany . "' ORDER BY users.name";
        $this->requested_for_dd = DB::select($strsql);
        $this->buyer_dd = DB::select($strsql);

        //Delivery Address
        $strsql = "SELECT address_id, SUBSTRING(address,1,30) as address FROM site 
                WHERE company = '" . $this->workAtCompany . "' ORDER BY address_id";
        $this->delivery_address_dd = DB::select($strsql);

        //Cost_Center
        $strsql = "SELECT cost_center, description FROM cost_center 
                WHERE company = '" . $this->workAtCompany . "' ORDER BY department";
        $this->cost_center_dd = DB::select($strsql);
    }

    public function mount()
    {
        if ($_GET['mode'] == "create") {
            $this->isCreateMode = true;
            $this->workAtCompany = $_GET['company'];
            // $this->loadDropdownList();
            $this->createPrHeader();
        } else if ($_GET['mode'] == "edit") {
            $this->isCreateMode = false;
            $this->editPRNo = $_GET['prno'];
            //หา Company 
            $strsql = "SELECT company FROM pr_header WHERE prno='" . $this->editPRNo . "'";
            $data = DB::select($strsql);
            if (count($data)) {
                $this->workAtCompany = $data[0]->company;
            }
            //$this->loadDropdownList();
            $this->editPR();
        }
    }

    public function render()
    {
        $this->loadDropdownList();
        return view('livewire.purchase-requisition.purchase-requisition-details');
    }
}
