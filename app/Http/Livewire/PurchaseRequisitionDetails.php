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
use DateInterval;
use DateTime;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PurchaseRequisitionLog;
use Exception;

class PurchaseRequisitionDetails extends Component
{
    use WithFileUploads;
    use WithPagination; 

    public $listeners = ['deleteConfirmed' => '']; //หลังจากกด Confirm Delete จะให้ไปทำ Function ไหน

    public $isCreateMode, $editPRNo, $isBlanket, $orderType;
    public $deleteID, $deleteType; //เพื่อให้สามารถใช้ Function confirmDelete ร่วมกับการลบหลาย ๆ แบบได้ 
    public $currentTab = "";
    public $enableAddPlan = false;
    public $selectedRows = [];
    public $numberOfPage = 10;
    public $emailAddress, $emailAddressTo, $emailAddressCC;

    //Header
    public $prHeader, $requested_for_dd, $delivery_address_dd, $buyer_dd, $cost_center_dd, $budget_year, $isBuyer, $isRequester_RequestedFor
        , $cancelReason; 

    //Line Items > $itemList=in table, $prItem=ใน Modal, 
    public $prItem = [], $partno_dd, $currency_dd, $internal_order_dd, $budget_code, $purchaseunit_dd, $purchasegroup_dd
        , $budgetcode_dd, $prLineNo_dd, $isCreateLineItem, $showMore1Year; 

    //DeliveryPlan
    public $prDeliveryPlan = [];  //$prDeliveryPlan=ใน Tab, $prListDeliveryPlan=ใน Grid

    //Authorization > deciderList=in table, $decider=Dropdown, validatorList=in table, $validator=Dropdown
    public $decider_dd, $deciderList = [], $decider = [], $validator_dd, $validatorList = [], $validator = [], $rejectReason
        , $isValidator_Decider;

    //Attachment Dropdown ใช้ตัวแปรร่วมกับ prLineNo_dd
    public $editAttachment, $attachmentDocType_dd, $maxSize;
    public $prLineNoAtt_dd, $attachment_lineno, $attachment_filetype, $attachment_edecisionno, $attachment_file; //Header

    //History Log
    public $historyLog;

    //=== Start Function ===

    //Share Function
        public function getNewRFQNo()
        {
            $newRFQNo = "";
    
            $strsql = "SELECT lastnumber FROM tran_type_number WHERE tran_type='RFQ' 
            AND calendar_year='" . $this->prHeader['budget_year'] . "' AND last_calendar_year='". $this->prHeader['budget_year'] . "'";
            $data = DB::select($strsql);

            if ($data){
                DB::statement("UPDATE tran_type_number SET lastnumber=?, changed_by=?, changed_on=? 
                            WHERE tran_type=? AND calendar_year=? AND last_calendar_year=?"
                , [$data[0]->lastnumber + 1, auth()->user()->id, Carbon::now()
                , 'RFQ', $this->prHeader['budget_year'], $this->prHeader['budget_year']]);

                $newRFQNo = 'RFQ' . substr($this->prHeader['budget_year'], 2, 2) . sprintf("%05d", $data[0]->lastnumber + 1);
            }
    
            return $newRFQNo;
        }

