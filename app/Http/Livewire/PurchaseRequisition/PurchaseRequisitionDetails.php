<?php

namespace App\Http\Livewire\PurchaseRequisition;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\Mail;
use App\Support\Collection;
use Livewire\WithPagination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    //Header
    public $prHeader, $requested_for_dd, $delivery_address_dd, $buyer_dd, $cost_center_dd, $budget_year; 

    //Line Items > $itemList=in table, $prItem=ใน Modal, 
    public $prItem = [], $itemList = [], $partno_dd, $currency_dd, $internal_order_dd, $budget_code, $purchaseunit_dd
        , $purchasegroup_dd, $budgetcode_dd, $prLineNo_dd, $isCreateLineItem; 

    //DeliveryPlan
    public $prDeliveryPlan, $prListDeliveryPlan = [];  //$prDeliveryPlan=ใน Tab, $prListDeliveryPlan=ใน Grid

    //Authorization > deciderList=in table, $decider=Dropdown, validatorList=in table, $validator=Dropdown
    public $decider_dd, $deciderList = [], $decider = [], $isValidatorApprove = false, $validator_dd, $validatorList = [], $validator = [], $rejectReason;
    public $isValidator_Decider =false;

    //Attachment Dropdown ใช้ตัวแปรร่วมกับ prLineNo_dd
    public $prLineNoAtt_dd, $attachment_lineid, $attachment_file, $attachmentFileList;

    //History Log
    public $historyLog;

    //=== Start Function ===

    //Share Function
        // ยังไม่ได้ใช้เพราะมี Event ที่ต้องใช้แค่ Release for Sourcing
        // public function updatePrHeader($prno)
        // {
        //     $strsql = "SELECT MIN(status) AS status FROM pr_item WHERE prno ='" . $prno . "'";
        //     $data = DB::select($strsql);
        //     if (count($data) > 0) {
        //         $strsql = "UPDATE pr_header SET status='" . $data[0]->status . "'"; 
        //         DB::statement($strsql);
        //     }
        // }

        public function confirmDelete($deleteID, $deleteType)
        {
            $this->deleteID = $deleteID;

            if ($deleteType == "item") {
                $this->listeners = ['deleteConfirmed' => 'deleteLineItem'];
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Do you want to delete / Cancel ?',
                ]);
            } else if ($deleteType == "deliveryPlan") {
                $this->listeners = ['deleteConfirmed' => 'deleteDeliveryPlan'];
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Do you want to delete / Cancel ?',
                ]);
            } else if ($deleteType == "decider") {
                $this->listeners = ['deleteConfirmed' => 'deleteDecider'];
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Do you want to delete / Cancel ?',
                ]);
            } else if ($deleteType == "validator") {
                $this->listeners = ['deleteConfirmed' => 'deleteValidator'];
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Do you want to delete / Cancel ?',
                ]);
            } else if ($deleteType == "attachment") {
                $this->listeners = ['deleteConfirmed' => 'deleteAttachment'];
                $this->dispatchBrowserEvent('delete-confirmation',[
                    'title' => 'Do you want to delete / Cancel ?',
                ]);
            }
        }

        public function clearVariablePR()
        {
            $this->reset(['prHeader', 'prItem', 'itemList', 'prListDeliveryPlan', 'deciderList', 'validatorList', 'attachmentFileList', 'historyLog'
                        , 'isCreateMode', 'editPRNo', 'isBlanket', 'orderType', 'deleteID', 'deleteType', 'currentTab', 'prDeliveryPlan'
                        , 'decider', 'isValidatorApprove', 'isValidator_Decider', 'validator', 'rejectReason', 'attachment_lineid', 'attachment_file']);
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

        public function reopen()
        {
            if ($this->selectedRows) {
                DB::transaction(function () {
                    //Copy pr_header
                    $newPrNo = $this->getNewPrNo();
                    $strsql = "INSERT INTO pr_header(prno, ordertype, status, requestor, requested_for, buyer, delivery_address, request_date, company
                        , site, functions, department, division, section, cost_center, edecision, valid_until, days_to_notify, notify_below_10
                        , notify_below_25, notify_below_35, rejection_reason, budget_year, purpose_pr, deletion_flag
                        , create_by, create_on)
                        SELECT '" . $newPrNo . "', ordertype, '01', requestor, requested_for, buyer, delivery_address, '" 
                        . date_format(Carbon::now(), 'Y-m-d') . "', company
                        , site, functions, department, division, section, cost_center, edecision, valid_until, days_to_notify, notify_below_10
                        , notify_below_25, notify_below_35, rejection_reason, budget_year, purpose_pr, deletion_flag
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

                    //Copy pr_item
                    $strsql = "insert into pr_item(prno, prno_id, [lineno], partno, description, purchase_unit, unit_price, unit_price_local, currency
                        , exchange_rate, purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life
                        , snn_service, snn_production, final_price, final_price_local, quotation_expiry_date, quotation_date, nominated_supplier
                        , remarks, skip_rfq, skip_doa, reference_pr, reference_po, reference_po_item, status, close_reason, edecision
                        , create_by, create_on)
                        select '" . $newPrNo . "', " . $xPrID . ", [lineno], partno, description, purchase_unit, unit_price, unit_price_local, currency
                        , exchange_rate, purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life
                        , snn_service, snn_production, final_price, final_price_local, quotation_expiry_date, quotation_date, nominated_supplier
                        , remarks, skip_rfq, skip_doa, '" . $this->prHeader['prno'] . "', reference_po, reference_po_item, status, close_reason, edecision
                        , '" . auth()->user()->id . "', '" . Carbon::now() . "'
                        from pr_item
                        where id in (" . myWhereInID($this->selectedRows) . ")";
                    DB::statement($strsql);

                    //Set PR Item Status = Cancelled
                    $strsql = "UPDATE pr_item SET status='70' WHERE prno='" . $this->prHeader['prno'] . "'";
                    DB::statement($strsql);

                    //Popup Message
                    $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='100' AND class='PURCHASE REQUISITION'";
                    $data = DB::select($strsql);
                    if (count($data) > 0) {
                        $this->dispatchBrowserEvent('popup-success', [
                            'title' => str_replace("<PR No.>", $newPrNo, $data[0]->msg_text),
                        ]);
                    }

                    $this->reset(['selectedRows']);

                    return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $newPrNo . "&tab=item");
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

        public function cancel()
        {
            if ($this->selectedRows) {
                $xID = myWhereInID($this->selectedRows);
                DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=? 
                    WHERE id IN (" . $xID . ")"
                    , ['70', auth()->user()->id, Carbon::now()]);

                $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='103' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => $data[0]->msg_text,
                    ]);
                }
                
                $this->reset(['selectedRows']);

                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");

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

        public function releaseSourcing()
        {
            //2022-01-30 IF there is no Decider selected (Ref. P2P-PUR-001-FS-Purchase Requisition_(2022-01-28))
            $strsql = "SELECT approver FROM dec_val_workflow WHERE ref_doc_no='" . $this->prHeader['prno'] . "' AND approval_type='DECIDER'";
            if (count(DB::select($strsql)) == 0) {
                $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='113' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-alert', ['title' => $data[0]->msg_text]);
                }

            } else {
                DB::transaction(function () {
                    //2022-01-30  Set PR ITEM Status to 'RELEASED FOR SOURCING' (20), disable editting for the PR
                    DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=?
                    WHERE prno=?" 
                    , ['20', auth()->user()->id, Carbon::now(), $this->prHeader['prno']]);

                    //2022-01-30  Update the DEC_VAL WORKFLOW Status to Open (20) for all validators.
                    DB::statement("UPDATE dec_val_workflow SET status=?, changed_by=?, changed_on=?
                    WHERE ref_doc_no=?" 
                    , ['20', auth()->user()->id, Carbon::now(), $this->prHeader['prno']]);

                    //2022-01-30 Update PR HEADER Status to 'RELEASED FOR SOURCING' (20)
                    DB::statement("UPDATE pr_header SET status=?, changed_by=?, changed_on=?
                    WHERE prno=?" 
                    , ['20', auth()->user()->id, Carbon::now(), $this->prHeader['prno']]);
                });

                $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='101' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => str_replace("<PR No.>", $this->prHeader['prno'], $data[0]->msg_text),
                    ]);
                }
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
                DB::statement("DELETE FROM pr_item WHERE id IN (" . $xID . ")");

                $strsql = "SELECT msg_text, class FROM message_list WHERE msg_no='104' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => $data[0]->msg_text,
                    ]);
                }
                
                $this->reset(['selectedRows']);

                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");

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

                return redirect("purchase-requisition/purchaserequisitionlist");
            }
        }

        public function backToPRList()
        {
            $this->clearVariablePR();

            return redirect("purchase-requisition/purchaserequisitionlist");
        }

        public function savePR()
        {
            //Validaate required field
            Validator::make($this->prHeader, [
                'requested_for' => 'required',
                'delivery_address' => 'required',
                'buyer' => 'required',
                'cost_center' => 'required',
                'purpose_pr' => 'required',
            ])->validate();

            if ($this->prHeader['ordertype'] == '21') {
                Validator::make($this->prHeader, [
                    'budget_year' => 'required',
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
                if ($this->isCreateMode) {
                    $this->prHeader['prno'] = $this->getNewPrNo();
                    $this->prHeader['status'] = '10';
                    
                    DB::transaction(function () {
                    //pr_header
                        DB::statement(
                            "INSERT INTO pr_header(prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                            , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                            , days_to_notify, notify_below_10, notify_below_25, notify_below_35, budget_year, purpose_pr, create_by, create_on)
                            
                            VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)",
                                [
                                    $this->prHeader['prno'], $this->prHeader['ordertype'], $this->prHeader['status'], $this->prHeader['requestor']
                                    , $this->prHeader['requested_for'], $this->prHeader['buyer'], $this->prHeader['delivery_address'], $this->prHeader['request_date']
                                    , $this->prHeader['company'], $this->prHeader['site'], $this->prHeader['functions'], $this->prHeader['department']
                                    , $this->prHeader['division'], $this->prHeader['section'], $this->prHeader['cost_center'], $this->prHeader['edecision']
                                    , $this->prHeader['valid_until'], $this->prHeader['days_to_notify'], $this->prHeader['notify_below_10']
                                    , $this->prHeader['notify_below_25'], $this->prHeader['notify_below_35'], $this->prHeader['budget_year']
                                    , $this->prHeader['purpose_pr'], auth()->user()->id, Carbon::now()
                                ]
                        );
    
                        //30-01-2022 Hold
                        //History Log
                            // $xRandom = Str::random(20);
                            // DB::statement(
                            //     "INSERT INTO history_log(object_type, object_id, action_type, action_where, line_no, history_table, history_ref, company
                            //     , changed_by, changed_on)
                                
                            //     VALUES(?,?,?,?,?,?,?,?,?,?)",
                            //     [
                            //         'PR', $this->prHeader['prno'], 'Insert', 'Header', 0, 'history_prheader', $xRandom, auth()->user()->company
                            //         , auth()->user()->id, Carbon::now()
                            //     ]
                            // );
    
                            // DB::statement(
                            //     "INSERT INTO history_prheader(history_ref, prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                            //     , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                            //     , days_to_notify, notify_below_10, notify_below_25, notify_below_35, create_by, create_on)
                                
                            //     SELECT '" . $xRandom . "', prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                            //     , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                            //     , days_to_notify, notify_below_10, notify_below_25, notify_below_35, create_by, create_on
                            //     FROM pr_header
                            //     WHERE prno='" . $this->prHeader['prno'] . "' AND company='" . auth()->user()->company . "'"
                            // );
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
                        DB::statement("UPDATE pr_header SET requested_for=?, delivery_address=?, request_date=?, site=?, functions=?, department=?
                        , division=?, section=?, buyer=?, cost_center=?, edecision=?, valid_until=?, days_to_notify=?, notify_below_10=?, notify_below_25=?
                        , notify_below_35=?,budget_year=?, purpose_pr=?, status=?, changed_by=?, changed_on=?
                        where prno=?" 
                        , [$this->prHeader['requested_for'], $this->prHeader['delivery_address'], $this->prHeader['request_date']
                        , $this->prHeader['site'], $this->prHeader['functions'], $this->prHeader['department'], $this->prHeader['division'], $this->prHeader['section']
                        , $this->prHeader['buyer'], $this->prHeader['cost_center'], $this->prHeader['edecision'], $this->prHeader['valid_until']
                        , $this->prHeader['days_to_notify'], $this->prHeader['notify_below_10'], $this->prHeader['notify_below_25'], $this->prHeader['notify_below_35']
                        , $this->prHeader['budget_year'], $this->prHeader['purpose_pr'], $this->prHeader['status'], auth()->user()->id, Carbon::now(), $this->prHeader['prno']]);
                    
                        //30-01-2022 Hold
                        //History Log
                            // $xRandom = Str::random(20);
                            // DB::statement(
                            //     "INSERT INTO history_log(object_type, object_id, action_type, action_where, line_no, history_table, history_ref, company
                            //     , changed_by, changed_on)
                            // VALUES(?,?,?,?,?,?,?,?,?,?)",
                            //     [
                            //         'PR', $this->prHeader['prno'], 'Update', 'Header', 0, 'history_prheader', $xRandom, auth()->user()->company
                            //         , auth()->user()->id, Carbon::now()
                            //     ]
                            // );
    
                            // DB::statement("INSERT INTO history_prheader(history_ref, prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                            //     , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                            //     , days_to_notify, notify_below_10, notify_below_25, notify_below_35, create_by, create_on, changed_by, changed_on)
                            //         SELECT '" . $xRandom . "', prno, ordertype, status, requestor, requested_for, buyer, delivery_address
                            //     , request_date, company, site, functions, department, division, section, cost_center, edecision, valid_until
                            //     , days_to_notify, notify_below_10, notify_below_25, notify_below_35, create_by, create_on, changed_by, changed_on
                            //     FROM pr_header
                            //     WHERE prno='" . $this->prHeader['prno'] . "' AND company='" . auth()->user()->company . "'"
                            // );
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

        }

    //Action Button End
    
    //Attachment

        //Test Dropzone not work with $this->prHeader['prno']
            public function attactFilePR(Request $request)
            {
                $data = array();

                $validator = Validator::make($request->all(),[
                    'file' => 'required|mimes:png,jpg,jpeg,pdf|max:2048'
                ]);
        
                if($validator->fails()){
                    $data['success'] = 0;
                    $data['error'] = $validator->errors()->first('file');
                }else{
                    $file = $request->file('file');
                    $filename = time().'_'.$file->getClientOriginalName();
        
                    //File upload location in public folder
                    $location = 'attachments';
        
                    $file->move($location, $filename);
        
                    $data['success'] = 1;
                    $data['message'] = 'Uploaded Successfully';
        
                    // $strsql = "SELECT [lineno] FROM pr_item WHERE id=" . $this->attachment_lineid;
                    // $data = DB::select($strsql);

                    $xLineNo = "0";
                    // if ($data) {
                    //     $xLineNo = $data[0]->lineno;
                    // }
        
                    DB::statement("INSERT INTO attactments (file_path, [file_name], ref_doctype, ref_docno, ref_docid, ref_lineno
                        , ref_lineid, create_by, create_on)
                    VALUES(?,?,?,?,?,?,?,?,?)"
                    ,[$filename, $file->getClientOriginalName(), 'PR', 'xxxx', 0
                    ,$xLineNo, 0, auth()->user()->id, Carbon::now()]);

                    //return response()->json($data);
                    return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=attachments");
                }
            }
        //Test Dropzone End

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
                    'attachment_file.*' => 'max:5120', // 5MB Max
                ]);

                DB::transaction(function() 
                {
                    foreach ($this->attachment_file as $file) {
                        $attachments = $file->store('/', 'attachments');
                        ///$attachments = $file->storeAs('public/attachments', 'zzz.pdf'); //ยังไม่ Work

                        $strsql = "SELECT [lineno] FROM pr_item WHERE id=" . $this->attachment_lineid;
                        $data = DB::select($strsql);
                        $xLineNo = "";
                        if ($data) {
                            $xLineNo = $data[0]->lineno;
                        }

                        DB::statement("INSERT INTO attactments (file_path, [file_name], ref_doctype, ref_docno, ref_docid, ref_lineno
                            , ref_lineid, create_by, create_on)
                        VALUES(?,?,?,?,?,?,?,?,?)"
                        ,[$attachments, $file->getClientOriginalName(), 'PR', $this->prHeader['prno'], $this->prHeader['id']
                        ,$xLineNo, $this->attachment_lineid, auth()->user()->id, Carbon::now()]);
                    }
                });


                //Single File 
                    // $this->validate([
                    //     'attachment_file' => 'max:5120', // 5MB Max
                    // ]);

                    // DB::transaction(function() 
                    // {
                    //     $attachments = $this->attachment_file->store('/', 'attachments');

                    //     $strsql = "SELECT [lineno] FROM pr_item WHERE id=" . $this->attachment_lineid;
                    //     $data = DB::select($strsql);
                    //     $xLineNo = "";
                    //     if ($data) {
                    //         $xLineNo = $data[0]->lineno;
                    //     }

                    //     DB::statement("INSERT INTO attactments (file_path, [file_name], ref_doctype, ref_docno, ref_docid, ref_lineno
                    //         , ref_lineid, create_by, create_on)
                    //     VALUES(?,?,?,?,?,?,?,?,?)"
                    //     ,[$attachments, $this->attachment_file->getClientOriginalName(), 'PR', $this->prHeader['prno'], $this->prHeader['id']
                    //     ,$xLineNo, $this->attachment_lineid, auth()->user()->id, Carbon::now()]);

                    // });
                //Single File End 
                
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
            $strsql = "SELECT username, company, department, position FROM users WHERE username='" . $this->decider['username'] . "'";
            $data = DB::select($strsql);
            if ($data) {
                $this->decider = json_decode(json_encode($data[0]), true);
            }
        }

        public function validatorDeciderApprove()
        {
            DB::statement("UPDATE dec_val_workflow SET status=?, changed_by=?, changed_on=?
                WHERE approver=? AND ref_doc_id=?" 
                , ['30', auth()->user()->id, Carbon::now(), auth()->user()->username, $this->prHeader['id']]);

            DB::statement("UPDATE dec_val_workflow_log SET status=?, completed_date=?, changed_by=?, changed_on=?
            WHERE refdoc_id=? AND approver=?"
            , ['30', Carbon::now(), auth()->user()->id, Carbon::now(), $this->prHeader['id'], auth()->user()->username]);

            $strsql = "SELECT approval_type FROM dec_val_workflow WHERE ref_doc_id =" . $this->prHeader['id'] . " AND approver='" . auth()->user()->username . "'";
            $data = DB::select($strsql);
            $xApproval_type = '';
            if ($data) {
                $xApproval_type = $data[0]->approval_type;
            }

           
            if ($xApproval_type == 'VALIDATOR'){
                DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=?
                WHERE prno_id=?" 
                , ['21', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

                DB::statement("UPDATE pr_header SET status=?, changed_by=?, changed_on=?
                WHERE id=?" 
                , ['21', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

            } else if ($xApproval_type == 'DECIDER'){
                DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=?
                WHERE prno_id=?" 
                , ['30', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);

                DB::statement("UPDATE pr_header SET status=?, changed_by=?, changed_on=?
                WHERE id=?" 
                , ['30', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);
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

                    DB::statement("UPDATE dec_val_workflow_log SET status=?, reject_reason=?, completed_date=?, changed_by=?, changed_on=?
                        WHERE refdoc_id=? AND approver=?"
                        , ['10', $this->rejectReason, Carbon::now(), auth()->user()->id, Carbon::now(), $this->prHeader['id'], auth()->user()->username]);

                    DB::statement("UPDATE pr_item SET status=?, changed_by=?, changed_on=?
                        WHERE prno_id=?" 
                        , ['10', auth()->user()->id, Carbon::now(), $this->prHeader['id']]);
                    
                    DB::statement("UPDATE pr_header SET status=?, changed_by=?, changed_on=?
                        WHERE id=?" 
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

                    $this->reset(['rejectReason']);

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
            DB::transaction(function() 
            {
                DB::statement("DELETE FROM dec_val_workflow where ref_doc_id=" . $this->prHeader['id'] . " AND approval_type='VALIDATOR' 
                    AND approver=? " , [$this->deleteID]);

                DB::statement("DELETE FROM dec_val_workflow_log where refdoc_id=" . $this->prHeader['id'] . " AND approval_type='VALIDATOR' 
                    AND approver=? " , [$this->deleteID]);
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
                ,['VALIDATOR', $this->validator['username'], '10', '10', $this->prHeader['prno'], $this->prHeader['id'], auth()->user()->id, Carbon::now()]);

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

                //Approval History
                    DB::statement("INSERT INTO dec_val_workflow_log (approval_type, approver, status, refdoc_type, refdoc_no, refdoc_id
                    , submitted_date, submitted_by, create_by, create_on)
                    VALUES(?,?,?,?,?,?,?,?,?,?)"
                    ,['VALIDATOR', $this->validator['username'], '10', '10', $this->prHeader['prno'], $this->prHeader['id']
                    , Carbon::now(), auth()->user()->id, auth()->user()->id, Carbon::now()]);
                //Approval History End


                $this->reset(['validator']);
                $this->dispatchBrowserEvent('clear-select2');
                return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=auth");
            }
        }

        public function deleteDecider()
        {
            DB::transaction(function() 
            {
                DB::statement("DELETE FROM dec_val_workflow WHERE ref_doc_id=" . $this->prHeader['id'] . " AND approval_type='DECIDER' 
                    AND approver=? " , [$this->deleteID]);

                DB::statement("DELETE FROM dec_val_workflow_log WHERE refdoc_id=" . $this->prHeader['id'] . " AND approval_type='DECIDER' 
                    AND approver=? " , [$this->deleteID]);
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
                ,['DECIDER', $this->decider['username'], '10', '10', $this->prHeader['prno'], $this->prHeader['id'], auth()->user()->id, Carbon::now()]);

                //Approval History
                    DB::statement("INSERT INTO dec_val_workflow_log (approval_type, approver, status, refdoc_type, refdoc_no, refdoc_id
                    , submitted_date, submitted_by, create_by, create_on)
                    VALUES(?,?,?,?,?,?,?,?,?,?)"
                    ,['DECIDER', $this->decider['username'], '10', '10', $this->prHeader['prno'], $this->prHeader['id']
                    , Carbon::now(), auth()->user()->id, auth()->user()->id, Carbon::now()]);
                //Approval History End

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
                    $newOption = "<option value=' '>--- Please Select ---</option>";
                    $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                    foreach ($xBudgetcode_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                        if ($row['account'] == $this->prItem['budget_code']) {
                            $newOption = $newOption . "selected='selected'";
                        }
                        $newOption = $newOption . ">" . $row['account'] . ':' . $row['description'] . "</option>";
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
                        $newOption = $newOption . ">" . $row['partno'] . "</option>";
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
                    $newOption = "<option value=' '>--- Please Select ---</option>";
                    $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                    foreach ($xBudgetcode_dd as $row) {
                        $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                        if ($row['account'] == $this->prItem['budget_code']) {
                            $newOption = $newOption . "selected='selected'";
                        }
                        $newOption = $newOption . ">" . $row['account'] . ':' . $row['description'] . "</option>";
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

            $strsql = "SELECT a.id, a.[lineno], a.partno, a.description, a.purchase_unit, a.unit_price, a.currency, a.exchange_rate
                , a.purchase_group, a.account_group, a.qty, a.internal_order, FORMAT(a.req_date,'yyyy-MM-dd') AS req_date, a.budget_code, a.over_1_year_life
                , a.snn_service, a.snn_production, b.name1 as nominated_supplier, a.remarks, a.skip_rfq, a.skip_doa, a.final_price
                , FORMAT(a.quotation_expiry_date,'yyyy-MM-dd') AS quotation_expiry_date, a.reference_pr, c.min_order_qty, c.supplier_lead_time
                FROM pr_item a
                LEFT JOIN supplier b ON b.vendor_code = a.nominated_supplier
                LEFT JOIN part_master c ON c.partno = a.partno
                WHERE a.id ='" . $lineItemId . "'";
            $data = DB::select($strsql);
            if (count($data)) {
                $this->prItem = collect($data[0]);
                if ($this->orderType == "10" or $this->orderType == "20" ) {
                    $this->dispatchBrowserEvent('show-modelPartLineItem');
                } else if ($this->orderType == "11" or $this->orderType == "21") {
                    $this->dispatchBrowserEvent('show-modelExpenseLineItem');
                }
                //ต้องเป็น Array เพราะต้องใช้ FUnction Validation
                $this->prItem = json_decode(json_encode($this->prItem), true);
            }

            $this->setDefaultSelect2InModelLineItem();
        }

        public function deleteLineItem()
        {
            //???ก่อน Delete ต้องตรวจสอบอะไรบ้าง ?
            ///!!!ต้องลบ Delivery Plan ออกด้วย

            DB::transaction(function() 
            {
                //30-01-2022 Hold
                //History Log
                    //หา Line no
                    // $strsql = "SELECT [lineno] FROM pr_item WHERE id=". $this->deleteID;
                    // $data = DB::select($strsql);
                    // $lineno = 0;
                    // if ($data) {
                    //     $lineno = $data[0]->lineno;
                    // } 

                    // $xRandom = Str::random(20);
                    // DB::statement(
                    //     "INSERT INTO history_log(object_type, object_id, action_type, action_where, line_no, history_table, history_ref, company
                    //     , changed_by, changed_on)
                    // VALUES(?,?,?,?,?,?,?,?,?,?)",
                    //     [
                    //         'PR', $this->prHeader['prno'], 'Delete', 'Line Item', $lineno, 'history_pritem', $xRandom, auth()->user()->company
                    //         , auth()->user()->id, Carbon::now()
                    //     ]
                    // );

                    // DB::statement("INSERT INTO history_pritem(history_ref, prno, prno_id, [lineno], partno, description, purchase_unit, unit_price
                    // , currency, exchange_rate,purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                    // ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on, changed_by, changed_on)
                    // SELECT '" . $xRandom . "', prno, prno_id, [lineno], partno, description, purchase_unit, unit_price
                    // , currency, exchange_rate,purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                    // ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on, changed_by, changed_on
                    // FROM pr_item
                    // WHERE id=" . $this->deleteID
                    // );
                //History Log End

                DB::statement("DELETE FROM pr_item where id=? " , [$this->prItem['id']]);
                
                $strsql = "SELECT msg_text FROM message_list WHERE msg_no='104' AND class='PURCHASE REQUISITION'";
                $data = DB::select($strsql);
                if (count($data) > 0) {
                    $this->dispatchBrowserEvent('popup-success', [
                        'title' => $data[0]->msg_text,
                    ]);                
                }

            });

            $this->reset(['prItem']);
            return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
        }

        public function saveLineItem()
        {
            //Validaate required field
            if ($this->prHeader['ordertype'] == '10' OR $this->prHeader['ordertype'] == '20' OR $this->prHeader['ordertype'] == '30') {
                Validator::make($this->prItem, [
                    'partno' => 'required',
                    'description' => 'required',
                    'qty' => 'required|numeric|min:0|max:99999999.99', 
                    'req_date' => 'required',
                    'budget_code' => 'required',
                ])->validate();
            } else if ($this->prHeader['ordertype'] == '11' OR $this->prHeader['ordertype'] == '21') {
                Validator::make($this->prItem, [
                    'description' => 'required',
                    'purchase_unit' => 'required',
                    'currency' => 'required',
                    'exchange_rate' => 'required',
                    'purchase_unit' => 'required',
                    'unit_price' => 'required',
                    'qty' => 'required|numeric|min:0|max:99999999.99',
                    'req_date' => 'required',
                    'budget_code' => 'required',
                ])->validate();
            }


            //2022-01-30 > Add Validate
                $xValidate = true;
                //IF PR ORDER TYPE = STANDARD PARTS or BLANKET PARTS AND PR ITEM.Req Date - Current Date < PART.Lead Time
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
            //2022-01-30 > Add Validate End

            if ($xValidate){
                if ($this->isCreateLineItem) {
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
        
                        DB::statement("INSERT INTO pr_item (prno, prno_id, [lineno], partno, description, purchase_unit, unit_price, unit_price_local
                        , currency, exchange_rate, purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                        ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on)
                        VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                        ,[$this->prHeader['prno'], $this->prHeader['id'], $lineno, $this->prItem['partno'], $this->prItem['description']
                        , $this->prItem['purchase_unit'], $this->prItem['unit_price'], $this->prItem['unit_price'] * $this->prItem['exchange_rate']
                        , $this->prItem['currency'], $this->prItem['exchange_rate']
                        , $this->prItem['purchase_group'], $this->prItem['account_group'], $this->prItem['qty']
                        , $this->prItem['req_date'], $this->prItem['internal_order'] ,$this->prItem['budget_code'], $this->prItem['over_1_year_life']
                        , $this->prItem['snn_service'], $this->prItem['snn_production'] ,$this->prItem['nominated_supplier'], $this->prItem['remarks']
                        , $this->prItem['skip_rfq'], $this->prItem['skip_doa'], $this->prItem['reference_pr'], "10" ,auth()->user()->id, Carbon::now()
                        ]);
        
                        //30-01-2022 Hold
                            //History Log
                            // $xRandom = Str::random(20);
                            // DB::statement(
                            //     "INSERT INTO history_log(object_type, object_id, action_type, action_where, line_no, history_table, history_ref, company
                            //     , changed_by, changed_on)
                            // VALUES(?,?,?,?,?,?,?,?,?,?)",
                            //     [
                            //         'PR', $this->prHeader['prno'], 'Insert', 'Line Item', $lineno, 'history_pritem', $xRandom, auth()->user()->company
                            //         , auth()->user()->id, Carbon::now()
                            //     ]
                            // );
            
                            // DB::statement("INSERT INTO history_pritem(history_ref, prno, prno_id, [lineno], partno, description, purchase_unit, unit_price
                            // , currency, exchange_rate,purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                            // ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on, changed_by, changed_on)
                            // SELECT '" . $xRandom . "', prno, prno_id, [lineno], partno, description, purchase_unit, unit_price
                            // , currency, exchange_rate,purchase_group, account_group, qty, req_date, internal_order, budget_code, over_1_year_life, snn_service
                            // ,snn_production, nominated_supplier, remarks, skip_rfq, skip_doa, reference_pr, status, create_by, create_on, changed_by, changed_on
                            // FROM pr_item
                            // WHERE prno='" . $this->prHeader['prno'] . "' AND [lineno]=" .  $lineno
                            // );
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
                }else{
                    //Assign ค่าให้ Partno, account_group กรณีเป็น Free Text
                    if ($this->orderType == "11" or $this->orderType == "21"){
                        $this->prItem['partno'] = "";
                        $this->prItem['account_group'] = "";
                    }
    
                    DB::statement("UPDATE pr_item SET partno=?, description=?, purchase_unit=?, unit_price=?, unit_price_local=?, currency=?, exchange_rate=?
                        ,purchase_group=?, account_group=?, qty=?, req_date=?, internal_order=?, budget_code=?, over_1_year_life=?, snn_service=?
                        ,snn_production=?, nominated_supplier=?, remarks=?, skip_rfq=?, skip_doa=?, reference_pr=?, changed_by=?, changed_on=?
                        WHERE id=?"
                    , [
                        $this->prItem['partno'], $this->prItem['description'], $this->prItem['purchase_unit'], $this->prItem['unit_price']
                        , $this->prItem['unit_price'] * $this->prItem['exchange_rate']
                        , $this->prItem['currency'], $this->prItem['exchange_rate'], $this->prItem['purchase_group'], $this->prItem['account_group']
                        , $this->prItem['qty'], $this->prItem['req_date'], $this->prItem['internal_order'] ,$this->prItem['budget_code']
                        , $this->prItem['over_1_year_life'], $this->prItem['snn_service'], $this->prItem['snn_production'] ,$this->prItem['nominated_supplier']
                        , $this->prItem['remarks'], $this->prItem['skip_rfq'], $this->prItem['skip_doa'], $this->prItem['reference_pr']
                        ,auth()->user()->id, Carbon::now(), $this->prItem['id']
                    ]);

                    $strsql = "SELECT msg_text FROM message_list WHERE msg_no='111' AND class='PURCHASE REQUISITION'";
                    $data = DB::select($strsql);
                    if (count($data) > 0) {
                        $this->dispatchBrowserEvent('popup-success', [
                            'title' => str_replace("<PR Line No.>", $this->prItem['lineno'], $data[0]->msg_text),
                        ]);
                    }

                    $this->reset(['prItem']);
                    return redirect("purchase-requisition/purchaserequisitiondetails?mode=edit&prno=" . $this->prHeader['prno'] . "&tab=item");
                }
            }
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
            $strsql = "SELECT a.partno, a.part_name, a.purchase_uom, a.purchase_group, a.account_group, a.brand, a.model, a.skip_rfq, a.skip_doa
                    , a.primary_supplier, a.base_price AS unit_price, a.currency, b.ratio_from AS exchange_rate, a.min_order_qty, a.supplier_lead_time
                    , c.debit_gl as budget_code, a.supplier_id
                    FROM part_master a
                    LEFT JOIN currency_trans_ratios b ON a.currency = b.from_currency
                    LEFT JOIN gl_mapping c ON a.account_group = c.account_group
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
                    $this->prItem['unit_price'] = round($data[0]->unit_price,2);
                    $this->prItem['currency'] = $data[0]->currency;
                    $this->prItem['exchange_rate'] = round($data[0]->exchange_rate,2);

                    if ($data[0]->budget_code) {
                        $this->prItem['budget_code'] = $data[0]->budget_code;

                        //Default budgetcode-select2 ให้
                        $newOption = "<option value=' '>--- Please Select ---</option>";
                        $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                        foreach ($xBudgetcode_dd as $row) {
                            $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                            if ($row['account'] == $this->prItem['budget_code']) {
                                $newOption = $newOption . "selected='selected'";
                            }
                            $newOption = $newOption . ">" . $row['account'] . ':' . $row['description'] . "</option>";
                        }
                        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#budgetcode-select2']);

                    }else{
                        //Get ค่าใน budgetcode_dd ใหม่                        
                        $this->budgetcode_dd = [];
                        $strsql = "select c.account, c.description
                            FROM pr_header a
                            LEFT JOIN cost_center b ON b.cost_center = b.cost_center
                            LEFT JOIN gl_master c ON c.category = b.gl_category
                            WHERE a.cost_center='" . $this->prHeader['cost_center'] . "' AND (GETDATE() between c.valid_from AND c.valid_to) 
                            GROUP BY c.account, c.description
                            ORDER BY c.account";
                        $this->budgetcode_dd = DB::select($strsql);

                        $newOption = "<option value=' '>--- Please Select ---</option>";
                        $xBudgetcode_dd = json_decode(json_encode($this->budgetcode_dd), true);
                        foreach ($xBudgetcode_dd as $row) {
                            $newOption = $newOption . "<option value='" . $row['account'] . "' ";
                            $newOption = $newOption . ">" . $row['account'] . ':' . $row['description'] . "</option>";
                        }
                        $this->dispatchBrowserEvent('bindToSelect2', ['newOption' => $newOption, 'selectName' => '#budgetcode-select2']);
                    }
                    
                    // $this->prItem['snn_service'] = false; 
                    // $this->prItem['snn_production'] = false; 
                    $this->prItem['nominated_supplier'] = $data[0]->supplier_id;
                    // $this->prItem['reference_pr'] = ""; //???ดึงมาจากไหน
                    // $this->prItem['over_1_year_life'] = false;
                    // $this->prItem['remarks'] = "";
                    $this->prItem['min_order_qty'] = $data[0]->min_order_qty;
                    $this->prItem['supplier_lead_time'] = $data[0]->supplier_lead_time;

                }else{
                    $this->dispatchBrowserEvent('popup-alert', ['title' => "Minimum order quantity in part master not config"]);
                }
            }
        }
    //Line Item End

    public function showAddItem()
    {
        $this->isCreateLineItem = true;

        $this->reset(['prItem']);

        //สร้างฟิลด์ใน Array 
        $this->prItem['budget_code'] = "";
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

        $this->dispatchBrowserEvent('clear-select2-modal');

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
                    , prh.notify_below_25, prh.notify_below_35, company.name AS company_name, prh.ordertype, prh.requestor, prh.status
                    , prh.budget_year, prh.purpose_pr
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
                $this->prHeader['company'] = $data[0]->company;
                
                $this->orderType = $this->prHeader['ordertype'];

                if ($this->prHeader['ordertype'] == "20" OR $this->prHeader['ordertype'] == "21"){
                    $this->isBlanket = true;
                }

                //ถ้าไม่เป็น Array จะใช้ Valdation ไม่ได้
                $this->prHeader = json_decode(json_encode($this->prHeader), true); 
            }
        //PR Header End

        //Item List
            $strsql = "SELECT pri.id, pri.[lineno], pri.description, pri.partno, pri.[status] + ' : ' + sts.[description] AS [status]
                    , pri.qty, pri.purchase_unit, pri.unit_price, pri.qty * pri.unit_price AS budgettotal, pri.req_date, pri.final_price, pri.currency
                    FROM pr_item pri
                    LEFT JOIN pr_status sts ON sts.status=pri.[status]
                    WHERE pri.prno='" . $this->prHeader['prno'] . "'
                    ORDER BY pri.[lineno]";
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
            $strsql = "SELECT dec.approver, usr.name + ' ' + usr.lastname AS fullname, dvstatus.description AS status
                    , company.name AS company, usr.position
                    FROM dec_val_workflow dec
                    JOIN users usr ON usr.username = dec.approver
                    LEFT JOIN dec_val_status dvstatus ON dvstatus.status_no = dec.status
                    LEFT JOIN company ON company.company = usr.company
                    WHERE dec.approval_type='DECIDER' AND dec.ref_doc_id =" . $this->prHeader['id'];
            $this->deciderList = json_decode(json_encode(DB::select($strsql)), true);

            $strsql = "SELECT dec.approver, usr.name + ' ' + usr.lastname AS fullname, dvstatus.description AS status
                    , company.name AS company, usr.position
                    FROM dec_val_workflow dec
                    JOIN users usr ON usr.username = dec.approver
                    LEFT JOIN dec_val_status dvstatus ON dvstatus.status_no = dec.status
                    LEFT JOIN company ON company.company = usr.company
                    WHERE dec.approval_type='VALIDATOR' AND dec.ref_doc_id =" . $this->prHeader['id'];
            $this->validatorList = json_decode(json_encode(DB::select($strsql)), true);

            //ตรวจสอบว่าเป็น Validator หรือ Decider หรือไม่
            $strsql = "SELECT approver FROM dec_val_workflow WHERE ref_doc_id=" . $this->prHeader['id'] . " 
                AND approver = '" . auth()->user()->username . "'";
            $data = DB::select($strsql);
            if ($data) {
                $this->isValidator_Decider = true;
            }

            //ตรวจสอบว่าจะ Enable ปุ่ม ของ Decider ได้หรือไม่ ***ใช้ตัวล่างแทน
                // $strsql = "SELECT approver FROM dec_val_workflow 
                //             WHERE approval_type = 'VALIDATOR' AND ref_doc_id =" . $this->prHeader['id']; 
                // $strsql2 = "SELECT approver FROM dec_val_workflow 
                //             WHERE approval_type = 'VALIDATOR' AND status = 'APPROVED' AND ref_doc_id =" . $this->prHeader['id']; 

                // if (count(DB::select($strsql)) == count(DB::select($strsql2))) {
                //     $this->isValidatorApprove = true;
                // };
            //ตรวจสอบว่าจะ Enable ปุ่ม ของ Decider ได้หรือไม่ ***ใช้ตัวล่างแทน End

            //ตรวจสอบว่า Validator Approve ครบหรือไม่ ถ้าครบจะ Enable ปุ่มให้ Decider Approve
            $strsql = "SELECT approver FROM dec_val_workflow 
                    WHERE approval_type = 'VALIDATOR' AND status <> '30' AND ref_doc_id =" . $this->prHeader['id']; 

            if (count(DB::select($strsql)) == 0) {
                $this->isValidatorApprove = true;
            };

            //ตรวจสอบว่า Decider Approve แล้วหรือไม่ ถ้า Approve จะ Disable ปุ่ม Approve ของ Validator
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
        if ($this->prHeader['requested_for'] != " " AND $this->prHeader['requested_for'] != "") {
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

        $this->prHeader['requested_for'] = auth()->user()->id;

        $strsql = "SELECT TOP 1 address_id, SUBSTRING(address,1,30) AS address FROM site WHERE company='" . auth()->user()->company . "'";
        $data = DB::select($strsql);
        if (count($data)) {
            $this->prHeader['delivery_address'] = $data[0]->address_id;
        }

        $this->prHeader['request_date'] = date_format(Carbon::now(), 'Y-m-d');

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

        //budget year
        $this->prHeader['budget_year'] =  "";
        $this->prHeader['purpose_pr'] =  "";

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

            //Budget Year
            $xYear = intval(date_format(Carbon::now(),'Y'));
            $this->budgetyear_dd = [['year' => $xYear], ['year' => $xYear + 1], ['year' => $xYear + 2]];
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

            //purchase_group
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
            // $strsql = "SELECT a.*, b.name + ' ' + b.lastname as fname
            // FROM history_log a
            // JOIN users b ON b.id = a.changed_by
            // WHERE a.object_type = 'PR' AND a.object_id='" . $this->prHeader['prno'] . "' ORDER BY a.id";
            // $historyLog = (new Collection(DB::select($strsql)))->paginate(10);

            $strsql = "SELECT a.approver, b.name + ' ' + b.lastname as fullname, a.approval_type, b.company, b.department, b.position
                , c.description as status, a.reject_reason, FORMAT(a.submitted_date, 'dd-MMMM-yy') as submitted_date
                , FORMAT(a.completed_date, 'dd-MMMM-yy') as completed_date
                FROM dec_val_workflow_log a
                LEFT JOIN users b ON a.approver = b.username
                LEFT JOIN dec_val_status c ON a.status = c.status_no
                WHERE a.refdoc_type='10' AND a.refdoc_id=" . $this->prHeader['id'];
            $approval_history = (new Collection(DB::select($strsql)))->paginate(10);

            return view('livewire.purchase-requisition.purchase-requisition-details',[
                'approval_history' => $approval_history,
            ]);
        }
        
    }
}
