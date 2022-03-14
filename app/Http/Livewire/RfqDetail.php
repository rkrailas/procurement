<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Support\Collection;
use Illuminate\Support\Facades\Validator;

class RfqDetail extends Component
{
    public $editRFQNo, $currentTab, $rfqHeader;
    public $numberOfPage = 10;

    // Header
    public $buyer_dd, $buyergroup_dd, $currency_dd;

    // Tab Item ไม่ได้ใช้งาน $supplierForAssign_dd, $selectedRows, $tabLineItem
    public $editItem;

    // Tab Supplier
    public $tabSupplier, $supplier_dd, $supplierContact_dd;

    // Tab Quotation Details
    public $tabQuotationDetails, $quotationEexpiryTerm_dd, $paymentTerm_dd;


    // public function assignSupplier()
    // {
    //     //Validate
    //     Validator::make($this->tabLineItem, [
    //         'selectSupplierItem' => 'required',
    //     ])->validate();

    //     DB::statement("UPDATE rfq_item SET supplier=?, final_price=0, final_price_lc=0, changed_by=?, changed_on=?
    //         WHERE id IN (?)"
    //     , [$this->tabLineItem['selectSupplier'], auth()->user()->id, Carbon::now(), $this->selectedRows]);

    //     $this->reset(['selectedRows']);

    //     $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='100' AND class='RFQ'";
    //     $data = DB::select($strsql);
    //     if (count($data) > 0) {
    //         $this->dispatchBrowserEvent('popup-success', [
    //             'title' => str_replace("<RFQ No.>", $this->rfqHeader['rfqno'], $data[0]->msg_text),
    //         ]);
    //     }
    // }

    // Tab Quotation Details Start
    public function updatedTabQuotationDetailsCurrency()
    {
        $strsql = "SELECT from_currency, exchange_rate FROM currency_exchange_rate 
                WHERE rate_type='Y' AND valid_from <= FORMAT(GETDATE(),'yyyy-MM-dd')
                AND from_currency='" . $this->tabQuotationDetails['currency'] . "'";
        $data = DB::select($strsql);
        if ($data) {
            $this->tabQuotationDetails['exchange_rate'] = $data[0]->exchange_rate;
        }
    }

    public function updatedTabQuotationDetailsMainContactPerson()
    {
        $strsql = "SELECT phone, email FROM supplier_contact WHERE company='" . $this->rfqHeader['company'] . "' AND supplier='" 
            . $this->tabQuotationDetails['selectSupplier2'] . "'";
        $data = DB::select($strsql);
        if ($data) {
            $this->tabQuotationDetails['telephone_number'] = $data[0]->phone;
            $this->tabQuotationDetails['email'] = $data[0]->email; 
        }         
    }

    public function updatedTabQuotationDetailsSelectSupplier2()
    {
        //Bind ค่า mainContactPersonier-select2
        $strsql = "SELECT id, name, phone, email
            FROM supplier_contact 
            WHERE company='" . $this->rfqHeader['company'] . "' AND supplier='" . $this->tabQuotationDetails['selectSupplier2'] . "'";
        $data = DB::select($strsql);
        $newOption = "<option value=''>--- Please Select ---</option>";
        if ($data) {
            foreach ($data as $row) {
                $newOption = $newOption . "<option value='" . $row->id . "'>" . $row->name . "</option>";
            }
        }

        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#mainContactPerson-select2']);
    }
    // Tab Quotation Details End

    // Tab Suppliers Start
    public function deleteRFQSupplier($xSupplierID)
    {
        dd('here');
        //Validate-มีใน rfq_item หรือไม่
        // $strsql = "SELECT supplier FROM rfq_item WHERE rfqno='" . $this->rfqHeader['rfqno'] . "' AND supplier='" 
        //         . $xSupplierID . "'";
        // $data = DB::select($strsql);
        // if ($data) {
        //     $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='107' AND class='RFQ'";
        //     $data = DB::select($strsql);
        //     if (count($data) > 0) {
        //         $this->dispatchBrowserEvent('popup-alert', ['title' => $data[0]->msg_text]);
        //     }

        //     //ถ้า Validate ไม่ผ่าน
        //     return;
    }