        public function confirmDelete($deleteID, $deleteType)
        {
            $this->deleteID = $deleteID;

            if ($deleteType == "item") {
                $this->listeners = ['deleteConfirmed' => 'deleteLineItem'];
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Do you want to delete / Cancel ?',
                    'text' => '',
                ]);
            } else if ($deleteType == "deliveryPlan") {
                $this->listeners = ['deleteConfirmed' => 'deleteDeliveryPlan'];
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Do you want to delete / Cancel ?',
                    'text' => '',
                ]);
            } else if ($deleteType == "decider") {
                $this->listeners = ['deleteConfirmed' => 'deleteDecider'];
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Do you want to delete / Cancel ?',
                    'text' => '',
                ]);
            } else if ($deleteType == "validator") {
                $this->listeners = ['deleteConfirmed' => 'deleteValidator'];
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Do you want to delete / Cancel ?',
                    'text' => '',
                ]);
            } else if ($deleteType == "attachment") {
                $this->listeners = ['deleteConfirmed' => 'deleteAttachment'];
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Delete Attachment',
                    'text' => 'Are you sure you want to delete this attachment? <br /> You cannot undo this action.' ,
                ]);
            }
        }

        public function clearVariablePR()
        {
            $this->reset(['prHeader', 'prItem', 'deciderList', 'validatorList', 'historyLog'
                    , 'isCreateMode', 'editPRNo', 'isBlanket', 'orderType', 'deleteID', 'deleteType', 'currentTab', 'prDeliveryPlan'
                    , 'decider', 'isValidator_Decider', 'validator', 'rejectReason', 'attachment_lineno', 'attachment_file'
                    , 'isRequester_RequestedFor']);
        }
    //Share Function End

    //Action Button
        // print_prform not work
            // public function print_prform()
            // {
            //     $input = storage_path("app/public/reports/pr_form1.jrxml");
            //     $name = "pr_form";
            //     $filename = $name . time();
            //     $output = base_path("public/reports/" . $filename);
            //     $jdbc_dir = 'C:\xampp\htdocs\PHPJasper\vendor\geekcom\phpjasper\bin\jasperstarter\jdbc';
            //     $options = [
            //         'format' => ['pdf'],
            //         'locale' => 'en',
            //         'params' => ['prno' => 'NM22000027'],
            //         'db_connection' => [
            //             'driver'    => 'generic',
            //             'host'      => env('DB_HOST'),
            //             'port'      => env('DB_PORT'),
            //             'username'  => env('DB_USERNAME'),
            //             'password'  => env('DB_PASSWORD'),
            //             'database'  => env('DB_DATABASE'),
            //             'jdbc_driver' => 'com.microsoft.sqlserver.jdbc.SQLServerDriver',
            //             'jdbc_url'  => 'jdbc:sqlserver://localhost:1433;databaseName='.env('DB_DATABASE'),
            //             'jdbc_dir'  => $jdbc_dir 
            //         ]
            //     ];
        
            //     $jasper = new PHPJasper;
        
            //     //$jasper->compile($input)->execute();
        
            //     $jasper->process(
            //             $input,
            //             $output,
            //             $options
            //         )->execute();

            //     //dd(response()->file($output . ".pdf"));
            //     return response()->file($output . ".pdf")->deleteFileAfterSend(true);
            // }
        // print_prform not work End

        public function releaseForPO()
        {
            //??? for test
        }

        public function reopen()
        {
            if ($this->selectedRows) {
                DB::transaction(function () {
                    //Copy pr_header
                    $newPrNo = $this->getNewPrNo();
                    $strsql = "INSERT INTO pr_header(prno, ordertype, status, requestor, requested_for, buyer, delivery_address, request_date, company
                        , site, functions, department, division, section, cost_center, valid_until, days_to_notify, notify_below_10
                        , notify_below_25, notify_below_35, rejection_reason, budget_year, purpose_pr, capexno
                        , create_by, create_on)
                        SELECT '" . $newPrNo . "', ordertype, '10', requestor, requested_for, buyer, delivery_address, '" 
                        . date_format(Carbon::now(), 'Y-m-d') . "', company
                        , site, functions, department, division, section, cost_center, valid_until, days_to_notify, notify_below_10
                        , notify_below_25, notify_below_35, rejection_reason, budget_year, purpose_pr, capexno
                        , '" . auth()->user()->id . "', '" . Carbon::now() . "'
                        FROM pr_header 
                        WHERE id=" . $this->prHeader['id'];
                    DB::statement($strsql);

                    //หา ID ของ prno ใหม่
                    $xPrID = 0;
                    $strsql = "SELECT id FROM pr_header WHERE prno='" . $newPrNo . "'";
                    $data = DB::select($strsql);
                    if (count($data) > 0) {
                        $xPrID = $data[0]->id;
                    }

                    //Copy pr_item ที่เลือก และ Status=Cancel
                    $strsql = "insert into pr_item(prno, prno_id, [lineno], partno, description, purchase_unit, unit_price, unit_price_local, currency
                        , exchange_rate, purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life
                        , snn_service, snn_production, final_price, final_price_local, quotation_expiry_date, quotation_date, nominated_supplier
                        , remarks, skip_rfq, skip_doa, reference_pr, reference_po, reference_po_item, status, close_reason
                        , create_by, create_on)
                        select '" . $newPrNo . "', " . $xPrID . ", [lineno], partno, description, purchase_unit, unit_price, unit_price_local, currency
                        , exchange_rate, purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life
                        , snn_service, snn_production, final_price, final_price_local, quotation_expiry_date, quotation_date, nominated_supplier
                        , remarks, skip_rfq, skip_doa, '" . $this->prHeader['prno'] . "', reference_po, reference_po_item, '10', close_reason
                        , '" . auth()->user()->id . "', '" . Carbon::now() . "'
                        from pr_item
                        where id in (" . myWhereInID($this->selectedRows) . ") AND status='70'";
                    DB::statement($strsql);

                    //26-2-22 ไม่ใช้งาน Set PR Item Status = Cancelled
                    // $strsql = "UPDATE pr_item SET status='70' WHERE prno='" . $this->prHeader['prno'] . "'";
                    // DB::statement($strsql);

                    //Popup Message
                    $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='100' AND class='PURCHASE REQUISITION'";
                    $data = DB::select($strsql);
                    if (count($data) > 0) {
                        $this->dispatchBrowserEvent('popup-success', [
                            'title' => str_replace("<PR No.>", $newPrNo, $data[0]->msg_text),
                        ]);
                    }

                    $this->reset(['selectedRows']);

                    return redirect("purchaserequisitiondetails?mode=edit&prno=" . $newPrNo . "&tab=item");
                });

            }else{
                $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='105' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-alert', [
                        'title' => $data[0]->msg_text,
                    ]);
                }
            }
        }

        //2-3-2022 Change from UAT1 > confirmCancelPrHeader
        // public function cancelPR()
        // {
            // if ($this->selectedRows) {
            //     $xID = myWhereInID($this->selectedRows);
            //     DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=? 
            //         WHERE id IN (" . $xID . ")"
            //         , ['70', auth()->user()->id, Carbon::now()]);

            //     $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='103' AND class='PURCHASE REQUISITION'";
            //     $data = DB::select($strsql);
            //     if (count($data) > 0) {
            //         $this->dispatchBrowserEvent('popup-success', [
            //             'title' => $data[0]->msg_text,
            //         ]);
            //     }
                
            //     $this->reset(['selectedRows']);

            //     return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");

            // }else{
            //     $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='105' AND class='PURCHASE REQUISITION'";
            //     $data = DB::select($strsql);
            //     if (count($data) > 0) {
            //         $this->dispatchBrowserEvent('popup-alert', [
            //             'title' => $data[0]->msg_text,
            //         ]);
            //     }
            // }
        // }

        public function confirmCancelPrHeader()
        {
            $this->cancelReason = "";

            $this->dispatchBrowserEvent('show-modelCancelReason');
        }

        public function cancelPrHeader()
        {
            DB::transaction(function () {
                DB::statement("UPDATE pr_header SET status=?, cancel_reason=?, changed_by=?, changed_on=?
                WHERE id=?" 
                , ['70', $this->cancelReason, auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

                DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=?
                WHERE prno_id=?" 
                , ['70', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

                DB::statement("UPDATE dec_val_workflow SET status=?, changed_by=?, changed_on=?
                WHERE ref_doc_id=? AND status=?" 
                , ['50', auth()->user()->id, Carbon::now(), $this->prHeader['id'], '20']);
            });

            //Send Mail (.env "APP_SENDMAIL=Yes")
                if (config('app.sendmail') == "Yes") {

                    $approver = '';
                    $cancel_by = auth()->user()->name . " " . auth()->user()->lastname;
                    $requested_for_email = '';
                    $approver_email = '';

                    //approver
                    $strsql = "SELECT name + ' ' + lastname AS fname, email FROM users 
                            WHERE username IN (SELECT approver FROM dec_val_workflow WHERE ref_doc_id=" . $this->prHeader['id'] . ")";
                    $data = DB::select($strsql);
                    if ($data) {
                        $approver = implode(', ', array_column($data, 'fname'));
                        $approver_email = array_column($data, 'email');

                        //Validate Mail กรณีหลาย Mail
                        $this->emailAddressTo = $approver_email;
                        $this->validate([
                            'emailAddressTo.*' => 'required|email',
                        ]);
                    }

                    //requested_for
                    $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                        WHERE id=" . $this->prHeader['requested_for'];
                    $data = DB::select($strsql);
                    if ($data) {
                        $requested_for_fullname = $data[0]->fullname;
                        $requested_for_email = $data[0]->email;

                        //Validate Mail กรณี Mail เดียว
                        $this->emailAddressCC = $requested_for_email;
                        $this->validate([
                            'emailAddressCC' => 'required|email',
                        ]);
                    }

                    //เนื้อหาใน Mail
                    $detailMail = [
                        'template' => 'MAIL_PR02',
                        'subject' => 'Your Purchase Requisition No ' . $this->prHeader['prno'] . ' has been cancelled.',
                        'dear' => $approver,
                        'docno' => $this->prHeader['prno'],
                        'cancel_by' => $cancel_by,
                        'link_url' => url('/purchaserequisitiondetails?mode=edit&prno=' . $this->prHeader['prno'] . '&tab=item'),
                    ];

                    Mail::to($approver_email)->cc([$requested_for_email])
                        ->send(New WelcomeMail($detailMail));

                    $this->reset(['emailAddress']);
                }
            //Send Mail End

            $strsql = "SELECT msg_text FROM message_list WHERE msg_no='103' AND class='PURCHASE REQUISITION'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-success', [
                    'title' => $data[0]->msg_text,
                ]);
            }

            $this->clearVariablePR();

            return redirect("purchaserequisitionlist");
        }

        public function releaseForSourcing()
        {
            //Validate
            $myValidate = true;

            //16-03-22 ตรวจสอบว่ามี Line Item หรือไม่
            $strsql = "SELECT id FROM pr_item WHERE prno_id=" . $this->prHeader['id'];
            $data = DB::select($strsql);
            if (!$data){
                $myValidate = false;
                $this->dispatchBrowserEvent('popup-alert', ['title' => 'There must be at least one product.']);
            }

            //03-01-22 IF there is no Decider selected (Ref. P2P-PUR-001-FS-Purchase Requisition_(2022-01-28))
            $strsql = "SELECT approver FROM dec_val_workflow WHERE ref_doc_no='" . $this->prHeader['prno'] . "' AND approval_type='DECIDER'";
            if (count(DB::select($strsql)) == 0) { //ถ้ายังไม่เลือก Decider
                $myValidate = false;

                $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='113' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-alert', ['title' => $data[0]->msg_text]);
                }
            }
            
            if ($myValidate) {
                $this->savePR();

                // DB::transaction(function () { //Fiexed Case Sirina releaseForSourcing ไม่ได้
                    //2022-01-30 Set PR ITEM Status to 'RELEASED FOR SOURCING' (20), disable editting for the PR
                    DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=?
                    WHERE prno=?" 
                    , ['20', auth()->user()->id, Carbon::now(), $this->prHeader['prno']]);

                    //dec_val_workflow
                    DB::statement("UPDATE dec_val_workflow SET submitted_date=?, submitted_by=?, changed_by=?, changed_on=?
                    WHERE ref_doc_type=? AND ref_doc_id=?" 
                    , [Carbon::now(), auth()->user()->id, auth()->user()->id, Carbon::now(), '10', $this->prHeader['id']]);

                    //2022-01-30 Update PR HEADER Status to 'RELEASED FOR SOURCING' (status=20)
                    DB::statement("UPDATE pr_header SET status=?, changed_by=?, changed_on=?
                        WHERE prno=?" 
                        , ['20', auth()->user()->id, Carbon::now(), $this->prHeader['prno']]);
                        
                    $this->prHeader['statusname'] = 'Released for Sourcing'; //ใช้วิธีนี้เพราะไม่ต้องการ Redirect

                    //Update Status=Open
                    //Check have vlidator?
                    $strsql = "SELECT count(*) as count_val FROM dec_val_workflow WHERE approval_type='VALIDATOR' 
                        AND ref_doc_type='10' AND ref_doc_id=" . $this->prHeader['id'];
                    $data = DB::select($strsql);

                    $this->emailAddress = []; //Clear ค่าก่อนเพื่อนำไปตรวจสอบว่าต้องส่ง Mail หรือไม่

                    if ($data[0]->count_val > 0){

                        //Validator ที่ Seq=1 Status=Open
                        $strsql = "SELECT TOP 1 * FROM dec_val_workflow WHERE approval_type='VALIDATOR' 
                            AND ref_doc_type='10' AND ref_doc_id=" . $this->prHeader['id']
                            . " ORDER BY seqno";
                        $xApprover = DB::select($strsql);

                        //ถ้ามีข้อมูล Validator
                        if ($xApprover){
                            DB::statement("UPDATE dec_val_workflow SET status='20' WHERE id=" . $xApprover[0]->id);
                        }

                    } else {
                        //DECIDER Status=Open
                        $strsql = "SELECT * FROM dec_val_workflow WHERE approval_type='DECIDER' 
                            AND ref_doc_type='10' AND ref_doc_id=" . $this->prHeader['id'];
                        $xApprover = DB::select($strsql);

                        //ถ้ามีข้อมมูล Decider
                        if ($xApprover) {
                            DB::statement("UPDATE dec_val_workflow SET status='20' WHERE id=" . $xApprover[0]->id);
                        }
                    }

                    //Send Mail (.env "APP_SENDMAIL=Yes")
                        if (config('app.sendmail') == "Yes") {

                            $requester_fullname = '';
                            $requester_email = '';
                            $requested_for_fullname = '';
                            $requested_for_email = '';
                            $approver_fullname = '';
                            $approver_email = '';

                            //requester
                            $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                                WHERE id=" . $this->prHeader['requestor'];
                            $data = DB::select($strsql);
                            if ($data) {
                                $requester_fullname = $data[0]->fullname;
                                $requester_email = $data[0]->email;
                            }

                            //requested_for
                            $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                                WHERE id=" . $this->prHeader['requested_for'];
                            $data = DB::select($strsql);
                            if ($data) {
                                $requested_for_fullname = $data[0]->fullname;
                                $requested_for_email = $data[0]->email;
                            }

                            //approver
                            $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                            WHERE username='" . $xApprover[0]->approver . "'";
                            $data = DB::select($strsql);
                            if ($data) {
                                $approver_fullname = $data[0]->fullname;
                                $approver_email = $data[0]->email;
                            }

                            //เนื้อหาใน Mail
                            $detailMail = [
                                'template' => 'MAIL_PR01_Approval',
                                'subject' => 'Request to approve Purchase Requisition No: ' . $this->prHeader['prno'],
                                'dear' => $approver_fullname,
                                'docno' => $this->prHeader['prno'],
                                'releasedby' => $requester_fullname,
                                'link_url' => url('/purchaserequisitiondetails?mode=edit&prno=' . $this->prHeader['prno'] . '&tab=auth'),
                            ];

                            //Validate Email
                            $this->emailAddress['approver_email'] = $approver_email;
                            $this->emailAddress['requester_email'] = $requester_email;
                            $this->emailAddress['requested_for_email'] = $requested_for_email;
                    
                            $this->validate([
                                'emailAddress.*' => 'required|email',
                            ]);

                            Mail::to($approver_email)->cc([$requester_email, $requested_for_email])
                                ->send(New WelcomeMail($detailMail));

                            $this->reset(['emailAddress']);
                        }
                    //Send Mail End

                // });

                $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='101' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
                    ]);
                }

                return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
            };
        }

        public function confirmDeletePrHeader_Detail()
        {
            $this->listeners = ['deleteConfirmed' => 'deletePrHeader_Detail'];

            if ($this->selectedRows) {
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Are you sure you want to delete the selected items?',
                ]);
                
            }else{
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Do you want to delete Purchase Requisition No. ' . $this->prHeader['prno'] . '?',
                ]);
            }
        }

        public function deletePrHeader_Detail()
        {
            if ($this->selectedRows) {

                $xID = myWhereInID($this->selectedRows);
                DB::statement("UPDATE pr_item SET deletion_flag = 1 WHERE id IN (" . $xID . ")"); //19-03-2022
                DB::statement("UPDATE pr_delivery_plan SET deletion_flag = 1 WHERE ref_prline_id IN (" . $xID . ")"); //19-03-2022

                 //Histroy Log
                 $this->writeItemHistoryLog($this->selectedRows,"DELETE");
                 //END Histroy Log

                $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='104' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => $data[0]->msg_text,
                    ]);
                }
                
                $this->reset(['selectedRows']);

                //return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");

            }else{
                DB::statement("UPDATE pr_header SET deletion_flag=?, changed_by=?, changed_on=?
                    WHERE id=?" 
                    , [1, auth()->user()->id, Carbon::now(), $this->prHeader['id']]);
            
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='107' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
                    ]);
                }

                $this->clearVariablePR();

                return redirect("purchaserequisitionlist");
            }
        }

        public function backToPRList()
        {
            $this->clearVariablePR();

            return redirect("purchaserequisitionlist");
        }

        public function savePR()
        {
            //Validaate required field
            Validator::make($this->prHeader, [
                'phone' => 'required',
                'extention' => 'required',
                'phone_reqf' => 'required',
                'extention_reqf' => 'required',
                'requested_for' => 'required',
                'delivery_address' => 'required',
                'buyer' => 'required',
                'cost_center' => 'required',
                'purpose_pr' => 'required',
                'budget_year' => 'required',
            ])->validate();

            //13-03-2022 Fixed ตรวจสอบว่าต้องไม่ใช่การสร้างครั้งแรก
            if (!empty($this->prHeader['id'])) {
                //11-03-2022 ตรวจสอบว่ามี Item ที่มี Unit เป็น Project หรือไม่
                $xHaveUOMProject = "N";
                $xYear = "";
                $xEndFiscalYear = "";
                $strsql = "SELECT purchase_unit FROM pr_item WHERE prno_id=" . $this->prHeader['id'] . " AND purchase_unit='Project'";
                $data = DB::select($strsql);
                if ($data) {
                    $xHaveUOMProject = "Y";
                    $xYear = date_format(Carbon::now(), 'Y');
                    $xEndFiscalYear = $xYear . '-03-31';
                    $this->prHeader['endfiscalyear'] = $xEndFiscalYear;
                }
            }

            if ($this->prHeader['ordertype'] == '21' AND $xHaveUOMProject = "N") {
                Validator::make($this->prHeader, [
                    'budget_year' => 'required',
                    'valid_until' => 'required|date|date_format:Y-m-d|after:yesterday|before_or_equal:endfiscalyear',
                ])->validate();
            } else if ($this->prHeader['ordertype'] == '20' AND $xHaveUOMProject = "N") {
                Validator::make($this->prHeader, [
                    'valid_until' => 'required|date|date_format:Y-m-d|after:yesterday|before_or_equal:endfiscalyear',
                ])->validate();
            }

            if ($this->prHeader['ordertype'] == '21' AND $xHaveUOMProject = "Y") {
                Validator::make($this->prHeader, [
                    'budget_year' => 'required',
                    'valid_until' => 'required|date|date_format:Y-m-d|after:yesterday',
                ])->validate();
            } else if ($this->prHeader['ordertype'] == '20' AND $xHaveUOMProject = "Y") {
                Validator::make($this->prHeader, [
                    'valid_until' => 'required|date|date_format:Y-m-d|after:yesterday',
                ])->validate();
            }

            $xValidate = true;
            //31-01-2022 Validate IF Order Type = Blanket Free Text & IF UoM <> PJ (Project) and PR Header.Valid Until > End of Fiscal Year
            if ($this->prHeader['ordertype'] == '21') {
                $xYear = $this->prHeader['budget_year'] + 1;
                $xEndFiscalYear = $xYear . '-03-31';
                if ($this->prHeader['ordertype'] == '21' AND  $this->prHeader['valid_until'] > $xEndFiscalYear) {
                    $xValidate = false;
    
                    $strsql = "SELECT msg_text FROM message_list WHERE msg_no='116' AND class='PURCHASE REQUISITION'";
                    $data = DB::select($strsql);
                    if (count($data) > 0) {
                        $this->dispatchBrowserEvent('popup-alert', [
                            'title' => $data[0]->msg_text,
                        ]);
                    }
                }
            }

            if ($xValidate) {
                //Create PR
                if ($this->isCreateMode) {
                    $this->prHeader['prno'] = $this->getNewPrNo();
                    $this->prHeader['status'] = '10';
                    DB::transaction(function () {
                    //pr_header
                        DB::statement(
                            "INSERT INTO pr_header(prno, ordertype, status, requestor, requested_for, buyer
                            , delivery_address, delivery_location, delivery_site
                            , requestor_phone, requestor_ext, requested_for_phone, requested_for_ext, requested_for_email
                            , request_date, company, site, functions, department, division, section, cost_center, valid_until
                            , days_to_notify, notify_below_10, notify_below_25, notify_below_35, budget_year, purpose_pr, capexno, create_by, create_on)
                            
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                                [
                                    $this->prHeader['prno'], $this->prHeader['ordertype'], $this->prHeader['status'], $this->prHeader['requestor']
                                    , $this->prHeader['requested_for'], $this->prHeader['buyer'], $this->prHeader['delivery_address']
                                    , $this->prHeader['delivery_location'], $this->prHeader['delivery_site']
                                    , $this->prHeader['phone'], $this->prHeader['extention'], $this->prHeader['phone_reqf'], $this->prHeader['extention_reqf']
                                    , $this->prHeader['email_reqf'], $this->prHeader['request_date']
                                    , $this->prHeader['company'], $this->prHeader['site'], $this->prHeader['functions'], $this->prHeader['department']
                                    , $this->prHeader['division'], $this->prHeader['section'], $this->prHeader['cost_center']
                                    , $this->prHeader['valid_until'], $this->prHeader['days_to_notify'], $this->prHeader['notify_below_10']
                                    , $this->prHeader['notify_below_25'], $this->prHeader['notify_below_35'], $this->prHeader['budget_year']
                                    , $this->prHeader['purpose_pr'], $this->prHeader['capexno'], auth()->user()->id, Carbon::now()
                                ]
                        );
                    });

                    //HISTROY LOG
                    $idPrHeaderHistroy = DB::getPdo()->lastInsertId();
                    $obj = DB::table('pr_header_history')->select('id_original')->where('id','=',$idPrHeaderHistroy)->first();
                    if($obj != null){
                        $idPrHeader = $obj->id_original;
                        $this->writeHeaderHistoryLog($idPrHeader,"INSERT");
                    }
                    //HISTROY LOG
                   
    
                    $strsql = "SELECT msg_text FROM message_list WHERE msg_no='100' AND class='PURCHASE REQUISITION'";
                    $data = DB::select($strsql);
                    if (count($data) > 0) {
                        $this->dispatchBrowserEvent('popup-success', [
                            'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
                        ]);
                    }
    
                    return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
    
                } else {
                    //Edit PR
                    DB::transaction(function () {
                        DB::statement("UPDATE pr_header SET requested_for=?, delivery_address=?, delivery_location=?, delivery_site=?
                        , request_date=?, site=?, functions=?, department=?
                        , requestor_phone=?, requestor_ext=?, requested_for_phone=?, requested_for_ext=?, requested_for_email=?
                        , division=?, section=?, buyer=?, cost_center=?, valid_until=?, days_to_notify=?, notify_below_10=?, notify_below_25=?
                        , notify_below_35=?,budget_year=?, purpose_pr=?, capexno=?, status=?, changed_by=?, changed_on=?
                        where prno=?" 
                        , [$this->prHeader['requested_for'], $this->prHeader['delivery_address'], $this->prHeader['delivery_location'], $this->prHeader['delivery_site']
                        , $this->prHeader['request_date'], $this->prHeader['site'], $this->prHeader['functions'], $this->prHeader['department']
                        , $this->prHeader['phone'], $this->prHeader['extention'], $this->prHeader['phone_reqf'], $this->prHeader['extention_reqf']
                        , $this->prHeader['email_reqf'], $this->prHeader['division'], $this->prHeader['section']
                        , $this->prHeader['buyer'], $this->prHeader['cost_center'], $this->prHeader['valid_until']
                        , $this->prHeader['days_to_notify'], $this->prHeader['notify_below_10'], $this->prHeader['notify_below_25'], $this->prHeader['notify_below_35']
                        , $this->prHeader['budget_year'], $this->prHeader['purpose_pr'], $this->prHeader['capexno'], $this->prHeader['status']
                        , auth()->user()->id, Carbon::now(), $this->prHeader['prno']]);
                    });
      
                    $this->writeHeaderHistoryLog($this->prHeader['id'],"UPDATE");

                    $strsql = "select msg_text from message_list where msg_no='110' AND class='PURCHASE REQUISITION'";
                    $data = DB::select($strsql);
                    if (count($data) > 0) {
                        $this->dispatchBrowserEvent('popup-success', [
                            'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
                        ]);
                    }
    
                    return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
                }
            }

        }

    //Action Button End
    
    //Attachment
        public function deleteAttachmentFile($index)
        {
            unset($this->attachment_file[$index]);
        }

        // เตรียมลบออก
        // public function updatedAttachmentFile()
        // {
        //     $this->validate([
        //         'attachment_file.*' => 'max:5120', // 5MB Max 
        //     ]);
        // }

        public function formatSizeUnits($fileSize)
        {
            //Call Golbal Function
            return formatSizeUnits($fileSize);
        }

        public function updatedEditAttachmentFileType(){
            if ($this->editAttachment['file_type'] <> 'eDecision') {
                $this->editAttachment['edecision_no'] = '';
            }
        }

        public function editAttachment_Save()
        {
            //ตรวจสอบว่าเป็น Header หรือไม่
            if (in_array("0", array($this->editAttachment['ref_lineno']))) {
                $isHeader = true;
            }else{
                $isHeader = false;
            }

            DB::transaction(function() use ($isHeader)
            {
                DB::statement("UPDATE attactments SET file_name=?, ref_lineno=?, file_type=?, edecision_no=?, isheader_level=?, changed_by=?, changed_on=?
                WHERE id=?" 
                , [$this->editAttachment['file_name'], json_encode($this->editAttachment['ref_lineno']), $this->editAttachment['file_type']
                , $this->editAttachment['edecision_no'], $isHeader, auth()->user()->id, Carbon::now(), $this->editAttachment['id']]);

                //Add History Log
                $xNewValue = "File : " . $this->editAttachment['file_path'] . " apply to " . $this->editAttachment['ref_lineno'] . " has been updated.";
                DB::statement("INSERT INTO history_logs(id_original, obj_type, line_no, refdocno, field, new_value, created_by, created_on, changed_by, changed_on)
                VALUES(?,?,?,?,?,?,?,?,?,?)"
                ,[$this->deleteID, "PR", "", $this->prHeader['prno'], "FILE ATTACHMENT", $xNewValue, auth()->user()->id, Carbon::now()
                    , auth()->user()->id, Carbon::now() ]);
            });

            $strsql = "SELECT msg_text FROM message_list WHERE msg_no='100' AND class='FILE ATTACHMENT'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-success', [
                    'title' => $data[0]->msg_text,
                ]);
            }

            $this->reset(['editAttachment']);
            $this->dispatchBrowserEvent('hide-modelEditAttachment');
        }

        public function editAttachment($rowID)
        {
            //แสดง Modal สำหรับ Edit
            $strsql = "SELECT a.id, a.file_name, a.file_path, a.ref_lineno, a.file_type, a.edecision_no
                ,b.name + ' ' + b.lastname as create_by
                , CASE 
                    WHEN ISNULL(a.create_on,'') = '' THEN a.changed_on
                    ELSE a.create_on
                    END AS last_modified
                FROM attactments a
                LEFT JOIN users b ON a.create_by = b.id
                WHERE a.id=" . $rowID;
            $data = DB::select($strsql);

            if ($data) {
                $this->editAttachment = json_decode(json_encode($data[0]), true);

                $xPrLineNoAtt_dd = $this->prLineNoAtt_dd;
                // Add Level 0
                array_push($xPrLineNoAtt_dd, ["id" => "", "lineno" => "0", "description" => "Level PR Header",]);
                sort($xPrLineNoAtt_dd);

                //Bind ค่า editattachment_lineno-select2
                $xRef_lineno = json_decode($this->editAttachment['ref_lineno']);
                $newOption = '';
                foreach ($xPrLineNoAtt_dd as $row) {
                    $newOption = $newOption . "<option value='" . $row['lineno'] . "' ";
                    if ( !is_null($xRef_lineno) ) {
                        for ($i=0; $i < count($xRef_lineno); $i++){
                            if ($row['lineno'] == $xRef_lineno[$i]) {
                                $newOption = $newOption . "selected='selected'";
                            }
                        }
                    }                    
                    $newOption = $newOption . ">" . $row['lineno'] . ': ' . $row['description'] . "</option>";
                }

                $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#editattachment_lineno-select2']);
            }

            $this->dispatchBrowserEvent('show-modelEditAttachment');
        }

        public function deleteAttachment()
        {
            $strsql = "SELECT file_path from attactments WHERE id=" . $this->deleteID;
            $data = DB::select($strsql);
            if ($data){
                if(File::exists(public_path("storage/attachments/" . $data[0]->file_path))){
                    File::delete(public_path("storage/attachments/" . $data[0]->file_path));
                }
            }

            DB::transaction(function() use ($data)
            {
                //Add History Log
                $xNewValue = "File : " . $data[0]->file_path . " has been removed.";
                DB::statement("INSERT INTO history_logs(id_original, obj_type, line_no, refdocno, field, new_value, created_by, created_on, changed_by, changed_on)
                VALUES(?,?,?,?,?,?,?,?,?,?)"
                ,[$this->deleteID, "PR", "", $this->prHeader['prno'], "FILE ATTACHMENT", $xNewValue, auth()->user()->id, Carbon::now()
                    , auth()->user()->id, Carbon::now() ]);
    
                DB::statement("DELETE FROM attactments WHERE id=?" , [$this->deleteID]);
            });

            $strsql = "SELECT msg_text FROM message_list WHERE msg_no='102' AND class='FILE ATTACHMENT'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $this->dispatchBrowserEvent('popup-success', [
                    'title' => $data[0]->msg_text,
                ]);
            }
        }

        public function addAttachment()
        {
            if ($this->attachment_file) {

                $this->validate([
                    'attachment_file.*' => 'max:5120', // 5MB Max
                ]);

                DB::transaction(function() 
                {
                    foreach ($this->attachment_file as $file) {
                        //$attachments = $file->store('/', 'attachments'); //Work
                        //เปลี่ยนชื่อไฟล์เป็น ชื่อเดิม + เวลา
                        $newFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '_' . time();
                        $newFileName = $newFileName . '.' . pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);

                        //Save ลงที่ public\storage\attachments
                        $attachments = $file->storeAs('/public/attachments', $newFileName);

                        //ตรวจสอบว่าเป็น Header หรือไม่ is_array()
                        $isHeader = true;
                        if (is_array($this->attachment_lineno)) {
                            if (in_array("0", $this->attachment_lineno)) {
                                $isHeader = true;
                            }else{
                                $isHeader = false;
                            }
                        }

                        DB::statement("INSERT INTO attactments ([file_name], file_type, file_path, ref_doctype, ref_docid, ref_docno
                            , edecision_no, isheader_level, ref_lineno, create_by, create_on)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?)"
                        ,[$file->getClientOriginalName(), $this->attachment_filetype, $newFileName, '10'
                        , $this->prHeader['id'], $this->prHeader['prno'], $this->attachment_edecisionno, $isHeader
                        , json_encode($this->attachment_lineno), auth()->user()->id, Carbon::now()]);

                        //Add History Log
                        //Get last id in table attactments
                        $strsql = "SELECT id FROM attactments WHERE file_path='" . $newFileName . "'";
                        $data = DB::select($strsql);
                        if ($data) {
                            $xNewValue = "File : " . $newFileName . " apply to " . implode(", ",$this->attachment_lineno) . " has been created.";
                            DB::statement("INSERT INTO history_logs(id_original, obj_type, line_no, refdocno, field, new_value, created_by, created_on, changed_by, changed_on)
                            VALUES(?,?,?,?,?,?,?,?,?,?)"
                            ,[$data[0]->id, "PR", "", $this->prHeader['prno'], "FILE ATTACHMENT", $xNewValue, auth()->user()->id, Carbon::now()
                                , auth()->user()->id, Carbon::now() ]);
                        }                        
                    }
                });
                
                $this->reset(['attachment_lineno', 'attachment_filetype', 'attachment_edecisionno', 'attachment_file']);
                $this->dispatchBrowserEvent('clear-select2');

            } else {
                $this->dispatchBrowserEvent('popup-alert', ['title' => "Please select a file"]);
            }
        }
    //Attachment End

    //Authorization
        public function updatedValidatorUsername()
        {
            $strsql = "SELECT username, company, department, position FROM users WHERE username='" . $this->validator['username'] . "'";
            $data = DB::select($strsql);
            if ($data) {
                $this->validator = json_decode(json_encode($data[0]), true);
            }
        }

        public function updatedDeciderUsername()
        {
            $strsql = "SELECT a.username, b.name AS company, a.department, a.position 
                FROM users a
                LEFT JOIN company b ON a.company = b.company
                WHERE username='" . $this->decider['username'] . "'";
            $data = DB::select($strsql);
            if ($data) {
                $this->decider = json_decode(json_encode($data[0]), true);
            }
        }

        public function validatorDeciderApprove()
        {
            //??? รอแก้เรื่อง DB::transaction(function() และ SendMail 

            //Update Status dec_val_workflow 
            DB::statement("UPDATE dec_val_workflow SET status=?, completed_date=?, changed_by=?, changed_on=?
                WHERE approver=? AND ref_doc_id=? AND ref_doc_type=?" 
                , ['30', Carbon::now(), auth()->user()->id, Carbon::now(), auth()->user()->username, $this->prHeader['id'], '10']);

            $strsql = "SELECT * FROM dec_val_workflow 
                WHERE approver='" . auth()->user()->username . "' AND ref_doc_id=" . $this->prHeader['id'] . " AND ref_doc_type='10'";
            $data = DB::select($strsql);
            $xApproval_type = $data[0]->approval_type; //use next step

            //Add Log dec_val_workflow_log
            if ($data) {
                DB::statement("INSERT INTO dec_val_workflow_log (seqno, approval_type, approver, status, refdoc_type, refdoc_no, refdoc_id
                    , submitted_date, completed_date, submitted_by, create_by, create_on)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?)"
                ,[$data[0]->seqno, $data[0]->approval_type, $data[0]->approver, $data[0]->status, $data[0]->ref_doc_type, $data[0]->ref_doc_no
                    , $data[0]->ref_doc_id, $data[0]->submitted_date, $data[0]->completed_date, $data[0]->submitted_by
                    , auth()->user()->id, Carbon::now()]);
            }

            //ตรวจสอบว่า Approver เป็น Validator หรือ Decider 
            if ($xApproval_type == 'VALIDATOR') {
                //Update status > pr_header & pr_item
                DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=?
                    WHERE prno_id=?" 
                    , ['21', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

                DB::statement("UPDATE pr_header SET status=?, changed_by=?, changed_on=?
                    WHERE id=?" 
                    , ['21', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);
                
                $this->prHeader['statusname'] = 'Partially Authorized'; //ใช้วิธีนี้เพราะไม่ต้องการ Redirect

                //ตรวจสอบว่ายังมี Validator ที่ seq มากกว่ายังไม่ได้ Appvore
                $strsql = "SELECT top 1 * FROM dec_val_workflow WHERE approval_type='VALIDATOR' AND seqno > " . $data[0]->seqno 
                    . " AND ref_doc_id=" . $this->prHeader['id'] . " AND ref_doc_type='10'";
                $xApprover = DB::select($strsql);

                //ถ้ายังมี Validator คนต่อไป
                if ($xApprover) {
                    //Set status=open to next validator
                    DB::statement("UPDATE dec_val_workflow SET status='20' WHERE id=" . $xApprover[0]->id);

                }else{
                    //หาคนที่เป็น Decider
                    $strsql = "SELECT * FROM dec_val_workflow WHERE approval_type='DECIDER'
                        AND ref_doc_id=" . $this->prHeader['id'] . " AND ref_doc_type='10'";
                    $xApprover = DB::select($strsql);

                    if ($xApprover) {
                        //Set status=Open decider
                        DB::statement("UPDATE dec_val_workflow SET status='20' WHERE approval_type='DECIDER' 
                            AND ref_doc_id=" . $this->prHeader['id'] . " AND ref_doc_type='10'");
                    }
                }

                //Send Mail
                    if (config('app.sendmail') == "Yes") {

                        $requester_fullname = '';
                        $requester_email = '';
                        $requested_for_fullname = '';
                        $requested_for_email = '';
                        $approver_fullname = '';
                        $approver_email = '';

                        //requester
                        $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                            WHERE id=" . $this->prHeader['requestor'];
                        $data = DB::select($strsql);
                        if ($data) {
                            $requester_fullname = $data[0]->fullname;
                            $requester_email = $data[0]->email;
                        }

                        //requested_for
                        $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                            WHERE id=" . $this->prHeader['requested_for'];
                        $data = DB::select($strsql);
                        if ($data) {
                            $requested_for_fullname = $data[0]->fullname;
                            $requested_for_email = $data[0]->email;
                        }

                        //approver
                        $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                        WHERE username='" . $xApprover[0]->approver . "'";
                        $data = DB::select($strsql);
                        if ($data) {
                            $approver_fullname = $data[0]->fullname;
                            $approver_email = $data[0]->email;
                        }

                        //เนื้อหาใน Mail
                        $detailMail = [
                            'template' => 'MAIL_PR01_Approval',
                            'subject' => 'Request to approve Purchase Requisition No: ' . $this->prHeader['prno'],
                            'dear' => $approver_fullname,
                            'docno' => $this->prHeader['prno'],
                            'releasedby' => $requester_fullname,
                            'link_url' => url('/purchaserequisitiondetails?mode=edit&prno=' . $this->prHeader['prno'] . '&tab=auth'),
                        ];

                        //Validate Email
                        $this->emailAddress['approver_email'] = $approver_email;
                        $this->emailAddress['requester_email'] = $requester_email;
                        $this->emailAddress['requested_for_email'] = $requested_for_email;
                
                        $this->validate([
                            'emailAddress.*' => 'required|email',
                        ]);

                        Mail::to($approver_email)->cc([$requester_email, $requested_for_email])
                            ->send(New WelcomeMail($detailMail));
                    }
                //Send Mail End

            } else if ($xApproval_type == 'DECIDER'){
                //Create RFQ & update status pr_header and pr_item
                DB::transaction(function() 
                {
                    $xNewRFQNo = $this->getNewRFQNo();

                    //Find Buyer Group
                    $xBuyerGrp = '';
                    $strsql = "SELECT b.buyer_group_code
                        FROM buyer a
                        LEFT JOIN buyer_group b ON a.username=b.buyer_id
                        WHERE b.ismark_delete=0 AND a.username='" . $this->prHeader['buyer'] . "'";
                    $data = DB::select($strsql);
                    if ($data) {
                        $xBuyerGrp = $data[0]->buyer_group_code;
                    }

                    //Create RFQ HEADER
                    DB::statement("INSERT INTO rfq_header(rfqno, prno, prno_id, status, company, site, buyer, buyer_group, workflow_step
                    , total_base_price_local, total_final_price_local, cr_amount_local, cr_percent_local, create_by, create_on)
                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                    ,[$xNewRFQNo, $this->prHeader['prno'], $this->prHeader['id'], '10', $this->prHeader['company'], $this->prHeader['site']
                    , $this->prHeader['buyer'], $xBuyerGrp, 1, 0, 0, 0, 0, auth()->user()->id, Carbon::now()]);

                    //Create RFQ SUPPLIER (If there is at least one PR Item.Skip RFQ = TRUE) 
                    //$strsql = "SELECT prno FROM pr_item WHERE skip_rfq=1 AND prno_id=" . $this->prHeader['id'];
                    $strsql = "SELECT * FROM pr_item WHERE skip_rfq=1 AND prno='" . $this->prHeader['prno'] . "' AND status BETWEEN '20' AND '30'";
                    $data = DB::select($strsql);

                    if ($data) {
                        $data = json_decode(json_encode($data), true);

                        foreach ($data as $row) {
                            //Total Base Price, Total Final Price, Total Base Price (Local Currency), Total Final Price (Local Currency)
                            $total_base_price = 0;
                            $total_final_price = 0;
                            $total_base_price_local = 0;
                            $total_final_price_local = 0;
                            $strsql = "SELECT ISNULL(ROUND(SUM(unit_price * qty), 2), 0) AS total_base_price
                                    , ISNULL(ROUND(SUM(final_price), 2), 0) AS total_final_price
                                    , ISNULL(ROUND(SUM(unit_price * qty * exchange_rate),2), 0) AS total_base_price_local
                                    , ISNULL(ROUND(SUM(final_price_local), 2), 0) AS total_final_price_local
                                    FROM pr_item where prno='" . $this->prHeader['prno'] . "'";
                            $data2 = DB::select($strsql);
                            if ($data2) {
                                $total_base_price = $data2[0]->total_base_price;
                                $total_final_price = $data2[0]->total_final_price;
                                $total_base_price_local = $data2[0]->total_base_price_local;
                                $total_final_price_local = $data2[0]->total_final_price_local;
                            }

                            $strsql = "INSERT INTO rfq_supplier_quotation(rfqno, company, supplier_quotationno, supplier, supplier_name, main_contact_person
                                , telephone_number, email, payment_term, exchange_rate, quotation_expiry_term, quotation_expiry, payment_pattern
                                , total_base_price, total_final_price, total_base_price_local, total_final_price_local, create_by, create_on) 
                                SELECT '" . $xNewRFQNo . "', '" . $this->prHeader['company'] . "', '', a.nominated_supplier, b.name1 + ' ' + b.name2 
                                , b.contact_person, b.telphone_number, b.email, b.payment_key, a.exchange_rate, '', '', ''
                                ," . $total_base_price ." ," . $total_final_price . ", " . $total_base_price_local . ", " . $total_final_price_local . "
                                , '" . auth()->user()->id . "', '" . Carbon::now() . "'
                                FROM pr_item a
                                LEFT JOIN supplier b ON a.nominated_supplier=b.supplier
                                WHERE a.id='" . $row['id'] . "'";
                            DB::statement($strsql);
                        }
                    }

                    //Create RFQ ITEM
                    $strsql = "SELECT * FROM pr_item WHERE prno='" . $this->prHeader['prno'] . "' AND status BETWEEN '20' AND '30'";
                    $data = DB::select($strsql);
                    if ($data) {
                        $data = json_decode(json_encode($data), true);
                        foreach ($data as $row) {
                            //If PR Item.Skip RFQ = TRUE
                            if ($row['skip_rfq']) {
                                $xBasePrice = $row['unit_price'];
                                $xTotalBasePrice = $row['unit_price'] * $row['qty'];
                                $xBasePriceLocal = $row['unit_price_local'];
                                $xTotalBasePriceLocal = $row['unit_price_local'] * $row['qty'];
                                $xFinalPrice = $row['unit_price'];
                                $xTotalFinalPrice = $row['unit_price'] * $row['qty'];
                                $xFinalPriceLocal = $row['unit_price_local'];
                                $xTotalFinalPriceLocal = $row['unit_price_local'] * $row['qty'];
                                $xCrAmount = $xTotalFinalPrice - $xTotalBasePrice;
                                $xCrPercent = ($xCrAmount / $xTotalBasePrice) * 100;
                                $xCrAmountLocal = $xTotalFinalPriceLocal - $xTotalBasePriceLocal;
                                $xCrPercentLocal = ($xCrAmountLocal / $xTotalBasePriceLocal) * 100;

                                DB::statement("INSERT INTO rfq_item(rfqno, prno, prlineno_id, prlineno, partno, description, skip_rfq, non_stock_control
                                , over1_year_life, status, qty, uom, delivery_date, currency
                                , base_price, total_base_price, base_price_local, total_base_price_local
                                , exchange_rate, blanket_order_type, edecisionno, edecision_fileid
                                , rfq_supplier_quotation, final_price, total_final_price, final_price_local
                                , total_final_price_local, cr_amount, cr_percent, cr_amount_local, cr_percent_local
                                , supplier, create_by, create_on)
                                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                                ,[$xNewRFQNo, $row['prno'], $row['id'], $row['lineno'], $row['partno'], $row['description'], $row['skip_rfq'], $row['nonstock_control']
                                    , $row['over_1_year_life'], '11', $row['qty'], $row['purchase_unit'], $row['req_date'], $row['currency']
                                    , round($xBasePrice, 2), round($xTotalBasePrice, 2), round($xBasePriceLocal, 2), round($xTotalBasePriceLocal, 2)
                                    , $row['exchange_rate'], $row['blanket_order_type'], $row['edecision_no'], $row['edecision_file']
                                    , $row['nominated_supplier'] , round($xFinalPrice, 2), round($xTotalFinalPrice, 2), round($xFinalPriceLocal, 2)
                                    , round($xTotalFinalPriceLocal, 2), round($xCrAmount , 2), round($xCrPercent, 2), round($xCrAmountLocal, 2)
                                    , round($xCrPercentLocal, 2), $row['nominated_supplier'], auth()->user()->id, Carbon::now()
                                ]);

                            } else {
                                DB::statement("INSERT INTO rfq_item(rfqno, prno, prlineno_id, prlineno, partno, description, skip_rfq, non_stock_control
                                , over1_year_life, status, qty, uom, delivery_date, currency, base_price, total_base_price, base_price_local
                                ,total_base_price_local, exchange_rate, blanket_order_type, edecisionno, edecision_fileid, create_by, create_on)
                                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                                ,[$xNewRFQNo, $row['prno'], $row['id'], $row['lineno'], $row['partno'], $row['description'], $row['skip_rfq']
                                    , $row['nonstock_control'], $row['over_1_year_life'], '10', $row['qty'], $row['purchase_unit'], $row['req_date']
                                    , $row['currency'], $row['unit_price'], round($row['unit_price'] * $row['qty'], 2), $row['unit_price_local']
                                    , round($row['unit_price_local'] * $row['qty'], 2) , $row['exchange_rate'], $row['blanket_order_type']
                                    , $row['edecision_no'], $row['edecision_file'], auth()->user()->id, Carbon::now()]);
                            }
                        }
                    }

                    //Update PR Item Reference RFQ, Reference RFQ Item
                    $strsql = "UPDATE pr_item SET reference_rfq=a.rfqno, reference_rfqitem=a.id
                            FROM rfq_item a
                            JOIN pr_item b ON a.prlineno_id=b.id
                            WHERE a.rfqno='" . $xNewRFQNo . "'";
                    DB::statement($strsql);

                    //Update status pr_header & pr_item to RFQ Created (31)
                    DB::statement("UPDATE pr_header SET status=?, pr_authorized_date=?, changed_by=?, changed_on=? WHERE id=?" 
                        , ['31', Carbon::now(), auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

                    DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=? WHERE prno_id=?" 
                        , ['31', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

                    //Update PR Item Status="Pending Review" If PR Item.Skip RFQ = TRUE
                    DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=? WHERE prno_id=? AND skip_rfq=?" 
                        , ['32', auth()->user()->id, Carbon::now(), $this->prHeader['id'], 1]);

                    //Update Pr Header Status Name
                    $this->prHeader['statusname'] = 'RFQ Created';

                    //Setup Reminder (CR No.9)
                    if ( $this->prHeader['valid_until'] AND ($this->prHeader['ordertype'] == '20' OR $this->prHeader['ordertype'] == '21') ){
                        $interval = Carbon::now()->diff($this->prHeader['valid_until']);
                        $days = $interval->format('%a');

                        $reminder1 = new DateTime($this->prHeader['valid_until']);
                        $reminder2 = new DateTime($this->prHeader['valid_until']);
                        $reminder3 = new DateTime($this->prHeader['valid_until']);

                        if ($days >= 30){
                            $interval = new DateInterval('P30D');
                            $reminder1 = $reminder1->sub($interval);

                            $interval = new DateInterval('P15D');
                            $reminder2 = $reminder2->sub($interval);

                            $interval = new DateInterval('P7D');
                            $reminder3 = $reminder3->sub($interval);

                            DB::statement("UPDATE pr_header SET reminder1=?, reminder2=?, reminder3=? WHERE id=?" 
                                , [$reminder1, $reminder2, $reminder3, $this->prHeader['id']]);

                        }else if ($days < 30 AND $days >= 15){
                            $date = new DateTime($this->prHeader['valid_until']);

                            $interval = new DateInterval('P15D');
                            $reminder1 = $reminder1->sub($interval);

                            $interval = new DateInterval('P7D');
                            $reminder2 = $reminder2->sub($interval);

                            DB::statement("UPDATE pr_header SET reminder1=?, reminder2=? WHERE id=?" 
                                , [$reminder1, $reminder2, $this->prHeader['id']]);

                        }else if ($days < 15 AND $days >= 7){
                            $interval = new DateInterval('P7D');
                            $reminder1 = $reminder1->sub($interval);

                            DB::statement("UPDATE pr_header SET reminder1=? WHERE id=?" 
                                , [$reminder1, $this->prHeader['id']]);
                        }
                    }
                });

                //Send Mail
                if (config('app.sendmail') == "Yes") {
                    
                    //requester
                    // $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                    //     WHERE company = '" . $this->prHeader['company'] . "' 
                    //     AND id=" . $this->prHeader['requestor'];
                    $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                    WHERE id=" . $this->prHeader['requestor'];
                    $data = DB::select($strsql);
                    if ($data) {
                        $requester_fullname = $data[0]->fullname;
                        $requester_email = $data[0]->email;
                    }

                    //requested_for
                    $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                        WHERE id=" . $this->prHeader['requested_for'];
                    $data = DB::select($strsql);
                    if ($data) {
                        $requested_for_fullname = $data[0]->fullname;
                        $requested_for_email = $data[0]->email;
                    }

                    //approver
                    $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                        WHERE id=" . auth()->user()->id;
                    $data = DB::select($strsql);
                    if ($data) {
                        $approver_fullname = $data[0]->fullname;
                        $approver_email = $data[0]->email;
                    }

                    //เนื้อหาใน Mail
                    $detailMail = [
                        'template' => 'MAIL_PR02_Approval',
                        'subject' => 'You Purchase Requisition No ' . $this->prHeader['prno'] . ' has been approved.',
                        'dear' => $requester_fullname,
                        'docno' => $this->prHeader['prno'],
                        'actionby' => $approver_fullname,
                        'link_url' => url('/purchaserequisitiondetails?mode=edit&prno=' . $this->prHeader['prno'] . '&tab=auth'),
                    ];

                    //Validate Email
                    $this->emailAddress['approver_email'] = $approver_email;
                    $this->emailAddress['requester_email'] = $requester_email;
                    $this->emailAddress['requested_for_email'] = $requested_for_email;
            
                    $this->validate([
                        'emailAddress.*' => 'required|email',
                    ]);

                    Mail::to($requester_email)->cc([$approver_email, $requested_for_email])->send(New WelcomeMail($detailMail));
                }
                //Send Mail End
            }

            $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='100' AND class='DECIDER VALIDATOR'";
            $data = DB::select($strsql);
            if (count($data) > 0) {
                $xMsg = $data[0]->msg_text;
                $xMsg =  str_replace("<Doc Type>", $data[0]->class . " / ", $xMsg);
                $xMsg =  str_replace("<Doc No>", $this->prHeader['prno'], $xMsg);
                $this->dispatchBrowserEvent('popup-success', [
                    'title' => $xMsg,
                ]);
            }

            //return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=auth");
        }

        public function validatorDeciderReject()
        {
            //ตรวจ Reason > pr_header.rejection_reason > del_val_workflow.status=DRAFT (all user) > Mail to Requestor
            if ($this->rejectReason) {
                DB::transaction(function() 
                {
                    //Update Status dec_val_workflow 
                    DB::statement("UPDATE dec_val_workflow SET status=?, reject_reason=?, completed_date=?, changed_by=?, changed_on=?
                        WHERE ref_doc_type=? AND ref_doc_id=? AND approver=?"
                    , ['40', $this->rejectReason, Carbon::now(), auth()->user()->id, Carbon::now(), '10', $this->prHeader['id']
                        ,auth()->user()->username]);

                    //Add Log dec_val_workflow_log
                    $strsql = "SELECT * FROM dec_val_workflow 
                        WHERE approver='" . auth()->user()->username . "' AND ref_doc_id=" . $this->prHeader['id'] . " AND ref_doc_type='10'";
                    $data = DB::select($strsql);

                    if ($data) {
                        DB::statement("INSERT INTO dec_val_workflow_log (seqno, approval_type, approver, status, refdoc_type, refdoc_no, refdoc_id
                            , reject_reason, submitted_date, completed_date, submitted_by, create_by, create_on)
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        ,[$data[0]->seqno, $data[0]->approval_type, $data[0]->approver, $data[0]->status, $data[0]->ref_doc_type, $data[0]->ref_doc_no
                            , $data[0]->ref_doc_id, $data[0]->reject_reason, $data[0]->submitted_date, $data[0]->completed_date, $data[0]->submitted_by
                            , auth()->user()->id, Carbon::now()]);
                    }

                    //Update Status pr_header, pr_item
                    DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=?
                        WHERE prno_id=?" 
                        , ['10', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);
                    
                    DB::statement("UPDATE pr_header SET status=?, changed_by=?, changed_on=?
                        WHERE id=?" 
                        , ['10', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);
                    
                    $this->prHeader['statusname'] = 'Planned'; //ใช้วิธีนี้เพราะไม่ต้องการ Redirect

                    //Send Mail
                    if (config('app.sendmail') == "Yes") {

                        //requester
                        $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                            WHERE id=" . $this->prHeader['requestor'];
                        $data = DB::select($strsql);
                        if ($data) {
                            $requester_fullname = $data[0]->fullname;
                            $requester_email = $data[0]->email;
                        }

                        //requested_for
                        $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                            WHERE id=" . $this->prHeader['requested_for'];
                        $data = DB::select($strsql);
                        if ($data) {
                            $requested_for_fullname = $data[0]->fullname;
                            $requested_for_email = $data[0]->email;
                        }

                        //approver
                        $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                            WHERE id=" . auth()->user()->id;
                        $data = DB::select($strsql);
                        if ($data) {
                            $approver_fullname = $data[0]->fullname;
                            $approver_email = $data[0]->email;
                        }
                        
                        //เนื้อหาใน Mail
                        if ($data) {
                            $detailMail = [
                                'template' => 'MAIL_PR03_Approval',
                                'subject' => 'You Purchase Requisition No ' . $this->prHeader['prno'] . ' has been rejected.',
                                'dear' => $requester_fullname,
                                'docno' => $this->prHeader['prno'],
                                'actionby' => $approver_fullname,
                                'reasons' => $this->rejectReason,
                                'link_url' => url('/purchaserequisitiondetails?mode=edit&prno=' . $this->prHeader['prno'] . '&tab=auth'),
                            ];

                            //Validate Email
                            $this->emailAddress['approver_email'] = $approver_email;
                            $this->emailAddress['requester_email'] = $requester_email;
                            $this->emailAddress['requested_for_email'] = $requested_for_email;
                    
                            $this->validate([
                                'emailAddress.*' => 'required|email',
                            ]);
                            
                            Mail::to($requester_email)->cc([$approver_email, $requested_for_email])->send(New WelcomeMail($detailMail));
                        }
                    }
                    //Send Mail End

                    $this->reset(['rejectReason']);
                    //return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=auth");
                });

            } else {
                $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='102' AND class='DECIDER VALIDATOR'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-alert', ['title' => $data[0]->msg_text]);
                }
            }
        }

        public function deleteValidator()
        {
            DB::transaction(function() 
            {
                DB::statement("DELETE FROM dec_val_workflow where ref_doc_type='10' AND ref_doc_id=" . $this->prHeader['id'] 
                    . " AND approval_type='VALIDATOR' AND approver=? " , [$this->deleteID]);

                DB::statement("DELETE FROM dec_val_workflow_log where refdoc_type='10' AND refdoc_id=" . $this->prHeader['id'] 
                    . " AND approval_type='VALIDATOR' AND approver=? " , [$this->deleteID]);
            });

            $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='105' AND class='DECIDER VALIDATOR'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $xMsg = $data[0]->msg_text;
                    $xMsg =  str_replace("<Doc Type>", $data[0]->class . " / ", $xMsg);
                    $xMsg =  str_replace("<Doc No>", $this->prHeader['prno'], $xMsg);
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => $xMsg,
                    ]);
                }
        }

        public function addValidator() 
        {
            //Validaate required field
            Validator::make($this->validator, [
                'username' => 'required',
            ])->validate();

            $myValidate = true;
            $maxValidator = 0;

            //ตรวจสอบว่าเลือก Validator หรือยัง
            if ($this->validator == ""){
                $this->dispatchBrowserEvent('popup-alert', [
                    'title' => 'Please Select Validator',
                ]);

                $myValidate = false;
            }

            //ตรวจสอบจำนวน Validator
            $strsql = "SELECT MAX(seqno) AS max_seq FROM dec_val_workflow WHERE approval_type='VALIDATOR' AND ref_doc_type='10' 
                AND ref_doc_id=" . $this->prHeader['id'];
            $data = DB::select($strsql);
            $maxSeq = $data[0]->max_seq; //เอาไปใช้สร้าง seqno ด้วย

            if ($maxValidator >= 10){
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='107' AND class='DECIDER VALIDATOR'";
                $data2 = DB::select($strsql);
                if (count($data2) > 0) {
                    $this->dispatchBrowserEvent('popup-alert', [
                        'title' => $data2[0]->msg_text,
                    ]);
                }

                $myValidate = false;
            };

            //14-03-2022 ตรวจสอบว่า Validator & Decider ซ้ำหรือไม่
            $strsql = "SELECT COUNT(*) AS val_count FROM dec_val_workflow 
                WHERE ref_doc_type='10' 
                AND ref_doc_id=" . $this->prHeader['id'] . " AND approver='" . $this->validator['username'] . "'";
            $data = DB::select($strsql);

            if ($data[0]->val_count > 0){
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='106' AND class='DECIDER VALIDATOR'";
                $data2 = DB::select($strsql);

                if (count($data2) > 0) {
                    $this->dispatchBrowserEvent('popup-alert', [
                        'title' => $data2[0]->msg_text,
                    ]);
                }

                $myValidate = false;
            };
            
            if ($myValidate){
                $maxSeq = $maxSeq + 1;

                DB::statement("INSERT INTO dec_val_workflow (seqno, approval_type, approver, status, ref_doc_type, ref_doc_no, ref_doc_id
                    , create_by, create_on)
                    VALUES(?,?,?,?,?,?,?,?,?)"
                ,[$maxSeq, 'VALIDATOR', $this->validator['username'], '10', '10', $this->prHeader['prno'], $this->prHeader['id']
                    , auth()->user()->id, Carbon::now()]);

                $this->reset(['validator']);
                $this->dispatchBrowserEvent('clear-select2');
            }
        }

        public function deleteDecider()
        {
            DB::transaction(function() 
            {
                DB::statement("DELETE FROM dec_val_workflow WHERE ref_doc_type='10' AND ref_doc_id=" . $this->prHeader['id'] . " AND approval_type='DECIDER' 
                    AND approver=? " , [$this->deleteID]);

                // 10-2-2022 ปิด Function ไว้ก่อน
                // DB::statement("DELETE FROM dec_val_workflow_log WHERE refdoc_type='10' AND refdoc_id=" . $this->prHeader['id'] . " AND approval_type='DECIDER' 
                //     AND approver=? " , [$this->deleteID]);
            });

            $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='105' AND class='DECIDER VALIDATOR'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $xMsg = $data[0]->msg_text;
                    $xMsg =  str_replace("<Doc Type>", $data[0]->class . " / ", $xMsg);
                    $xMsg =  str_replace("<Doc No>", $this->prHeader['prno'], $xMsg);
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => $xMsg,
                    ]);
                }
        }

        public function addDecider() 
        {
            $myValidate = true;

            //Validaate required field
            Validator::make($this->decider, [
                'username' => 'required',
            ])->validate();

            //14-03-2022 ตรวจสอบว่า Validator & Decider ซ้ำหรือไม่
            $strsql = "SELECT COUNT(*) AS val_count FROM dec_val_workflow 
                WHERE ref_doc_type='10' 
                AND ref_doc_id=" . $this->prHeader['id'] . " AND approver='" . $this->decider['username'] . "'";
            $data = DB::select($strsql);

            if ($data[0]->val_count > 0){
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='106' AND class='DECIDER VALIDATOR'";
                $data2 = DB::select($strsql);

                if (count($data2) > 0) {
                    $this->dispatchBrowserEvent('popup-alert', [
                        'title' => $data2[0]->msg_text,
                    ]);
                }
                $myValidate = false;
            };

            if ($this->decider == ""){
                $this->dispatchBrowserEvent('popup-alert', [
                    'title' => 'Please Select Decider',
                ]);

                $myValidate = false;
            }

            if ($myValidate){
                DB::statement("INSERT INTO dec_val_workflow (approval_type, approver, status, ref_doc_type, ref_doc_no, ref_doc_id, create_by, create_on)
                VALUES(?,?,?,?,?,?,?,?)"
                ,['DECIDER', $this->decider['username'], '10', '10', $this->prHeader['prno'], $this->prHeader['id'], auth()->user()->id, Carbon::now()]);

                $this->reset(['decider']);
                $this->dispatchBrowserEvent('clear-select2');
            }
        }
    //Authorization End

    //Delivery Plan
        public function deleteDeliveryPlan()
        {
            DB::transaction(function() 
            {
                DB::statement("DELETE FROM pr_delivery_plan where id=? " , [$this->deleteID]);
                
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='104' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => $data[0]->msg_text,
                    ]);                
                }
            });
            $this->reset(['deleteID', 'deleteType','prDeliveryPlan']);
        }

        public function addDeliveryPlan()
        {
            //Validate
            Validator::make($this->prDeliveryPlan, [
                'ref_prline_id' => 'required',
                'qty' => 'required|numeric|min:1|max:99999999.99', 
                'delivery_date' => 'required|date|date_format:Y-m-d|after:yesterday',
            ])->validate();

            //ตรวจสอบว่า QTY ที่จะ Add เกินกว่าที่เหลือหรือไม่
            if ($this->prDeliveryPlan['totalQtyPlanned'] + $this->prDeliveryPlan['qty'] <= $this->prDeliveryPlan['totalQty']) {
                DB::statement("INSERT INTO pr_delivery_plan (qty, delivery_date, ref_prno, ref_pr_id, ref_prline_id, create_by, create_on)
                VALUES(?,?,?,?,?,?,?)"
                ,[$this->prDeliveryPlan['qty'], $this->prDeliveryPlan['delivery_date'], $this->prHeader['prno'], $this->prHeader['id']
                , $this->prDeliveryPlan['ref_prline_id'], auth()->user()->id, Carbon::now()
                ]);
        
                $this->reset(['prDeliveryPlan']);
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
                $strsql = "SELECT SUM(qty) as sumqty FROM pr_delivery_plan WHERE ref_prline_id = " . $this->prDeliveryPlan['ref_prline_id'];
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
        public function clearAll()
        {
            $this->reset(['prItem']);

            //สร้างฟิลด์ใน Array 
            $this->prItem['budget_code'] = "";
            $this->prItem['snn_service'] = false; 
            $this->prItem['snn_production'] = false; 
            $this->prItem['reference_pr'] = ""; 
            $this->prItem['over_1_year_life'] = false;
            $this->prItem['remarks'] = "";
            $this->prItem['nominated_supplier'] = "";
            $this->prItem['brand'] = "";
            $this->prItem['model'] = "";
            $this->prItem['skip_rfq'] = false;
            $this->prItem['skip_doa'] = false;
    
            $this->dispatchBrowserEvent('clear-select2-modal');
        }

        public function closedModal()
        {
            $this->reset(['prItem']);
        }

        public function setDefaultSelect2InModelLineItem()
        {
            //31-01-22 
            if ($this->prItem){
                if ($this->prHeader['ordertype'] == '11' OR $this->prHeader['ordertype'] == '21'){

                    //purchase_unit-select2 (uomno)
                    $newOption = "<option value=' '>--- Please Select ---</option>";
                    $xPurchaseunit_dd = json_decode(json_encode($this->purchaseunit_dd), true);
                    foreach ($xPurchaseunit_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['uomno'] . "' ";
                        if ($row['uomno'] == $this->prItem['purchase_unit']) {
                            $newOption = $newOption . "selected='selected'";
                        }
                        $newOption = $newOption . ">" . $row['uomno'] . "</option>";
                    }
                    $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#purchase_unit-select2']);

                    //currency-select2
                    $newOption = "<option value=' '>--- Please Select ---</option>";
                    $xCurrency_dd = json_decode(json_encode($this->currency_dd), true);
                    foreach ($xCurrency_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['currency'] . "' ";
                        if ($row['currency'] == $this->prItem['currency']) {
                            $newOption = $newOption . "selected='selected'";
                        }
                        $newOption = $newOption . ">" . $row['currency'] . "</option>";
                    }
                    $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#currency-select2']);

                    //purchase_group-select2
                    $newOption = "<option value=' '>--- Please Select ---</option>";
                    $xPurchasegroup_dd = json_decode(json_encode($this->purchasegroup_dd), true);
                    foreach ($xPurchasegroup_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['groupno'] . "' ";
                        if ($row['groupno'] == $this->prItem['purchase_group']) {
                            $newOption = $newOption . "selected='selected'";
                        }
                        $newOption = $newOption . ">" . $row['groupno'] . ':' . $row['description'] . "</option>";
                    }
                    $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#purchase_group-select2']);

                    //internalorder-select2
                    $newOption = "<option value=' '>--- Please Select ---</option>";
                    $xInternal_order_dd = json_decode(json_encode($this->internal_order_dd), true);
                    foreach ($xInternal_order_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['internal_order'] . "' ";
                        if ($row['internal_order'] == $this->prItem['internal_order']) {
                            $newOption = $newOption . "selected='selected'";
                        }
                        $newOption = $newOption . ">" . $row['internal_order'] . "</option>";
                    }
                    $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#internalorder-select2']);

                    //budgetcode-select2
                    // $newOption = "<option value=' '>--- Please Select ---</option>";
                    $newOption = "";
                    $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                    foreach ($xBudgetcode_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                        if ($row['account'] == $this->prItem['budget_code']) {
                            $newOption = $newOption . "selected='selected'";
                        }
                        $newOption = $newOption . ">" . $row['account'] . ' : ' . $row['description'] . "</option>";
                    }
                    $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#budgetcode-select2']);

                } else if (($this->prHeader['ordertype'] == '10' OR $this->prHeader['ordertype'] == '20') OR $this->prHeader['ordertype'] == '30'){
                    //partno
                    $newOption = "<option value=' '>--- Please Select ---</option>";
                    $xpartno_dd = json_decode(json_encode($this->partno_dd), true);
                    foreach ($xpartno_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['partno'] . "' ";
                        if ($row['partno'] == $this->prItem['partno']) {
                            $newOption = $newOption . "selected='selected'";
                        }
                        $newOption = $newOption . ">" . $row['partno'] . " : " . $row['part_name'] . "</option>";
                    }
                    $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#partno-select2']);

                    //internalorder-select2
                    $newOption = "<option value=' '>--- Please Select ---</option>";
                    $xInternal_order_dd = json_decode(json_encode($this->internal_order_dd), true);
                    foreach ($xInternal_order_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['internal_order'] . "' ";
                        if ($row['internal_order'] == $this->prItem['internal_order']) {
                            $newOption = $newOption . "selected='selected'";
                        }
                        $newOption = $newOption . ">" . $row['internal_order'] . "</option>";
                    }
                    $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#internalorder-select2']);

                    //budgetcode-select2
                    //$newOption = "<option value=' '>--- Please Select ---</option>";
                    $newOption = "";
                    $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                    foreach ($xBudgetcode_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                        if ($row['account'] == $this->prItem['budget_code']) {
                            $newOption = $newOption . "selected='selected'";
                        }
                        $newOption = $newOption . ">" . $row['account'] . ' : ' . $row['description'] . "</option>";
                    }
                    $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#budgetcode-select2']);
                }
            }

            $this->skipRender();
        }

        public function editLineItem($lineItemId)
        {
            //Get Line Item
            $this->isCreateLineItem = false;

            //การ Disable aelect2 ยังไม่ได้ใช้ตอนนี้
            //$this->dispatchBrowserEvent('disable-partno-select2');

            $strsql = "SELECT a.id, a.[lineno], a.partno, a.description, a.purchase_unit, a.unit_price, a.currency, a.exchange_rate
                , a.purchase_group, a.account_group, a.qty, a.internal_order, FORMAT(a.req_date,'yyyy-MM-dd') AS req_date, a.budget_code, a.over_1_year_life
                , a.snn_service, a.snn_production, b.supplier AS nominated_supplier, b.name1 + ' ' + b.name2 AS nominated_supplier_name, a.remarks
                , a.skip_rfq, a.skip_doa, a.final_price
                , FORMAT(a.quotation_expiry_date,'yyyy-MM-dd') AS quotation_expiry_date, a.reference_pr, c.min_order_qty, c.supplier_lead_time
                , d.status + ':' + d.description AS status_des, d.status, a.nonstock_control
                FROM pr_item a
                LEFT JOIN supplier b ON b.supplier = a.nominated_supplier
                LEFT JOIN part_master c ON c.partno = a.partno
                LEFT JOIN pr_status d ON d.status = a.status
                WHERE a.id ='" . $lineItemId . "'";
            $data = DB::select($strsql);
            if (count($data)) {
                $this->prItem = collect($data[0]);
                if ($this->orderType == "10" or $this->orderType == "20" ) {
                    $this->dispatchBrowserEvent('show-modelPartLineItem');
                } else if ($this->orderType == "11" or $this->orderType == "21") {
                    $this->dispatchBrowserEvent('show-modelExpenseLineItem');
                }

                $this->prItem['skip_rfq'] = tinyToBoolean($this->prItem['skip_rfq']);
                $this->prItem['skip_doa'] = tinyToBoolean($this->prItem['skip_doa']);
                $this->prItem['over_1_year_life'] = tinyToBoolean($this->prItem['over_1_year_life']);

                $this->prItem['snn_service'] = bitToRedio($this->prItem['snn_service']);
                $this->prItem['snn_production'] = bitToRedio($this->prItem['snn_production']);

                // $this->prItem['unit_price'] = round($this->prItem['unit_price'], 2);

                //ต้องเป็น Array เพราะต้องใช้ FUnction Validation
                $this->prItem = json_decode(json_encode($this->prItem), true);
            }

            //16-03-2022 Bind ค่าใน Budget Code
            if ($this->orderType == "10" or $this->orderType == "20" ) {
                //ถ้าเป็น Part
                $strsql = "SELECT ISNULL(gl_account,'') AS gl_account FROM part_master WHERE partno='" . $this->prItem['partno'] . "'";
                $data = DB::select($strsql);

                if ($data) {
                    if ($data[0]->gl_account) {
                        //ถ้า Item มี Set gl_account
                        $this->prItem['budget_code'] = $data[0]->gl_account;
                        $strsql = "SELECT account, description FROM gl_master 
                            WHERE company='" . $this->prHeader['company'] . "' 
                            AND account = '" . $data[0]->gl_account . "'";
                        $this->budgetcode_dd = DB::select($strsql);
            
                        $newOption = "";
                        $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                        foreach ($xBudgetcode_dd as $row) {
                            $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                            $newOption = $newOption . ">" . $row['account'] . ' : ' . $row['description'] . "</option>";
                        }
            
                        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#budgetcode-select2']);

                    } else {
                        //ถ้า Item ไม่มี gl_account ให้หาจาก gl_mapping_noninv
                        $this->budgetcode_dd = [];
                        $strsql = "SELECT account, description FROM gl_master 
                                WHERE category IN ('Noninventory', 'Asset Noninventory')
                                AND company = '" . $this->prHeader['company'] . "'
                                AND type IN (SELECT account_type FROM gl_mapping_noninv WHERE cost_center='" . $this->prHeader['cost_center'] . "')";
                        $this->budgetcode_dd = DB::select($strsql);

                        $newOption = "";
                        $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                        foreach ($xBudgetcode_dd as $row) {
                            $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                            $newOption = $newOption . ">" . $row['account'] . ' : ' . $row['description'] . "</option>";
                        }

                        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#budgetcode-select2']);
                    }

                }

            } else if ($this->orderType == "11" or $this->orderType == "21") {
                    //ถ้า Item เป็น Free Text
                    $this->budgetcode_dd = [];
                    $strsql = "SELECT account, description FROM gl_master 
                            WHERE category IN ('Noninventory', 'Asset Noninventory')
                            AND company = '" . $this->prHeader['company'] . "'
                            AND type IN (SELECT account_type FROM gl_mapping_noninv WHERE cost_center='" . $this->prHeader['cost_center'] . "')";
                    $this->budgetcode_dd = DB::select($strsql);

                    $newOption = "";
                    $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                    foreach ($xBudgetcode_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                        $newOption = $newOption . ">" . $row['account'] . ' : ' . $row['description'] . "</option>";
                    }

                    $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#budgetcode-select2']);
            }

 
            $this->setDefaultSelect2InModelLineItem();
        }

        public function deleteLineItem()
        {
            //???ต้องลบ Delivery Plan ออกด้วย

            DB::transaction(function() 
            {
                DB::statement("UPDATE pr_item SET deletion_flag = 1 WHERE id = " . $this->prItem['id'] ); //19-03-2022
                DB::statement("UPDATE pr_delivery_plan SET deletion_flag = 1 WHERE ref_prline_id=" . $this->prItem['id'] ); //19-03-2022
                

                //Histroy Log
                $this->writeItemHistoryLog($this->prItem['id'],"DELETE");
                //Histroy Log
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='104' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => $data[0]->msg_text,
                    ]);                
                }

            });

            $this->reset(['prItem']);
            $this->dispatchBrowserEvent('hide-modelPartLineItem');
            $this->dispatchBrowserEvent('hide-modelExpenseLineItem');
        }

        public function saveLineItem()
        {
            //Validaate required field
            if ($this->prHeader['ordertype'] == '10' OR $this->prHeader['ordertype'] == '20' OR $this->prHeader['ordertype'] == '30') {
                Validator::make($this->prItem, [
                    'partno' => 'required',
                    'description' => 'required',
                    'qty' => 'required|numeric|min:1|max:99999999.99', 
                    'req_date' => 'required|date|date_format:Y-m-d|after:yesterday',
                    'budget_code' => 'required',
                ])->validate();
            } else if ($this->prHeader['ordertype'] == '11' OR $this->prHeader['ordertype'] == '21') {
                Validator::make($this->prItem, [
                    'description' => 'required',
                    'purchase_unit' => 'required',
                    'currency' => 'required',
                    'exchange_rate' => 'required',
                    'purchase_unit' => 'required',
                    'unit_price' => 'required|numeric|min:1|max:99999999.99',
                    'qty' => 'required|numeric|min:1|max:99999999.99',
                    'req_date' => 'required|date|date_format:Y-m-d|after:yesterday',
                    'budget_code' => 'required',
                ])->validate();
            }


            //2022-01-30 > Add Validate
            $xValidate = true;
            //IF PR ORDER TYPE = STANDARD PARTS or BLANKET PARTS AND (PR ITEM.Req Date - Current Date) < PART.Lead Time
            //(Ref. P2P-PUR-001-FS-Purchase Requisition_(2022-01-28))

            $interval = Carbon::now()->diff($this->prItem['req_date']);
            $days = $interval->format('%a');
            if (($this->prHeader['ordertype'] == '10' OR $this->prHeader['ordertype'] == '20' OR $this->prHeader['ordertype'] == '30') 
                AND $days < $this->prItem['supplier_lead_time'] )
            {
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='115' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-alert', [
                        'title' => $data[0]->msg_text,
                    ]);
                }
                $xValidate = false;
            }

            //2022-01-30 IF PR ORDER TYPE = STANDARD PARTS or BLANKET PARTS AND QTY < minimum order quantity 
            //(Ref. P2P-PUR-001-FS-Purchase Requisition_(2022-01-28))
            
            if (($this->prHeader['ordertype'] == '10' OR $this->prHeader['ordertype'] == '20' OR $this->prHeader['ordertype'] == '30') 
                AND $this->prItem['qty'] < $this->prItem['min_order_qty'] )
            {
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='114' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-alert', [
                        'title' => str_replace("<PART.MOQ>", $this->prItem['min_order_qty'], $data[0]->msg_text),
                    ]);
                }
                $xValidate = false;
            }

            if ($xValidate){
                //08-03-22
                $blanket_order_type = "";                
                if ($this->isBlanket == true) {
                    if ($this->prItem['qty'] == 1) {
                        $blanket_order_type  = "Amount-Based";
                    } else if ($this->prItem['qty'] > 1)  {
                        $blanket_order_type  = "Quantity-Based";
                    }
                }

                if ($this->isCreateLineItem) {
                    DB::transaction(function () use ($blanket_order_type) {
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

                        //03-03-22 ชั่วคร่าวเพราะ Abeam ยังตกลงเรื่องนี้ไม่ได้ (สรุปว่าไม่ได้ใช้งาน)
                        $this->prItem['purchase_group'] = "";
                        $this->prItem['account_group'] = "";

                        DB::statement("INSERT INTO pr_item (prno, prno_id, [lineno], partno, description, purchase_unit, unit_price, unit_price_local
                            , currency, exchange_rate, purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                            ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, blanket_order_type, status, nonstock_control
                            , create_by, create_on)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                            ,[$this->prHeader['prno'], $this->prHeader['id'], $lineno, $this->prItem['partno'], $this->prItem['description']
                            , $this->prItem['purchase_unit'], $this->prItem['unit_price'], $this->prItem['unit_price'] * $this->prItem['exchange_rate']
                            , $this->prItem['currency'], $this->prItem['exchange_rate']
                            , $this->prItem['purchase_group'], $this->prItem['account_group'], $this->prItem['qty']
                            , $this->prItem['req_date'], $this->prItem['internal_order'] ,$this->prItem['budget_code'], $this->prItem['over_1_year_life']
                            , radioToBit($this->prItem['snn_service']), radioToBit($this->prItem['snn_production']) ,$this->prItem['nominated_supplier'], $this->prItem['remarks']
                            , $this->prItem['skip_rfq'], $this->prItem['skip_doa'], $this->prItem['reference_pr'], $blanket_order_type 
                            , "10", $this->prItem['nonstock_control'] ,auth()->user()->id, Carbon::now()
                            ]);

                        //History log
                        $idPrItemHistroy = DB::getPdo()->lastInsertId();
                        $obj = DB::table('pr_item_history')->select('id_original')->where('id','=',$idPrItemHistroy)->first();
                        if($obj != null){
                            $idPrHeader = $obj->id_original;
                            $this->writeItemHistoryLog($idPrHeader,"INSERT");
                        }
                        
                        //End histroy log
        
                        $strsql = "SELECT msg_text FROM message_list WHERE msg_no='111' AND class='PURCHASE REQUISITION'";
                        $data = DB::select($strsql);
                        if (count($data) > 0) {
                            $this->dispatchBrowserEvent('popup-success', [
                                'title' => str_replace("<PR Line No.>", $lineno, $data[0]->msg_text),
                            ]);
                        }
                    });
    
                    $this->reset(['prItem']);
                    $this->dispatchBrowserEvent('hide-modelPartLineItem');
                    $this->dispatchBrowserEvent('hide-modelExpenseLineItem');
                }else{
                    //Assign ค่าให้ Partno, account_group กรณีเป็น Free Text
                    if ($this->orderType == "11" or $this->orderType == "21"){
                        $this->prItem['partno'] = "";
                        $this->prItem['account_group'] = "";
                    }
    
                    DB::statement("UPDATE pr_item SET partno=?, description=?, purchase_unit=?, unit_price=?, unit_price_local=?, currency=?, exchange_rate=?
                        ,purchase_group=?, account_group=?, qty=?, req_date=?, internal_order=?, budget_code=?, over_1_year_life=?, snn_service=?
                        ,snn_production=?, nominated_supplier=?, remarks=?, skip_rfq=?, skip_doa=?, reference_pr=?, blanket_order_type=?, nonstock_control=?
                        , changed_by=?, changed_on=?
                        WHERE id=?"
                    , [
                        $this->prItem['partno'], $this->prItem['description'], $this->prItem['purchase_unit'], $this->prItem['unit_price']
                        , $this->prItem['unit_price'] * $this->prItem['exchange_rate']
                        , $this->prItem['currency'], $this->prItem['exchange_rate'], $this->prItem['purchase_group'], $this->prItem['account_group']
                        , $this->prItem['qty'], $this->prItem['req_date'], $this->prItem['internal_order'] ,$this->prItem['budget_code']
                        , $this->prItem['over_1_year_life'], radioToBit($this->prItem['snn_service']), radioToBit($this->prItem['snn_production']) ,$this->prItem['nominated_supplier']
                        , $this->prItem['remarks'], $this->prItem['skip_rfq'], $this->prItem['skip_doa'], $this->prItem['reference_pr']
                        , $blanket_order_type, $this->prItem['nonstock_control'], auth()->user()->id, Carbon::now(), $this->prItem['id']
                    ]);

                    //Histroy Log
                    $this->writeItemHistoryLog($this->prItem['id'],"UPDATE");
                    //Histroy Log

                    $strsql = "SELECT msg_text FROM message_list WHERE msg_no='111' AND class='PURCHASE REQUISITION'";
                    $data = DB::select($strsql);
                    if (count($data) > 0) {
                        $this->dispatchBrowserEvent('popup-success', [
                            'title' => str_replace("<PR Line No.>", $this->prItem['lineno'], $data[0]->msg_text),
                        ]);
                    }

                    $this->reset(['prItem']);
                    $this->dispatchBrowserEvent('hide-modelPartLineItem');
                    $this->dispatchBrowserEvent('hide-modelExpenseLineItem');
                }
            }
        }

        public function updatedPrItemCurrency()
        {
            if ($this->prItem['currency'] != " ") {
                //$strsql = "SELECT ratio_from FROM currency_trans_ratios WHERE from_currency='" . $this->prItem['currency'] . "'";
                $strsql = "SELECT exchange_rate FROM currency_exchange_rate WHERE from_currency='" . $this->prItem['currency'] . "'";
                $data = DB::select($strsql);
                if ($data) {
                    $this->prItem['exchange_rate'] = $data[0]->exchange_rate;
                }
            }
        }

        public function updatedPrItemPartno() 
        {
            $strsql = "SELECT a.partno, a.part_name, a.purchase_uom, a.purchase_group, ISNULL(a.account_group,'') AS account_group, a.brand
                    , a.model, a.skip_rfq, a.skip_doa, a.primary_supplier, a.base_price, a.final_price, a.currency, b.exchange_rate
                    , a.min_order_qty, a.supplier_lead_time, a.supplier_id, ISNULL(a.gl_account,'') AS gl_account
                    FROM part_master a
                    LEFT JOIN currency_exchange_rate b ON a.currency = b.from_currency
                    WHERE partno = '" . $this->prItem['partno'] . "'";
            $data = DB::select($strsql);

            if ($data) {
                if ($data[0]->min_order_qty) {
                    $this->prItem['description'] = $data[0]->part_name;
                    $this->prItem['purchase_unit'] = $data[0]->purchase_uom;
                    $this->prItem['purchase_group'] = $data[0]->purchase_group;
                    $this->prItem['account_group'] = $data[0]->account_group;
                    $this->prItem['brand'] = $data[0]->brand;
                    $this->prItem['model'] = $data[0]->model;
                    $this->prItem['skip_rfq'] = boolval($data[0]->skip_rfq);
                    $this->prItem['skip_doa'] = boolval($data[0]->skip_doa);
                    $this->prItem['currency'] = $data[0]->currency;
                    $this->prItem['exchange_rate'] = round($data[0]->exchange_rate,2);
                    $this->prItem['nominated_supplier'] = $data[0]->supplier_id;
                    $this->prItem['min_order_qty'] = $data[0]->min_order_qty;
                    $this->prItem['supplier_lead_time'] = $data[0]->supplier_lead_time;

                    //On Part Select ข้อ 1.2 Copy Pricing value based on the following condition:
                    if ($this->prItem['account_group'] <> '' AND $this->prItem['skip_rfq'] == true ){
                        $this->prItem['unit_price'] = round($data[0]->final_price,2);

                    }else{
                        $this->prItem['unit_price'] = round($data[0]->base_price,2);
                    }

                    if ($data[0]->gl_account <> ''){

                        //16-03-22 non stock control
                        $strsql = "SELECT id FROM gl_mapping_nonstockcontrol
                                WHERE company = '" . $this->prHeader['company'] . "'
                                AND notin_costcenter = '" . $this->prHeader['cost_center'] . "'
                                AND gl_account = '" . $data[0]->gl_account ."'
                                AND isactive=1";
                        //$this->prItem['nonstock_control'] = true;
                        $data2 = DB::select($strsql);
                        if ($data2) {
                            $this->prItem['nonstock_control'] = false;
                            $newOption = "<input class='form-check-input' type='checkbox' checked wire:model.defer='prItem.nonstock_control' disabled>";
                            $this->dispatchBrowserEvent('bindToCheckbox', ['newOption' => $newOption, 'selectName' => '#nonstock_control']);

                        } else {
                            $this->prItem['nonstock_control'] = true;
                            $newOption = "<input class='form-check-input' type='checkbox' wire:model.defer='prItem.nonstock_control' disabled>";
                            $this->dispatchBrowserEvent('bindToCheckbox', ['newOption' => $newOption, 'selectName' => '#nonstock_control']);
                        }

                        //16-03-22 หา Budget Code
                        $this->prItem['budget_code'] = $data[0]->gl_account;
                        $strsql = "SELECT account, description FROM gl_master 
                            WHERE company='" . $this->prHeader['company'] . "' 
                            AND account = '" . $data[0]->gl_account . "'";
                        $this->budgetcode_dd = DB::select($strsql);

                        $newOption = "";
                        $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                        foreach ($xBudgetcode_dd as $row) {
                            $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                            $newOption = $newOption . ">" . $row['account'] . ' : ' . $row['description'] . "</option>";
                        }

                        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#budgetcode-select2']);

                    }else{
                        //16-03-22 non stock control
                        $this->prItem['nonstock_control'] = true;

                        //16-03-22 ถ้า Item ไม่มี gl_account ให้หาจาก gl_mapping_noninv
                        $this->budgetcode_dd = [];
                        $strsql = "SELECT account, description FROM gl_master 
                                WHERE category IN ('Noninventory', 'Asset Noninventory')
                                AND company = '" . $this->prHeader['company'] . "'
                                AND type IN (SELECT account_type FROM gl_mapping_noninv WHERE cost_center='" . $this->prHeader['cost_center'] . "')";
                        $this->budgetcode_dd = DB::select($strsql);

                        $newOption = "<option value=' '>--- Please Select ---</option>";
                        $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                        foreach ($xBudgetcode_dd as $row) {
                            $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                            $newOption = $newOption . ">" . $row['account'] . ' : ' . $row['description'] . "</option>";
                        }

                        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#budgetcode-select2']);
                    }
                }else{

                    $this->dispatchBrowserEvent('popup-alert', ['title' => "Minimum order quantity in part master not config"]);
                }
            }
        }
    //Line Item End

    // ไม่ Work กรณีกด Modal แล้วมันจะกลับมา Enable
    // public function disablePRHeader()
    // {
    //     if ($this->prHeader['status'] >= '30') {
    //         $this->dispatchBrowserEvent('prheader-disable');
    //     }
    // }

    public function showAddItem()
    {
        $this->isCreateLineItem = true;

        $this->reset(['prItem']);

        //สร้างฟิลด์ใน Array  
        $this->prItem['budget_code'] = "";
        $this->prItem['snn_service'] = false; 
        $this->prItem['snn_production'] = false; 
        $this->prItem['reference_pr'] = ""; 
        $this->prItem['over_1_year_life'] = false;
        $this->prItem['remarks'] = "";
        $this->prItem['nominated_supplier'] = "";
        $this->prItem['brand'] = "";
        $this->prItem['model'] = "";
        $this->prItem['skip_rfq'] = false;
        $this->prItem['skip_doa'] = false;
        //$this->prItem['req_date'] = " "; //10-03-22 Test For fixed calendar

        $this->dispatchBrowserEvent('clear-select2-modal');

        if ($this->orderType == "10" or $this->orderType == "20" ) {
            //Clear ค่าใน Dropdown
            $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => "", 'selectName' => '#budgetcode-select2']);
            $this->dispatchBrowserEvent('show-modelPartLineItem');

        } else if ($this->orderType == "11" or $this->orderType == "21") {
            //16-03-22 Non Stock Control
            $this->prItem['nonstock_control'] = true;

            $this->dispatchBrowserEvent('show-modelExpenseLineItem');

            //16-03-2022 ถ้าเป็น Free Text ให้หา Budget Code จาก gl_mapping_noninv
            $strsql = "SELECT account, description FROM gl_master 
                    WHERE category IN ('Noninventory', 'Asset Noninventory')
                    AND company = '" . $this->prHeader['company'] . "'
                    AND type IN (SELECT account_type FROM gl_mapping_noninv WHERE cost_center='" . $this->prHeader['cost_center'] . "')";
            $this->budgetcode_dd = DB::select($strsql);

            $newOption = "<option value=' '>--- Please Select ---</option>";
            $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
            foreach ($xBudgetcode_dd as $row) {
                $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                $newOption = $newOption . ">" . $row['account'] . ' : ' . $row['description'] . "</option>";
            }

            $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#budgetcode-select2']);
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
        $newOption = "<option value=' '>--- Please Select ---</option>";
        foreach ($xBuyer_dd as $row) {
            $newOption = $newOption . "<option value='" . $row['username'] . "' ";
            if ($row['username'] == $this->prHeader['buyer']) {
                $newOption = $newOption . "selected='selected'";
            }
            $newOption = $newOption . ">" . $row['fullname'] . "</option>";
        }
        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#buyer-select2']);

        $this->skipRender();

    }

    public function revokePrHeader()
    {
        //26-2-22 Change for 'P2P-PR Process for UAT 20220224.pptx'
        DB::transaction(function() 
        {
            DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=? where prno_id=?"
            , ['10',auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

            DB::statement("UPDATE pr_header SET status=?, changed_by=?, changed_on=? where id=?"
            , ['10',auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

            $this->prHeader['statusname'] = 'Planned'; //ใช้วิธีนี้เพราะไม่ต้องการ Redirect
        });

        $strsql = "SELECT msg_text FROM message_list WHERE msg_no='110' AND class='PURCHASE REQUISITION'";
        $data = DB::select($strsql);
        if (count($data) > 0) {
            $this->dispatchBrowserEvent('popup-success', [
                'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
            ]);
        }

        return redirect("purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
    }

    public function editPR()
    {
        //PR Header
        $strsql = "SELECT prh.id, prh.prno, ort.description AS ordertypename
                , isnull(req.name,'') + ' ' + isnull(req.lastname,'') AS requestor_name, prh.requestor_ext AS extention, prh.requestor_phone AS phone
                , prh.requested_for, prh.requested_for_email AS email_reqf, prh.requested_for_ext AS extention_reqf
                , prh.requested_for_phone AS phone_reqf
                , prh.company, company.name AS company_name, prh.site, prh.site + ' : ' + site.site_description AS site_description, prh.functions, prh.department, prh.division, prh.section
                , prh.cost_center, cc.description AS costcenter_desc
                , prh.buyer, prh.delivery_address, prh.delivery_location, prh.delivery_site, prh.budget_year, prh.purpose_pr, prh.capexno
                , FORMAT(prh.request_date,'yyy-MM-dd') AS request_date                    
                , pr_status.description AS statusname, FORMAT(prh.valid_until,'yyy-MM-dd') AS valid_until, prh.days_to_notify, prh.notify_below_10
                , prh.notify_below_25, prh.notify_below_35, prh.ordertype, prh.requestor, prh.status
                FROM pr_header prh
                LEFT JOIN order_type ort ON ort.ordertype=prh.ordertype
                LEFT JOIN users req ON req.id=prh.requestor
                LEFT JOIN users reqf ON reqf.id=prh.requested_for
                LEFT JOIN pr_status ON pr_status.status=prh.status
                LEFT JOIN company ON company.company=prh.company
                LEFT JOIN cost_center cc ON cc.cost_center=prh.cost_center 
                LEFT JOIN (SELECT site, site_description FROM site WHERE SUBSTRING(address_id, 7, 2)='EN') site ON site.site=prh.site
                WHERE prh.prno ='" . $this->editPRNo . "'";
        $data = DB::select($strsql);

        if (count($data)) {
            $this->prHeader = collect($data[0]);
            $this->prHeader['notify_below_10'] = boolval($this->prHeader['notify_below_10']);
            $this->prHeader['notify_below_25'] = boolval($this->prHeader['notify_below_25']);
            $this->prHeader['notify_below_35'] = boolval($this->prHeader['notify_below_35']);
            $this->prHeader['company'] = $data[0]->company;
            
            $this->orderType = $this->prHeader['ordertype'];

            if ($this->prHeader['ordertype'] == "20" OR $this->prHeader['ordertype'] == "21"){
                $this->isBlanket = true;
            }

            //ถ้าไม่เป็น Array จะใช้ Valdation ไม่ได้
            $this->prHeader = json_decode(json_encode($this->prHeader), true);
        }
        //PR Header End ***ที่เป็น Tab ย้ายไปอยู่ที่ Render

        //ตรวจสอบว่าเป็น Buyer หรือไม่
        $strsql = "SELECT username FROM buyer WHERE username='" . auth()->user()->username . "'";
        $data = DB::select($strsql);
        if (count($data) > 0) {
            $this->isBuyer = true;
        } else {
            $this->isBuyer = false;
        }

        //ตรวจสอบว่าเป็น Requester หรือ RequestedFor หรือไม่
        $strsql = "SELECT requested_for, requestor FROM pr_header WHERE id=" . $this->prHeader['id'] . " 
                AND (requested_for=" . auth()->user()->id . " OR requestor=" . auth()->user()->id . ")";
        $data = DB::select($strsql);
        if ($data) {
            $this->isRequester_RequestedFor = true;
        } else {
            $this->isRequester_RequestedFor = false;
        }

        //Authorization
        //ตรวจสอบว่าเป็น Validator หรือ Decider หรือไม่
        $strsql = "SELECT approver FROM dec_val_workflow WHERE ref_doc_type='10' AND ref_doc_id=" . $this->prHeader['id'] . " 
            AND approver = '" . auth()->user()->username . "'";
        $data = DB::select($strsql);
        if ($data) {
            $this->isValidator_Decider = true;
        }
        //Authorization End
    }

    public function updatedprHeaderDeliveryAddress()
    {
        $strsql = "SELECT site FROM site WHERE address_id='" . $this->prHeader['delivery_address'] . "'";
        $data = DB::select($strsql);
            if (count($data)) {
                $this->prHeader['delivery_location'] = $this->prHeader['delivery_address'];
                $this->prHeader['delivery_site'] = $data[0]->site;
            }
    }

    public function updatedprHeaderCostCenter()
    {
        $strsql = "SELECT description FROM cost_center WHERE cost_center='" . $this->prHeader['cost_center'] . "'";
        $data = DB::select($strsql);
            if (count($data)) {
                $this->prHeader['costcenter_desc'] = $data[0]->description;
            }
    }

    public function updatedprHeaderRequestedFor() 
    {
        if ($this->prHeader['requested_for'] != " " AND $this->prHeader['requested_for'] != "") {
            $strsql = "SELECT usr.company, usr.department, usr.site, usr.functions, usr.division, usr.section, usr.cost_center
                        , usr.email, usr.extention, cost.description AS costcenter_desc
                        , CASE 
                            WHEN ISNULL(mobile,'') = '' THEN phone
                            ELSE mobile
                          END AS phone
                        FROM users usr
                        LEFT JOIN company com ON com.company = usr.company
                        LEFT JOIN cost_center cost ON cost.cost_center = usr.cost_center
                        WHERE usr.id='" . $this->prHeader['requested_for'] . "'";
            $data = DB::select($strsql);
            if (count($data)) {
                $this->prHeader['company'] = $data[0]->company;
                $this->prHeader['site'] = $data[0]->site;
                $this->prHeader['functions'] = $data[0]->functions;
                $this->prHeader['department'] = $data[0]->department;
                $this->prHeader['division'] = $data[0]->division;
                $this->prHeader['section'] = $data[0]->section;
                $this->prHeader['email_reqf'] = $data[0]->email;
                $this->prHeader['phone_reqf'] = $data[0]->phone;
                $this->prHeader['extention_reqf'] = $data[0]->extention;
                $this->prHeader['cost_center'] = $data[0]->cost_center;
                $this->prHeader['costcenter_desc'] = $data[0]->costcenter_desc;
            }

            //9-2-2022 Update partno for CR No.10
            $this->loadDD_partno_dd(true);
            $this->loadDD_buyer_dd(true);    
            
            //18-3-2022
            $this->decider_dd = [];
            $this->validator_dd = [];
            $strsql = "SELECT usr.username, usr.name + ' ' + usr.lastname AS fullname FROM users usr
                        JOIN user_roles uro ON uro.username = usr.username
                        WHERE uro.role_id='10' 
                            AND (usr.id <> " . $this->prHeader['requestor'] . " AND usr.id <> " . $this->prHeader['requested_for'] . ")
                        ORDER BY usr.username";
            $this->decider_dd = DB::select($strsql);

            if ($this->decider_dd){
                $newOption = "<option value=' '>--- Please Select ---</option>";
                $xdecider_dd = json_decode(json_encode($this->decider_dd), true);
                foreach ($xdecider_dd as $row) {
                    $newOption = $newOption . "<option value='" . $row['username'] . "'>" . $row['fullname'] . "</option>";
                }

                $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#decider-select2']);
                $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#validator-select2']);
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
            $this->prHeader['extention'] = "";
            $this->prHeader['cost_center'] = "";
            $this->prHeader['costcenter_desc'] = "";
        }
    }

    public function getNewPrNo()
    {
        $newPRNo = "";

        //หาว่าอยู่ FY ไหน
        $ํYearNow = date_format(Carbon::now(), 'Y');
        $xEndFiscalYear = $ํYearNow . '-03-31';
        if (date_format(Carbon::now(), 'Y-m-d') <= $xEndFiscalYear) {
            $FY = strval($ํYearNow - 1);
        } else {
            $FY = strval($ํYearNow);
        }

        if ($this->orderType == '10' OR $this->orderType == '11'){
            $strsql = "SELECT lastnumber FROM tran_type_number WHERE tran_type='PR' 
                    AND calendar_year='" . $FY . "' AND last_calendar_year='". $FY . "'";
            
            $data = DB::select($strsql);

            if ($data){
                DB::statement("UPDATE tran_type_number SET lastnumber=?, changed_by=?, changed_on=? 
                            WHERE tran_type=? AND calendar_year=? AND last_calendar_year=?"
                    , [$data[0]->lastnumber + 1, auth()->user()->id, Carbon::now()
                    , 'PR', $FY, $FY]);

                    $newPRNo = 'PR' . substr($FY, 2, 2) . sprintf("%05d", $data[0]->lastnumber + 1);
            }

        }else if ($this->orderType == '20' OR $this->orderType == '21'){
            $strsql = "SELECT lastnumber FROM tran_type_number WHERE tran_type='BPR' 
                    AND calendar_year='" . $FY . "' AND last_calendar_year='". $FY . "'";
            $data = DB::select($strsql);

            if ($data){
                DB::statement("UPDATE tran_type_number SET lastnumber=?, changed_by=?, changed_on=? 
                            WHERE tran_type=? AND calendar_year=? AND last_calendar_year=?"
                    , [$data[0]->lastnumber + 1, auth()->user()->id, Carbon::now()
                    , 'BPR', $FY, $FY]);

                    $newPRNo = 'BPR' . substr($FY, 2, 2) . sprintf("%05d", $data[0]->lastnumber + 1);
            }
        }

        return $newPRNo;
    }

    public function createPrHeader()
    {
        //Header
        $this->prHeader['prno'] = "";
        $this->prHeader['statusname'] = "";
        $this->prHeader['ordertype'] = $_GET['ordertype'];

        $strsql = "SELECT description as ordertypename FROM order_type WHERE ordertype='" . $this->prHeader['ordertype'] . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['ordertypename'] = $data[0]->ordertypename;
        }

        $this->prHeader['status'] = "01";
        $this->prHeader['statusname'] = "Draft";

        //requestor
        $this->prHeader['requestor'] = auth()->user()->id;

        $strsql = "SELECT id AS requestor, isnull(name,'') + ' ' + isnull(lastname,'') AS requestor_name 
                    FROM users WHERE id=" . auth()->user()->id;
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['requestor_name'] = $data[0]->requestor_name;
        }
        $this->prHeader['phone'] =  auth()->user()->phone;
        $this->prHeader['extention'] =  auth()->user()->extention;
        $this->prHeader['request_date'] = date_format(Carbon::now(), 'Y-m-d');
        $this->prHeader['email'] =  auth()->user()->email;


        //Requested For
        $this->prHeader['requested_for'] = auth()->user()->id;
        $this->prHeader['phone_reqf'] = auth()->user()->phone;
        $this->prHeader['extention_reqf'] = auth()->user()->extention;
        $this->prHeader['email_reqf'] = auth()->user()->email;

        $strsql = "SELECT TOP 1 address_id, site, SUBSTRING(address,1,30) AS address FROM site WHERE company='" . auth()->user()->company . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['delivery_address'] = $data[0]->address_id;
            $this->prHeader['delivery_location'] = $data[0]->address_id;
            $this->prHeader['delivery_site'] = $data[0]->site;
        }

        //company
        $this->prHeader['company'] = auth()->user()->company;
        $strsql = "SELECT name FROM company WHERE company='" . auth()->user()->company . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['company_name'] = $data[0]->name;
        }

        $this->prHeader['site'] = auth()->user()->site;
        $strsql = "SELECT site + ' : ' + site_description AS site_description FROM site WHERE site='" . auth()->user()->site . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['site_description'] = $data[0]->site_description;
        }

        $this->prHeader['functions'] =  auth()->user()->functions;
        $this->prHeader['department'] =  auth()->user()->department;

        //division
        $this->prHeader['division'] =  auth()->user()->division;
        $this->prHeader['section'] =  auth()->user()->section;

        //11-03-2022 ตรวจสอบว่า Cost Center ที่อยู่ใน TB users มีใน TB costcenter หรือไม่
        $strsql = "SELECT cost_center FROM cost_center WHERE company='" . auth()->user()->company . "' 
                AND cost_center='" . auth()->user()->cost_center . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['cost_center'] = auth()->user()->cost_center;
        } else {
            $this->prHeader['cost_center'] = "";
        }

        $strsql = "SELECT cost_center, description FROM cost_center WHERE cost_center='" . auth()->user()->cost_center . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['costcenter_desc'] = $data[0]->description;
        }

        //buyer
        $this->prHeader['buyer'] =  "";

        //budget year
        $this->prHeader['budget_year'] =  "";
        $this->prHeader['purpose_pr'] =  "";
        $this->prHeader['capexno'] =  "";

        //Blanket Request
        $this->prHeader['valid_until'] =  "";
        $this->prHeader['days_to_notify'] =  0;
        $this->prHeader['notify_below_10'] =  false;
        $this->prHeader['notify_below_25'] =  false;
        $this->prHeader['notify_below_35'] =  false;
    }

    public function loadDD_partno_dd($myRender=false)
    {
        $this->partno_dd = [];
        $strsql = "SELECT partno, part_name FROM part_master 
            WHERE ISNULL(block,0) = 0 AND ISNULL(flag_deletion,0) = 0 AND ( GETDATE() BETWEEN valid_from AND valid_until )
            AND site = '" . $this->prHeader['site'] . "' ORDER BY partno";
        $this->partno_dd = DB::select($strsql);

        if ($myRender){
            $newOption = "<option value=' '>--- Please Select ---</option>";
            $xpartno_dd = json_decode(json_encode($this->partno_dd), true);
            foreach ($xpartno_dd as $row) {
                $newOption = $newOption . "<option value='" . $row['partno'] . "'>" . $row['partno'] . " : " . $row['part_name'] . "</option>";
            }
            $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#partno-select2']);
        }
    }

    public function loadDD_buyer_dd($myRender=false)
    {
        //16-03-2022
        $this->buyer_dd = [];
        $strsql = "SELECT a.username, b.name + ' ' + b.lastname AS fullname
            FROM buyer a
            JOIN users b ON a.username=b.username
            JOIN buyer_company_mapping c ON a.username=c.buyer
            WHERE c.company='" . $this->prHeader['company'] . "' AND c.site='" . $this->prHeader['site'] . "'";
        $this->buyer_dd = DB::select($strsql);

        if ($myRender){
            $newOption = "<option value=' '>--- Please Select ---</option>";
            $xbuyer_dd = json_decode(json_encode($this->buyer_dd), true);
            foreach ($xbuyer_dd as $row) {
                $newOption = $newOption . "<option value='" . $row['username'] . "'>" . $row['fullname'] . "</option>";
            }
            $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#buyer-select2']);
        }
    }

    public function loadDropdownList()
    {
        //Herder
            //Requested_For & Buyer
            $strsql = "SELECT id, name + ' ' + ISNULL(lastname, '') as fullname, username FROM users WHERE company='" . auth()->user()->company . "' ORDER BY users.name";
            $this->requested_for_dd = DB::select($strsql);

            //16-03-22 แก้เพิ่มเพราะเปลี่ยน Table
            $strsql = "SELECT a.username, b.name + ' ' + b.lastname AS fullname
                FROM buyer a
                JOIN users b ON a.username=b.username
                JOIN buyer_company_mapping c ON a.username=c.buyer
                WHERE c.company='" . $this->prHeader['company'] . "' AND c.site='" . $this->prHeader['site'] . "'";
            $this->buyer_dd = DB::select($strsql);

            //Delivery Address
            $strsql = "SELECT address_id, delivery_location FROM site 
                    WHERE company = '" . auth()->user()->company . "' AND SUBSTRING(address_id, 7, 2)='EN'
                    ORDER BY address_id";
            $this->delivery_address_dd = DB::select($strsql);

            //Cost_Center
            $strsql = "SELECT cost_center, description FROM cost_center WHERE company = '" . auth()->user()->company . "' ORDER BY department";
            $this->cost_center_dd = DB::select($strsql);

            //Budget Year
            //ตรวจสอบว่าเป็นเเดือน 01, 02, 03 หรือไม่
            $xMonth = date_format(Carbon::now(),'m');
            if ($xMonth == '01' OR $xMonth == '02' OR $xMonth == '03'){
                $xYear = intval(date_format(Carbon::now(),'Y'));
                $this->budgetyear_dd = [['year' => $xYear - 1], ['year' => $xYear], ['year' => $xYear + 1]];
            }else{
                $xYear = intval(date_format(Carbon::now(),'Y'));
                $this->budgetyear_dd = [['year' => $xYear], ['year' => $xYear + 1], ['year' => $xYear + 2]];
            }
            
        //Header End

        //Line Items
            //partno
            $this->loadDD_partno_dd();

            //currency
            $this->currency_dd = [];
            $strsql = "SELECT currency FROM currency_master";
            $this->currency_dd = DB::select($strsql);

            //internal_order
            $this->internal_order_dd = [];
            $strsql = "SELECT internal_order, description FROM internal_order 
                    WHERE company = '" . $this->prHeader['company'] . "' ORDER BY internal_order";
            $this->internal_order_dd = DB::select($strsql);

            //purchase_unit
            $this->purchaseunit_dd = [];
            $strsql = "SELECT uomno FROM inv_uom ORDER BY uomno";
            $this->purchaseunit_dd = DB::select($strsql);

            //purchase_group
            $this->purchasegroup_dd= [];
            $strsql = "SELECT groupno, description FROM purchase_group ORDER BY groupno";
            $this->purchasegroup_dd = DB::select($strsql);
            
            //budget_code
            // $this->budgetcode_dd = [];
            // $strsql = "SELECT account, description FROM gl_master WHERE company = '" . $this->prHeader['company'] . "' ORDER BY account";
            // $this->budgetcode_dd = DB::select($strsql);

            //Delivery Plan
            $this->prLineNo_dd = [];
            if ($this->prHeader['ordertype'] == "20" or $this->prHeader['ordertype'] == "21"){
                $strsql = "SELECT id, [lineno], description FROM pr_item 
                    WHERE prno = '" . $this->prHeader['prno'] . "' AND ISNULL(deletion_flag,0) = 0
                    ORDER BY [lineno]";
                $this->prLineNo_dd = DB::select($strsql);
            }
        //Line Items End

        //Authorization
            $this->decider_dd = [];
            $this->validator_dd = [];
            $strsql = "SELECT usr.username, usr.name + ' ' + usr.lastname AS fullname FROM users usr
                        JOIN user_roles uro ON uro.username = usr.username
                        WHERE uro.role_id='10' 
                            AND (usr.id <> " . $this->prHeader['requestor'] . " AND usr.id <> " . $this->prHeader['requested_for'] . ")
                        ORDER BY usr.username";
            $this->decider_dd = DB::select($strsql);
            $this->validator_dd = DB::select($strsql);
        //Authorization End

        //Attachment
            $this->prLineNoAtt_dd = [];
            $strsql = "SELECT id, [lineno], description FROM pr_item 
                    WHERE prno = '" . $this->prHeader['prno'] . "' AND ISNULL(deletion_flag,0) = 0
                    ORDER BY [lineno]";
            $this->prLineNoAtt_dd = DB::select($strsql);
        //Attachment End
    }

    public function mount()
    {
        if ($_GET['mode'] == "create") {
            $this->isCreateMode = true;
            if ($_GET['ordertype'] == '20' or  $_GET['ordertype'] == '21') {
                $this->isBlanket = true;
            }
            $this->orderType = $_GET['ordertype'];
            $this->createPrHeader();

        } else if ($_GET['mode'] == "edit") {
            $this->isCreateMode = false;
            $this->editPRNo = $_GET['prno'];
            $this->currentTab = $_GET['tab'];
            $this->editPR();
            //$this->isBlanket, $this->orderType Assign ค่าใน Function editPR
        }
        //???กำลังแก้
        $this->attachment_lineno[] = "0";
        $this->maxSize = config('constants.maxAttachmentSize');
    }

    public function render()
    {
        $this->loadDropdownList();

        if ($this->isCreateMode){
            return view('livewire.purchase-requisition-details');

        }else{
            //itemList
            $strsql = "SELECT pri.id, pri.[lineno], pri.description, pri.partno, sts.[description] AS status
                , pri.qty, pri.purchase_unit, pri.unit_price, pri.qty * pri.unit_price AS budgettotal, pri.req_date, pri.final_price, pri.currency
                , pri.reference_po
                FROM pr_item pri
                LEFT JOIN pr_status sts ON sts.status=pri.[status]
                WHERE pri.prno='" . $this->prHeader['prno'] . "'
                    AND isnull(pri.deletion_flag, 0) = 0
                ORDER BY pri.id";
            $itemList = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

            //prListDeliveryPlan
            if ($this->prHeader['ordertype'] == "20" or $this->prHeader['ordertype'] == "21"){
                $strsql = "SELECT del.id, pri.[lineno], pri.description, pri.partno, del.qty, pri.purchase_unit, del.delivery_date
                        FROM pr_delivery_plan del
                        JOIN pr_item pri ON pri.id = del.ref_prline_id
                        WHERE del.ref_pr_id=" . $this->prHeader['id'] . " AND ISNULL(del.deletion_flag,0) = 0
                        ORDER BY del.id";
                $prListDeliveryPlan = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);
            } else {
                $prListDeliveryPlan = (new Collection([]))->paginate($this->numberOfPage);
            }

            //deciderList
            $strsql = "SELECT dec.approver, usr.name + ' ' + usr.lastname AS fullname, dvstatus.description AS statusname
                , dec.status, company.name AS company, usr.position
                FROM dec_val_workflow dec
                JOIN users usr ON usr.username = dec.approver
                LEFT JOIN dec_val_status dvstatus ON dvstatus.status_no = dec.status
                LEFT JOIN company ON company.company = usr.company
                WHERE dec.approval_type='DECIDER' AND dec.ref_doc_type='10' AND dec.ref_doc_id =" . $this->prHeader['id'];
            $this->deciderList = json_decode(json_encode(DB::select($strsql)), true);

            //validatorList
            $strsql = "SELECT dec.seqno, dec.approver, usr.name + ' ' + usr.lastname AS fullname, dvstatus.description AS statusname
                , dec.status, company.name AS company, usr.position
                FROM dec_val_workflow dec
                JOIN users usr ON usr.username = dec.approver
                LEFT JOIN dec_val_status dvstatus ON dvstatus.status_no = dec.status
                LEFT JOIN company ON company.company = usr.company
                WHERE dec.approval_type='VALIDATOR' AND dec.ref_doc_type='10' AND dec.ref_doc_id =" . $this->prHeader['id']
                . " ORDER BY dec.seqno";
            $this->validatorList = json_decode(json_encode(DB::select($strsql)), true);

            //attachmentFileList
            $strsql = "SELECT a.id, a.file_name, a.file_path, a.file_type, a.edecision_no, a.ref_docno, a.ref_lineno
            , FORMAT(a.create_on, 'dd-MMM-yy HH:mm:ss') AS create_on
            , b.description AS ref_doctype, c.name + ' ' + c.lastname AS create_by
                FROM attactments a
                LEFT JOIN document_file_type b ON a.ref_doctype = b.doc_type_no
                LEFT JOIN users c ON a.create_by = c.id
                
                WHERE ref_docid =" . $this->prHeader['id'] . " ORDER BY ref_lineno";
            $attachmentFileList = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

            //approval_history ที่อยู่ตรงนี้เพราะ pagination ไม่สามารถส่งค่าผ่ายตัวแปร $this->historylog ได้
            $strsql = "SELECT a.approver, b.name + ' ' + b.lastname as fullname, a.approval_type, b.company, b.department, b.position
                , c.description as status, a.reject_reason, FORMAT(a.submitted_date, 'dd-MMM-yy HH:mm:ss') as submitted_date
                , FORMAT(a.completed_date, 'dd-MMM-yy HH:mm:ss') as completed_date
                FROM dec_val_workflow_log a
                LEFT JOIN users b ON a.approver = b.username
                LEFT JOIN dec_val_status c ON a.status = c.status_no
                WHERE a.refdoc_type='10' AND a.refdoc_id=" . $this->prHeader['id'];
            $approval_history = (new Collection(DB::select($strsql)))->paginate($this->numberOfPage);

            //Reset Pagination
            $this->resetPage();

            $this->historyLog = PurchaseRequisitionLog::showHistroyLog($this->prHeader['prno']);

            return view('livewire.purchase-requisition-details',[
                'itemList' => $itemList,
                'prListDeliveryPlan' => $prListDeliveryPlan,
                'deciderList' => $this->deciderList,
                'validatorList' => $this->validatorList,
                'attachmentFileList' => $attachmentFileList,
                'approval_history' => $approval_history,
                'historylog' => $this->historyLog
            ]);
        }
    }

    //----------------------------------------- PR HISTROY LOG FUNCTION -----------------------------------------
    // VERSION 2 
    public function writeHeaderHistoryLog($prHeaderId, $actionType)
    {
        $actionType = strtoupper($actionType);

        $obj_type = "PR_HEADER";

        try {
            if ($actionType == "INSERT") {
                $prHeaderLog = DB::table('pr_header_history')->where('id_original', '=', $prHeaderId)->orderBy('id', 'desc')->first();
                $prepareData = [
                    'id_original' => $prHeaderLog->id_original,
                    'obj_type' => $obj_type,
                    'line_no' => null,
                    'refdocno' => $prHeaderLog->prno,
                    'field' => "action_type",
                    'old_value' => null,
                    'new_value' => $actionType,
                    'created_by' => $prHeaderLog->create_by,
                    'changed_by' => null,
                    'created_on' => $prHeaderLog->create_on,
                    'changed_on' => null,
                ];
                PurchaseRequisitionLog::insertLog($prepareData);
            } else if ($actionType == "DELETE" || $actionType == "UPDATE") {

                $prHeaderLogs = DB::table('pr_header_history')->where('id_original', '=', $prHeaderId)->orderBy('id', 'desc')->limit(2)->get();
                $data_after = $prHeaderLogs[0];
                $data_before = $prHeaderLogs[1];

                $array_data_before = (array)$data_before;
                unset($array_data_before['id']);
                unset($array_data_before['action_on']);

                $array_data_after = (array)$data_after;
                unset($array_data_after['id']);
                unset($array_data_after['action_on']);

                foreach ($array_data_before as $key => $val) {

                    if ($array_data_before[$key] === $array_data_after[$key]) {
                    } else {
                        $prepareData = [
                            'obj_type' => $obj_type,
                            'id_original' => $array_data_before['id_original'],
                            'line_no' => null,
                            'refdocno' => $array_data_after['prno'],
                            'field' => $key,
                            'old_value' => $array_data_before[$key],
                            'new_value' => $array_data_after[$key],
                            'created_by' => $array_data_before['create_by'],
                            'created_on' => $array_data_before['create_on'],

                            'changed_by' => $array_data_after['changed_by'],
                            'changed_on' => $array_data_after['changed_on'],
                        ];

                        PurchaseRequisitionLog::insertLog($prepareData);
                    }
                }
            }
        } catch (Exception $e) {
            
        }
    }

    public function writeItemHistoryLog($selectId, $actionType)
    {
        
        $actionType = strtoupper($actionType);

        $obj_type = "PR_ITEM";

        if(!is_array($selectId)){
            $selectId = [$selectId];
        }

        foreach($selectId as $prItemId){
            try {
                if ($actionType == "INSERT") {
                    $prItemLog = DB::table('pr_item_history')->where('id_original', '=', $prItemId)->orderBy('id', 'desc')->first();
                   
                    $prepareData = [
                        'id_original' => $prItemLog->id_original,
                        'obj_type' => $obj_type,
                        'line_no' => $prItemLog->lineno,
                        'refdocno' => $prItemLog->prno,
                        'field' => "action_type",
                        'old_value' => null,
                        'new_value' => $actionType,
                        'created_by' => $prItemLog->create_by,
                        'changed_by' => null,
                        'created_on' => $prItemLog->create_on,
                        'changed_on' => null,
                    ];
                    PurchaseRequisitionLog::insertLog($prepareData);
                } else if ($actionType == "DELETE" || $actionType == "UPDATE") {
    
                    $prItemLogs = DB::table('pr_item_history')->where('id_original', '=', $prItemId)->orderBy('id', 'desc')->limit(2)->get();
                    $data_after = $prItemLogs[0];
                    $data_before = $prItemLogs[1];
    
                    $array_data_before = (array)$data_before;
                    unset($array_data_before['id']);
                    unset($array_data_before['action_on']);
    
                    $array_data_after = (array)$data_after;
                    unset($array_data_after['id']);
                    unset($array_data_after['action_on']);
    
                    foreach ($array_data_before as $key => $val) {
    
                        if ($array_data_before[$key] === $array_data_after[$key]) {
                        } else {
                            $prepareData = [
                                'obj_type' => $obj_type,
                                'id_original' => $array_data_before['id_original'],
                                'line_no' => $array_data_after['lineno'],
                                'refdocno' => $array_data_after['prno'],
                                'field' => $key,
                                'old_value' => $array_data_before[$key],
                                'new_value' => $array_data_after[$key],
                                'created_by' => $array_data_before['create_by'],
                                'created_on' => $array_data_before['create_on'],
    
                                'changed_by' => $array_data_after['changed_by'],
                                'changed_on' => $array_data_after['changed_on'],
                            ];
    
                            PurchaseRequisitionLog::insertLog($prepareData);
                        }
                    }
                }
            } catch (Exception $e) {
              
            }
        }

        
    }
    
}
