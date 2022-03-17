<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Support\Collection;
// use DateInterval;
// use DateTime;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderDetails extends Component
{
    use WithFileUploads;
    use WithPagination; 

    public $listeners = ['deleteConfirmed' => '']; //หลังจากกด Confirm Delete จะให้ไปทำ Function ไหน

    public $isCreateMode, $editPONo, $order_type;
    public $deleteID, $deleteType; //เพื่อให้สามารถใช้ Function confirmDelete ร่วมกับการลบหลาย ๆ แบบได้ 
    public $currentTab = "";
    public $enableAddPlan = false;
    public $selectedRows = [];
    public $numberOfPage = 10;
    public $emailAddress, $emailAddressTo, $emailAddressCC;

    //Header
    public $poHeader, $supplier_dd, $currency_dd, $buyer_dd, $delivery_location_dd, $supplier, $supplier_contact_dd, $requested_for_dd, $cost_center_dd
        , $po_prefix;
    
    // $requested_for_dd, $delivery_address_dd, $buyer_dd, $cost_center_dd, $budget_year, $isBuyer, $isRequester_RequestedFor
    //     , $cancelReason; 

    public function editPO()
    {
        //??? ตรงนี้
        dd('here');
    }

    public function getNewPoNo()
    {
        $newPONo = "";

        if ($this->poHeader['po_prefix']) {
            $strsql = "SELECT lastnumber, year FROM buyer_group_prefix WHERE prefix_type='" . $this->poHeader['po_prefix'] . "'";
            $data = DB::select($strsql);
    
            if ($data){
                DB::statement("UPDATE buyer_group_prefix SET lastnumber=?, changed_by=?, changed_on=? WHERE prefix_type=?"
                    , [$data[0]->lastnumber + 1, auth()->user()->id, Carbon::now(), $this->poHeader['po_prefix']]);
                    $newPONo = 'PO' . substr($data[0]->year, 2, 2) . sprintf("%05d", $data[0]->lastnumber + 1);
            }
        }

        return $newPONo;
    }

    public function backToPOList()
    {
        //$this->clearVariablePR();
        return redirect("purchaseorderlist");
    }

    public function showModal_PrefixConfirm()
    {
        //Validaate required field
        Validator::make($this->poHeader, [
            'supplier' => 'required',
            'po_currency' => 'required',
            'supplier_contact' => 'required',
            'buyer' => 'required',
            'requested_for' => 'required',
            'delivery_location' => 'required',
        ])->validate();

        $this->poHeader['po_prefix'] = "";
        $this->dispatchBrowserEvent('show-modelPrefixConfirm');
    }

    public function savePO()
    {
        //Validaate required field
        Validator::make($this->poHeader, [
            'po_prefix' => 'required',
        ])->validate();

        if ($this->isCreateMode) {
            $this->poHeader['pono'] = $this->getNewPoNo();
            
            DB::transaction(function () {
            //pr_header
                DB::statement(
                    "INSERT INTO po_header(pono, order_type, status, supplier, po_currency, supplier_contact, buyer, delivery_location
                    , requester, requestor_phone, requestor_ext, requested_for, requested_for_phone, requested_for_ext, requested_for_email
                    , company, delivery_site, cost_center, valid_until, notify_below10, notify_below25, notify_below35, create_by, create_on)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                        [
                            $this->poHeader['pono'], $this->poHeader['order_type'], '10', $this->poHeader['supplier'], $this->poHeader['po_currency']
                            , $this->poHeader['supplier_contact'], $this->poHeader['buyer']
                            , $this->poHeader['delivery_location'], $this->poHeader['requestor'], $this->poHeader['requestor_phone']
                            , $this->poHeader['requestor_ext'], $this->poHeader['requested_for'], $this->poHeader['requested_for_phone']
                            , $this->poHeader['requested_for_ext'], $this->poHeader['requested_for_email'], $this->poHeader['company'], $this->poHeader['site']
                            , $this->poHeader['cost_center'], $this->poHeader['valid_until'], $this->poHeader['notify_below_10'], $this->poHeader['notify_below_25']
                            , $this->poHeader['notify_below_35'], auth()->user()->id, Carbon::now()
                        ]
                );
            });

            $strsql = "SELECT msg_text FROM message_list WHERE msg_no='107' AND class='PURCHASE ORDER'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-success', [
                    'title' => str_replace("<PO No.>", $this->poHeader['pono'], $data[0]->msg_text),
                ]);
            }

            return redirect("purchasereorederdetails?mode=edit&pono=" . $this->poHeader['pono'] . "&tab=item");

        } else {
            //Edit PR
            // DB::transaction(function () {
            //     DB::statement("UPDATE pr_header SET requested_for=?, delivery_address=?, delivery_location=?, delivery_site=?
            //     , request_date=?, site=?, functions=?, department=?
            //     , requestor_phone=?, requestor_ext=?, requested_for_phone=?, requested_for_ext=?, requested_for_email=?
            //     , division=?, section=?, buyer=?, cost_center=?, valid_until=?, days_to_notify=?, notify_below_10=?, notify_below_25=?
            //     , notify_below_35=?,budget_year=?, purpose_pr=?, status=?, changed_by=?, changed_on=?
            //     where prno=?" 
            //     , [$this->poHeader['requested_for'], $this->poHeader['delivery_address'], $this->poHeader['delivery_location'], $this->poHeader['delivery_site']
            //     , $this->poHeader['request_date'], $this->poHeader['site'], $this->poHeader['functions'], $this->poHeader['department']
            //     , $this->poHeader['phone'], $this->poHeader['extention'], $this->poHeader['phone_reqf'], $this->poHeader['extention_reqf']
            //     , $this->poHeader['email_reqf'], $this->poHeader['division'], $this->poHeader['section']
            //     , $this->poHeader['buyer'], $this->poHeader['cost_center'], $this->poHeader['valid_until']
            //     , $this->poHeader['days_to_notify'], $this->poHeader['notify_below_10'], $this->poHeader['notify_below_25'], $this->poHeader['notify_below_35']
            //     , $this->poHeader['budget_year'], $this->poHeader['purpose_pr'], $this->poHeader['status'], auth()->user()->id, Carbon::now(), $this->poHeader['prno']]);
            // });

            // $strsql = "select msg_text from message_list where msg_no='110' AND class='PURCHASE REQUISITION'";
            // $data = DB::select($strsql);
            // if (count($data) > 0) {
            //     $this->dispatchBrowserEvent('popup-success', [
            //         'title' => str_replace("<PR No.>", $this->poHeader['prno'], $data[0]->msg_text),
            //     ]);
            // }

            // return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->poHeader['prno'] . "&tab=item");
        }
    }

    public function updatedPoHeaderRequestedFor()
    {
        $strsql = "SELECT usr.company, usr.department, usr.site, usr.cost_center, usr.email, usr.extention, com.name AS company_name
                , CASE 
                    WHEN ISNULL(mobile,'') = '' THEN phone
                    ELSE mobile
                END AS phone
                FROM users usr
                LEFT JOIN company com ON com.company = usr.company
                WHERE usr.id='" . $this->poHeader['requested_for'] . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->poHeader['company'] = $data[0]->company;
            $this->poHeader['company_name'] = $data[0]->company_name;
            $this->poHeader['site'] = $data[0]->site;
            $this->poHeader['department'] = $data[0]->department;
            $this->poHeader['requested_for_email'] = $data[0]->email;
            $this->poHeader['requested_for_phone'] = $data[0]->phone;
            $this->poHeader['requested_for_ext'] = $data[0]->extention;
            $this->poHeader['cost_center'] = $data[0]->cost_center;
        }

        $this->setDefaultSelect2();
    }

    public function setDefaultSelect2()
    {
        //requested_for-select2
        $xRequested_for_dd = json_decode(json_encode($this->requested_for_dd), true);
        // $newOption = "<option value=' '>--- Please Select ---</option>";
        $newOption = "";
        foreach ($xRequested_for_dd as $row) {
            $newOption = $newOption . "<option value='" . $row['id'] . "' ";
            if ($row['id'] == $this->poHeader['requested_for']) {
                $newOption = $newOption . "selected='selected'";
            }
            $newOption = $newOption . ">" . $row['fullname'] . "</option>";
        }
        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#requested_for-select2']);

        //costcenter-select2
        $xCost_center_dd = json_decode(json_encode($this->cost_center_dd), true);
        $newOption = "<option value=' '>--- Please Select ---</option>";
        foreach ($xCost_center_dd as $row) {
            $newOption = $newOption . "<option value='" . $row['cost_center'] . "' ";
            if ($row['cost_center'] == $this->poHeader['cost_center']) {
                $newOption = $newOption . "selected='selected'";
            }
            $newOption = $newOption . ">" . $row['cost_center'] . " : " . $row['description'] . "</option>";
        }
        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#costcenter-select2']);

        $this->skipRender();
    }

    public function updatedPoHeaderSupplierContact()
    {
        //get name & email
        $strsql = "SELECT top 1 phone, email FROM supplier_contact WHERE id=" . $this->poHeader['supplier_contact'];
        $data = DB::select($strsql);
        if ($data) {
            $this->poHeader['supplier_contact_email'] = $data[0]->email;
            $this->poHeader['supplier_contact_phone'] = $data[0]->phone;
        }
    }

    public function updatedPoHeaderSupplier()
    {
        //get supplier contact
        $strsql = "SELECT id, name FROM supplier_contact WHERE company='" . auth()->user()->company . "' 
                AND supplier='" . $this->poHeader['supplier'] . "' ORDER BY name";
        $this->supplier_contact_dd = DB::select($strsql);

        if ($this->supplier_contact_dd) {
            //Load & Default supplier_contact_dd
            $newOption = "<option value=' '>--- Please Select ---</option>";
            $xsupplier_contact_dd = json_decode(json_encode($this->supplier_contact_dd), true);
            foreach ($xsupplier_contact_dd as $row) {
                $newOption = $newOption . "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
            $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#supplier_contact-select2']);
        }
    }

    public function createPoHeader()
    {
        //Header
        $this->poHeader['pono'] = "";
        $this->poHeader['statusname'] = "";
        $this->poHeader['order_type'] = $_GET['ordertype'];

        $strsql = "SELECT description as order_typename FROM order_type WHERE ordertype='" . $this->poHeader['order_type'] . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->poHeader['order_typename'] = $data[0]->order_typename;
        }

        $this->poHeader['status'] = "";
        $this->poHeader['statusname'] = "";
        $this->poHeader['create_on'] = date_format(Carbon::now(),'Y-m-d');
        $this->poHeader['issued_by'] = auth()->user()->id;
        $this->poHeader['issued_by_name'] = auth()->user()->name . " " . auth()->user()->lastname;

        //requestor
        $this->poHeader['requestor'] = auth()->user()->id;
        $this->poHeader['requestor_name'] = auth()->user()->name . " " . auth()->user()->lastname;
        $this->poHeader['requestor_phone'] =  auth()->user()->phone;
        $this->poHeader['requestor_ext'] =  auth()->user()->extention;
        $this->poHeader['prno'] =  "";


        //Requested For
        $this->poHeader['requested_for'] = auth()->user()->id;
        $this->poHeader['requested_for_phone'] = auth()->user()->phone;
        $this->poHeader['requested_for_ext'] = auth()->user()->extention;
        $this->poHeader['requested_for_email'] = auth()->user()->email;

        //company
        $this->poHeader['company'] = auth()->user()->company;
        $strsql = "SELECT name FROM company WHERE company='" . auth()->user()->company . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->poHeader['company_name'] = $data[0]->name;
        }
        $this->poHeader['site'] = auth()->user()->site;
        $this->poHeader['cost_center'] = auth()->user()->cost_center;
        $this->poHeader['rfqno'] =  "";

        // //Blanket Request
        $this->poHeader['po_expirein'] = "";
        $this->poHeader['valid_until'] =  "";
        $this->poHeader['notify_below_10'] =  false;
        $this->poHeader['notify_below_25'] =  false;
        $this->poHeader['notify_below_35'] =  false;
    }

    public function loadDropdownList()
    {
        //supplier
        $strsql = "SELECT supplier, name1 + ' ' + name2 AS fullname FROM supplier ORDER BY supplier";
        $this->supplier_dd = DB::select($strsql);

        //buyer
        $strsql = "SELECT a.username, b.name + ' ' + b.lastname AS fullname FROM buyer a 
            LEFT JOIN users b ON a.username=b.username";
        $this->buyer_dd = DB::select($strsql);

        //Delivery Location
        $strsql = "SELECT address_id, delivery_location FROM site WHERE SUBSTRING(address_id, 7, 2)='EN' ORDER BY address_id";
        $this->delivery_location_dd = DB::select($strsql);

        //currency
        $strsql = "SELECT currency FROM currency_master ORDER BY id";
        $this->currency_dd = DB::select($strsql);

        //Requested_For
        $strsql = "SELECT id, name + ' ' + ISNULL(lastname, '') as fullname, username FROM users 
                WHERE company='" . auth()->user()->company . "' ORDER BY users.name";
        $this->requested_for_dd = DB::select($strsql);

        //Cost_Center
        $strsql = "SELECT cost_center, description FROM cost_center WHERE company = '" . auth()->user()->company . "' ORDER BY department";
        $this->cost_center_dd = DB::select($strsql);
        
        //PO Prefix
        $strsql = "SELECT prefix_type FROM buyer_group_prefix ORDER BY prefix_type";
        $this->po_prefix_dd = DB::select($strsql);
}

    public function mount()
    {
        if ($_GET['mode'] == "create") {
            $this->isCreateMode = true;
            $this->order_type = $_GET['ordertype'];
            $this->createPoHeader();

        } else if ($_GET['mode'] == "edit") {
            $this->isCreateMode = false;
            $this->editPONo = $_GET['pono'];
            $this->currentTab = $_GET['tab'];
            $this->editPO();
        }
        $this->maxSize = config('constants.maxAttachmentSize');
    }

    public function render()
    {
        $this->loadDropdownList();

        return view('livewire.purchase-order-details');
    }
}