    public function updatedTabSupplierSelectSupplier()
    {
        //Bind ค่า supplierContact-select2
        $strsql = "SELECT id, name, phone, email 
            FROM supplier_contact 
            WHERE company='" . $this->rfqHeader['company'] . "' AND supplier='" . $this->tabSupplier['selectSupplier'] . "'";
        $data = DB::select($strsql);
        $newOption = "<option value=''>--- Please Select ---</option>";
        if ($data) {
            foreach ($data as $row) {
                $newOption = $newOption . "<option value='" . $row->id . "'>" . $row->name . "</option>";
            }
        }

        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#supplierContact-select2']);
    }

    public function addSupplier()
    {
        //Validate-Required ???ยังมี Bug ไม่ตรวจสอบ selectSupplier
        Validator::make($this->tabSupplier, [
            'selectSupplier' => 'required',
            'selectSupplierContact' => 'required',
        ])->validate();

        //Validate-มีอยู่แล้วหรือไม่
        $strsql = "SELECT supplier FROM rfq_supplier_quotation WHERE rfqno='" . $this->rfqHeader['rfqno'] . "' AND supplier='" 
                . $this->tabSupplier['selectSupplier'] . "'";
        $data = DB::select($strsql);
        if ($data) {
            $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='200' AND class='RFQ'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-alert', ['title' => $data[0]->msg_text]);
            }
            return;
        }

        //get supplier_name, currency, payment_term, exchange_rate
        $strsql = "SELECT a.name1+' '+a.name2 AS supplier_name, a.po_currency, a.payment_key, ISNULL(b.exchange_rate, 1) AS exchange_rate
            FROM supplier a
            LEFT JOIN currency_exchange_rate b ON a.po_currency=b.from_currency AND b.rate_type='Y'
            WHERE supplier='" . $this->tabSupplier['selectSupplier'] . "'";
        $data = DB::select($strsql);
        $xCurrency = "";
        $xPayment_key = "";
        $xExchange_rate = "";
        if ($data) {
            $xSupplier_name = $data[0]->supplier_name;
            $xCurrency = $data[0]->po_currency;
            $xPayment_key = $data[0]->payment_key;
            $xExchange_rate = $data[0]->exchange_rate;
        }

        //Get telephone_number, email
        $strsql = "SELECT name, phone, email FROM supplier_contact WHERE id=" . $this->tabSupplier['selectSupplierContact'];
        $data = DB::select($strsql);
        $xContactName = "";
        $xContactPhone = "";
        $xContactEmail = "";
        if ($data) {
            $xContactName = $data[0]->name;
            $xContactPhone = $data[0]->phone;
            $xContactEmail = $data[0]->email;
        }

