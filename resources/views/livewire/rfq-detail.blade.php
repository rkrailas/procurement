<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-md-6" style="font-size: 20px; color: #C3002F">
                    RFQ : <span>{{ $rfqHeader['rfqno'] }}</span>
                </div>
                <div class="col-sm-6 text-right">
                    <button wire:click.prevent="goto_prdetail" class="btn btn-sm btn-danger">Go to PR Detail</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        {{-- Header --}}
        <div class="card shadow-none border rounded">
            <div class="card-header my-card-header">
                <div class="row py-0 my-0">
                    <div class="col-12 d-flex justify-content-between">
                        <div>
                            Order Type : <span>{{ $rfqHeader['ordertype'] }}</span>
                        </div>
                        <div>
                            Status : <span>{{ $rfqHeader['rfqstatus'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body my-card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label>PR No.</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="rfqHeader.prno">
                    </div>
                    <div class="col-md-3">
                        <label>Purpose of PR</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="rfqHeader.site">
                    </div>
                    <div class="col-md-3">
                        <label>Requester</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="rfqHeader.requestor">
                    </div>
                    <div class="col-md-3">
                        <label>Requested For</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="rfqHeader.requested_for">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Buyer</label>
                        <x-select2 id="buyer-select2" wire:model.defer="rfqHeader.buyer">
                            @foreach($buyer_dd as $row)
                            <option value="{{ $row->username }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                    </div>
                    <div class="col-md-3">
                        <label>Buyer Group</label>
                        <x-select2 id="buyergroup-select2" wire:model.defer="rfqHeader.buyer_group">
                            @foreach($buyergroup_dd as $row)
                            <option value="{{ $row->buyer_group_code }}">
                                {{ $row->buyer_group_code }}
                            </option>
                            @endforeach
                        </x-select2>
                    </div>
                    <div class="col-md-3">
                        <label>Site</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="rfqHeader.site">
                    </div>
                    <div class="col-md-3">
                        <label>Delivery Location</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="rfqHeader.delivery_location">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-1">
                        <label>L. Currency</label>
                        <input class="form-control form-control-sm text-right" type="text" readonly value="THB">
                    </div>
                    <div class="col-md-2">
                        <label>Total Base Price</label>
                        <input class="form-control form-control-sm text-right" type="text" readonly wire:model.defer="rfqHeader.total_base_price_local">
                    </div>
                    <div class="col-md-3">
                        <label>Total Final Price</label>
                        <input class="form-control form-control-sm text-right" type="text" readonly wire:model="rfqHeader.total_final_price_local">
                    </div>
                    <div class="col-md-3">
                        <label>C/R Amount</label>
                        <input class="form-control form-control-sm text-right" type="text" readonly wire:model.defer="rfqHeader.cramount_local">
                    </div>
                    <div class="col-md-3">
                        <label>CR%</label>
                        <input class="form-control form-control-sm text-right" type="text" readonly wire:model.defer="rfqHeader.crpercent_local">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>eDecision No.</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="rfqHeader.edecisionno">
                    </div>
                    <div class="col-md-3">
                        <label>RFQ Remark</label>
                        <input class="form-control form-control-sm" type="text" wire:model.defer="rfqHeader.rfq_remark">
                    </div>
                    <div class="col-md-3">
                        <label>RFQ Created Date</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="rfqHeader.create_on">
                    </div>
                    <div class="col-md-3">
                        <label>RFQ Last Modified Date</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model="rfqHeader.changed_on">
                    </div>

                </div>
            </div>
        </div>
        {{-- Header End--}}

        <!-- .Tab Header -->
        <ul class="nav nav-tabs" id="pills-tab" role="tablist">
            <li class="nav-item" wire:ignore>
                <a class="nav-link {{ $currentTab == 'item' ? 'active' : '' }}" id="pills-lineitem-tab" data-toggle="pill" href="#pills-lineitem" role="tab" 
                    aria-controls="pills-lineitem" aria-selected="true">Line Item</a>
            </li>
            <li class="nav-item" wire:ignore>
                <a class="nav-link {{ $currentTab == 'Suppliers' ? 'active' : '' }}" id="pills-Suppliers-tab" data-toggle="pill" href="#pills-Suppliers" role="tab" 
                    aria-controls="pills-Suppliers" aria-selected="false">Suppliers</a>
            </li>
            <li class="nav-item" wire:ignore>
                <a class="nav-link {{ $currentTab == 'QuotationDetails' ? 'active' : '' }}" id="pills-QuotationDetails-tab" data-toggle="pill" href="#pills-QuotationDetails" role="tab" 
                    aria-controls="pills-QuotationDetails" aria-selected="false">Quotation Detail</a>
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

        {{-- Tab Content --}}
        <div class="tab-content m-0 pb-0" id="pills-tabContent">
            {{-- Tab Line Item --}}
            <div class="tab-pane fade {{ $currentTab == 'item' ? 'show active' : '' }}" id="pills-lineitem" role="tabpanel" aria-labelledby="pills-lineitem-tab" wire:ignore.self>
                <div class="row m-0 p-0">
                    <div class="col-md-12">
                        <table class="table table-sm nissanTB">
                            <thead>
                            <tr class="text-center">
                                <tr>
                                    <th colspan="6" style="background-color: white; border-color: white;"></th>
                                    <th colspan="2" class="text-center">Base Price</th>
                                    <th colspan="2" class="text-center">Final Price</th>
                                    <th colspan="7" style="background-color: white; border-color: white;"></th>
                                </tr>
                                <tr>
                                    <th scope="col">Line No.</th>
                                    <th scope="col">Part No.</th>
                                    <th scope="col">Description</th>                                
                                    <th scope="col">Qty</th>
                                    <th scope="col">UoM</th>
                                    <th scope="col">Currency</th>
                                    <th scope="col">Base Price</th>
                                    <th scope="col">Total Base Price</th>
                                    <th scope="col">Final Price</th>
                                    <th scope="col">Total Final Price</th>
                                    <th scope="col">C/R Amount</th>
                                    <th scope="col">C/R %</th>
                                    <th scope="col">Delivery Date</th>
                                    <th scope="col">e-Decision</th>
                                    <th scope="col">PO No.</th>
                                    <th scope="col">Status</th>
                                    <th></th>
                                </tr>
                            </tr>
                            </thead>
                            <tbody>
                            {{-- Edit in line notuse
                            @foreach ($itemList as $index => $row)
                            <tr>
                                <td scope="col" class="text-center">{{$index}}</td>
                                <td scope="col">{{ $row['partno'] }}</td>
                                <td scope="col">
                                    <input type="text" class="form-control form-control-sm" maxlength="200" wire:model.lazy="itemList.{{$index}}.description">
                                </td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row['qty'], 2) }}</td>
                                <td scope="col" class="text-center">{{ $row['uom'] }}</td>
                                <td scope="col" class="text-center">{{ $row['currency'] }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row['base_price'], 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format( $row['total_base_price'], 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row['final_price'], 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format( $row['total_final_price'], 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row['cr_amount'], 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row['cr_percent'], 2) }}</td>
                                <td scope="col" class="text-center">
                                    <div class="input-group mb-1">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                <i class="fas fa-calendar"></i>
                                            </span>
                                        </div>
                                        <x-datepicker wire:model="itemList.{{$index}}.delivery_date" id="delivery_date{{$index}}"
                                            :error="'date'"/>
                                    </div>
                                </td>
                                <td scope="col">{{ $row['edecisionno'] }}</td>
                                <td scope="col">{{ $row['pono'] }}</td>
                                <td scope="col">{{ $row['status'] }}</td>
                            </tr>
                            @endforeach --}}
                            @foreach ($itemList as $row)
                            <tr>
                                <td scope="col" class="text-center">{{ $loop->iteration + $itemList->firstitem()-1 }}</td>
                                <td scope="col">{{ $row->partno }}</td>
                                <td scope="col">{{ $row->description }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row->qty, 2) }}</td>
                                <td scope="col" class="text-center">{{ $row->uom }}</td>
                                <td scope="col" class="text-center">{{ $row->currency }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row->base_price, 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format( $row->total_base_price, 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row->final_price, 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format( $row->total_final_price, 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row->cr_amount, 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row->cr_percent, 2) }}</td>
                                <td scope="col" class="text-center">{{ \Carbon\Carbon::parse( $row->delivery_date)->format('d-M-Y') }} </td>
                                <td scope="col">{{ $row->edecisionno }}</td>
                                <td scope="col">{{ $row->pono }}</td>
                                <td scope="col">{{ $row->status }}</td>
                                <td>
                                    <a href="" wire:click.prevent="editLineItem('{{ $row->id }}')">
                                        <i class="fa fa-edit mr-2"></i>
                                    </a>
                                </td>

                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col-md-12 mb-1">
                        {{ $itemList->links() }}
                    </div>
                </div> --}}
            </div>

            {{-- Suppliers --}}
            <div class="tab-pane fade {{ $currentTab == 'Suppliers' ? 'show active' : '' }}" id="pills-Suppliers" role="tabpanel" aria-labelledby="pills-Suppliers-tab" wire:ignore.self>
                <div class="row">
                    <div class="col-md-4">
                        <label>Supplier</label>
                        <x-select2 id="supplier-select2" wire:model.defer="tabSupplier.selectSupplier">
                            <option value="">--- Please Select ---</option>
                            @foreach($supplier_dd as $row)
                            <option value="{{ $row->supplier }}">
                                {{ $row->supplier }} : {{ $row->supplier_name }}
                            </option> 
                            @endforeach
                        </x-select2>
                        @error('selectSupplier') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4">
                        <label>Supplier Contact</label>
                        <x-select2 id="supplierContact-select2" wire:model.defer="tabSupplier.selectSupplierContact">
                            {{-- รอค่าจากการ Bind --}}
                        </x-select2>
                        @error('selectSupplierContact') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-4 mt-auto">
                        <button wire:click.prevent="addSupplier" class="btn btn-sm btn-danger"><i class="fas fa-plus-square mr-1"></i>ADD</button>
                    </div>
                </div>
                <div class="row m-0 p-0">
                    <div class="col-md-12">
                        <table class="table table-sm nissanTB">
                            <thead>
                            <tr class="text-center">
                                <th scope="col">Supplier</th>
                                <th scope="col">Supplier Name</th>
                                <th scope="col">Address</th>
                                <th scope="col">PO Currency</th>
                                <th scope="col">Contact Person</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Email</th>
                                <th scope="col"></th>

                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($supplierList as $row)
                            <tr>
                                <td scope="col">{{ $row->supplier }}</td>
                                <td scope="col">{{ $row->supplier_name }}</td>
                                <td scope="col">{{ $row->address }}</td>
                                <td scope="col">{{ $row->supplier_currency }}</td>
                                <td scope="col">{{ $row->contact_person_name }}</td>
                                <td scope="col">{{ $row->telephone_number }}</td>
                                <td scope="col">{{ $row->email }}</td>
                                <td scope="col">
                                    <a href="" wire:click.prevent="deleteRFQSupplier('{{ $row->supplier }}')">
                                        <i class="fas fa-times text-center mr-1" style="color: red"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-1">
                        {{ $supplierList->links() }}
                    </div>
                </div>
            </div>

            {{-- Quotation Details --}}
            <div class="tab-pane fade {{ $currentTab == 'QuotationDetails' ? 'show active' : '' }}" id="pills-QuotationDetails" role="tabpanel" aria-labelledby="pills-QuotationDetails-tab" wire:ignore.self>
                <div class="row">
                    <div class="col-md-6">
                        <label>Supplier</label>
                        <x-select2 id="supplier2-select2" wire:model.defer="tabQuotationDetails.selectSupplier2">
                            <option value="">--- Please Select ---</option>
                            @foreach($supplierQuotation_dd as $row)
                            <option value="{{ $row->supplier }}">
                                {{ $row->supplier }} : {{ $row->supplier_name }}
                            </option> 
                            @endforeach
                        </x-select2>
                        @error('selectSupplier2') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6"></div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Supplier Quotation No. <span style="color: red"> *</span></label>
                        <input class="form-control form-control-sm" type="text" maxlength="40" wire:model.defer="tabQuotationDetails.edecisionno">
                    </div>
                    <div class="col-md-3">
                        <label>Main Contact Person <span style="color: red"> *</span></label>
                        <x-select2 id="mainContactPerson-select2" wire:model.defer="tabQuotationDetails.main_contact_person">
                            {{-- รอค่าจากการ Bind --}}
                        </x-select2>
                        @error('main_contact_person') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-3">
                        <label>Phone</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="tabQuotationDetails.telephone_number">
                    </div>
                    <div class="col-md-3">
                        <label>Email</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="tabQuotationDetails.email">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Expiration Term <span style="color: red">*</span></label>
                        <select class="form-control form-control-sm" wire:model="tabQuotationDetails.quotation_expiry_term">
                            <option value="">--- Please Select ---</option>
                            @foreach($quotationEexpiryTerm_dd as $row)
                            <option value="{{ $row->termno }}">
                                {{ $row->description }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="">Expiry Date <span style="color: red">*</span></label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="tabQuotationDetails.quotation_expiry" id="quotation_expiry"
                                :error="'date'"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label>Payment Terms</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="tabQuotationDetails.payment_term">
                        {{-- <x-select2 id="paymentTerm-select2" wire:model.defer="tabQuotationDetails.payment_term">
                            <option value="">--- Please Select ---</option>
                            @foreach($paymentTerm_dd as $row)
                            <option value="{{ $row->payment_code }}">
                                {{ $row->description }}
                            </option> 
                            @endforeach
                        </x-select2>
                        @error('payment_term') <span class="text-red">{{ $message }}</span> @enderror --}}
                    </div>
                    <div class="col-md-3">
                        <label>Payment Term Pattern</label>
                        <x-select2 id="payment_pattern-select2" wire:model.defer="tabQuotationDetails.payment_pattern">
                            <option value="">--- Please Select ---</option>
                            {{-- @foreach($paymentTerm_dd as $row)
                            <option value="{{ $row->supplier }}">
                                {{ $row->supplier }} : {{ $row->supplier_name }}
                            </option> 
                            @endforeach --}}
                        </x-select2>
                        @error('payment_pattern') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Currency <span style="color: red">*</span></label>
                        <select class="form-control form-control-sm" wire:model="tabQuotationDetails.currency">
                            <option value="">--- Please Select ---</option>
                            @foreach($currency_dd as $row)
                            <option value="{{ $row->currency }}">
                                {{ $row->currency }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Exchange Rate</label>
                        <input class="form-control form-control-sm" type="number" readonly wire:model.defer="tabQuotationDetails.exchange_rate">
                    </div>
                    <div class="col-md-3">
                        <label>Total Base Price</label>
                        <input class="form-control form-control-sm" type="number" readonly wire:model.defer="tabQuotationDetails.total_base_price">
                    </div>
                    <div class="col-md-3">
                        <label>Total Final Price</label>
                        <input class="form-control form-control-sm" type="number" readonly wire:model.defer="tabQuotationDetails.total_final_price">
                    </div>
                </div>
    
                <div class="row">
                    <div class="col-md-3">
                        <label>Local Currency</label>
                        <input class="form-control form-control-sm" type="text" readonly value="THB">
                    </div>
                    <div class="col-md-3">
                        <label>Total Base Price (Local)</label>
                        <input class="form-control form-control-sm" type="number" readonly wire:model.defer="tabQuotationDetails.total_base_price_local">
                    </div>
                    <div class="col-md-3">
                        <label>Total Final Price (Local)</label>
                        <input class="form-control form-control-sm" type="number" readonly wire:model.defer="tabQuotationDetails.total_final_price_local">
                    </div>
                    <div class="col-md-3">
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
                                <th scope="col">Supplier</th>
                                <th scope="col">Qty</th>
                                <th scope="col">UOM</th>
                                <th scope="col">Base Price</th>
                                <th scope="col">Final Price</th>
                                <th scope="col">Total Final Price</th>
                                <th scope="col">Currency</th>
                                <th scope="col"></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($quotationDetailsList as $row)
                            <tr>
                                <td scope="col">
                                    <div d-inline ml-2>
                                        <input wire:model="selectedRows" type="checkbox" value="{{ $row->id }}"
                                            id="{{ $row->id }}">
                                        <span>{{ $loop->iteration + $itemList->firstitem()-1 }}</span>
                                    </div>                                    
                                </td>
                                <td scope="col">{{ $row->partno }}</td>
                                <td scope="col">{{ $row->description }}</td>
                                <td scope="col">{{ $row->status }}</td>
                                <td scope="col">{{ $row->supplier }}</td>
                                <td scope="col">{{ $row->qty }}</td>
                                <td scope="col">{{ $row->uom }}</td>
                                <td scope="col">{{ $row->base_price }}</td>
                                <td scope="col">{{ $row->final_price }}</td>
                                <td scope="col">{{ $row->total_final_price }}</td>
                                <td scope="col">{{ $row->currency }}</td>
                                <td scope="col">
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-1">
                        {{ $supplierList->links() }}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button wire:click.prevent="saveQuotation" class="btn btn-sm btn-danger">
                            <i class="fas fa-save mr-1"></i>Save Quotation</button>
                    </div>
                </div>
            </div>

        </div>
        {{-- Tab Content End--}} 

        <div class="row mt-5">
            <div class="col-md-12">
                <hr width="100%">
            </div>
        </div>
    </div>

    {{-- Modal Edit Line Item --}}
    <div class="modal" id="modelEditLineItem" tabindex="-1" role="dialog" data-backdrop="static" wire:ignore.self>
        <div class="modal-dialog" role="document" style="max-width: 60%;">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-size: 20px;">
                        Edit Item
                    </h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Part No.</label>
                            <input class="form-control form-control-sm" type="text" readonly wire:model.defer="editItem.partno">
                        </div>
                        <div class="col-md-6">
                            <label>Delivery Date</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                </div>
                                <x-datepicker wire:model="editItem.delivery_date" id="delivery_date"
                                    :error="'date'"/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label>Description</label>
                            <input class="form-control form-control-sm" type="text" maxlength="200" wire:model.defer="editItem.description">
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-12 d-flex justify-content-end">
                            <div>
                                <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">
                                    <i class="fa fa-times mr-1"></i>Cancel</button>
                                <button type="button" class="btn btn-sm btn-danger" wire:click.prevent="editItem_Save">
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

    window.addEventListener('show-modelEditLineItem', event => {
        $('#modelEditLineItem').modal('show');
    })

    window.addEventListener('hide-modelEditLineItem', event => {
        $('#modelEditLineItem').modal('hide');
    })

</script>

@endpush