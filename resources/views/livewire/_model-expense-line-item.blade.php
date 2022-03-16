<div class="modal" id="modelExpenseLineItem" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog" role="document" style="max-width: 80%;">
        <div class="modal-content ">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                    Expense Line Item
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

                {{-- Description --}}
                <div class="row">
                    <div class="col-9">
                        <label>Description <span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="text" maxlength="200"
                            wire:model.defer="prItem.description">
                        @error('description') <span class="text-red">{{ $message }}.</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Non Stock Control</label>
                        <div>
                            <div class="form-check form-check-inline" id="nonstock_control">
                                <input class="form-check-input" type="checkbox" wire:model.defer="prItem.nonstock_control" disabled>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Purchase Unit --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>Purchase Unit <span style="color: red">*</span></label>
                        <x-select2 id="purchase_unit-select2" wire:model.defer="prItem.purchase_unit">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($purchaseunit_dd as $row)
                            <option value='{{ $row->uomno }}'>
                                {{ $row->uomno }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('purchase_unit') <span class="text-red">{{ $message }}.</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Budget Price Per Unit <span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="number" step="0.01"
                            wire:model.defer="prItem.unit_price">
                        @error('unit_price') <span class="text-red">{{ $message }}.</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Currency <span style="color: red">*</span></label>
                        <x-select2 id="currency-select2" wire:model.defer="prItem.currency">
                            <option value=" ">--- Please Select ---</option>
                            @foreach($currency_dd as $row)
                            <option value="{{ $row->currency }}">
                                {{ $row->currency }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('currency') <span class="text-red">{{ $message }}.</span> @enderror
                    </div>
                </div>

                {{-- Exchange Rate --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>Exchange Rate <span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="text" readonly
                            wire:model.defer="prItem.exchange_rate">
                        @error('exchange_rate') <span class="text-red">{{ $message }}.</span> @enderror
                    </div>
                    <div class="col-md-4">
                        {{-- <label>Purchase Group</label>
                        <x-select2 id="purchase_group-select2" wire:model.defer="prItem.purchase_group">
                            @foreach($purchasegroup_dd as $row)
                            <option value='{{ $row->groupno }}'>
                                {{ $row->groupno }} : {{ $row->description }}
                            </option>
                            @endforeach
                        </x-select2> --}}
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>

                {{-- QTY --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>QTY <span style="color: red">*</span></label>
                        <input class="form-control form-control-sm" type="number" step="0.01"
                            wire:model.defer="prItem.qty">
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
                            <x-datepicker autocomplete="off" wire:model.defer="prItem.req_date" id="request_date2" :error="'date'"/>
                        </div>
                        @error('req_date') <span class="text-red">{{ $message }}.</span> @enderror
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
                        <label>Useful life more than 1 year</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox"
                                    wire:model.defer="prItem.over_1_year_life">
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
                                <input class="form-control form-control-sm" type="number" readonly step="0.01"
                                    wire:model.defer="prItem.final_price">
                            </div>
                            <div class="col-md-4">
                                <label>Quotation Expiry Date</label>
                                <input class="form-control form-control-sm" type="text" readonly
                                    wire:model.defer="prItem.quotation_expiry_date">
                            </div>
                            <div class="col-md-4">
                                <label>Nominated Supplier</label>
                                <input class="form-control form-control-sm" type="text" readonly
                                    wire:model.defer="prItem.nominated_supplier">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Remarks --}}
                <div class="row">
                    <div class="col-md-12">
                        <label>Remarks</label>
                        <textarea class="form-control form-control-sm" rows="3" maxlength="250"
                            wire:model.defer="prItem.remarks"></textarea>
                    </div>
                </div>

                {{-- Reference PR --}}
                <div class="row">
                    <div class="col-md-4">
                        <label>Reference PR</label>
                        <input class="form-control form-control-sm" type="text" readonly
                            wire:model.defer="prItem.reference_pr">
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
    window.addEventListener('show-modelExpenseLineItem', event => {
        $('#modelExpenseLineItem').modal('show');
    })

    window.addEventListener('hide-modelExpenseLineItem', event => {
        $('#modelExpenseLineItem').modal('hide');
    })

    window.addEventListener('clear-select2-modal', event => {
        clearSelect2('purchase_unit-select2');
        clearSelect2('currency-select2');
        clearSelect2('purchase_group-select2');
        clearSelect2('internalorder-select2');
        clearSelect2('budgetcode-select2'); 
    })

    // window.addEventListener('bindToSelect', event => {
    //     $(event.detail.selectName).html(" ");
    //     $(event.detail.selectName).append(event.detail.newOption);
    // })
</script>
@endpush