        DB::transaction(function () use ($xSupplier_name, $xCurrency, $xPayment_key, $xExchange_rate, $xContactName, $xContactPhone, $xContactEmail)  {
            //insert rfq_supplier_quotation
            DB::statement("INSERT INTO rfq_supplier_quotation(rfqno, company, supplier, supplier_name, supplier_currency
            , payment_term, exchange_rate, create_by, create_on)
            VALUES(?,?,?,?,?,?,?,?,?)"
            ,[$this->rfqHeader['rfqno'], $this->rfqHeader['company'], $this->tabSupplier['selectSupplier'], $xSupplier_name, $xCurrency
                , $xPayment_key, $xExchange_rate, auth()->user()->id, Carbon::now()]);

            //Get ID of rfq_supplier_quotation 
            $strsql = "SELECT id FROM rfq_supplier_quotation WHERE rfqno='" . $this->rfqHeader['rfqno'] . "' AND supplier='" . $this->tabSupplier['selectSupplier'] . "'";
            $data = DB::select($strsql);
            $xIdrfq_supplier_quotation = 0;
            if ($data) {
                $xIdrfq_supplier_quotation = $data[0]->id;
            }

            //insert rfq_supplier_contact_person
            DB::statement("INSERT INTO rfq_supplier_contact_person(rfqno, rfq_supplier_quotation, company, supplier
                , supplier_contact_id, contact_person_name, telephone_number, email, create_by, create_on)
            VALUES(?,?,?,?,?,?,?,?,?,?)"
            ,[$this->rfqHeader['rfqno'], $xIdrfq_supplier_quotation, $this->rfqHeader['company'], $this->tabSupplier['selectSupplier']
            , $this->tabSupplier['selectSupplierContact'], $xContactName, $xContactPhone, $xContactEmail, auth()->user()->id, Carbon::now()]);
        });

        $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='201' AND class='RFQ'";
        $data = DB::select($strsql);
        if ($data) {
            $this->dispatchBrowserEvent('popup-success', ['title' => $data[0]->msg_text]);
        }

        $this->reset(['tabSupplier']);
    }

    // Tab Suppliers End

    // Tab Line Items Start

    // Edit in Line เก็บไว้ก่อน
    // public function updatedItemList($value, $key) //No Refresh
    // {
    //     $data = explode("." , $key);

    //     if ($data[1] == "description"){
    //         DB::statement("UPDATE rfq_item SET description=?, changed_by=?, changed_on=?
    //         WHERE id=?" 
    //         , [$value, auth()->user()->id, Carbon::now(), $this->itemList[$data[0]]['id']]);

    //     } else if ($data[1] == "delivery_date") {
    //         DB::statement("UPDATE rfq_item SET delivery_date=?, changed_by=?, changed_on=?
    //         WHERE id=?" 
    //         , [$value, auth()->user()->id, Carbon::now(), $this->itemList[$data[0]]['id']]);
    //     }
    // }

    public function editLineItem($rowID)
    {
        $this->reset(['editItem']);
        $strsql = "SELECT id, partno, description, delivery_date FROM rfq_item WHERE id=" . $rowID;
        $data = DB::select($strsql);
        if ($data) {
            $this->editItem = json_decode(json_encode($data[0]), true);
            $this->dispatchBrowserEvent('show-modelEditLineItem');
        }     
    }

    public function editItem_Save()
    {
        DB::statement("UPDATE rfq_item SET description=?, delivery_date=?, changed_by=?, changed_on=?
            WHERE id=?" 
            , [$this->editItem['description'], $this->editItem['delivery_date'], auth()->user()->id, Carbon::now(), $this->editItem['id']]);

        $this->dispatchBrowserEvent('hide-modelEditLineItem');
    }

    // Tab Line Items End

    public function loadDropdownList()
    {
        $strsql = "SELECT a.buyer, b.name + ' ' + b.lastname AS fullname
            FROM buyer a 
            left join users b ON a.username=b.username";
        $this->buyer_dd = DB::select($strsql);

        $strsql = "SELECT buyer_group FROM buyer_group ORDER BY buyer_group";
        $this->buyergroup_dd = DB::select($strsql);

        $this->currency_dd = [];
        $strsql = "SELECT currency FROM currency_master ORDER BY currency";
        $this->currency_dd = DB::select($strsql);

        $this->supplier_dd = [];
        $strsql = "SELECT supplier, name1 + ' ' + name2 AS supplier_name FROM supplier 
            WHERE company='" . $this->rfqHeader['company'] . "' ORDER BY supplier";
        $this->supplier_dd = DB::select($strsql);

        $this->quotationEexpiryTerm_dd = [];
        $strsql = "SELECT termno, description FROM rfq_quotation_expiration_term ORDER BY termno";
        $this->quotationEexpiryTerm_dd = DB::select($strsql);

        $this->paymentTerm_dd = [];
        $strsql = "SELECT payment_code, description FROM payment_term ORDER BY payment_code";
        $this->paymentTerm_dd = DB::select($strsql);

    }

    public function goto_prdetail()
    {
        return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->rfqHeader['prno'] . "&tab=item");
    }

    public function editRFQ()
    {
        //RFQ Header
        $strsql = "SELECT a.id, a.rfqno, a.company, d.description AS ordertype, a.prno, e.description AS rfqstatus, j.site_code + ' : ' + j.description AS site
            , f.name + ' ' + f.lastname AS requested_for, g.name + ' ' + g.lastname AS requestor, i.name + ' ' + i.lastname AS buyer, a.buyer_group, c.delivery_location 
            , 'THB' AS currency, a.rfq_remark, FORMAT(a.create_on,'yyy-MM-dd') AS create_on, FORMAT(a.changed_on,'yyy-MM-dd') AS changed_on 
            , b.total_base_price_local, b.total_final_price_local
            , b.total_final_price_local - b.total_base_price_local AS cramount_local
            , (b.total_final_price_local - b.total_base_price_local) / b.total_base_price_local * 100 AS crpercent_local
            FROM rfq_header a 
            LEFT JOIN (SELECT rfqno, SUM(total_base_price_local) AS total_base_price_local, SUM(total_final_price_local) AS total_final_price_local 
                        FROM rfq_item GROUP BY rfqno) b ON a.rfqno=b.rfqno
            LEFT JOIN pr_header c ON a.prno_id=c.id 
            LEFT JOIN order_type d ON c.ordertype=d.ordertype 
            LEFT JOIN rfq_status e ON a.status=e.status 
            LEFT JOIN users f ON c.requested_for=f.id 
            LEFT JOIN users g ON c.requestor=g.id 
            LEFT JOIN buyer h ON c.buyer=h.buyer 
            LEFT JOIN users i ON h.username=i.username 
            LEFT JOIN (SELECT site_code, description FROM site_location GROUP BY site_code, description) j ON c.site=j.site_code 
            WHERE a.rfqno='" . $this->editRFQNo . "'";

        $this->rfqHeader = DB::select($strsql);
        if ($this->rfqHeader) {
            //ถ้าไม่เป็น Array จะใช้ Valdation ไม่ได้
            $this->rfqHeader = json_decode(json_encode($this->rfqHeader[0]), true);
            $this->rfqHeader['total_base_price_local'] = number_format($this->rfqHeader['total_base_price_local'], 2);
            $this->rfqHeader['total_final_price_local'] = number_format($this->rfqHeader['total_final_price_local'], 2);
            $this->rfqHeader['cramount_local'] = number_format($this->rfqHeader['cramount_local'], 2);
            $this->rfqHeader['crpercent_local'] = number_format($this->rfqHeader['crpercent_local'], 2);
        }
    }

    public function mount()
    {
        $this->editRFQNo = $_GET['rfqno'];
        $this->currentTab = $_GET['tab'];
        $this->editRFQ();
    }

    public function render()
    {
        $this->loadDropdownList();

        //Tab itemList 
        $strsql = "SELECT a.id, a.line_no, a.partno, a.description, a.qty, a.uom, a.currency, a.base_price, a.total_base_price
                , a.final_price, a.total_final_price, a.cr_amount, a.cr_percent, a.delivery_date, a.edecisionno, a.pono, b.description AS status
                FROM rfq_item a
                LEFT JOIN rfq_status b ON a.status=b.status
                WHERE a.rfqno='" . $this->rfqHeader['rfqno'] . "'";
        //$this->itemList = json_decode(json_encode(DB::select($strsql)), true);
        $itemList = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        //Tab Supplier 
        $strsql = "SELECT a.supplier, c.name1+' '+c.name2 AS supplier_name, c.street_house_number+' '+c.location+' '+c.city+' '+c.postal_code AS address
            , a.supplier_currency, b.contact_person_name, b.telephone_number, b.email
            FROM rfq_supplier_quotation a
            LEFT JOIN rfq_supplier_contact_person b ON a.id=b.rfq_supplier_quotation
            LEFT JOIN supplier c ON a.supplier=c.supplier AND c.company='" . $this->rfqHeader['company'] . "'
            WHERE a.rfqno='" . $this->rfqHeader['rfqno'] . "'";
        $supplierList = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);
        
        //Tab Quotation Details
        $strsql = "SELECT a.partno, a.description, b.description AS status, a.supplier, a.qty, a.uom, a.base_price, a.final_price, a.total_final_price, a.currency
            FROM rfq_item a
            LEFT JOIN rfq_status b ON a.status=b.status
            WHERE a.rfqno='" . $this->rfqHeader['rfqno'] . "'";
        $quotationDetailsList = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        return view('livewire.rfq-detail', 
            [
                'itemList' => $itemList,
                'supplierList' => $supplierList,
                'quotationDetailsList' => $quotationDetailsList,
            ]);
    }
}
