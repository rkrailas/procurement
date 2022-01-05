<div class="modal" id="modelPartLineItem" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
    <div class="modal-dialog" role="document" style="max-width: 80%;">
        <form autocomplete="off" wire:submit.prevent="addEditItem">
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
                                <label>PR Line No : </label>
                            </div>
                            <div>
                                <label>Status :</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <hr width="100%">
                    </div>

                    {{-- Part --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label>Part <span style="color: red">*</span></label>
                            <x-select2 id="partno-select2" wire:model.defer="">
                                <option value=" ">--- Please Select ---</option>
                                {{-- @foreach($requested_for_dd as $row)
                                <option value="{{ $row->id }}">
                                    {{ $row->fullname }}
                                </option>
                                @endforeach --}}
                            </x-select2>
                        </div>
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4">
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="row">
                        <div class="col-12">
                            <label>Description <span style="color: red">*</span></label>
                            <input class="form-control form-control-sm" type="text" id="" wire:model.defer="">
                        </div>
                    </div>

                    {{-- Purchase Unit --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label>Purchase Unit</label>
                            <input class="form-control form-control-sm" type="text" readonly id="" wire:model.defer="">
                        </div>
                        <div class="col-md-4">
                            <label>Budget Price Per Unit <span style="color: red">*</span></label>
                            <input class="form-control form-control-sm" type="number" step="0.01" id=""
                                wire:model.defer="">
                        </div>
                        <div class="col-md-4">
                            <label>Currency <span style="color: red">*</span></label>
                            <x-select2 id="currency-select2" wire:model.defer="">
                                <option value=" ">--- Please Select ---</option>
                                {{-- @foreach($citys_dd as $row)
                                <option value='{{ $row->city }}'>
                                    {{ $row->city }}
                                </option>
                                @endforeach --}}
                            </x-select2>
                        </div>
                    </div>

                    {{-- Exchange Rate --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label>Exchange Rate</label>
                            <input class="form-control form-control-sm" type="text" readonly id="" wire:model.defer="">
                        </div>
                        <div class="col-md-4">
                            <label>Purchase Group</label>
                            <input class="form-control form-control-sm" type="text" readonly id="" wire:model.defer="">
                        </div>
                        <div class="col-md-4">
                            <label>Accounting Group</label>
                            <input class="form-control form-control-sm" type="text" readonly id="" wire:model.defer="">
                        </div>
                    </div>

                    {{-- QTY --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label>QTY <span style="color: red">*</span></label>
                            <input class="form-control form-control-sm" type="number" step="0.01" id=""
                                wire:model.defer="">
                        </div>
                        <div class="col-md-4">
                            <label>Requested Delivery Date <span style="color: red">*</span></label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model.defer="" id="request_date1" :error="'date'" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Internal Order</label>
                            <x-select2 id="internalorder-select2" wire:model.defer="">
                                <option value=" ">--- Please Select ---</option>
                                {{-- @foreach($citys_dd as $row)
                                <option value='{{ $row->city }}'>
                                    {{ $row->city }}
                                </option>
                                @endforeach --}}
                            </x-select2>
                        </div>
                    </div>

                    {{-- Budget Code --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label>Budget Code <span style="color: red">*</span></label>
                            <x-select2 id="budgetcode-select2" wire:model.defer="">
                                <option value=" ">--- Please Select ---</option>
                                {{-- @foreach($citys_dd as $row)
                                <option value='{{ $row->city }}'>
                                    {{ $row->city }}
                                </option>
                                @endforeach --}}
                            </x-select2>
                        </div>
                        <div class="col-md-4">
                            <label>Brand</label>
                            <input class="form-control form-control-sm" type="text" readonly id="" wire:model.defer="">
                        </div>
                        <div class="col-md-4">
                            <label>Model / Spec</label>
                            <input class="form-control form-control-sm" type="text" readonly id="" wire:model.defer="">
                        </div>
                    </div>

                    {{-- RFQ & DOA --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label>RFQ & DOA</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" wire:model.defer="">
                                    <label class="form-check-label">Skip RFQ</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" wire:model.defer="">
                                    <label class="form-check-label">Skip RFQ</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Useful life more than 1 year</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="radioMoreThan1"
                                        id="radioMoreThan1_Yes" value="service" />
                                    <label class="form-check-label" for="radioMoreThan1_Yes">Yes</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="radioMoreThan1"
                                        id="radioMoreThan1_No" value="production" />
                                    <label class="form-check-label" for="radioMoreThan1_No">No</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>For SNN</label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="radioForSNN"
                                        id="radioForSNN_Service" value="service" />
                                    <label class="form-check-label" for="radioForSNN_Service">Service</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="radioForSNN"
                                        id="radioForSNN_Production" value="production" />
                                    <label class="form-check-label" for="radioForSNN_Production">Production</label>
                                </div>
                            </div>
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
                                    <input class="form-control form-control-sm" type="number" step="0.01" id=""
                                        wire:model.defer="">
                                </div>
                                <div class="col-md-4">
                                    <label>Quotation Expiry Date</label>
                                    <div class="input-group mb-1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                        </div>
                                        <x-datepicker wire:model.defer="" id="expiry_date1" :error="'date'" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label>Nominated Supplier</label>
                                    <input class="form-control form-control-sm" type="text" id="" wire:model.defer="">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Remarks --}}
                    <div class="row">
                        <div class="col-md-12">
                            <label>Remarks</label>
                            <textarea class="form-control form-control-sm" rows="3" wire:model.defer=""></textarea>
                        </div>
                    </div>

                    {{-- Reference PR --}}
                    <div class="row">
                        <div class="col-md-4">
                            <label>Reference PR</label>
                            <input class="form-control form-control-sm" type="text" id="" wire:model.defer="">
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
                                <button type="button" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash-alt mr-1"></i>Delete</button>
                                <button type="button" class="btn btn-sm btn-warning">
                                    <i class="fas fa-undo mr-1"></i>Revoke</button>
                            </div>
                            <div>
                                <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">
                                    <i class="fa fa-times mr-1"></i>Cancel</button>
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fa fa-save mr-1"></i>Confirm
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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

    // window.addEventListener('clear-select2', event => {
    //     clearSelect2('salesaccount-select2');
    // })

    // window.addEventListener('bindToSelect', event => {
    //     $(event.detail.selectName).html(" ");
    //     $(event.detail.selectName).append(event.detail.newOption);
    // })
</script>
@endpush