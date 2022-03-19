<div>
    <x-loading-indicator target="validatorDeciderApprove" />
    <x-loading-indicator target="validatorDeciderReject" />
    <x-loading-indicator target="releaseForSourcing" />
    <x-loading-indicator target="addAttachment" />
    <x-loading-indicator target="attachment_file" />
    <x-loading-indicator target="cancelPrHeader" />  
    <x-loading-indicator target="selectedRows" />      

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-6" style="font-size: 20px; color: #C3002F">
                    Purchase Requsition No : <span>{{ $prHeader['prno'] }}</span>
                </div>
                <div class="col-sm-6">
                    {{-- <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Purchase Requisition</li>
                        <li class="breadcrumb-item active" style="color: #C3002F;">Purchase Requisition Details</li>
                    </ol> --}}
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        {{-- Header --}}
        <div class="card shadow-none border rounded" id="PR_Header">
            <div class="card-header my-card-header">
                <div class="row py-0 my-0">
                    <div class="col-12 d-flex justify-content-between">
                        <div>
                            Order Type : <span>{{ $prHeader['ordertypename'] }}</span>
                        </div>
                        <div>
                            Status : <span>{{ $prHeader['statusname'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body my-card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="prno">Requestor</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prHeader.requestor_name">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Phone (Requestor)<span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.phone"
                            @if($prHeader['status'] >= '20' OR $isValidator_Decider == true OR $isValidator_Decider == true) readonly @endif>
                        @error('phone') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Ext. (Requestor)<span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.extention"
                            @if($prHeader['status'] >= '20' OR $isValidator_Decider == true) readonly @endif>
                        @error('extention') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-3">
                        <label class="">Request Date</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="prHeader.request_date" id="request_date" readonly="true"
                                :error="'date'"/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Requested For <span style="color: red">*</span></label>
                        @if($prHeader['status'] >= '20' OR $isValidator_Decider == true) 
                        <x-select2 id="requestedfor-select2" disabled="true" wire:model.defer="prHeader.requested_for">
                            @foreach($requested_for_dd as $row)
                            <option value="{{ $row->id }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                        @else
                        <x-select2 id="requestedfor-select2" wire:model.defer="prHeader.requested_for">
                            @foreach($requested_for_dd as $row)
                            <option value="{{ $row->id }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                        @endif

                        @error('requested_for') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Phone (Requestor For)<span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.phone_reqf"
                            @if($prHeader['status'] >= '20' OR $isValidator_Decider == true) readonly @endif>
                        @error('phone_reqf') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Ext. (Requestor For)<span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.extention_reqf"
                            @if($prHeader['status'] >= '20' OR $isValidator_Decider == true) readonly @endif>
                        @error('extention_reqf') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Email (Requestor For)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.email_reqf"
                            @if($prHeader['status'] >= '20' OR $isValidator_Decider == true) readonly @endif>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Company</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prHeader.company_name">
                    </div>
                    <div class="col-md-3">
                        <label>Site</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model="prHeader.site_description">
                    </div>
                    <div class="col-md-3">
                        <label>Function</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prHeader.functions">
                    </div>
                    <div class="col-md-3">
                        <label>Department</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prHeader.department">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Division</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prHeader.division">
                    </div>
                    <div class="col-md-3">
                        <label>Section</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prHeader.section">
                    </div>
                    <div class="col-md-3">
                        <label>Cost Center (Department Code) <span style="color: red">*</span></label>
                        <select class="form-control form-control-sm" id="cost_center" wire:model="prHeader.cost_center" disabled>
                            {{-- @if($prHeader['status'] >= '20' OR $isValidator_Decider == true) disabled @endif> --}}
                            <option value="">--- Please Select ---</option>
                            @foreach($cost_center_dd as $row)
                            <option value="{{ $row->cost_center }}">
                                {{ $row->cost_center }}
                            </option>
                            @endforeach
                        </select>
                        @error('cost_center') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Cost Center Description</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model="prHeader.costcenter_desc">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Buyer <span style="color: red">*</span></label>
                        @if($prHeader['status'] >= '20' OR $isValidator_Decider == true)
                        <x-select2 id="buyer-select2" disabled="true" wire:model.defer="prHeader.buyer">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($buyer_dd as $row)
                            <option value="{{ $row->username }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                        @else
                        <x-select2 id="buyer-select2" wire:model.defer="prHeader.buyer">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($buyer_dd as $row)
                            <option value="{{ $row->username }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                        @endif
                        @error('buyer') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Delivery Location <span style="color: red">*</span></label>
                        <select class="form-control form-control-sm" id="delivery_address" wire:model="prHeader.delivery_address"
                            @if($prHeader['status'] >= '20' OR $isValidator_Decider == true) disabled @endif>
                            <option value="">--- Please Select ---</option>
                            @foreach($delivery_address_dd as $row)
                            <option value="{{ $row->address_id }}">
                                {{ $row->address_id }} : {{ $row->delivery_location }}
                            </option>
                            @endforeach
                        </select>
                        @error('delivery_address') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Budget Year <span style="color: red">*</span></label>
                        <select class="form-control form-control-sm" id="budget_year" wire:model.defer="prHeader.budget_year"
                            @if($prHeader['status'] >= '20' OR $isValidator_Decider == true) disabled @endif>
                            <option value="">--- Please Select ---</option>
                            @foreach($budgetyear_dd as $row)
                            <option value="{{ $row['year'] }}">
                                {{ $row['year'] }}
                            </option>
                            @endforeach
                        </select>
                        @error('budget_year') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-3">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label for="prno">Capex No</label>
                        <input class="form-control form-control-sm" type="text" maxlength="50" wire:model.defer="prHeader.capexno"
                            @if($prHeader['status'] >= '20' OR $isValidator_Decider == true) readonly @endif>
                    </div>
                    <div class="col-6">
                        <label for="prno">Purpose of PR <span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.purpose_pr"
                            @if($prHeader['status'] >= '20' OR $isValidator_Decider == true) readonly @endif>
                        @error('purpose_pr') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>
        {{-- Header End--}}

        @if ($isBlanket)
        {{-- Blanket Request --}}
        <div class="card shadow-none border rounded">
            <div class="card-header my-card-header">
                Blanket Request
            </div>
            <div class="card-body my-card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="">Valid Until <span style="color: red">*</span></label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="prHeader.valid_until" id="valid_until"
                                :error="'date'"/>
                        </div>
                        @error('valid_until') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        {{-- Remove Ref. 18-Jan-2022: fix notification at D-30, D-15, D-7 before expiry. --}}
                        {{-- <label for="date_to_notify">Days to Notify before BPO Expiry</label>
                        <input class="form-control form-control-sm text-right" type="number" step="1" id="days_to_notify" wire:model.defer="prHeader.days_to_notify"> --}}
                    </div>
                    <div class="col-md-6">
                        <label>Notify when remaining value is below</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="prHeader.notify_below_10">
                                <label class="form-check-label">10%</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="prHeader.notify_below_25">
                                <label class="form-check-label">25%</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="prHeader.notify_below_35">
                                <label class="form-check-label">35%</label>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        {{-- Blanket Request End--}}
        @endif

        @if ($prHeader['prno'])
        <!-- .Tab Header -->
            <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                <li class="nav-item" wire:ignore>
                    <a class="nav-link {{ $currentTab == 'item' ? 'active' : '' }}" id="pills-lineitem-tab" data-toggle="pill" href="#pills-lineitem" role="tab" 
                        aria-controls="pills-lineitem" aria-selected="true">Line Item</a>
                </li>
                @if ($isBlanket)
                <li class="nav-item" wire:ignore>
                    <a class="nav-link {{ $currentTab == 'delivery' ? 'active' : '' }}" id="pills-delivery-tab" data-toggle="pill" href="#pills-delivery" role="tab" 
                        aria-controls="pills-delivery" aria-selected="false">Estimate Delivery Plan</a>
                </li>
                @endif            
                <li class="nav-item" wire:ignore>
                    <a class="nav-link {{ $currentTab == 'auth' ? 'active' : '' }}" id="pills-auth-tab" data-toggle="pill" href="#pills-auth" role="tab" 
                        aria-controls="pills-auth" aria-selected="false">Authorization</a>
                </li>
                <li class="nav-item" wire:ignore>
                    <a class="nav-link {{ $currentTab == 'attachments' ? 'active' : '' }}" id="pills-attachments-tab" data-toggle="pill" href="#pills-attachments" role="tab" 
                        aria-controls="pills-attachments" aria-selected="false">Attachments</a>
                </li>
                <li class="nav-item" wire:ignore>
                    <a class="nav-link {{ $currentTab == 'history' ? 'active' : '' }}" id="pills-history-tab" data-toggle="pill" href="#pills-history" role="tab" 
                        aria-controls="pills-history" aria-selected="false">History Log</a>
                </li>
            </ul>
        <!-- Tab Header End -->

        {{-- Tab Content --}}        
            <div class="tab-content m-0 pb-0" id="pills-tabContent">
                {{-- Tab lineitem --}}  
                    <div class="tab-pane fade {{ $currentTab == 'item' ? 'show active' : '' }}" id="pills-lineitem" role="tabpanel" aria-labelledby="pills-lineitem-tab" wire:ignore.self>
                        <div class="row">
                            <div class="col-md-12">
                                @if ( $prHeader['status'] < '20' ) 
                                <div class="d-flex justify-content-end mb-2">
                                    <button wire:click.prevent="showAddItem" class="btn btn-sm btn-danger"><i class="fas fa-plus-square mr-1"></i>Add Item</button>
                                </div>
                                @endif
                            </div>
                        </div>                
                        <div class="row m-0 p-0">
                            <div class="col-md-12">
                                <table class="table table-sm nissanTB">
                                    <thead>
                                    <tr class="text-center">
                                        <th scope="col">Line No.</th>
                                        <th scope="col">Part No.</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">UOM</th>
                                        <th scope="col">Unit Price</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Currency</th>
                                        <th scope="col">
                                        @if($prHeader['ordertype'] == '20' OR $prHeader['ordertype'] == '21')
                                        Earliest Delivery Date
                                        @else
                                        Requested Delivery Date
                                        @endif
                                        </th>
                                        <th scope="col">Final Price</th>
                                        <th scope="col">PO No.</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($itemList as $row)
                                    <tr>
                                        <td>
                                            <div d-inline ml-2>
                                                <input wire:model="selectedRows" type="checkbox" value="{{ $row->id }}"
                                                    id="{{ $row->lineno }}">
                                                <span>{{ $loop->iteration + $itemList->firstitem()-1 }}</span>
                                            </div>
                                        </td>
                                        <td scope="col">{{ $row->partno }}</td>
                                        <td scope="col">{{ $row->description }}</td>                                
                                        <td scope="col">{{ $row->status }}</td>
                                        <td scope="col" class="text-right pr-2">{{ number_format($row->qty, 2) }}</td>
                                        <td scope="col" class="text-center">{{ $row->purchase_unit }}</td>
                                        <td scope="col" class="text-right pr-2">{{ number_format($row->unit_price, 2) }}</td>
                                        <td scope="col" class="text-right pr-2">{{ number_format( $row->budgettotal, 2) }}</td>
                                        <td scope="col" class="text-center">{{ $row->currency }}</td>
                                        <td scope="col" class="text-center">{{ \Carbon\Carbon::parse( $row->req_date)->format('d-M-Y') }} </td>
                                        <td scope="col" class="text-right pr-2">{{ number_format($row->final_price, 2) }} </td>
                                        <td scope="col">{{ $row->reference_po }}</td>
                                        <td scope="col">
                                            <center>
                                                <a href="" wire:click.prevent="editLineItem('{{ $row->id }}')">
                                                    <i class="fa fa-edit mr-2"></i>
                                                </a>
                                            </center>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-1">
                                {{ $itemList->links() }}
                            </div>
                        </div>
                    </div>                  
                {{-- Tab lineitem End --}}  

                {{-- Tab Delivery --}}            
                    <div class="tab-pane fade {{ $currentTab == 'delivery' ? 'show active' : '' }}" id="pills-delivery" role="tabpanel" aria-labelledby="pills-delivery-tab" wire:ignore.self>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Select Item</label>
                                <select class="form-control form-control-sm" id="" wire:model="prDeliveryPlan.ref_prline_id">
                                    <option value="">--- Please Select ---</option>
                                    @foreach($prLineNo_dd as $row)
                                    <option value="{{ $row->id }}">
                                        {{ $row->lineno }} : {{ $row->description }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('ref_prline_id') <span class="text-red">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label>Quantity</label>
                                <input class="form-control form-control-sm" type="number" step="1" wire:model.defer="prDeliveryPlan.qty">
                                @error('qty') <span class="text-red">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-3">
                                <label>UoM</label>
                                <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prDeliveryPlan.uom">
                            </div>
                            <div class="col-md-3">
                                <label>Planned Delivery Date</label>
                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">
                                            <i class="fas fa-calendar"></i>
                                        </span>
                                    </div>
                                    <x-datepicker wire:model.defer="prDeliveryPlan.delivery_date" id="plan_delivery_date_1"
                                        :error="'date'"/>
                                </div>
                                @error('delivery_date') <span class="text-red">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>Total QTY</label>
                                <input class="form-control form-control-sm text-right pr-2" type="text" readonly wire:model.defer="prDeliveryPlan.totalQty">
                            </div>
                            <div class="col-md-3">
                                <label>Total Planned</label>
                                <input class="form-control form-control-sm text-right pr-2" type="text" readonly wire:model.defer="prDeliveryPlan.totalQtyPlanned">
                            </div>
                            <div class="col-md-3"></div>
                            <div class="col-md-3 d-flex justify-content-end">
                                @if ( $prHeader['status'] < '20' ) 
                                <button wire:click.prevent="addDeliveryPlan" class="btn btn-sm btn-danger mt-auto" {{ $enableAddPlan ? '' : 'disabled' }}>
                                    <i class="fas fa-plus-square mr-1"></i>Add Plan</button>
                                @endif
                            </div>
                        </div>
                        <div class="row m-0 p-0">
                            <div class="col-md-12">
                                <table class="table table-sm nissanTB">
                                    <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Ref Line No.</th>
                                        <th scope="col">Description</th>
                                        <th scope="col">Part No.</th>
                                        <th scope="col">Qty</th>
                                        <th scope="col">UoM</th>
                                        <th scope="col">Planned Delivery Date</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($prListDeliveryPlan as $row)
                                    <tr>
                                        <td scope="col">{{ $loop->iteration + $prListDeliveryPlan->firstitem()-1 }}</td>
                                        <td scope="col">{{ $row->lineno }}</td>
                                        <td scope="col">{{ $row->description }}</td>
                                        <td scope="col">{{ $row->partno }}</td>
                                        <td scope="col" class="text-right pr-2">{{ number_format($row->qty, 2) }}</td>
                                        <td scope="col">{{ $row->purchase_unit }}</td>
                                        <td scope="col">{{ \Carbon\Carbon::parse( $row->delivery_date)->format('d-M-Y') }} </td>
                                        <td scope="col">
                                            @if ( $prHeader['status'] < '20' ) 
                                            <center>
                                                <a href="" wire:click.prevent="confirmDelete('{{ $row->id }}', 'deliveryPlan')">
                                                    <i class="fas fa-times text-center" style="color: red"></i>
                                                </a>
                                            </center>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-1">
                                {{ $prListDeliveryPlan->links() }}
                            </div>
                        </div>
                    </div>
                {{-- Tab Delivery End --}}
                
                {{-- Tab Authorization --}}
                    <div class="tab-pane fade {{ $currentTab == 'auth' ? 'show active' : '' }}" id="pills-auth" role="tabpanel" aria-labelledby="pills-auth-tab" wire:ignore.self>
                        {{-- แสดงเฉพาะ Status < RFQ Created --}}
                        @if ( $prHeader['status'] < '31' )

                            {{-- Decider --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card shadow-none border rounded">
                                            <div class="card-header my-card-header">
                                                Decider
                                            </div>
                                            <div class="card-body my-card-body">
                                                {{-- ตรวจสอบว่าเป็น Validator หรือ Decider หรือไม่ --}}
                                                @if ($isValidator_Decider <> true)
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>Select Decider</label>
                                                        <x-select2 id="decider-select2" wire:model.defer="decider.username">
                                                            <option value="">--- Please Select ---</option>
                                                            @foreach($decider_dd as $row)
                                                            <option value="{{ $row->username }}">
                                                                {{ $row->fullname }}
                                                            </option>
                                                            @endforeach
                                                        </x-select2>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Company</label>
                                                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="decider.company">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Department</label>
                                                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="decider.department">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Position</label>
                                                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="decider.position">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-9">
                                                        <b>Note:</b> <br/>
                                                        <div class="ml-3">
                                                            <small style="color: blue">Request amount > 20,000 THB approved by GM or GM up</small> <br/>
                                                            <small style="color: blue">Request amount <= 20,000 THB approved by AGM/DGM</small> <br/>
                                                        </div>
                                                        
                                                    </div>
                                                    <div class="col-md-3 text-right">
                                                        @if ( $prHeader['status'] < '20' ) 
                                                        <button class="btn btn-sm btn-danger" {{ $deciderList ? 'disabled' : ''}} 
                                                            wire:click.prevent="addDecider"><i class="fas fa-plus-square mr-1"></i>Confirm</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif
    
                                                {{-- ตรวจสอบว่าเป็น Validator หรือ Decider หรือไม่ --}}
                                                @if ($isValidator_Decider)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-sm">
                                                            <thead>
                                                            <tr>
                                                                <th scope="col">User ID</th>
                                                                <th scope="col">Name</th>
                                                                <th scope="col">Company</th>
                                                                <th scope="col">Position</th>
                                                                <th scope="col">Status</th>
                                                                <th scope="col" style="width: 20%">Action</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach ($deciderList as $index => $row)
                                                            <tr>
                                                                <td scope="col">{{ $deciderList[$index]['approver'] }}</td>
                                                                <td scope="col">{{ $deciderList[$index]['fullname'] }}</td>
                                                                <td scope="col">{{ $deciderList[$index]['company'] }}</td>
                                                                <td scope="col">{{ $deciderList[$index]['position'] }}</td>
                                                                <td scope="col">{{ $deciderList[$index]['statusname'] }}</td>
                                                                <td scope="col">
                                                                    @if (auth()->user()->username == $deciderList[$index]['approver'] 
                                                                        AND $deciderList[$index]['status'] == '20')
                                                                    <button class="btn btn-sm btn-danger" wire:click.prevent="validatorDeciderApprove" >Approve</button>
                                                                    <button class="btn btn-sm btn-light" wire:click.prevent="validatorDeciderReject" >Reject</button>
                                                                    @else
                                                                    <button class="btn btn-sm btn-danger" disabled wire:click.prevent="" >Approve</button>
                                                                    <button class="btn btn-sm btn-light" disabled wire:click.prevent="" >Reject</button>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-sm">
                                                            <thead>
                                                            <tr>
                                                                <th scope="col">User ID</th>
                                                                <th scope="col">Name</th>
                                                                <th scope="col">Company</th>
                                                                <th scope="col">Position</th>
                                                                <th scope="col">Status</th>
                                                                <th scope="col"></th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @foreach ($deciderList as $index => $row)
                                                            <tr>
                                                                <td scope="col">{{ $deciderList[$index]['approver'] }}</td>
                                                                <td scope="col">{{ $deciderList[$index]['fullname'] }}</td>
                                                                <td scope="col">{{ $deciderList[$index]['company'] }}</td>
                                                                <td scope="col">{{ $deciderList[$index]['position'] }}</td>
                                                                <td scope="col">{{ $deciderList[$index]['statusname'] }}</td>
                                                                <td scope="col">
                                                                    @if ( $prHeader['status'] < '20' ) 
                                                                    <center>
                                                                        <a href="" wire:click.prevent="confirmDelete('{{ $deciderList[$index]['approver'] }}', 'decider')">
                                                                            <i class="fas fa-times text-center" style="color: red"></i>
                                                                        </a>
                                                                    </center>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                @endif
    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {{-- Decider End--}}

                            {{-- Validator --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card shadow-none border rounded">
                                            <div class="card-header my-card-header">
                                                Validator
                                            </div>
                                            <div class="card-body my-card-body">

                                                {{-- ตรวจสอบว่าเป็น Validator หรือ Decider หรือไม่ --}}
                                                @if ($isValidator_Decider <> true)
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <label>Select Validator</label>
                                                        <x-select2 id="validator-select2" wire:model.defer="validator.username">
                                                            <option value="">--- Please Select ---</option>
                                                            @foreach($validator_dd as $row)
                                                            <option value="{{ $row->username }}">
                                                                {{ $row->fullname }}
                                                            </option>
                                                            @endforeach
                                                        </x-select2>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Company</label>
                                                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="validator.company">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Department</label>
                                                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="validator.department">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label>Position</label>
                                                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="validator.position">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-12 text-right">
                                                        @if ( $prHeader['status'] < '20' ) 
                                                        <button class="btn btn-sm btn-danger"
                                                            wire:click.prevent="addValidator"><i class="fas fa-plus-square mr-1"></i>Add</button>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endif

                                                {{-- ตรวจสอบว่าเป็น Validator หรือ Decider หรือไม่ --}}
                                                @if ($isValidator_Decider)
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                <th scope="col">User ID</th>
                                                                <th scope="col">Name</th>
                                                                <th scope="col">Company</th>
                                                                <th scope="col">Position</th>
                                                                <th scope="col">Status</th>
                                                                <th scope="col" style="width: 20%">Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($validatorList as $index => $row)
                                                                <tr>
                                                                    <td scope="col">{{ $validatorList[$index]['approver'] }}</td>
                                                                    <td scope="col">{{ $validatorList[$index]['fullname'] }}</td>
                                                                    <td scope="col">{{ $validatorList[$index]['company'] }}</td>
                                                                    <td scope="col">{{ $validatorList[$index]['position'] }}</td>
                                                                    <td scope="col">{{ $validatorList[$index]['statusname'] }}</td>
                                                                    <td scope="col">
                                                                        @if (auth()->user()->username == $validatorList[$index]['approver'] 
                                                                            AND $validatorList[$index]['status'] == '20')
                                                                        <button class="btn btn-sm btn-danger" wire:click.prevent="validatorDeciderApprove" >Approve</button>
                                                                        <button class="btn btn-sm btn-light" wire:click.prevent="validatorDeciderReject" >Reject</button>
                                                                        @else
                                                                        <button class="btn btn-sm btn-danger" disabled wire:click.prevent="" >Approve</button>
                                                                        <button class="btn btn-sm btn-light" disabled wire:click.prevent="" >Reject</button>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                @else
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <table class="table table-sm">
                                                            <thead>
                                                                <tr>
                                                                <th scope="col">Seq</th>
                                                                <th scope="col">User ID</th>
                                                                <th scope="col">Name</th>
                                                                <th scope="col">Company</th>
                                                                <th scope="col">Position</th>
                                                                <th scope="col">Status</th>
                                                                <th scope="col"></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($validatorList as $index => $row)
                                                                <tr>
                                                                    <td scope="col">{{ $validatorList[$index]['seqno'] }}</td>
                                                                    <td scope="col">{{ $validatorList[$index]['approver'] }}</td>
                                                                    <td scope="col">{{ $validatorList[$index]['fullname'] }}</td>
                                                                    <td scope="col">{{ $validatorList[$index]['company'] }}</td>
                                                                    <td scope="col">{{ $validatorList[$index]['position'] }}</td>
                                                                    <td scope="col">{{ $validatorList[$index]['statusname'] }}</td>
                                                                    <td scope="col">
                                                                        @if ( $prHeader['status'] < '20' ) 
                                                                        <center>
                                                                            <a href="" wire:click.prevent="confirmDelete('{{ $validatorList[$index]['approver'] }}', 'validator')">
                                                                                <i class="fas fa-times text-center" style="color: red"></i>
                                                                            </a>
                                                                        </center>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {{-- Validator End--}}

                            {{-- ตรวจสอบว่าเป็น Validator หรือ Decider หรือไม่ --}}
                            @if ($isValidator_Decider)
                            <div class="row mb-3">
                                <div class="col-md-12">
                                    <label>Rejection Reason</label>

                                    {{-- @if (auth()->user()->username == $validatorList[$index]['approver'] 
                                        AND $validatorList[$index]['status'] == '20')                                        
                                    <textarea class="form-control form-control-sm" rows="2" maxlength="250" wire:model.defer="rejectReason"></textarea>
                                    
                                    @elseif (auth()->user()->username == $deciderList[$index]['approver'] 
                                        AND $deciderList[$index]['status'] == '20')
                                    <textarea class="form-control form-control-sm" rows="2" maxlength="250" wire:model.defer="rejectReason"></textarea>

                                    @else
                                    <textarea class="form-control form-control-sm" rows="2" maxlength="250" disabled wire:model.defer="rejectReason"></textarea>

                                    @endif --}}

                                    <textarea class="form-control form-control-sm" rows="2" maxlength="200" wire:model.defer="rejectReason"></textarea>
                                    <div id="count" class="d-flex justify-content-end">
                                        <span id="current_count">0</span>
                                        <span id="maximum_count">/ 250</span>
                                    </div>
                                </div>
                            </div>
                            @endif

                        @endif
                        
                        {{-- Approval History --}}
                            <div class="card shadow-none border rounded">
                                <div class="card-header my-card-header">
                                    Approval History
                                </div>
                                <div class="card-body my-card-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                    <th scope="col">Seq</th>
                                                    <th scope="col">User ID</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Company</th>
                                                    <th scope="col">Department</th>
                                                    <th scope="col">Position</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Reject Reason</th>
                                                    <th scope="col">Submitted Date</th>
                                                    <th scope="col">Completed Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($approval_history as $row)
                                                    <tr>
                                                        <td scope="col">{{ $loop->iteration + $approval_history->firstitem()-1  }}</td>
                                                        <td scope="col">{{ $row->approver }}</td>
                                                        <td scope="col">{{ $row->fullname }}</td>
                                                        <td scope="col">{{ $row->approval_type }}</td>
                                                        <td scope="col">{{ $row->company }}</td>
                                                        <td scope="col">{{ $row->department }}</td>
                                                        <td scope="col">{{ $row->position }}</td>
                                                        <td scope="col">{{ $row->status }}</td>
                                                        <td scope="col">{{ $row->reject_reason }}</td>
                                                        <td scope="col">{{ $row->submitted_date }}</td>
                                                        <td scope="col">{{ $row->completed_date }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 mb-1">
                                            {{ $approval_history->links() }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        {{-- Approval History End --}}

                    </div>
                {{-- Tab Authorization End --}}  
                
                {{-- Tab Attachments --}} 
                    <div class="tab-pane fade {{ $currentTab == 'attachments' ? 'show active' : '' }}" id="pills-attachments" role="tabpanel" aria-labelledby="pills-attachments-tab" wire:ignore.self>
                        @if ($isRequester_RequestedFor == true)
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Attachment Level <span style="color: blue; font-weight: normal">(Please select before add new files.)</span></label>
                                    <x-select2-multiple id="attachment_lineno-select2" wire:model.defer="attachment_lineno">
                                        <option value="0" selected="selected">0 : Level PR Header</option> 
                                        @foreach($prLineNoAtt_dd as $row)
                                        <option value="{{ $row->lineno }}">
                                            {{ $row->lineno }} : {{ $row->description }}
                                        </option>
                                        @endforeach
                                    </x-select2-multiple>
                                </div>
                                <div class="col-md-4">
                                    <label>Document Type</label>
                                    <select class="form-control form-control-sm" wire:model="attachment_filetype">
                                        <option value="">--- Please Select ---</option>
                                        <option value="General_Documents">General Documents</option>
                                        <option value="eDecision">eDecision</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    @if ($attachment_filetype == 'eDecision')
                                    <label>eDecision No.</label>
                                    <input class="form-control form-control-sm" type="text" 
                                        wire:model="attachment_edecisionno">
                                    @endif                                    
                                </div>
                            </div>

                            <form autocomplete="off" enctype="multipart/form-data" wire:submit.prevent="addAttachment">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-9">
                                        <div class="custom-file">
                                            <input wire:model="attachment_file" type="file" class="custom-file-input" id="customFile" multiple>
                                            @error('attachment_file.*')
                                            <div class="alert alert-danger" role="alert">
                                                The attachment file must not be greater than 5 mb.
                                            </div>
                                            @enderror
                                            <label class="custom-file-label" for="customFile">Browse Files</label>

                                            {{-- ตรวจสอบขนาดไฟล์ และแสดงรายชื่อไฟล์ --}}
                                            @if ($attachment_file)
                                                @foreach ($attachment_file as $k => $file)
                                                {{ $file->getClientOriginalName() }} ({{ $this->formatSizeUnits($file->getSize()) }}) 
                                                <a href="" wire:click.prevent="deleteAttachmentFile('{{ $k }}')">
                                                    <i class="fas fa-times text-center mr-1" style="color: red"></i>
                                                </a>
                                                @if ($file->getSize() > $maxSize)
                                                <span class="text-danger">File size is too large.</span>
                                                @endif
                                                <br/>
                                                @endforeach
                                            @endif
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-left">
                                        @if ( $prHeader['status'] < '20' ) 
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-cloud-upload-alt mr-1"></i>Upload</button>
                                        <span style="vertical-align:bottom; color:red">max file size 5 mb.</span> 
                                        @endif
                                    </div>
                                </div>
                            </form>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm nissanTB">
                                    <thead>
                                    <tr>
                                        <th scope="col">File Name</th>
                                        <th scope="col">Document Type</th>
                                        <th scope="col">eDecision No.</th>
                                        <th scope="col">Ref Document</th>
                                        <th scope="col">Ref Document No.</th>
                                        <th scope="col">Line No.</th>
                                        <th scope="col">Attached By</th>
                                        <th scope="col">Attached Date/Time</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($attachmentFileList as $row)
                                    <tr>
                                        <td scope="col">{{ $row->file_name }}</td>
                                        <td scope="col">{{ $row->file_type }}</td>
                                        <td scope="col">{{ $row->edecision_no }}</td>
                                        <td scope="col">{{ $row->ref_doctype }}</td>
                                        <td scope="col">{{ $row->ref_docno }}</td>
                                        <td scope="col">{{ str_replace('[', '', (str_replace(']', '', (str_replace('"', '', $row->ref_lineno))))) }}</td>
                                        <td scope="col">{{ $row->create_by }}</td>
                                        <td scope="col">{{ $row->create_on }}</td>
                                        <td scope="col" class="d-flex justify-content-between">
                                            @if ($isRequester_RequestedFor == true AND $prHeader['status'] < '20' )
                                            <div>
                                                <a href="" wire:click.prevent="confirmDelete('{{ $row->id }}', 'attachment')">
                                                    <i class="fas fa-times text-center mr-2" style="color: red"></i>
                                                </a>
                                            </div>
                                            <div>
                                                <a href="" wire:click.prevent="editAttachment('{{ $row->id }}')">
                                                    <i class="fa fa-edit mr-2"></i>
                                                </a>
                                            </div>
                                            @endif
                                            <div>
                                                <a href="{{url('storage/attachments/' . $row->file_path )}}">
                                                    <i class="fas fa-download mr-2"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-1">
                                {{ $attachmentFileList->links() }}
                            </div>
                        </div>
                    </div>
                {{-- Tab Attachments End --}}
                
                {{-- Tab History --}}           
                    <div class="tab-pane fade {{ $currentTab == 'history' ? 'show active' : '' }}" id="pills-history" role="tabpanel" aria-labelledby="pills-history-tab" wire:ignore.self>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-sm nissanTB">
                                    <thead>
                                    <tr>
                                        <th scope="col">Line No.</th>
                                        <th scope="col">Field</th>
                                        <th scope="col">Value</th>
                                        <th scope="col">Changed By</th>
                                        <th scope="col">Changed On</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if(!empty($historylog))
                                        @foreach ($historylog as $row)
                                        <tr>
                                            <td scope="col">{{ $row->line_no }} </td>
                                            <td scope="col">{{ $row->field }} </td>
                                            <td scope="col">{{ $row->new_value }} </td>
                                            @if($row->new_value == "INSERT")
                                            <td></td>
                                            <td></td>
                                            @else
                                            <td scope="col">{{ $row->name." ".$row->lastname  }} </td>
                                            <td scope="col">{{ \Carbon\Carbon::parse($row->changed_on)->format('d-M-Y H:i:s') }} </td>
                                            @endif
                                            

                                        </tr>
                                        @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-12 mb-1">
                                {{ $historylog->links() }}
                            </div>
                        </div> --}}
                    </div>
                {{-- Tab History End --}} 

            </div>
        {{-- Tab Content End --}}
        @endif

        {{-- Actions --}}
        <div class="row mt-5">
            <div class="col-md-12">
                <hr width="100%">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <div>
                        @if ( $prHeader['prno'] <> '' )

                            {{-- 10-Planned --}}
                            @if ( $prHeader['status'] == '10' AND $isValidator_Decider != true) 
                            <button wire:click.prevent="releaseForSourcing" class="btn btn-sm btn-danger">
                                <i class="fas fa-check mr-2"></i>Release for Sourcing</button>
                            @endif

                            {{-- 40-ConfirmedFinalPrice, 50-ReleasedForPO --}}
                            @if (($prHeader['status'] == '40' OR  $prHeader['status'] == '50') AND $isValidator_Decider != true) 
                            <button wire:click.prevent="releaseForPO" class="btn btn-sm btn-danger">
                                <i class="fas fa-check mr-2"></i>Release for PO</button>
                            @endif

                            {{-- Between 30-PRAuthorized and 60-Closed --}}
                            {{-- @if ( $prHeader['status'] >= '30' OR $isValidator_Decider == true AND  $prHeader['status'] <= '60')  --}}
                            @if ($prHeader['status'] >= '30' AND  $prHeader['status'] <= '60') 
                            <a href="PRForm/{{ $prHeader['prno'] }}" target="_blank">
                                <button class="btn btn-sm btn-danger">
                                    <i class="fas fa-print mr-2"></i>Print</button>
                            </a>
                            @endif

                            {{-- Between 10=Planned and 40-Confirmed Final Price AND isRequester_RequestedFor --}}
                            {{-- @if (($prHeader['status'] >= '10' AND $prHeader['status'] <= '40') AND $isValidator_Decider != true) --}}
                            @if (($prHeader['status'] >= '10' AND $prHeader['status'] <= '40') AND $isRequester_RequestedFor == true)
                            <button wire:click.prevent="confirmCancelPrHeader" class="btn btn-sm btn-light">
                                <i class="fas fa-times mr-2"></i>Cancel</button>
                            @endif

                            {{-- 10=Planned --}}
                            @if ($prHeader['status'] == '10' AND $isValidator_Decider != true)
                            <button wire:click.prevent="confirmDeletePrHeader_Detail" class="btn btn-sm btn-light">
                                <i class="fas fa-trash-alt mr-2"></i>Delete</button>
                            @endif

                            {{--70-Cancelled AND isRequester_RequestedFor --}}
                            {{-- @if ($prHeader['status'] == '70' AND $isValidator_Decider != true) --}}
                            @if ($prHeader['status'] == '70' AND $isRequester_RequestedFor == true)
                            <button wire:click.prevent="reopen" class="btn btn-sm btn-danger">
                                <i class="fas fa-external-link-alt mr-2"></i>Re-Open</button>
                            @endif

                            {{-- 50-ReleasedForPO --}}
                            @if (($prHeader['status'] == '50' AND $isBuyer == true) AND $isValidator_Decider != true)
                            <button wire:click.prevent="" class="btn btn-sm btn-danger">
                                <i class="fas fa-shopping-cart mr-2"></i>Converet to PO</button>
                            @endif

                            {{-- 20-ReleasedforSourcing AND isRequester_RequestedFor --}}
                            @if ($prHeader['status'] == '20' AND $isRequester_RequestedFor == true)
                            <button wire:click.prevent="revokePrHeader" class="btn btn-sm btn-danger">
                                <i class="fas fa-undo mr-1"></i>Revoke</button>
                            
                            {{-- 21-PartiallyAuthorized AND (isRequester_RequestedFor OR isValidator_Decider) --}}
                            @elseif($prHeader['status'] == '21' AND ($isRequester_RequestedFor == true OR $isValidator_Decider == true))                                
                                <button wire:click.prevent="revokePrHeader" class="btn btn-sm btn-danger">
                                    <i class="fas fa-undo mr-1"></i>Revoke</button>
                            @endif

                        @endif
                    </div>
                    <div>
                        <button wire:click.prevent="backToPRList" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-alt-circle-left mr-1"></i></i>Back</button>

                        {{-- 01-Draft, 10-Planned --}}
                        @if ($prHeader['status'] == '01' OR $prHeader['status'] == '10')
                        <button wire:click.prevent="savePR" class="btn btn-sm btn-danger" class="btn btn-sm btn-light">
                            <i class="fas fa-save mr-1"></i>Save</button>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>
        {{-- Actions End--}}

    </div>
    @if ($orderType == "10" or $orderType == "20" )
        @include('livewire._model-part-line-item')
    @endif
    @if ($orderType == "11" or $orderType == "21" )
        @include('livewire._model-expense-line-item')
    @endif

    {{-- @error('emailAddress.*')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-danger" role="alert">
                    {{ $message }}
                </div>
            </div>
        </div>
    </div>
    @enderror --}}

    @if ($errors->any())
    <div class="container-fluid">
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Modal Edit Attachment --}}
    <div class="modal" id="modelEditAttachment" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
        <div class="modal-dialog" role="document" style="max-width: 60%;">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        Edit File Detail
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>File Name</label>
                            <input class="form-control form-control-sm" type="text" maxlength="200" wire:model.defer="editAttachment.file_name">
                        </div>
                        <div class="col-md-6">
                            <label>Attachment Level</label>
                            <x-select2-multiple id="editattachment_lineno-select2" wire:model.defer="editAttachment.ref_lineno">
                                {{-- รอค่าจากการ Bind --}}
                            </x-select2-multiple>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Document Type</label>
                            <select class="form-control form-control-sm" wire:model="editAttachment.file_type">
                                <option value="">--- Please Select ---</option>
                                <option value="General_Documents">General Documents</option>
                                <option value="eDecision">eDecision</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>eDecision No.</label>
                            @if( isset($editAttachment['file_type']) )
                            <input class="form-control form-control-sm" type="text" {{ $editAttachment['file_type'] == 'eDecision' ? '' : 'readonly' }}
                                wire:model.defer="editAttachment.edecision_no">
                            @endif
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label>Attachment By</label>
                            <input class="form-control form-control-sm" type="text" readonly wire:model="editAttachment.create_by">
                        </div>
                        <div class="col-md-6">
                            <label>Last Modified</label>
                            <input class="form-control form-control-sm" type="text" readonly wire:model="editAttachment.last_modified">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12 d-flex justify-content-end">
                            <div>
                                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal" wire:click.prevent="closedModal">
                                    <i class="fa fa-times mr-1"></i>Cancel</button>
                                <button type="button" class="btn btn-sm btn-danger" wire:click.prevent="editAttachment_Save">
                                    <i class="fa fa-save mr-1"></i>Save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Confirm Cancel (Cancel Reason) --}}
    <div class="modal" id="modelCancelReason" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
        <div class="modal-dialog" role="document" style="max-width: 40%;">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        Do you want to cancel Purchase Requisition No. {{ $prHeader['prno'] }}
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <label>Cancellation Reason</label>
                            <textarea class="form-control form-control-sm" rows="3" maxlength="250" wire:model.defer="cancelReason"></textarea>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12 d-flex justify-content-end">
                            <div>
                                <button type="button" class="btn btn-sm btn-danger" wire:click.prevent="cancelPrHeader">
                                    <i class="fa fa-save mr-1"></i>Save
                                </button>
                                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">
                                    <i class="fa fa-times mr-1"></i>Close</button>
                            </div>
                        </div>
                    </div>
                    @if ($errors->any())
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @push('styles')
<style>
    input[type="file"]{
        height:500px;
    }

    input[type="file"]::-webkit-file-upload-button{
        height:500px;
  }
</style>
@endpush --}}

@push('js')
<script>

    window.addEventListener('show-modelEditAttachment', event => {
        $('#modelEditAttachment').modal('show');
    })

    window.addEventListener('hide-modelEditAttachment', event => {
        $('#modelEditAttachment').modal('hide');
    })

    window.addEventListener('show-modelCancelReason', event => {
        $('#modelCancelReason').modal('show');
    })

    window.addEventListener('hide-modelCancelReason', event => {
        $('#modelCancelReason').modal('hide');
    })

    window.addEventListener('clear-select2', event => {
        clearSelect2('attachment_lineno-select2');
        clearSelect2('decider-select2');
        clearSelect2('validator-select2'); 
    })

    // ย้ายไปไว้ที่ App.blade.php
    // window.addEventListener('bindToSelect2', event => {
    //     $(event.detail.selectName).html(" ");
    //     $(event.detail.selectName).append(event.detail.newOption);
    // });

    window.addEventListener('prheader-disable', event => {
        $("#PR_Header :input").attr("disabled", true);
    });

    // Set default requester for & buyer
    document.addEventListener("livewire:load", function() { 
        @this.setDefaultSelect2();

        // ไม่ Work กรณีกด Modal แล้วมันจะหลด
        // @this.disablePRHeader();
    });

</script>

<script type="text/javascript">
    $('textarea').keyup(function() {    
        var characterCount = $(this).val().length,
            current_count = $('#current_count'),
            maximum_count = $('#maximum_count'),
            count = $('#count');    
            current_count.text(characterCount);        
    });
</script>
@endpush
