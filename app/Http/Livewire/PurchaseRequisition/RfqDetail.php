<?php

namespace App\Http\Livewire\PurchaseRequisition;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Illuminate\Support\Carbon;
use App\Support\Collection;

class RfqDetail extends Component
{
    public $editRFQNo, $currentTab, $rfqHeader;

    //Dropdown
    public $buyer_dd, $buyergroup_dd, $currency_dd;

    public function assignSupplier()
    {

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
        $strsql = "SELECT currency FROM currency_master";
        $this->currency_dd = DB::select($strsql);
    }

    public function goto_prdetail()
    {
        return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->rfqHeader['prno'] . "&tab=item");
    }

    public function editPR()
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
        $this->editPR();
    }

    public function render()
    {
        $this->loadDropdownList();

        //Tab itemList ???ถึงตรงนี้
        $strsql = "SELECT a.id, a.line_no, a.partno, a.description, a.status, a.qty, a.uom, a.delivery_date
                , a.total_price_lc, a.final_price_lc, a.currency, a.final_price-a.total_price_lc as cramt
                , (a.final_price-a.total_price_lc) * 100 / a.total_price_lc as cr
                , b.name1 AS supplier, a.edecision, a.pono
                FROM rfq_item a
                LEFT JOIN supplier b ON a.supplier=b.vendor_code
                WHERE a.rfqno='" . $this->rfqHeader['rfqno'] . "'";
        $this->itemList = json_decode(json_encode(DB::select($strsql)), true); 

        return view('livewire.purchase-requisition.rfq-detail', 
                    [
                        'itemList' => $this->itemList,
                    ]);
    }
}
