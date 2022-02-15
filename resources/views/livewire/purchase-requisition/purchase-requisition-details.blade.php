<div>
    <x-loading-indicator target="validatorDeciderApprove" />
    <x-loading-indicator target="validatorDeciderReject" />
    <x-loading-indicator target="addAttachment" />
    <x-loading-indicator target="attachment_file" />
    
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Purchase Requisition</li>
                        <li class="breadcrumb-item active" style="color: #C3002F;">Purchase Requisition Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container" id="PR_Detail">
        {{-- Header --}}
        <div class="row">
            <div class="col-md-12" style="font-size: 20px; color: #C3002F">
                Purchase Requsition No : <span>{{ $prHeader['prno'] }}</span>
            </div>
        </div>
        <div class="card shadow-none border rounded">
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
                        <label for="prno">Phone (Requestor)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.phone">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Ext. (Requestor)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.extention">
                    </div>
                    <div class="col-3">
                        <label class="">Request Date</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="prHeader.request_date" id="request_date"
                                :error="'date'"/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Requested For <span style="color: red">*</span></label>
                        <x-select2 id="requestedfor-select2" wire:model.defer="prHeader.requested_for">
                            @foreach($requested_for_dd as $row)
                            <option value="{{ $row->id }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('requested_for') <span class="text-red">This field is required.</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Phone (Requestor For)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.phone_reqf">
                    </div>
                    <div class="col-md-3">
                        <label>Ext. (Requestor For)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.extention_reqf">
                    </div>
                    <div class="col-md-3">
                        <label>Email (Requestor For)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.email_reqf">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Company</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prHeader.company_name">
                    </div>
                    <div class="col-md-3">
                        <label>Site</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model="prHeader.site">
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
                        <select class="form-control form-control-sm" id="cost_center" wire:model="prHeader.cost_center">
                            <option value="">--- Please Select ---</option>
                            @foreach($cost_center_dd as $row)
                            <option value="{{ $row->cost_center }}">
                                {{ $row->cost_center }}
                            </option>
                            @endforeach
                        </select>
                        @error('cost_center') <span class="text-red">This field is required.</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Cost Center Description</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prHeader.costcenter_desc">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Buyer <span style="color: red">*</span></label>
                        <x-select2 id="buyer-select2" wire:model.defer="prHeader.buyer">
                            @foreach($buyer_dd as $row)
                            <option value="{{ $row->id }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('buyer') <span class="text-red">This field is required.</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Delivery Address <span style="color: red">*</span></label>
                        <select class="form-control form-control-sm" id="delivery_address" wire:model="prHeader.delivery_address">
                            <option value="">--- Please Select ---</option>
                            @foreach($delivery_address_dd as $row)
                            <option value="{{ $row->address_id }}">
                                {{ $row->address_id }} : {{ $row->address }}
                            </option>
                            @endforeach
                        </select>
                        @error('delivery_address') <span class="text-red">This field is required.</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Budget Year <span style="color: red">*</span></label>
                        <select class="form-control form-control-sm" id="budget_year" wire:model.defer="prHeader.budget_year">
                            <option value="">--- Please Select ---</option>
                            @foreach($budgetyear_dd as $row)
                            <option value="{{ $row['year'] }}">
                                {{ $row['year'] }}
                            </option>
                            @endforeach
                        </select>
                        @error('budget_year') <span class="text-red">This field is required.</span> @enderror
                    </div>
                    <div class="col-3">
                        <label for="prno">Purpose of PR <span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="prHeader.purpose_pr">
                        @error('purpose_pr') <span class="text-red">This field is required.</span> @enderror
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
                        <label class="">Valid Until</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="prHeader.valid_until" id="valid_until"
                                :error="'date'"/>
                        </div>
                        @error('valid_until') <span class="text-red">This field is required.</span> @enderror
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
                        aria-controls="pills-delivery" aria-selected="false">Delivery Plan</a>
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
                                <div class="d-flex justify-content-end mb-2">
                                    <button wire:click.prevent="showAddItem" class="btn btn-sm btn-danger"><i class="fas fa-plus-square mr-1"></i>Add Item</button>
                                </div>
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
                                        <th scope="col">Purchasing Unit</th>
                                        <th scope="col">Budget Unit Price</th>
                                        <th scope="col">Budget Total Price</th>
                                        <th scope="col">Requested Delivery Date</th>
                                        <th scope="col">Final Price</th>
                                        <th scope="col">Currency</th>
                                        <th scope="col"></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($itemList as $index => $row)
                                    <tr>
                                        {{-- $bankDetails[$index]['taxref'] --}}
                                        <td>
                                            <div d-inline ml-2>
                                                <input wire:model="selectedRows" type="checkbox" value="{{ $itemList[$index]['id'] }}"
                                                    id="{{ $itemList[$index]['lineno'] }}">
                                                <span>{{ $itemList[$index]['lineno'] }}</span>
                                            </div>
                                        </td>
                                        <td scope="col">{{ $itemList[$index]['partno'] }}</td>
                                        <td scope="col">{{ $itemList[$index]['description'] }}</td>                                
                                        <td scope="col">{{ $itemList[$index]['status'] }}</td>
                                        <td scope="col" class="text-right pr-2">{{ number_format($itemList[$index]['qty'], 0) }}</td>
                                        <td scope="col" class="text-center">{{ $itemList[$index]['purchase_unit'] }}</td>
                                        <td scope="col" class="text-right pr-2">{{ number_format($itemList[$index]['unit_price'], 2) }}</td>
                                        <td scope="col" class="text-right pr-2">{{ number_format( $itemList[$index]['budgettotal'], 2) }}</td>
                                        <td scope="col" class="text-center">{{ \Carbon\Carbon::parse( $itemList[$index]['req_date'])->format('d-M-Y') }} </td>
                                        <td scope="col" class="text-right pr-2">{{ number_format($itemList[$index]['final_price'], 2) }} </td>
                                        <td scope="col" class="text-center">{{ $itemList[$index]['currency'] }}</td>
                                        <td scope="col">
                                            <center>
                                                <a href="" wire:click.prevent="editLineItem('{{ $itemList[$index]['id'] }}')">
                                                    <i class="fa fa-edit mr-2"></i>
                                                </a>
                                                {{-- <button class="btn btn-sm m-0 p-0" wire:click.prevent="editLineItem('{{ $itemList[$index]['id'] }}')">
                                                    <i class="fa fa-edit mr-2"></i>
                                                </button> --}}
                                            </center>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
                            </div>
                            <div class="col-md-3">
                                <label>Quantity</label>
                                <input class="form-control form-control-sm" type="number" step="1" wire:model.defer="prDeliveryPlan.qty">
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
                                <button wire:click.prevent="addDeliveryPlan" class="btn btn-sm btn-danger mt-auto" {{ $enableAddPlan ? '' : 'disabled' }}>
                                    <i class="fas fa-plus-square mr-1"></i>Add Plan</button>
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
                                    @foreach ($prListDeliveryPlan as $index => $row)
                                    <tr>
                                        <td scope="col">{{ $index + 1 }}</td>
                                        <td scope="col">{{ $prListDeliveryPlan[$index]['lineno'] }}</td>
                                        <td scope="col">{{ $prListDeliveryPlan[$index]['description'] }}</td>
                                        <td scope="col">{{ $prListDeliveryPlan[$index]['partno'] }}</td>
                                        <td scope="col" class="text-right pr-2">{{ number_format($prListDeliveryPlan[$index]['qty'], 2) }}</td>
                                        <td scope="col">{{ $prListDeliveryPlan[$index]['purchase_unit'] }}</td>
                                        <td scope="col" class="text-center">{{ \Carbon\Carbon::parse( $prListDeliveryPlan[$index]['delivery_date'])->format('d-M-Y') }} </td>
                                        <td scope="col">
                                            <center>
                                                <a href="" wire:click.prevent="confirmDelete('{{ $prListDeliveryPlan[$index]['id'] }}', 'deliveryPlan')">
                                                    <i class="fas fa-times text-center" style="color: red"></i>
                                                </a>
                                            </center>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                {{-- Tab Delivery End --}}  
                
                {{-- Tab Authorization --}}         
                    <div class="tab-pane fade {{ $currentTab == 'auth' ? 'show active' : '' }}" id="pills-auth" role="tabpanel" aria-labelledby="pills-auth-tab" wire:ignore.self>
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
                                                    <button class="btn btn-sm btn-danger" {{ $deciderList ? 'disabled' : ''}} 
                                                        wire:click.prevent="addDecider"><i class="fas fa-plus-square mr-1"></i>Confirm</button>
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
                                                                <center>
                                                                    <a href="" wire:click.prevent="confirmDelete('{{ $deciderList[$index]['approver'] }}', 'decider')">
                                                                        <i class="fas fa-times text-center" style="color: red"></i>
                                                                    </a>
                                                                </center>
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
                                                    <button class="btn btn-sm btn-danger"
                                                        wire:click.prevent="addValidator"><i class="fas fa-plus-square mr-1"></i>Add</button>
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
                                                                    <center>
                                                                        <a href="" wire:click.prevent="confirmDelete('{{ $validatorList[$index]['approver'] }}', 'validator')">
                                                                            <i class="fas fa-times text-center" style="color: red"></i>
                                                                        </a>
                                                                    </center>
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
                                <textarea class="form-control form-control-sm" rows="2" maxlength="250" wire:model.defer="rejectReason"></textarea>
                            </div>
                        </div>
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
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Attachment Level <span style="color: blue; font-weight: normal">(Please select before add new files.)</span></label>
                                    <select class="form-control form-control-sm" wire:model="attachment_lineno">
                                        <option value="">--- Please Select ---</option>
                                        <option value="0">0 : Level PR Header</option>
                                        @foreach($prLineNoAtt_dd as $row)
                                        <option value="{{ $row->lineno }}">
                                            {{ $row->lineno }} : {{ $row->description }}
                                        </option>
                                        @endforeach
                                    </select>
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
                                    <label>eDecision No.</label>
                                    <input class="form-control form-control-sm" type="text" {{ $attachment_filetype == 'eDecision' ? '' : 'readonly' }}
                                        wire:model="attachment_edecisionno">
                                </div>
                            </div>

                            @if ($this->attachment_lineno != '')
                            <form autocomplete="off" enctype="multipart/form-data" wire:submit.prevent="addAttachment">
                                @csrf
                                <div class="row mb-3">
                                    <div class="col-md-9">
                                        <div class="custom-file">
                                            <input wire:model="attachment_file" type="file" class="custom-file-input" id="customFile" multiple>
                                                @error('attachment_file.*') <span class="text-danger">{{ $message }}</span> @enderror
                                            {{-- <label class="custom-file-label" for="customFile">
                                                @if ($attachment_file)
                                                @foreach ($attachment_file as $file)
                                                {{ $file->getClientOriginalName() }} ({{ $this->formatSizeUnits($file->getSize()) }}) <br/>
                                                @endforeach
                                                @else
                                                Browse Files
                                                @endif
                                            </label> --}}
                                            <label class="custom-file-label" for="customFile">Browse Files</label>

                                            @if ($attachment_file)
                                                @foreach ($attachment_file as $file)
                                                {{ $file->getClientOriginalName() }} ({{ $this->formatSizeUnits($file->getSize()) }}) <br/>
                                                @endforeach
                                            @endif
                                            
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-left">
                                        <button type="submit" class="btn btn-danger"><i class="fas fa-cloud-upload-alt mr-1"></i>Upload</button>
                                        <span style="vertical-align:bottom; color:red">max file size 5 mb</span> 
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
                                    @foreach ($attachmentFileList as $index => $row)
                                    <tr>
                                        <td scope="col">{{ $attachmentFileList[$index]['file_name'] }}</td>
                                        <td scope="col">{{ $attachmentFileList[$index]['file_type'] }}</td>
                                        <td scope="col">{{ $attachmentFileList[$index]['edecision_no'] }}</td>
                                        <td scope="col">{{ $attachmentFileList[$index]['ref_doctype'] }}</td>
                                        <td scope="col">{{ $attachmentFileList[$index]['ref_docno'] }}</td>
                                        <td scope="col">{{ $attachmentFileList[$index]['ref_lineno'] }}</td>
                                        <td scope="col">{{ $attachmentFileList[$index]['create_by'] }}</td>
                                        <td scope="col">{{ $attachmentFileList[$index]['create_on'] }}</td>
                                        <td scope="col" class="d-flex justify-content-between">
                                            <div>
                                                <a href="" wire:click.prevent="editAttachment('{{ $attachmentFileList[$index]['id'] }}')">
                                                    <i class="fa fa-edit mr-2"></i>
                                                </a>
                                            </div>
                                            <div>
                                                <a href="{{url('storage/attachments/' . $attachmentFileList[$index]['file_path'] )}}">
                                                    <i class="fas fa-download mr-1"></i>
                                                </a>
                                            </div>
                                            <div>
                                                <a href="" wire:click.prevent="confirmDelete('{{ $attachmentFileList[$index]['id'] }}', 'attachment')">
                                                    <i class="fas fa-times text-center mr-1" style="color: red"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
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
                                        <th scope="col">Action</th>
                                        <th scope="col">Where</th>
                                        <th scope="col">Line No.</th>
                                        <th scope="col">History Table</th>
                                        <th scope="col">History Ref</th>
                                        <th scope="col">Changed By</th>
                                        <th scope="col">Changed On</th>
                                    </tr>
                                    </thead>
                                    {{-- <tbody>
                                        @foreach ($historylog as $row)
                                        <tr>
                                            <td scope="col">{{ $row->action_type }} </td>
                                            <td scope="col">{{ $row->action_where }} </td>
                                            <td scope="col">{{ $row->line_no }} </td>
                                            <td scope="col">{{ $row->history_table }} </td>
                                            <td scope="col">{{ $row->history_ref }} </td>
                                            <td scope="col">{{ $row->fname }} </td>
                                            <td scope="col">{{ \Carbon\Carbon::parse($row->changed_on)->format('d-M-Y H:i:s') }} </td>
                                        </tr>
                                        @endforeach
                                    </tbody> --}}
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
                        @if ( $prHeader['status'] < '20' )
                        <button wire:click.prevent="releaseForSourcing" class="btn btn-sm btn-danger">
                            <i class="fas fa-check mr-2"></i>Release for Sourcing</button>
                        @else
                        <button wire:click.prevent="releaseForSourcing" class="btn btn-sm btn-danger" disabled>
                            <i class="fas fa-check mr-2"></i>Release for Sourcing</button>
                        @endif

                        <button wire:click.prevent="releaseForPO" class="btn btn-sm btn-danger">
                            <i class="fas fa-check mr-2"></i>Release for PO</button>

                        <a href="PRForm/{{ $prHeader['prno'] }}" target="_blank">
                            <button class="btn btn-sm btn-danger" {{ $prHeader['prno'] ? '' : 'disabled' }}>
                                <i class="fas fa-print mr-2"></i>Print</button>
                        </a>

                        <button wire:click.prevent="cancelPR" class="btn btn-sm btn-light">
                            <i class="fas fa-times mr-2"></i>Cancel</button>

                        <button wire:click.prevent="confirmDeletePrHeader_Detail" 
                            class="btn btn-sm btn-light" {{ $prHeader['prno'] ? '' : 'disabled' }}>
                            <i class="fas fa-trash-alt mr-2"></i>Delete</button>

                        <button wire:click.prevent="reopen" class="btn btn-sm btn-danger">
                            <i class="fas fa-external-link-alt mr-2"></i>Re-Open</button>

                        <button wire:click.prevent="" class="btn btn-sm btn-danger" disabled>
                            <i class="fas fa-shopping-cart mr-2"></i>Converet to PO</button>

                    </div>
                    <div>
                        <button wire:click.prevent="backToPRList" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-alt-circle-left mr-1"></i></i>Back</button>
                        <button wire:click.prevent="savePR" class="btn btn-sm btn-danger" class="btn btn-sm btn-light" 
                            {{-- {{ $prHeader['status'] > '10' ? 'disabled' : '' }} --}}
                            >
                            <i class="fas fa-save mr-1"></i>Save</button>
                    </div>
                    
                </div>
            </div>
        </div>
        {{-- Actions End--}}

    </div>
    @if ($orderType == "10" or $orderType == "20" )
        @include('livewire.purchase-requisition._model-part-line-item')
    @endif
    @if ($orderType == "11" or $orderType == "21" )
        @include('livewire.purchase-requisition._model-expense-line-item')
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
                            <select class="form-control form-control-sm" wire:model="editAttachment.ref_lineno">
                                <option value="">--- Please Select ---</option>
                                <option value="0">0 : Level PR Header</option>
                                @foreach($prLineNoAtt_dd as $row)
                                <option value="{{ $row->lineno }}">
                                    {{ $row->lineno }} : {{ $row->description }}
                                </option>
                                @endforeach
                            </select>
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
                            @if( isset($editAttachment['file_type']))
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
</div>

@push('js')
<script>

window.addEventListener('show-modelEditAttachment', event => {
        $('#modelEditAttachment').modal('show');
    })

    window.addEventListener('hide-modelEditAttachment', event => {
        $('#modelEditAttachment').modal('hide');
    })

</script>
@endpush
