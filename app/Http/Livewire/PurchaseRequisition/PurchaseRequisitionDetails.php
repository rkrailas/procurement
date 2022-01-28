<?php

namespace App\Http\Livewire\PurchaseRequisition;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Support\Collection;
use Livewire\WithPagination;

class PurchaseRequisitionDetails extends Component
{
    use WithFileUploads;
    use WithPagination; 

    public $listeners = ['deleteConfirmed' => '']; //หลังจากกด Confirm Delete จะให้ไปทำ Function ไหน

    public $isCreateMode, $editPRNo, $isBlanket, $orderType;
    public $deleteID, $deleteType; //เพื่อให้สามารถใช้ Function confirmDelete ร่วมกับการลบหลาย ๆ แบบได้ 
    public $currentTab = "";
    public $enableAddPlan = false;

    //Header
    public $prHeader = [], $requested_for_dd, $delivery_address_dd, $buyer_dd, $cost_center_dd; 

    //Line Items > $itemList=in table, $prItem=ใน Modal, 
    public $prItem = [], $itemList = [], $partno_dd, $currency_dd, $internal_order_dd, $budget_code, $purchaseunit_dd
        , $purchasegroup_dd, $budgetcode_dd, $prLineNo_dd; 

    //DeliveryPlan
    public $prDeliveryPlan, $prListDeliveryPlan = [];  //$prDeliveryPlan=ใน Tab, $prListDeliveryPlan=ใน Grid

    //Authorization > deciderList=in table, $decider=Dropdown, validatorList=in table, $validator=Dropdown
    public $decider_dd, $deciderList = [], $decider, $isValidatorApprove = false, $validator_dd, $validatorList = [], $validator, $rejectReason;

    //Attachment Dropdown ใช้ตัวแปรร่วมกับ prLineNo_dd
    public $prLineNoAtt_dd, $attachment_lineid, $attachment_file, $attachmentFileList;

    //History Log
    public $historyLog;

    //=== Start Function ===

    //Share Function
        public function confirmDelete($deleteID, $deleteType)
        {
            $this->deleteID = $deleteID;

            if ($deleteType == "item") {
                $this->listeners = ['deleteConfirmed' => 'deleteLineItem'];
            } else if ($deleteType == "deliveryPlan") {
                $this->listeners = ['deleteConfirmed' => 'deleteDeliveryPlan'];
            } else if ($deleteType == "decider") {
                $this->listeners = ['deleteConfirmed' => 'deleteDecider'];
            } else if ($deleteType == "validator") {
                $this->listeners = ['deleteConfirmed' => 'deleteValidator'];
            } else if ($deleteType == "attachment") {
                $this->listeners = ['deleteConfirmed' => 'deleteAttachment'];
            } else if ($deleteType == "deletePrHeader") {
                $this->listeners = ['deleteConfirmed' => 'deletePrHeader'];
            } 

            $this->dispatchBrowserEvent('delete-confirmation');
        }

        public function clearVariableInPRDetails()
        {
            $this->reset(['isCreateMode','editPRNo', 'isBlanket', 'orderType', 'deleteID', 'deleteType', 'currentTab'
                , 'prHeader', 'prItem', 'itemList'
                , 'prDeliveryPlan', 'prListDeliveryPlan'
                , 'deciderList', 'decider', 'isValidatorApprove', 'validatorList', 'validator', 'rejectReason' 
                , 'attachment_lineid', 'attachment_file', 'attachmentFileList']);
        }
    //Share Function End

    //Action Button
        public function releaseSourcing()
        {
            //ตรวจสอบว่า Appvore หมดหรือยัง
            $strsql = "SELECT approver FROM dec_val_workflow 
                    WHERE status <> 'APPROVED' AND ref_doc_id =" . $this->prHeader['id']; 

            if (count(DB::select($strsql)) == 0) {
                dd('Release for Sourcing');
            } else {
                $this->dispatchBrowserEvent('popup-alert', ['title' => "Please check the approval."]);
            };
        }

        public function deletePrHeader()
        {
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

            $this->clearVariableInPRDetails();
            return redirect("purchase-requisition/purchaserequisitionlist");
        }

