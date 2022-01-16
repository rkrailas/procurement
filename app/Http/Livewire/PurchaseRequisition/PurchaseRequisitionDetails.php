<?php

namespace App\Http\Livewire\PurchaseRequisition;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PurchaseRequisitionDetails extends Component
{
    public $listeners = ['deleteConfirmed' => 'deleteLineItem'];
    public $prHeader, $prItem, $itemList = []; //$itemList=ใน Grid, $prItem=ใน Modal, 
    public $prDeliveryPlan, $prListDeliveryPlan = [];  //$prDeliveryPlan=ใน Tab, $prListDeliveryPlan=ใน Grid
    public $requested_for_dd, $delivery_address_dd, $buyer_dd, $cost_center_dd;
    public $partno_dd, $currency_dd, $internal_order_dd, $budget_code, $purchaseunit_dd, $purchasegroup_dd, $budgetcode_dd, $prLineNo_dd;
    public $isCreateMode, $editPRNo, $workAtCompany, $isBlanket, $orderType;
    public $deleteID, $deleteType; //เพื่อให้สามารถใช้ Function confirmDeleteLineItem ร่วมกับการลบหลาย ๆ แบบได้ 
    public $currentTab = "";
    public $enableAddPlan = false;

    //Delivery Plan
    public function deleteDeliveryPlan()
    {
        DB::transaction(function() 
        {
            DB::statement("DELETE FROM delivery_plan where id=? " , [$this->deleteID]);
            
            $strsql = "SELECT msg_text FROM message_list WHERE msg_no='104'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-success', [
                    'title' => $data[0]->msg_text,
                ]);
                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=delivery");
            }
        });

        $this->reset(['deleteID', 'deleteType']);
    }

    public function addDeliveryPlan()
    {
        //ตรวจสอบว่า QTY ที่จะ Add เกินกว่าที่เหลือหรือไม่
        if ($this->prDeliveryPlan['totalQtyPlanned'] + $this->prDeliveryPlan['qty'] <= $this->prDeliveryPlan['totalQty']) {
            DB::statement("INSERT INTO delivery_plan (qty, delivery_date, ref_pr_id, ref_prline_id, create_by, create_on)
            VALUES(?,?,?,?,?,?)"
            ,[$this->prDeliveryPlan['qty'], $this->prDeliveryPlan['delivery_date'], $this->prHeader['id'], $this->prDeliveryPlan['ref_prline_id']
            , auth()->user()->id, Carbon::now()
            ]);
    
            $this->reset(['prDeliveryPlan']);
            return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=delivery");
        } else {
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => "QTY to plan is more than the QTY in Purchase Requisition",
            ]);
        }
    }

    public function updatedPrDeliveryPlanRefPrLineId()
    {
        if ($this->prDeliveryPlan['ref_prline_id']) {
            $strsql = "SELECT purchase_unit, qty FROM pr_item WHERE id = " . $this->prDeliveryPlan['ref_prline_id'];
            $data = DB::select($strsql);
            if ($data) {
                $this->prDeliveryPlan['uom'] = $data[0]->purchase_unit;
                $this->prDeliveryPlan['totalQty'] = $data[0]->qty;
            }
    
            //Sum QTY ของ PrLine นั้น ๆ
            $strsql = "SELECT SUM(qty) as sumqty FROM delivery_plan WHERE ref_prline_id = " . $this->prDeliveryPlan['ref_prline_id'];
            $data = DB::select($strsql);
            $this->prDeliveryPlan['totalQtyPlanned'] = 0;
            if ($data) {
                if (is_null($data[0]->sumqty)) {
                    $this->prDeliveryPlan['totalQtyPlanned'] = 0;
                } else {
                    $this->prDeliveryPlan['totalQtyPlanned'] = $data[0]->sumqty;
                }

                //ตรวจสอบว่า Planned ครบจำนวนหรือยัง
                if ($this->prDeliveryPlan['totalQtyPlanned'] < $this->prDeliveryPlan['totalQty'] ) {
                    $this->enableAddPlan = true;
                } else {
                    $this->enableAddPlan = false;
                }
            }
        } else {
            $this->reset(['prDeliveryPlan', 'enableAddPlan']);
        }
    }
    //Delivery Plan End

    //Line Item
    public function confirmDeleteLineItem($deleteID, $deleteType)
    {
        $this->deleteID = $deleteID;

        if ($deleteType == "item") {
            $this->listeners = ['deleteConfirmed' => 'deleteLineItem'];
        } else if ($deleteType == "deliveryPlan") {
            $this->listeners = ['deleteConfirmed' => 'deleteDeliveryPlan'];
        }

        $this->dispatchBrowserEvent('delete-confirmation');
    }

    public function deleteLineItem()
    {
        //???ก่อน Delete ต้องตรวจสอบอะไรบ้าง ?
        ///!!!ต้องลบ Delivery Plan ออกด้วย

        DB::transaction(function() 
        {
            DB::statement("DELETE FROM pr_item where id=? " , [$this->deleteID]);
            
            $strsql = "SELECT msg_text FROM message_list WHERE msg_no='104'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-success', [
                    'title' => $data[0]->msg_text,
                ]);
                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
            }
        });

        $this->reset(['deleteID', 'deleteType']);
    }

    public function addLineItem()
    {
        DB::transaction(function () {
            //หา Line no
            $strsql = "SELECT MAX([lineno]) as max_lineno FROM pr_item WHERE prno='". $this->prHeader['prno'] . "'";
            $data = DB::select($strsql);
            if ($data) {
                $lineno = $data[0]->max_lineno + 1;
            } else {
                $lineno = 0;
            }

            //Assign ค่าให้ Partno, account_group กรณีเป็น Free Text
            if ($this->orderType == "11" or $this->orderType == "21"){
                $this->prItem['partno'] = "";
                $this->prItem['account_group'] = "";
            }

            //??? ยังไม่มี unit_price
            //24 ฟิลด์
            DB::statement("INSERT INTO pr_item (prno, [lineno], partno, description, purchase_unit, unit_price, currency, exchange_rate
            ,purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
            ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on)
            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
            ,[$this->prHeader['prno'], $lineno, $this->prItem['partno'], $this->prItem['description'], $this->prItem['purchase_unit'], $this->prItem['unit_price']
            , $this->prItem['currency'], $this->prItem['exchange_rate'], $this->prItem['purchase_group'], $this->prItem['account_group'], $this->prItem['qty']
            , $this->prItem['req_date'], $this->prItem['internal_order'] ,$this->prItem['budget_code'], $this->prItem['over_1_year_life']
            , $this->prItem['snn_service'], $this->prItem['snn_production'] ,$this->prItem['nominated_supplier'], $this->prItem['remarks']
            , $this->prItem['skip_rfq'], $this->prItem['skip_doa'], $this->prItem['reference_pr'], "10" ,auth()->user()->id, Carbon::now()
            ]);

            $this->reset(['prItem']);

            $strsql = "SELECT msg_text FROM message_list WHERE msg_no='111'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-success', [
                    'title' => str_replace("<PR Line No.>", $lineno, $data[0]->msg_text),
                ]);

                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
            }
        });
    }

    public function updatedPrItemCurrency()
    {
        if ($this->prItem['currency'] != " ") {
            $strsql = "SELECT ratio_from FROM currency_trans_ratios WHERE from_currency='" . $this->prItem['currency'] . "'";
            $data = DB::select($strsql);
            if ($data) {
                $this->prItem['exchange_rate'] = $data[0]->ratio_from;
            }
        }
    }

    public function updatedPrItemPartno() 
    {
        $strsql = "SELECT partno, part_name, purchase_uom, purchase_group, account_group, brand, model, skip_rfq, skip_doa, primary_supplier
                FROM part_master WHERE partno = '" . $this->prItem['partno'] . "'";
        $data = DB::select($strsql);
        
        if ($data) {
            $this->prItem['description'] = $data[0]->part_name;
            $this->prItem['purchase_unit'] = $data[0]->purchase_uom;
            $this->prItem['purchase_group'] = $data[0]->purchase_group;
            $this->prItem['account_group'] = $data[0]->account_group;
            $this->prItem['brand'] = $data[0]->brand;
            $this->prItem['model'] = $data[0]->model;
            $this->prItem['skip_rfq'] = boolval($data[0]->skip_rfq);
            $this->prItem['skip_doa'] = boolval($data[0]->skip_doa);
            // $this->prItem['budget_code'] = ""; //???ยังไม่มีที่มา
            // $this->prItem['snn_service'] = false; 
            // $this->prItem['snn_production'] = false; 
            $this->prItem['nominated_supplier'] = $data[0]->primary_supplier;
            // $this->prItem['reference_pr'] = ""; //???ดึงมาจากไหน
            // $this->prItem['over_1_year_life'] = false;
            // $this->prItem['remarks'] = "";
        }
        
    }
    //Line Item End

    public function backToPRList()
    {
        return redirect("purchase-requisition/purchaserequisitionlist");
    }

    public function showAddItem()
    {
        $this->reset(['prItem']);

        //สร้างฟิลด์ใน Array 
        $this->prItem['budget_code'] = ""; //???ยังไม่มีที่มา
        $this->prItem['snn_service'] = false; 
        $this->prItem['snn_production'] = false; 
        $this->prItem['reference_pr'] = ""; //???ดึงมาจากไหน
        $this->prItem['over_1_year_life'] = false;
        $this->prItem['remarks'] = "";
        $this->prItem['nominated_supplier'] = "";
        $this->prItem['brand'] = "";
        $this->prItem['model'] = "";
        $this->prItem['skip_rfq'] = false;
        $this->prItem['skip_doa'] = false;

        $this->dispatchBrowserEvent('clear-select2');

        if ($this->orderType == "10" or $this->orderType == "20" ) {
            $this->dispatchBrowserEvent('show-modelPartLineItem');
        } else if ($this->orderType == "11" or $this->orderType == "21") {
            $this->dispatchBrowserEvent('show-modelExpenseLineItem');
        }
    }

    public function setDefaultSelect2()
    {
        //requestedfor-select2
        $xRequested_for_dd = json_decode(json_encode($this->requested_for_dd), true);
        // $newOption = "<option value=' '>--- Please Select ---</option>";
        $newOption = "";
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
        $newOption = "";
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
        //PR Header
        $strsql = "SELECT prh.id, prh.prno, ort.description AS ordertypename, isnull(req.name,'') + ' ' + isnull(req.lastname,'') AS requestor_name
                    , prh.requested_for, prh.delivery_address, FORMAT(prh.request_date,'yyy-MM-dd') AS request_date, prh.company, prh.site, prh.functions
                    , prh.department, prh.division, prh.section, reqf.email, reqf.phone , prh.buyer, prh.cost_center, prh.edecision
                    , pr_status.description AS statusname, FORMAT(prh.valid_until,'yyy-MM-dd') AS valid_until, prh.days_to_notify, prh.notify_below_10
                    , prh.notify_below_25, prh.notify_below_35, company.name as company_name, prh.ordertype
                    FROM pr_header prh
                    LEFT JOIN order_type ort ON ort.ordertype=prh.ordertype
                    LEFT JOIN users req ON req.id=prh.requestor
                    LEFT JOIN users reqf ON reqf.id=prh.requested_for
                    LEFT JOIN pr_status ON pr_status.status=prh.status
                    LEFT JOIN company ON company.company=prh.company
                    WHERE prh.prno ='" . $this->editPRNo . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader = collect($data[0]);
            $this->prHeader['notify_below_10'] = boolval($this->prHeader['notify_below_10']);
            $this->prHeader['notify_below_25'] = boolval($this->prHeader['notify_below_25']);
            $this->prHeader['notify_below_35'] = boolval($this->prHeader['notify_below_35']);
            
            $this->orderType = $this->prHeader['ordertype'];

            if ($this->prHeader['ordertype'] == "20" or $this->prHeader['ordertype'] == "21"){
                $this->isBlanket = true;
            }
        }

        //Item List
        $strsql = "SELECT pri.id, pri.[lineno], pri.description, pri.partno, pri.[status] + ' : ' + sts.[description] as [status]
                    , pri.qty, pri.purchase_unit, pri.unit_price, pri.qty * pri.unit_price as budgettotal, pri.req_date, pri.final_price
                    FROM pr_item pri
                    left join pr_status sts on sts.status=pri.[status]
                    WHERE pri.prno='" . $this->prHeader['prno'] . "'
                    ORDER BY pri.[lineno]";
        //$this->itemList = DB::select($strsql);
        $this->itemList = json_decode(json_encode(DB::select($strsql)), true);

        //Delivery Plan
        if ($this->prHeader['ordertype'] == "20" or $this->prHeader['ordertype'] == "21"){
            $strsql = "SELECT del.id, pri.[lineno], pri.description, pri.partno, del.qty, pri.purchase_unit, del.delivery_date
                        FROM delivery_plan del
                        JOIN pr_item pri ON pri.id = del.ref_prline_id
                        WHERE del.ref_pr_id=" . $this->prHeader['id'];
            $this->prListDeliveryPlan = json_decode(json_encode(DB::select($strsql)), true);
        }
    }

    public function savePR()
    {
        if ($this->isCreateMode) {

            $this->prHeader['prno'] = $this->getNewPrNo();
            $this->prHeader['status'] = '10';
            
            DB::transaction(function () {
                //pr_header
                DB::statement(
                    "INSERT INTO pr_header(prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                , days_to_notify, notify_below_10, notify_below_25, notify_below_35, create_by, create_on)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                    [
                        $this->prHeader['prno'], $this->prHeader['ordertype'], $this->prHeader['status'], $this->prHeader['requestor']
                        , $this->prHeader['requested_for'], $this->prHeader['buyer'], $this->prHeader['delivery_address'], $this->prHeader['request_date']
                        , $this->prHeader['company'], $this->prHeader['site'], $this->prHeader['functions'], $this->prHeader['department']
                        , $this->prHeader['division'], $this->prHeader['section'], $this->prHeader['cost_center'], $this->prHeader['edecision']
                        , $this->prHeader['valid_until'], $this->prHeader['days_to_notify'], $this->prHeader['notify_below_10']
                        , $this->prHeader['notify_below_25'], $this->prHeader['notify_below_35'], auth()->user()->id, Carbon::now()
                    ]
                );

                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='100'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
                    ]);
                }
                
                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
            });
        } else {
            DB::transaction(function () {
                DB::statement("UPDATE pr_header SET requested_for=?, delivery_address=?, request_date=?
                , site=?, functions=?, department=?, division=?, section=?, buyer=?, cost_center=?, edecision=?
                , valid_until=?, days_to_notify=?, notify_below_10=?, notify_below_25=?, notify_below_35=?, changed_by=?, changed_on=?
                where prno=?" 
                , [$this->prHeader['requested_for'], $this->prHeader['delivery_address'], $this->prHeader['request_date']
                , $this->prHeader['site'], $this->prHeader['functions'], $this->prHeader['department'], $this->prHeader['division'], $this->prHeader['section']
                , $this->prHeader['buyer'], $this->prHeader['cost_center'], $this->prHeader['edecision'], $this->prHeader['valid_until']
                , $this->prHeader['days_to_notify'], $this->prHeader['notify_below_10'], $this->prHeader['notify_below_25'], $this->prHeader['notify_below_35']
                , auth()->user()->id, Carbon::now(), $this->prHeader['prno']]);

                $strsql = "select msg_text from message_list where msg_no='110'";
                    $data = DB::select($strsql);
                    if (count($data) > 0) {
                        $this->dispatchBrowserEvent('popup-success', [
                            'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
                        ]);
                    }
            });
        }
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
    }

    public function getNewPrNo()
    {
        $newPRNo = "";
        $strsql = "SELECT pr_prefix1, pr_prefix2, pr_runno FROM company WHERE company='" . $this->workAtCompany . "'";
        $data = DB::select($strsql);

        if ($data){
            if ($data[0]->pr_prefix2 < date_format(now(),"y")) {
                DB::statement("UPDATE company SET pr_prefix2=?, pr_runno=? where company=?"
                 , [date_format(now(),"y"), 1, $this->workAtCompany]);

                $newPRNo = $data[0]->pr_prefix1 . date_format(now(),"y") . sprintf("%06d", 1);

            } else if ($data[0]->pr_prefix2 == date_format(now(),"y")) {
                DB::statement("UPDATE company SET pr_runno=? where company=?"
                , [$data[0]->pr_runno + 1, $this->workAtCompany]);

                $newPRNo = $data[0]->pr_prefix1 . $data[0]->pr_prefix2 . sprintf("%06d", $data[0]->pr_runno + 1);
            }
        }

        return $newPRNo;
    }

    public function createPrHeader()
    {
        //Header
        $this->prHeader['prno'] = "";
        $this->prHeader['ordertype'] = $_GET['ordertype'];

        $strsql = "SELECT description as ordertypename FROM order_type WHERE ordertype='" . $this->prHeader['ordertype'] . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['ordertypename'] = $data[0]->ordertypename;
        }

        $this->prHeader['status'] = "";
        $this->prHeader['statusname'] = "";

        //requestor
        $this->prHeader['requestor'] = auth()->user()->id;

        $strsql = "SELECT id as requestor, isnull(name,'') + ' ' + isnull(lastname,'') as requestor_name 
                    FROM users WHERE id=" . auth()->user()->id;
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['requestor_name'] = $data[0]->requestor_name;
        }

        $this->prHeader['requested_for'] = auth()->user()->id;

        $strsql = "SELECT top 1 address_id, SUBSTRING(address,1,30) as address FROM site WHERE company='" . auth()->user()->company . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['delivery_address'] = $data[0]->address_id;
        }

        $this->prHeader['request_date'] = date_format(Carbon::now()->addMonth(+1), 'Y-m-d');

        //company
        $this->prHeader['company'] = auth()->user()->company;

        $strsql = "SELECT name FROM company WHERE company='" . auth()->user()->company . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['company_name'] = $data[0]->name;
        }

        $this->prHeader['site'] = auth()->user()->site;
        $this->prHeader['functions'] =  auth()->user()->functions;
        $this->prHeader['department'] =  auth()->user()->department;

        //division
        $this->prHeader['division'] =  auth()->user()->division;
        $this->prHeader['section'] =  auth()->user()->section;
        $this->prHeader['email'] =  auth()->user()->email;
        $this->prHeader['phone'] =  auth()->user()->phone;

        //buyer
        $this->prHeader['buyer'] =  "";
        $this->prHeader['cost_center'] =  "";
        $this->prHeader['edecision'] =  "";

        //Blanket Request
        $this->prHeader['valid_until'] =  "";
        $this->prHeader['days_to_notify'] =  0;
        $this->prHeader['notify_below_10'] =  false;
        $this->prHeader['notify_below_25'] =  false;
        $this->prHeader['notify_below_35'] =  false;
    }

    public function loadDropdownList()
    {
        //Requested_For & Buyer
        $this->buyer_dd = [];
        $strsql = "SELECT id, name + ' ' + ISNULL(lastname, '') as fullname, username FROM users WHERE company='" . $this->workAtCompany . "' ORDER BY users.name";
        $this->requested_for_dd = DB::select($strsql);
        $this->buyer_dd = DB::select($strsql);

        //Delivery Address
        $this->cost_center_dd = [];
        $strsql = "SELECT address_id, SUBSTRING(address,1,30) as address FROM site WHERE company = '" . $this->workAtCompany . "' ORDER BY address_id";
        $this->delivery_address_dd = DB::select($strsql);

        //Cost_Center
        $strsql = "SELECT cost_center, description FROM cost_center WHERE company = '" . $this->workAtCompany . "' ORDER BY department";
        $this->cost_center_dd = DB::select($strsql);

        //For Modal
            //partno
            $this->partno_dd = [];
            $strsql = "SELECT partno, part_name FROM part_master WHERE site = '" . $this->prHeader['site'] . "' ORDER BY partno";
            $this->partno_dd = DB::select($strsql);

            //currency
            $this->currency_dd = [];
            $strsql = "SELECT currency FROM currency_master";
            $this->currency_dd = DB::select($strsql);

            //internal_order
            $this->internal_order_dd = [];
            $strsql = "SELECT internal_order FROM internal_order WHERE company = '" . $this->prHeader['company'] . "' ORDER BY internal_order";
            $this->internal_order_dd = DB::select($strsql);

            //purchase_unit
            $this->purchaseunit_dd = [];
            $strsql = "SELECT uomno FROM inv_uom ORDER BY uomno";
            $this->purchaseunit_dd = DB::select($strsql);

            //purchase_unit
            $this->purchasegroup_dd= [];
            $strsql = "SELECT groupno, description FROM purchase_group ORDER BY groupno";
            $this->purchasegroup_dd = DB::select($strsql);
            
            //budget_code
            $this->budgetcode_dd = [];
            $strsql = "SELECT account, description FROM gl_master WHERE company = '" . $this->prHeader['company'] . "' ORDER BY account";
            $this->budgetcode_dd = DB::select($strsql);

            //Delivery Plan
            $this->prLineNo_dd = [];
            if ($this->prHeader['ordertype'] == "20" or $this->prHeader['ordertype'] == "21"){
                $strsql = "SELECT id, [lineno], description FROM pr_item WHERE prno = '" . $this->prHeader['prno'] . "' ORDER BY [lineno]";
                $this->prLineNo_dd = DB::select($strsql);
            }
        //For Modal End
    }

    public function mount()
    {
        if ($_GET['mode'] == "create") {
            $this->isCreateMode = true;
            $this->workAtCompany = auth()->user()->company;
            if ($_GET['ordertype'] == '20' or  $_GET['ordertype'] == '21') {
                $this->isBlanket = true;
            }
            $this->orderType = $_GET['ordertype'];
            $this->createPrHeader();
        } else if ($_GET['mode'] == "edit") {
            $this->isCreateMode = false;
            $this->editPRNo = $_GET['prno'];
            $this->currentTab = $_GET['tab'];
            $this->workAtCompany = auth()->user()->company;
            $this->editPR();
            //$this->isBlanket, $this->orderType Assign ค่าใน Function editPR
        }
    }

    public function render()
    {
        $this->loadDropdownList();
        
        return view('livewire.purchase-requisition.purchase-requisition-details');
    }
}
