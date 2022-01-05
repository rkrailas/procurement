<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-12">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Purchase Requisition</li>
                        <li class="breadcrumb-item active">Purchase Requisition Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container">

        {{-- Header --}}
        <div class="row">
            <div class="col-md-12">
                <label style="color: blue; font-size: 20px;">Purchase Requsition</label>
                <hr width="100%">
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <label for="prno">Purchase Requsition No :</label>
                @if ($isCreateMode)
                    <input class="form-control form-control-sm" type="text" id="prno" maxlength="20" wire:model.defer="prHeader.prno">
                @else
                    <span style="color: blue">{{ $prHeader['prno'] }}</span>
                @endif
                
            </div>
            <div class="col-md-3">                
            </div>
            <div class="col-md-3">
                <label>Order Type : </label>
                <span style="color: blue">{{ $prHeader['ordertypename'] }}</span>
            </div>
            <div class="col-md-3">
                <label>Status : </label>
                <span style="color: blue">{{ $prHeader['statusname'] }}</span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <label for="prno">Requestor</label>
                <input class="form-control form-control-sm" type="text" id="requestor_name" readonly wire:model.defer="prHeader.requestor_name">
            </div>
            <div class="col-md-3">
                <label>Requested For</label>
                <x-select2 id="requestedfor-select2" wire:model.defer="prHeader.requested_for">
                    <option value=" ">--- All ---</option>
                    @foreach($requested_for_dd as $row)
                    <option value="{{ $row->id }}">
                        {{ $row->fullname }}
                    </option>
                    @endforeach
                </x-select2>
            </div>
            <div class="col-md-3">
                <label>Delivery Address</label>
                <select class="form-control form-control-sm" id="delivery_address" wire:model.defer="prHeader.delivery_address">
                    <option value="">--- Please Select ---</option>
                    @foreach($delivery_address_dd as $row)
                    <option value="{{ $row->address_id }}">
                        {{ $row->address_id }} : {{ $row->address }}
                    </option>
                    @endforeach
                </select>
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
                        :error="'date'" required/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <label for="prno">Company</label>
                <input class="form-control form-control-sm" type="text" id="company" maxlength="100" readonly wire:model="prHeader.company">
            </div>
            <div class="col-md-3">
                <label for="prno">Site</label>
                <input class="form-control form-control-sm" type="text" id="site" maxlength="100" readonly wire:model="prHeader.site">
            </div>
            <div class="col-md-3">
                <label for="prno">Function</label>
                <input class="form-control form-control-sm" type="text" id="functions" maxlength="100" readonly wire:model.defer="prHeader.functions">
            </div>
            <div class="col-md-3">
                <label for="prno">Department</label>
                <input class="form-control form-control-sm" type="text" id="department" maxlength="100" readonly wire:model.defer="prHeader.department">
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <label for="prno">Division</label>
                <input class="form-control form-control-sm" type="text" id="division" maxlength="100" readonly wire:model.defer="prHeader.division">
            </div>
            <div class="col-md-3">
                <label for="prno">Section</label>
                <input class="form-control form-control-sm" type="text" id="section" maxlength="100" readonly wire:model.defer="prHeader.section">
            </div>
            <div class="col-md-3">
                <label for="prno">Email</label>
                <input class="form-control form-control-sm" type="text" id="email" maxlength="100" readonly wire:model.defer="prHeader.email">
            </div>
            <div class="col-md-3">
                <label for="prno">Phone</label>
                <input class="form-control form-control-sm" type="text" id="phone" maxlength="100" readonly wire:model.defer="prHeader.phone">
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <label for="ordertype">Buyer</label>
                <x-select2 id="buyer-select2" wire:model.defer="prHeader.buyer">
                    <option value=" ">--- All ---</option>
                    @foreach($buyer_dd as $row)
                    <option value="{{ $row->id }}">
                        {{ $row->fullname }}
                    </option>
                    @endforeach
                </x-select2>
            </div>
            <div class="col-md-6">
                <label for="ordertype">Cost Center (Department Code)</label>
                <select class="form-control form-control-sm" id="cost_center" wire:model.defer="prHeader.cost_center">
                    <option value="">--- Please Select ---</option>
                    @foreach($cost_center_dd as $row)
                    <option value="{{ $row->department }}">
                        {{ $row->department }} : {{ $row->description }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="prno">E-Decision No.</label>
                <input class="form-control form-control-sm" type="text" id="edecision" maxlength="100" wire:model.defer="prHeader.edecision">
            </div>
        </div>

        {{-- Blanket Request --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <label style="color: blue; font-size: 20px;">Blanket Request</label>
                <hr width="100%">
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
                    <x-datepicker wire:model.defer="prHeader.valid_until" id="valid_until"
                        :error="'date'" required/>
                </div>
            </div>
            <div class="col-md-3">
                <label for="date_to_notify">Days to Notify before BPO Expiry</label>
                <input class="form-control form-control-sm" type="number" step="1" id="days_to_notify" wire:model.defer="prHeader.days_to_notify">
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

        {{-- Line items --}}
        <div class="row mt-3 mb-0">
            <div class="col-md-12">
                <div class="d-flex justify-content-between mb-2">
                    <label style="color: blue; font-size: 20px;">Line items</label>
                    <button wire:click.prevent="showAddItem" class="btn btn-sm btn-primary"><i class="fas fa-plus-square mr-1"></i>Add Item</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <hr width="100%">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-sm">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Description</th>
                        <th scope="col">Part</th>
                        <th scope="col">Status</th>
                        <th scope="col">Qty</th>
                        <th scope="col">Pur. Unit</th>
                        <th scope="col">Budget Unit</th>
                        <th scope="col">Budget Total</th>
                        <th scope="col">Req. Delivery Date</th>
                        <th scope="col">Final Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row">1</th>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                      </tr>
                      <tr>
                        <th scope="row">2</th>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                      </tr>
                      <tr>
                        <th scope="row">3</th>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                      </tr>
                      <tr>
                        <th scope="row">4</th>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                      </tr>
                      <tr>
                        <th scope="row">5</th>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                        <td scope="col"></td>
                      </tr>
                    </tbody>
                  </table>
            </div>
        </div>


        {{-- Actions --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <label style="color: blue; font-size: 20px;">Actions</label>
                <hr width="100%">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <button wire:click.prevent="" class="btn btn-sm btn-success" disabled>Release for Sourcing</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-success" disabled>Release for PO</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-info" disabled>Print</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-secondary" disabled>Cancel</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-danger" disabled>Delete</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-warning" disabled>Re-Open</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-success" disabled>Converet to PO</button>
                    </div>                
                    <button wire:click.prevent="savePR" class="btn btn-sm btn-primary"><i class="fas fa-save mr-1"></i>Save</button>
                </div>
                
            </div>
        </div>
    </div>
    @include('livewire.purchase-requisition._model-part-line-item')
    @include('livewire.purchase-requisition._model-expense-line-item')
</div>

@push('js')
<script>
    window.addEventListener('bindToSelect2', event => {
        $(event.detail.selectName).html(" ");
        $(event.detail.selectName).append(event.detail.newOption);
    })

    document.addEventListener("livewire:load", function() { 
        @this.setDefaultSelect2()
    });
</script>
@endpush
