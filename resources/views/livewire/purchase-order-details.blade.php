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
            <div class="row" style="height: 35px;">
                <div class="col-12 d-flex justify-content-between" style="border-radius: 5px; color: white; background-color:#C3002F;">
                    <div class="my-auto">
                        Order Type : <span>{{ $poHeader['order_typename'] }}</span>
                    </div>
                    <div class="my-auto">
                        Status : <span>{{ $poHeader['statusname'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid" id="PO_Detail">
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
                    <div class="col-md-6">
                        <label for="prno">Supplier<span style="color: red"> *</span></label>
                        @if ($order_type == '30')
                        <x-select2 id="supplier-select2" wire:model.defer="poHeader.supplier">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($supplier_dd as $row)
                            <option value="{{ $row->supplier }}">
                                {{ $row->supplier }} : {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('supplier') <span class="text-red">{{ $message }}</span> @enderror
                        @else
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.supplier">
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label for="prno">PO Currency<span style="color: red"> *</span></label>
                        <x-select2 id="currency-select2" wire:model.defer="poHeader.po_currency">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($currency_dd as $row)
                            <option value="{{ $row->currency }}">
                                {{ $row->currency }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('po_currency') <span class="text-red">{{ $message }}.</span> @enderror
                    </div>
                    <div class="col-3">
                        <label class="">Created On</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="poHeader.create_on" id="create_on" readonly="true"
                                :error="'date'"/>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Supplier Contact<span style="color: red"> *</span></label>
                        <x-select2 id="supplier_contact-select2" wire:model.defer="poHeader.supplier_contact">
                            {{-- รอ Bind หลังจากเลือก Supplier --}}
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
                        <label>Buyer<span style="color: red"> *</span></label>
                        @if ($order_type == '30')
                        <x-select2 id="buyer-select2" wire:model.defer="poHeader.buyer">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($buyer_dd as $row)
                            <option value="{{ $row->buyer }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('buyer') <span class="text-red">{{ $message }}</span> @enderror
                        @else
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.buyer">
                        @endif                        
                    </div>
                    <div class="col-md-3">
                        <label>Buyer Group</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.buyer_group">
                    </div>
                    <div class="col-md-3">
                        <label>Delivery Location</label>
                        @if ($order_type == '30')
                        <x-select2 id="delivery_location-select2" wire:model.defer="poHeader.delivery_location">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($delivery_location_dd as $row)
                            <option value="{{ $row->address_id }}">
                                {{ $row->delivery_location }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('delivery_location') <span class="text-red">{{ $message }}</span> @enderror
                        @else
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.delivery_location">
                        @endif

                    </div>                    
                    <div class="col-md-3">
                        <label>Issued By</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.issued_by_name">
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
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.requestor_name">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Phone (Requester)</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.requestor_phone">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Ext. (Requester)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="poHeader.requestor_ext">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Purchase Requisition No.</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.prno">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label for="prno">Requested For<span style="color: red"> *</span></label>
                        @if ($order_type == '30')
                        <x-select2 id="requested_for-select2" wire:model.defer="poHeader.requested_for">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($requested_for_dd as $row)
                            <option value="{{ $row->id }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('requested_for') <span class="text-red">{{ $message }}</span> @enderror
                        @else
                        <input class="form-control form-control-sm" type="text" wire:model.defer="poHeader.requested_for">
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Phone (Requested For)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="poHeader.requested_for_phone">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Ext. (Requested For)</label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" maxlength="40" wire:model.defer="poHeader.requested_for_ext">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Email (Requested For)</label>
                        <input class="form-control form-control-sm" type="text" wire:model.defer="poHeader.requested_for_email">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label for="prno">Company</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.company_name">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Site</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.site">
                    </div>
                    <div class="col-md-3">
                        <label for="prno">Department Code (Cost Center)</label>
                        @if ($order_type == '30')
                        <x-select2 id="costcenter-select2" wire:model.defer="poHeader.costcenter">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($cost_center_dd as $row)
                            <option value="{{ $row->cost_center }}">
                                {{ $row->cost_center }} : {{ $row->description }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('costcenter') <span class="text-red">{{ $message }}</span> @enderror
                        @else
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="poHeader.costcenter">
                        @endif
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
        <div class="card shadow-none border rounded">
            <div class="card-header my-card-header">
                Blanket Request
            </div>
            <div class="card-body my-card-body">
                <div class="row">
                    <div class="col-md-12">
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="poHeader.po_expirein">
                    </div>
                </div>
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
        {{-- Blanket Request End--}}

        <!-- .Tab Header -->
        <ul class="nav nav-tabs" id="pills-tab" role="tablist">
            <li class="nav-item" wire:ignore>
                <a class="nav-link {{ $currentTab == 'item' ? 'active' : '' }}" id="pills-lineitem-tab" data-toggle="pill" href="#pills-lineitem" role="tab" 
                    aria-controls="pills-lineitem" aria-selected="true">Line Item</a>
            </li>
            <li class="nav-item" wire:ignore>
                <a class="nav-link {{ $currentTab == 'delivery' ? 'active' : '' }}" id="pills-delivery-tab" data-toggle="pill" href="#pills-delivery" role="tab" 
                    aria-controls="pills-delivery" aria-selected="false">Delivery Schedule</a>
            </li>
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
                     </div>
                    <div>
                        <button wire:click.prevent="backToPOList" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-alt-circle-left mr-1"></i></i>Back</button>

                        <button wire:click.prevent="showModal_PrefixConfirm" class="btn btn-sm btn-danger" class="btn btn-sm btn-light">
                            <i class="fas fa-save mr-1"></i>Save</button>
                    </div>
                    
                </div>
            </div>
        </div>
        {{-- Actions End--}}

        {{-- Model Pre-Fix Confirmation --}}
        <div class="modal" id="modelPrefixConfirm" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
            <div class="modal-dialog" role="document" style="max-width: 60%;">
                <div class="modal-content ">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                            Please confirm the PO Prefix before saving.
                        </h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Choose PO Prefix</label>
                                <select class="form-control form-control-sm" wire:model="poHeader.po_prefix">
                                    <option value="">--- Please Select ---</option>
                                    @foreach($po_prefix_dd as $row)
                                    <option value="{{ $row->prefix_type }}">
                                        {{ $row->prefix_type }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('po_prefix') <span class="text-red">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="prno">Order Type</label>
                                <input class="form-control form-control-sm" type="text" readonly wire:model.defer="">
                            </div>
                            <div class="col-md-6">
                                <label for="prno">Reference PR</label>
                                <input class="form-control form-control-sm" type="text" readonly wire:model.defer="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="prno">Buyer (PR, RFQ)</label>
                                <input class="form-control form-control-sm" type="text" readonly wire:model.defer="">
                            </div>
                            <div class="col-md-6">
                                <label for="prno">Reference RFQ</label>
                                <input class="form-control form-control-sm" type="text" readonly wire:model.defer="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="prno">Buyer (PO Issuer)</label>
                                <input class="form-control form-control-sm" type="text" readonly wire:model.defer="">
                            </div>
                            <div class="col-md-6">
                                <label for="prno">Buyer Group</label>
                                <input class="form-control form-control-sm" type="text" readonly wire:model.defer="">
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12 d-flex justify-content-end">
                                <div>
                                    <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">
                                        <i class="fa fa-times mr-1"></i>Cancel</button>
                                    <button type="button" class="btn btn-sm btn-danger" wire:click.prevent="savePO">
                                        <i class="fa fa-save mr-1"></i>Save</button>

                                </div>
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

    window.addEventListener('show-modelPrefixConfirm', event => {
        $('#modelPrefixConfirm').modal('show');
    })

    window.addEventListener('hide-modelPrefixConfirm', event => {
        $('#modelPrefixConfirm').modal('hide');
    })

    // Set default requester for & buyer
    document.addEventListener("livewire:load", function() { 
        @this.setDefaultSelect2();
    });

</script>
@endpush