        public function backToPRList()
        {
            return redirect("purchase-requisition/purchaserequisitionlist");
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

                    //History Log
                    $xRandom = Str::random(20);
                    DB::statement(
                        "INSERT INTO history_log(object_type, object_id, action_type, action_where, line_no, history_table, history_ref, company
                        , changed_by, changed_on)
                        
                        VALUES(?,?,?,?,?,?,?,?,?,?)",
                        [
                            'PR', $this->prHeader['prno'], 'Insert', 'Header', 0, 'history_prheader', $xRandom, auth()->user()->company
                            , auth()->user()->id, Carbon::now()
                        ]
                    );

                    DB::statement(
                        "INSERT INTO history_prheader(history_ref, prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                        , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                        , days_to_notify, notify_below_10, notify_below_25, notify_below_35, create_by, create_on)
                        
                        SELECT '" . $xRandom . "', prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                        , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                        , days_to_notify, notify_below_10, notify_below_25, notify_below_35, create_by, create_on
                        FROM pr_header
                        WHERE prno='" . $this->prHeader['prno'] . "' AND company='" . auth()->user()->company . "'"
                    );
                    //History Log End

                });

                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='100' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
                    ]);
                }

                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");

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
                
                    //History Log
                    $xRandom = Str::random(20);
                    DB::statement(
                        "INSERT INTO history_log(object_type, object_id, action_type, action_where, line_no, history_table, history_ref, company
                        , changed_by, changed_on)
                    VALUES(?,?,?,?,?,?,?,?,?,?)",
                        [
                            'PR', $this->prHeader['prno'], 'Update', 'Header', 0, 'history_prheader', $xRandom, auth()->user()->company
                            , auth()->user()->id, Carbon::now()
                        ]
                    );

                    DB::statement("INSERT INTO history_prheader(history_ref, prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                        , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                        , days_to_notify, notify_below_10, notify_below_25, notify_below_35, create_by, create_on, changed_by, changed_on)
                            SELECT '" . $xRandom . "', prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                        , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                        , days_to_notify, notify_below_10, notify_below_25, notify_below_35, create_by, create_on, changed_by, changed_on
                        FROM pr_header
                        WHERE prno='" . $this->prHeader['prno'] . "' AND company='" . auth()->user()->company . "'"
                    );
                    //History Log End
                });

                $strsql = "select msg_text from message_list where msg_no='110' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
                    ]);
                }

                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
            }
        }

    //Action Button End
    
    //Attachment
        public function updatedAttachmentFileList($value, $key) //No Refresh
        {
            $data = explode("." , $key);
            DB::statement("UPDATE attactments SET file_type=?, changed_by=?, changed_on=?
                WHERE id=?" 
                , [$value, auth()->user()->id, Carbon::now(), $this->attachmentFileList[$data[0]]['id']]);
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

            DB::statement("DELETE FROM attactments WHERE id=?" , [$this->deleteID]);
            return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=attachments");
        }

        public function addAttachment()
        {
            if ($this->attachment_file) {
                
                $this->validate([
                    'attachment_file' => 'max:5120', // 1MB Max
                ]);

                DB::transaction(function() 
                {
                    $attachments = $this->attachment_file->store('/', 'attachments');

                    $strsql = "SELECT [lineno] FROM pr_item WHERE id=" . $this->attachment_lineid;
                    $data = DB::select($strsql);
                    $xLineNo = "";
                    if ($data) {
                        $xLineNo = $data[0]->lineno;
                    }

                    DB::statement("INSERT INTO attactments (file_path, [file_name], ref_doctype, ref_docno, ref_docid, ref_lineno
                        , ref_lineid, create_by, create_on)
                    VALUES(?,?,?,?,?,?,?,?,?)"
                    ,[$attachments, $this->attachment_file->getClientOriginalName(), 'PR', $this->prHeader['prno'], $this->prHeader['id']
                    ,$xLineNo, $this->attachment_lineid, auth()->user()->id, Carbon::now()]);

                });
                
                //History Log
                // DB::transaction(function() 
                // {
                //     $xRandom = Str::random(20);
                //     DB::statement(
                //         "INSERT INTO history_log(object_type, object_id, action_type, action_where, line_no, history_table, history_ref, company
                //         , changed_by, changed_on)
                //     VALUES(?,?,?,?,?,?,?,?,?,?)",
                //         [
                //             'PR', $this->prHeader['prno'], 'Insert', 'Attachments', 0, 'history_attachments', $xRandom, auth()->user()->company
                //             , auth()->user()->id, Carbon::now()
                //         ]
                //     );

                //     DB::statement(
                //         "INSERT INTO history_attachments(history_ref, file_path, [file_name], ref_doctype, ref_docno, ref_docid, ref_lineno
                //         , ref_lineid, create_by, create_on, changed_by, changed_on)
                //         SELECT '" . $xRandom . "', file_path, [file_name], ref_doctype, ref_docno, ref_docid, ref_lineno
                //         , ref_lineid, create_by, create_on, changed_by, changed_on
                //         FROM attactments
                //         WHERE ref_docno='" . $this->prHeader['prno'] . "' AND ref_lineid=" . $this->attachment_lineid
                //     );
                // });
                //History Log End

                $this->reset(['attachment_file']);

                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=attachments");

            } else {
                $this->dispatchBrowserEvent('popup-alert', ['title' => "Please select a file"]);
            }
        }
    //Attachment End

    //Authorization
        public function validatorDeciderApprove()
        {
            DB::statement("UPDATE dec_val_workflow SET status=?, changed_by=?, changed_on=?
                WHERE approver=? AND ref_doc_id=?" 
                , ['30', auth()->user()->id, Carbon::now(), auth()->user()->username, $this->prHeader['id']]);

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

            //ส่งเมล์
            if (config('app.sendmail') == "Yes") {
                
                //requester
                $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                    WHERE company = '" . auth()->user()->company . "' 
                    AND id=" . $this->prHeader['requestor'];
                $data = DB::select($strsql);
                if ($data) {
                    $requester_fullname = $data[0]->fullname;
                    $requester_email = $data[0]->email;
                }

                //requested_for
                $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                    WHERE company = '" . auth()->user()->company . "' 
                    AND id=" . $this->prHeader['requested_for'];
                $data = DB::select($strsql);
                if ($data) {
                    $requested_for_fullname = $data[0]->fullname;
                    $requested_for_email = $data[0]->email;
                }

                //approver
                $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                    WHERE company = '" . auth()->user()->company . "' 
                    AND id=" . auth()->user()->id;
                $data = DB::select($strsql);
                if ($data) {
                    $approver_fullname = $data[0]->fullname;
                    $approver_email = $data[0]->email;
                }

                //เนื้อหาใน Mail
                if ($data) {
                    $detailMail = [
                        'template' => 'MAIL_PR02',
                        'dear' => $requester_fullname,
                        'docno' => $this->prHeader['prno'],
                        'actionby' => $approver_fullname,
                    ];

                    Mail::to($approver_email)->cc([$requester_email, $requested_for_email])->send(New WelcomeMail($detailMail));
                }
            }

            return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=auth");
        }

        public function validatorDeciderReject()
        {
            //ตรวจ Reason > pr_header.rejection_reason > del_val_workflow.status=DRAFT (all user) > Mail to Requestor
            if ($this->rejectReason) {
                DB::transaction(function() 
                {
                    //ไม่เห็นใน Spec
                    // DB::statement("UPDATE pr_header SET rejection_reason=?, changed_by=?, changed_on=?
                    //     WHERE id=?" 
                    //     , [$this->rejectReason, auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

                    DB::statement("UPDATE dec_val_workflow SET status=?, changed_by=?, changed_on=?
                        WHERE ref_doc_id=?" 
                        , ['10', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

                    //ส่งเมล์
                    if (config('app.sendmail') == "Yes") {

                        //requester
                        $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                            WHERE company = '" . auth()->user()->company . "' 
                            AND id=" . $this->prHeader['requestor'];
                        $data = DB::select($strsql);
                        if ($data) {
                            $requester_fullname = $data[0]->fullname;
                            $requester_email = $data[0]->email;
                        }

                        //requested_for
                        $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                            WHERE company = '" . auth()->user()->company . "' 
                            AND id=" . $this->prHeader['requested_for'];
                        $data = DB::select($strsql);
                        if ($data) {
                            $requested_for_fullname = $data[0]->fullname;
                            $requested_for_email = $data[0]->email;
                        }

                        //approver
                        $strsql = "SELECT name + ' ' + lastname as fullname, email FROM users 
                            WHERE company = '" . auth()->user()->company . "' 
                            AND id=" . auth()->user()->id;
                        $data = DB::select($strsql);
                        if ($data) {
                            $approver_fullname = $data[0]->fullname;
                            $approver_email = $data[0]->email;
                        }
                        
                        //เนื้อหาใน Mail
                        if ($data) {
                            $detailMail = [
                                'template' => 'MAIL_PR03',
                                'dear' => $requester_fullname,
                                'docno' => $this->prHeader['prno'],
                                'actionby' => $approver_fullname,
                                'reasons' => $this->rejectReason,
                            ];

                            Mail::to($requester_email)->cc([$approver_email, $requested_for_email])->send(New WelcomeMail($detailMail));
                        }
                    }

                    return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=auth");
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
            DB::statement("DELETE FROM dec_val_workflow where approval_type='VALIDATOR' AND approver=? " , [$this->deleteID]);

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
            return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=auth");
        }

        public function addValidator() 
        {
            if ($this->validator == ""){
                $this->dispatchBrowserEvent('popup-alert', [
                    'title' => 'Please Select Validator',
                ]);
            } else {
                DB::statement("INSERT INTO dec_val_workflow (approval_type, approver, status, ref_doc_type, ref_doc_no, ref_doc_id, create_by, create_on)
                VALUES(?,?,?,?,?,?,?,?)"
                ,['VALIDATOR', $this->validator, 'DRAFT', '10', $this->prHeader['prno'], $this->prHeader['id'], auth()->user()->id, Carbon::now()]);

                //History Log
                    // DB::transaction(function() 
                    // {
                    //     $xRandom = Str::random(20);
                    //     DB::statement(
                    //         "INSERT INTO history_log(object_type, object_id, action_type, action_where, line_no, history_table, history_ref, company
                    //         , changed_by, changed_on)
                    //         VALUES(?,?,?,?,?,?,?,?,?,?)",
                    //             [
                    //                 'PR', $this->prHeader['prno'], 'Insert', 'Validator', 0, 'history_validator', $xRandom, auth()->user()->company
                    //                 , auth()->user()->id, Carbon::now()
                    //             ]
                    //     );

                    //     DB::statement(
                    //         "INSERT INTO history_validator(history_ref, approval_type, approver, status, ref_doc_type, ref_doc_no, ref_doc_id
                    //         , create_by, create_on, changed_by, changed_on)
                    //         SELECT '" . $xRandom . "', approval_type, approver, status, ref_doc_type, ref_doc_no, ref_doc_id
                    //         , create_by, create_on, changed_by, changed_on
                    //         FROM dec_val_workflow
                    //         WHERE ref_docno='" . $this->prHeader['prno'] . "' AND ref_lineid=" . $this->attachment_lineid
                    //     );
                    // });
                //History Log End

                $this->reset(['validator']);
                $this->dispatchBrowserEvent('clear-select2');
                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=auth");
            }
        }

        public function deleteDecider()
        {
            DB::statement("DELETE FROM dec_val_workflow where approval_type='DECIDER' AND approver=? " , [$this->deleteID]);

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
            return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=auth");
        }

        public function addDecider() 
        {
            if ($this->decider == ""){
                $this->dispatchBrowserEvent('popup-alert', [
                    'title' => 'Please Select Deceder',
                ]);
            } else {
                DB::statement("INSERT INTO dec_val_workflow (approval_type, approver, status, ref_doc_type, ref_doc_no, ref_doc_id, create_by, create_on)
                VALUES(?,?,?,?,?,?,?,?)"
                ,['DECIDER', $this->decider, 'DRAFT', '10', $this->prHeader['prno'], $this->prHeader['id'], auth()->user()->id, Carbon::now()]);

                $this->reset(['decider']);
                $this->dispatchBrowserEvent('clear-select2');
                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=auth");
            }
        }
    //Authorization End

    //Delivery Plan
        public function deleteDeliveryPlan()
        {
            DB::transaction(function() 
            {
                DB::statement("DELETE FROM delivery_plan where id=? " , [$this->deleteID]);
                
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='104' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => $data[0]->msg_text,
                    ]);                
                }

                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=delivery");
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
        public function deleteLineItem()
        {
            //???ก่อน Delete ต้องตรวจสอบอะไรบ้าง ?
            ///!!!ต้องลบ Delivery Plan ออกด้วย

            DB::transaction(function() 
            {
                //หา Line no
                $strsql = "SELECT [lineno] FROM pr_item WHERE id=". $this->deleteID;
                $data = DB::select($strsql);
                $lineno = 0;
                if ($data) {
                    $lineno = $data[0]->lineno;
                } 

                //History Log
                $xRandom = Str::random(20);
                DB::statement(
                    "INSERT INTO history_log(object_type, object_id, action_type, action_where, line_no, history_table, history_ref, company
                    , changed_by, changed_on)
                VALUES(?,?,?,?,?,?,?,?,?,?)",
                    [
                        'PR', $this->prHeader['prno'], 'Delete', 'Line Item', $lineno, 'history_pritem', $xRandom, auth()->user()->company
                        , auth()->user()->id, Carbon::now()
                    ]
                );

                DB::statement("INSERT INTO history_pritem(history_ref, prno, prno_id, [lineno], partno, description, purchase_unit, unit_price
                , currency, exchange_rate,purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on, changed_by, changed_on)
                SELECT '" . $xRandom . "', prno, prno_id, [lineno], partno, description, purchase_unit, unit_price
                , currency, exchange_rate,purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on, changed_by, changed_on
                FROM pr_item
                WHERE id=" . $this->deleteID
                );
                //History Log End

                DB::statement("DELETE FROM pr_item where id=? " , [$this->deleteID]);
                
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='104' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => $data[0]->msg_text,
                    ]);                
                }

            });

            $this->reset(['deleteID', 'deleteType']);
            return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
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

                DB::statement("INSERT INTO pr_item (prno, prno_id, [lineno], partno, description, purchase_unit, unit_price, currency, exchange_rate
                ,purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on)
                VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                ,[$this->prHeader['prno'], $this->prHeader['id'], $lineno, $this->prItem['partno'], $this->prItem['description']
                , $this->prItem['purchase_unit'], $this->prItem['unit_price'], $this->prItem['currency'], $this->prItem['exchange_rate']
                , $this->prItem['purchase_group'], $this->prItem['account_group'], $this->prItem['qty']
                , $this->prItem['req_date'], $this->prItem['internal_order'] ,$this->prItem['budget_code'], $this->prItem['over_1_year_life']
                , $this->prItem['snn_service'], $this->prItem['snn_production'] ,$this->prItem['nominated_supplier'], $this->prItem['remarks']
                , $this->prItem['skip_rfq'], $this->prItem['skip_doa'], $this->prItem['reference_pr'], "10" ,auth()->user()->id, Carbon::now()
                ]);

                //History Log
                $xRandom = Str::random(20);
                DB::statement(
                    "INSERT INTO history_log(object_type, object_id, action_type, action_where, line_no, history_table, history_ref, company
                    , changed_by, changed_on)
                VALUES(?,?,?,?,?,?,?,?,?,?)",
                    [
                        'PR', $this->prHeader['prno'], 'Insert', 'Line Item', $lineno, 'history_pritem', $xRandom, auth()->user()->company
                        , auth()->user()->id, Carbon::now()
                    ]
                );

                DB::statement("INSERT INTO history_pritem(history_ref, prno, prno_id, [lineno], partno, description, purchase_unit, unit_price
                , currency, exchange_rate,purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on, changed_by, changed_on)
                SELECT '" . $xRandom . "', prno, prno_id, [lineno], partno, description, purchase_unit, unit_price
                , currency, exchange_rate,purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on, changed_by, changed_on
                FROM pr_item
                WHERE prno='" . $this->prHeader['prno'] . "' AND [lineno]=" .  $lineno
                );
                //History Log End

                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='111' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => str_replace("<PR Line No.>", $lineno, $data[0]->msg_text),
                    ]);
                }
            });

            $this->reset(['prItem']);
            return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
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
        //PR Header
            $strsql = "SELECT prh.id, prh.prno, ort.description AS ordertypename, isnull(req.name,'') + ' ' + isnull(req.lastname,'') AS requestor_name
                        , prh.requested_for, prh.delivery_address, FORMAT(prh.request_date,'yyy-MM-dd') AS request_date, prh.company, prh.site, prh.functions
                        , prh.department, prh.division, prh.section, reqf.email, reqf.phone , prh.buyer, prh.cost_center, prh.edecision
                        , pr_status.description AS statusname, FORMAT(prh.valid_until,'yyy-MM-dd') AS valid_until, prh.days_to_notify, prh.notify_below_10
                        , prh.notify_below_25, prh.notify_below_35, company.name as company_name, prh.ordertype, prh.requestor
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
        //PR Header End

        //Item List
            $strsql = "SELECT pri.id, pri.[lineno], pri.description, pri.partno, pri.[status] + ' : ' + sts.[description] as [status]
                        , pri.qty, pri.purchase_unit, pri.unit_price, pri.qty * pri.unit_price as budgettotal, pri.req_date, pri.final_price
                        FROM pr_item pri
                        left join pr_status sts on sts.status=pri.[status]
                        WHERE pri.prno='" . $this->prHeader['prno'] . "'
                        ORDER BY pri.[lineno]";
            //$this->itemList = DB::select($strsql);
            $this->itemList = json_decode(json_encode(DB::select($strsql)), true);
        //Item List End

        //Delivery Plan
            if ($this->prHeader['ordertype'] == "20" or $this->prHeader['ordertype'] == "21"){
                $strsql = "SELECT del.id, pri.[lineno], pri.description, pri.partno, del.qty, pri.purchase_unit, del.delivery_date
                            FROM delivery_plan del
                            JOIN pr_item pri ON pri.id = del.ref_prline_id
                            WHERE del.ref_pr_id=" . $this->prHeader['id'];
                $this->prListDeliveryPlan = json_decode(json_encode(DB::select($strsql)), true);
            }
        //Delivery Plan End

        //Authorization
            $strsql = "SELECT dec.approver, usr.name + ' ' + usr.lastname AS fullname, status 
                        FROM dec_val_workflow dec
                        JOIN users usr ON usr.username = dec.approver
                        WHERE dec.approval_type='DECIDER' AND dec.ref_doc_id =" . $this->prHeader['id'];
            $this->deciderList = json_decode(json_encode(DB::select($strsql)), true);

            $strsql = "SELECT dec.approver, usr.name + ' ' + usr.lastname AS fullname, status 
                        FROM dec_val_workflow dec
                        JOIN users usr ON usr.username = dec.approver
                        WHERE dec.approval_type='VALIDATOR' AND dec.ref_doc_id =" . $this->prHeader['id'];
            $this->validatorList = json_decode(json_encode(DB::select($strsql)), true);

            //ตรวจสอบว่าจะ Enable ปุ่ม ของ Decider ได้หรือไม่ ***ใช้ตัวล่างแทน
            // $strsql = "SELECT approver FROM dec_val_workflow 
            //             WHERE approval_type = 'VALIDATOR' AND ref_doc_id =" . $this->prHeader['id']; 
            // $strsql2 = "SELECT approver FROM dec_val_workflow 
            //             WHERE approval_type = 'VALIDATOR' AND status = 'APPROVED' AND ref_doc_id =" . $this->prHeader['id']; 

            // if (count(DB::select($strsql)) == count(DB::select($strsql2))) {
            //     $this->isValidatorApprove = true;
            // };

            //ตรวจสอบว่า Validator Approve ครบหรือไม่
            $strsql = "SELECT approver FROM dec_val_workflow 
                    WHERE approval_type = 'VALIDATOR' AND status <> 'APPROVED' AND ref_doc_id =" . $this->prHeader['id']; 

            if (count(DB::select($strsql)) == 0) {
                $this->isValidatorApprove = true;
            };

            //ตรวจสอบว่า Decider Approve แล้วหรือไม่
            $strsql = "SELECT approver FROM dec_val_workflow 
            WHERE approval_type = 'DECIDER' AND status <> 'APPROVED' AND ref_doc_id =" . $this->prHeader['id']; 

            if (count(DB::select($strsql)) == 0) {
                $this->isValidatorApprove = false;
            };

        //Authorization End
        
        //Attachments
            $strsql = "SELECT id, file_name, file_type, ref_doctype, ref_docno, ref_lineno, file_path
                    FROM attactments 
                    WHERE ref_docid =" . $this->prHeader['id'] . "ORDER BY ref_lineno";
            $this->attachmentFileList = json_decode(json_encode(DB::select($strsql)), true);

        //Attachments End

        //History ย้ายไปที่ Render
            // $strsql = "SELECT a.*, b.name + ' ' + b.lastname as fname 
            //         FROM history_log a
            //         JOIN users b ON b.id = a.changed_by
            //         WHERE a.object_type = 'PR' AND a.object_id='" . $this->prHeader['prno'] . "' ORDER BY a.id";
            //$this->historyLog = json_decode(json_encode(DB::select($strsql)), true);
            // $this->historyLog = (new Collection(DB::select($strsql)))->paginate(10);
        //History End
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
        $strsql = "SELECT pr_prefix1, pr_prefix2, pr_runno FROM company WHERE company='" . auth()->user()->company . "'";
        $data = DB::select($strsql);

        if ($data){
            if ($data[0]->pr_prefix2 < date_format(now(),"y")) {
                DB::statement("UPDATE company SET pr_prefix2=?, pr_runno=? where company=?"
                 , [date_format(now(),"y"), 1, auth()->user()->company]);

                $newPRNo = $data[0]->pr_prefix1 . date_format(now(),"y") . sprintf("%06d", 1);

            } else if ($data[0]->pr_prefix2 == date_format(now(),"y")) {
                DB::statement("UPDATE company SET pr_runno=? where company=?"
                , [$data[0]->pr_runno + 1, auth()->user()->company]);

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
        $this->prHeader['buyer'] =  " ";
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
        //Herder
            //Requested_For & Buyer
            $this->buyer_dd = [];
            $strsql = "SELECT id, name + ' ' + ISNULL(lastname, '') as fullname, username FROM users WHERE company='" . auth()->user()->company . "' ORDER BY users.name";
            $this->requested_for_dd = DB::select($strsql);
            $this->buyer_dd = DB::select($strsql);

            //Delivery Address
            $this->cost_center_dd = [];
            $strsql = "SELECT address_id, SUBSTRING(address,1,30) as address FROM site WHERE company = '" . auth()->user()->company . "' ORDER BY address_id";
            $this->delivery_address_dd = DB::select($strsql);

            //Cost_Center
            $strsql = "SELECT cost_center, description FROM cost_center WHERE company = '" . auth()->user()->company . "' ORDER BY department";
            $this->cost_center_dd = DB::select($strsql);
        //Header End

        //Line Items
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
        //Line Items End

        //Authorization
            $this->decider_dd = [];
            $this->validator_dd = [];
            $strsql = "SELECT usr.username, usr.name + ' ' + usr.lastname AS fullname FROM users usr
                        JOIN user_roles uro ON uro.username = usr.username 
                        WHERE uro.role_id='10' AND usr.company = '" . $this->prHeader['company'] . "' ORDER BY usr.username";
            $this->decider_dd = DB::select($strsql);
            $this->validator_dd = DB::select($strsql);
        //Authorization End

        //Attachment
            $this->prLineNoAtt_dd = [];
            $strsql = "SELECT id, [lineno], description FROM pr_item WHERE prno = '" . $this->prHeader['prno'] . "' ORDER BY [lineno]";
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
    }

    public function render()
    {
        $this->loadDropdownList();

        if ($this->isCreateMode){
            return view('livewire.purchase-requisition.purchase-requisition-details');
        }else{
            //ถ้าเป็น Edit ให้ Query History มาด้วย ที่อยู่ตรงนี้เพราะ pagination ไม่สามารถส่งค่าผ่ายตัวแปร $this->historylog ได้
            $strsql = "SELECT a.*, b.name + ' ' + b.lastname as fname 
            FROM history_log a
            JOIN users b ON b.id = a.changed_by
            WHERE a.object_type = 'PR' AND a.object_id='" . $this->prHeader['prno'] . "' ORDER BY a.id";
        
            $historyLog = (new Collection(DB::select($strsql)))->paginate(10);

            return view('livewire.purchase-requisition.purchase-requisition-details',[
                'historylog' => $historyLog,
            ]);
        }
        
    }
}
