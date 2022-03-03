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

    //Header
    public $buyer_dd, $buyergroup_dd, $currency_dd;

    //Tab Item
    public $supplierForAssign_dd, $selectedRows, $tabLineItem;
    
    //Tab Supplier
    public $tabSupplier, $supplier_dd;


    public function assignSupplier()
    {
        //Validate
        Validator::make($this->tabLineItem, [
            'selectSupplierItem' => 'required',
        ])->validate();

        DB::statement("UPDATE rfq_item SET supplier=?, final_price=0, final_price_lc=0, changed_by=?, changed_on=?
            WHERE id IN (?)"
        , [$this->tabLineItem['selectSupplier'], auth()->user()->id, Carbon::now(), $this->selectedRows]);

        $this->reset(['selectedRows']);

        $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='100' AND class='RFQ'";
        $data = DB::select($strsql);
        if (count($data) > 0) {
            $this->dispatchBrowserEvent('popup-success', [
                'title' => str_replace("<RFQ No.>", $this->rfqHeader['rfqno'], $data[0]->msg_text),
            ]);
        }
    }

    public function deleteRFQSupplier($xSupplierID)
    {
        //Validate-มีใน rfq_item หรือไม่
        $strsql = "SELECT supplier FROM rfq_item WHERE rfqno='" . $this->rfqHeader['rfqno'] . "' AND supplier='" 
                . $xSupplierID . "'";
        $data = DB::select($strsql);
        if ($data) {
            $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='107' AND class='RFQ'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-alert', ['title' => $data[0]->msg_text]);
            }

            //ถ้า Validate ไม่ผ่าน
            return;
        }

        //ถ้า Validate ผ่าน
        DB::statement("DELETE FROM rfq_supplier WHERE rfqno=? AND supplier=?", [$this->rfqHeader['rfqno'], $xSupplierID]);

        $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='108' AND class='RFQ'";
        $data = DB::select($strsql);
        if (count($data) > 0) {
            $this->dispatchBrowserEvent('popup-success', ['title' => $data[0]->msg_text]);
        }
    }

    public function addSupplier()
    {
        //Validate-Required
        Validator::make($this->tabSupplier, [
            'selectSupplier' => 'required',
        ])->validate();

        //Validate-มีอยู่แล้วหรือไม่
        $strsql = "SELECT supplier FROM rfq_supplier WHERE rfqno='" . $this->rfqHeader['rfqno'] . "' AND supplier='" 
                . $this->tabSupplier['selectSupplier'] . "'";
        $data = DB::select($strsql);
        if ($data) {
            $this->dispatchBrowserEvent('popup-alert', [
                'title' => 'This supplier already exists!',
                ]);
            return;
        }

        //get currency
        $strsql = "SELECT po_currency FROM supplier WHERE supplier='" . $this->tabSupplier['selectsupplier'] . "'";
        $data = DB::select($strsql);
        $xCurrency = "";
        if ($data) {
            $xCurrency = $data[0]->po_currency;
        }

        DB::statement("INSERT INTO rfq_supplier(rfqno, supplier, currency, create_by, create_on)
        VALUES(?,?,?,?,?)"
        ,[$this->rfqHeader['rfqno'], $this->tabSupplier['selectSupplier'], $xCurrency, auth()->user()->id, Carbon::now()]);

        $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='106' AND class='RFQ'";
        $data = DB::select($strsql);
        if (count($data) > 0) {
            $this->dispatchBrowserEvent('popup-success', ['title' => $data[0]->msg_text]);
        }

        $this->reset(['tabSupplier']);
    }

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
        $strsql = "SELECT supplier, name1 + ' ' + name2 AS supplier_name FROM supplier ORDER BY supplier";
        $this->supplier_dd = DB::select($strsql);

        $this->supplierForAssign_dd = [];
        $strsql = "SELECT b.supplier, b.name1 + ' ' + b.name2 AS supplier_name
                FROM rfq_supplier a
                LEFT JOIN supplier b ON a.supplier=b.supplier
                WHERE a.rfqno='" . $this->rfqHeader['rfqno'] . "'
                ORDER BY supplier";
        $this->supplierForAssign_dd = DB::select($strsql);
    }

    public function goto_prdetail()
    {
        return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->rfqHeader['prno'] . "&tab=item");
    }

    public function editRFQ()
    {
        //RFQ Header
            $strsql = "SELECT a.rfqno, d.description AS ordertype, a.prno, e.description AS rfqstatus, c.site
                    , a.total_base_price, a.total_final_price, a.currency, c.delivery_location
                    , f.name + ' ' + f.lastname AS requested_for, g.name + ' ' + g.lastname AS requestor
                    , i.name + ' ' + i.lastname AS buyer
                    , FORMAT(a.create_on,'yyy-MM-dd') AS create_on, FORMAT(a.changed_on,'yyy-MM-dd') AS changed_on
                    , ((a.total_final_price - a.total_base_price) * 100) / a.total_base_price AS cr
                    , a.total_final_price - a.total_base_price AS cramt
                    FROM rfq_header a
                    LEFT JOIN pr_header c ON a.prno_id=c.id
                    LEFT JOIN order_type d ON c.ordertype=d.ordertype
                    LEFT JOIN rfq_status e ON a.status=e.status
                    LEFT JOIN users f ON c.requested_for=f.id
                    LEFT JOIN users g ON c.requestor=g.id
                    LEFT JOIN buyer h ON c.buyer=h.buyer
                    LEFT JOIN users i ON h.username=i.username
                    WHERE a.rfqno='" . $this->editRFQNo . "'";
            $this->rfqHeader = DB::select($strsql);
            if ($this->rfqHeader) {
                //ถ้าไม่เป็น Array จะใช้ Valdation ไม่ได้
                $this->rfqHeader = json_decode(json_encode($this->rfqHeader[0]), true);
                $this->rfqHeader['total_base_price'] = number_format($this->rfqHeader['total_base_price'], 2);
                $this->rfqHeader['total_final_price'] = number_format($this->rfqHeader['total_final_price'], 2);
                $this->rfqHeader['cr'] = number_format($this->rfqHeader['cr'], 2);
                $this->rfqHeader['cramt'] = number_format($this->rfqHeader['cramt'], 2);
            }
    }

    public function mount()
    {
        $this->editRFQNo = $_GET['rfqno'];
        $this->currentTab = $_GET['tab'];
        $this->tabSupplier['selectSupplier'] = "";
        $this->tabLineItem['selectSupplierItem'] = "";
        $this->editRFQ();
    }

    public function render()
    {
        $this->loadDropdownList();

        //Tab itemList 
        $strsql = "SELECT a.id, a.line_no, a.partno, a.description, a.status, a.qty, a.uom, a.delivery_date
                , a.total_price_lc, a.final_price_lc, a.currency, a.final_price-a.total_price_lc as cramt
                , (a.final_price-a.total_price_lc) * 100 / a.total_price_lc as cr
                , b.name1 AS supplier, a.edecision, a.pono
                FROM rfq_item a
                LEFT JOIN supplier b ON a.supplier=b.supplier
                WHERE a.rfqno='" . $this->rfqHeader['rfqno'] . "'";
        //$itemList = json_decode(json_encode(DB::select($strsql)), true);
        $itemList = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

        //Tab Supplier ???ถึงตรงนี้
        $strsql = "SELECT a.id, a.supplier, b.name1 + ' ' + b.name2 AS supplier_name, b.location
                , b.po_currency, b.contact_person, b.telphone_number, b.email
                FROM rfq_supplier a
                LEFT JOIN supplier b ON a.supplier=b.supplier
                WHERE a.rfqno='" . $this->rfqHeader['rfqno'] . "'";
        //$supplierList = json_decode(json_encode(DB::select($strsql)), true);
        $supplierList = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);


        return view('livewire.rfq-detail', 
            [
                'itemList' => $itemList,
                'supplierList' => $supplierList,
            ]);
    }
}
