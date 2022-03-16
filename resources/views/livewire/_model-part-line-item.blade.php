<div class="modal" id="modelPartLineItem" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog" role="document" style="max-width: 80%;">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                    Part Order Line Item
                </h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-between mb-2">
                        <div>
                            <label>PR Line No : </label> <input type="text" wire:model.defer="prItem.lineno">
                        </div>
                        <div>
                            <label>Status :</label> <input type="text" wire:model.defer="prItem.status_des">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <hr width="100%">
                </div>

                {{-- Part --}}
                <div class="row">
                    <div class="col-md-9">
                        <label>Part <span style="color: red">*</span></label>
                        <x-select2 id="partno-select2" wire:model.defer="prItem.partno">
                            @foreach($partno_dd as $row)
                            <option value="{{ $row->partno }}">
                                {{ $row->partno }} : {{ $row->part_name }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('partno') <span class="text-red">{{ $message }}.</span> @enderror
                       
                        {{-- @if ($isCreateLineItem)

                        @else
                        <label>Part <span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.partno">
                        @endif --}}
                    </div>
                    <div class="col-md-3">
                        <label>Non Stock Control</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" wire:model.defer="" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="row">
                    <div class="col-12">
                        <label>Description <span style="color: red">*</span></label>
                        {{-- Change to readonly Ref. 18-Jan-2022: update editable and non-editable fields --}}
                        <input class="form-control form-control-sm" type="text" readonly maxlength="200" wire:model.defer="prItem.description">
                        @error('description') <span class="text-red">{{ $message }}.</span> @enderror
                    </div>
                </div>

                {{-- Purchase Unit --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>Purchase Unit</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.purchase_unit">
                    </div>
                    <div class="col-md-4">
                        {{-- Change to Not required & Readonly Ref. 18-Jan-2022: update editable and non-editable fields --}}
                        <label>Budget Price Per Unit</label>
                        <input class="form-control form-control-sm" type="number" readonly step="0.01" wire:model.lazy="prItem.unit_price">
                        @error('unit_price') <span class="text-red">{{ $message }}.</span> @enderror
                    </div>
                    <div class="col-md-4">
                        {{-- Change to Not required & Readonly & input=text Ref. 18-Jan-2022: update editable and non-editable fields --}}
                        <label>Currency</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.currency">
                        {{-- <x-select2 id="currency-select2" wire:model.defer="prItem.currency">
                            @foreach($currency_dd as $row)
                            <option value="{{ $row->currency }}">
                                {{ $row->currency }}
                            </option>
                            @endforeach
                        </x-select2> --}}
                    </div>
                </div>

                {{-- Exchange Rate --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>Exchange Rate</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.exchange_rate">
                    </div>
                    <div class="col-md-4">
                        {{-- <label>Purchase Group</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.purchase_group"> --}}
                    </div>
                    <div class="col-md-4">
                        {{-- <label>Accounting Group</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.account_group"> --}}
                    </div>
                </div>

                {{-- QTY --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>QTY <span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="number" step="0.01" wire:model.lazy="prItem.qty">
                        @error('qty') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Earliest Delivery Date <span style="color: red">*</span></label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker autocomplete="off" wire:model.defer="prItem.req_date" id="request_date1" :error="'date'"/>
                        </div>
                        @error('req_date') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Internal Order</label>
                        <x-select2 id="internalorder-select2" wire:model.defer="prItem.internal_order">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($internal_order_dd as $row)
                            <option value='{{ $row->internal_order }}'>
                                {{ $row->internal_order }}
                            </option>
                            @endforeach
                        </x-select2>
                    </div>
                </div>

                {{-- Budget Code --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>Budget Code <span style="color: red">*</span></label>
                        <x-select2 id="budgetcode-select2" wire:model.defer="prItem.budget_code">
                            {{-- รอ Bind ค่า --}}
                        </x-select2>
                        @error('budget_code') <span class="text-red">{{ $message }}.</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Brand</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.brand">
                    </div>
                    <div class="col-md-4">
                        <label>Model / Spec</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.model">
                    </div>
                </div>

                {{-- RFQ & DOA --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>RFQ & DOA</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" disabled wire:model.defer="prItem.skip_rfq">
                                <label class="form-check-label">Skip RFQ</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" disabled wire:model.defer="prItem.skip_doa">
                                <label class="form-check-label">Skip DOA</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div id="more1year">
                            <label>Useful life more than 1 year</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" 
                                        wire:model="prItem.over_1_year_life">
                                    <label class="form-check-label">Yes</label>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div class="col-md-4">
                        @if ($prHeader['company'] == "2641")
                        <label>For SNN</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="radioForSNN"
                                    wire:model.defer="prItem.snn_service" />
                                <label class="form-check-label" for="radioForSNN_Service">Service</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="radioForSNN"
                                    wire:model.defer="prItem.snn_production" />
                                <label class="form-check-label" for="radioForSNN_Production">Production</label>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Quotation --}}
                <div class="row mb-0">
                    <div class="col-md-12">
                        <label>Quotation</label>
                    </div>
                </div>
                <div class="card shadow-none border rounded">
                    <div class="card-body p-1">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Final Price</label>
                                <input class="form-control form-control-sm" type="number" readonly step="0.01" wire:model.defer="prItem.final_price">
                            </div>
                            <div class="col-md-4">
                                <label>Quotation Expiry Date</label>
                                <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.quotation_expiry_date">
                            </div>
                            <div class="col-md-4">
                                <label>Nominated Supplier</label>
                                <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.nominated_supplier_name">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="row">
                    <div class="col-md-12">
                        <label>Remarks</label>
                        <textarea class="form-control form-control-sm" rows="3" maxlength="250" wire:model.defer="prItem.remarks"></textarea>
                    </div>
                </div>

                {{-- Reference PR --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>Reference PR</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="prItem.reference_pr">
                    </div>
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>

                <!-- Actions -->
                <div class="row mt-3">
                    <hr width="100%">
                </div>
                <div class="row mb-2">
                    <div class="col-md-12 d-flex justify-content-between">
                        <div>
                            @if ( $prHeader['status'] < '20' ) 
                            <button type="button" class="btn btn-sm btn-light" wire:click.prevent="deleteLineItem">
                                <i class="fas fa-trash-alt mr-1"></i>Delete</button>
                            @endif
                        </div>
                        <div>
                            @if ($isCreateLineItem == true)
                            <button type="button" class="btn btn-sm btn-light" wire:click.prevent="clearAll">
                                <i class="fa fa-times mr-1"></i>Clear All</button>
                            @endif

                            <button type="button" class="btn btn-sm btn-light" data-dismiss="modal" wire:click.prevent="closedModal">
                                <i class="fa fa-times mr-1"></i>Close</button>
                            @if ( $prHeader['status'] < '20' ) 
                            <button type="button" class="btn btn-sm btn-danger" wire:click.prevent="saveLineItem">
                                <i class="fa fa-save mr-1"></i>Confirm
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    window.addEventListener('show-modelPartLineItem', event => {
        $('#modelPartLineItem').modal('show');
    })

    window.addEventListener('hide-modelPartLineItem', event => {
        $('#modelPartLineItem').modal('hide');
    })

    window.addEventListener('clear-select2-modal', event => {
        clearSelect2('partno-select2');
        clearSelect2('currency-select2');
        clearSelect2('internalorder-select2');
        clearSelect2('budgetcode-select2');
    })


    // การ Disable aelect2 ยังไม่ได้ใช้ตอนนี้
    // window.addEventListener('disable-partno-select2', event => {
    //     $("#partno-select2").attr("disabled", true);
    // });

    document.addEventListener("livewire:load", function() { 
        @this.setDefaultSelect2InModelLineItem()
    });
</script>
@endpush