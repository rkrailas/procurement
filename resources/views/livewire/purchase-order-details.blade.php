<div>

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-6" style="font-size: 20px; color: #C3002F">
                    Purchase Requsition No : <span>{{ $poHeader['pono'] }}</span>
                </div>
                <div class="col-sm-6 text-right">
                    <button wire:click.prevent="goto_prlist" class="btn btn-sm btn-danger">Go to RFQ Details</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    Revision
                </div>
            </div>
            <div class="row" style="height: 30px;">
                <div class="col-12 d-flex justify-content-between" style="background-color:gray">
                    <div class="my-auto">
                        Order Type : <span>{{ $poHeader['ordertypename'] }}</span>
                    </div>
                    <div class="my-auto">
                        Status : <span>{{ $poHeader['statusname'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container" id="PO_Detail">
        {{-- Purchasing --}}
        <div class="card shadow-none border rounded">
            <div class="card-header my-card-header">
                <div class="row py-0 my-0">
                    <div class="col-12">
                        Purchasing
                    </div>
                </div>
            </div>

            <div class="card-body my-card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="prno">Supplier</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.supplier">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Supplier Name</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.supplier_name">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">PO Currency</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.currency">
                    </div>
                    <div class="col-3">
                        <label class="">Created On</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="poHeader.created_on" id="created_on" readonly="true"
                                :error="'date'"/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Supplier Contact<span style="color: red">*</span></label>
                        <x-select2 id="supplier_contact-select2" wire:model.defer="poHeader.supplier_contact">
                            {{-- @foreach($supplier_contact_dd as $row)
                            <option value="{{ $row->id }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach --}}
                        </x-select2>
                        @error('supplier_contact') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Supplier Contact Email</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.supplier_contact_email">
                    </div>
                    <div class="col-md-3">
                        <label>Supplier Contact Phone</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.supplier_contact_phone">
                    </div>
                    <div class="col-md-3">
                        <label class="">PO Released Date</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="poHeader.released_date" id="released_date" readonly="true"
                                :error="'date'"/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Buyer</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.buyer">
                    </div>
                    <div class="col-md-3">
                        <label>Buyer Group</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.buyer_group">
                    </div>
                    <div class="col-md-3">
                        <label>Delivery Location<span style="color: red">*</span></label>
                        <x-select2 id="delivery_location-select2" wire:model.defer="poHeader.delivery_location">
                            {{-- @foreach($supplier_contact_dd as $row)
                            <option value="{{ $row->id }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach --}}
                        </x-select2>
                        @error('delivery_location') <span class="text-red">{{ $message }}</span> @enderror
                    </div>                    
                    <div class="col-md-3">
                        <label>Issued By</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.issued_by">
                    </div>
                </div>


            </div>
        </div>
        {{-- Purchasing End--}}

        {{-- Requester --}}
        <div class="card shadow-none border rounded">
            <div class="card-header my-card-header">
                <div class="row py-0 my-0">
                    <div class="col-12">
                        Requester
                    </div>
                </div>
            </div>

            <div class="card-body my-card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label for="prno">Requester</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.requester">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Phone (Requester)</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.requester_phone">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Ext. (Requester)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="poHeader.requester_ext">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Purchase Requisition No.</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.prno">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label for="prno">Requested For</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.requestedfor">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Phone (Requested For)</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.requestedfor_phone">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Ext. (Requested For)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="poHeader.requestedfor_ext">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Email (Requested For)</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.requestedfor_email">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label for="prno">Company</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.company">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Site</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.site">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Department Code (Cost Center)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="poHeader.costcenter">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">RFQ No.</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.rfqno">
                    </div>
                </div>
            </div>
        </div>
        {{-- Requester End--}}

        {{-- Blanket Request --}}
        @if ($isBlanket)
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
                            <x-datepicker wire:model.defer="poHeader.valid_until" id="valid_until"
                                :error="'date'"/>
                        </div>
                        @error('valid_until') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                    </div>
                    <div class="col-md-6">
                        <label>Notify when remaining value is below</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="poHeader.notify_below_10">
                                <label class="form-check-label">10%</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="poHeader.notify_below_25">
                                <label class="form-check-label">25%</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="poHeader.notify_below_35">
                                <label class="form-check-label">35%</label>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        @endif
        {{-- Blanket Request End--}}

        <!-- .Tab Header -->
        <ul class="nav nav-tabs" id="pills-tab" role="tablist">
            <li class="nav-item" wire:ignore>
                <a class="nav-link {{ $currentTab == 'item' ? 'active' : '' }}" id="pills-lineitem-tab" data-toggle="pill" href="#pills-lineitem" role="tab" 
                    aria-controls="pills-lineitem" aria-selected="true">Line Item</a>
            </li>
            @if ($isBlanket)
            <li class="nav-item" wire:ignore>
                <a class="nav-link {{ $currentTab == 'delivery' ? 'active' : '' }}" id="pills-delivery-tab" data-toggle="pill" href="#pills-delivery" role="tab" 
                    aria-controls="pills-delivery" aria-selected="false">Delivery Schedule</a>
            </li>
            @endif
            <li class="nav-item" wire:ignore>
                <a class="nav-link {{ $currentTab == 'orderdetail' ? 'active' : '' }}" id="pills-orderdetail-tab" data-toggle="pill" href="#pills-orderdetail" role="tab" 
                    aria-controls="pills-orderdetail" aria-selected="false">Order Details</a>
            </li>
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

    </div>
    
</div>
