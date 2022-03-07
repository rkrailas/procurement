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
                        <label>Sitet</label>
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
                            <option value="{{ $row->buyer }}">
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2>
                    </div>
                    <div class="col-md-3">
                        <label>Buyer Group</label>
                        <x-select2 id="buyergroup-select2" wire:model.defer="rfqHeader.buyer_group">
                            @foreach($buyergroup_dd as $row)
                            <option value="{{ $row->buyer_group }}">
                                {{ $row->buyer_group }}
                            </option>
                            @endforeach
                        </x-select2>
                    </div>
                    <div class="col-md-3">
                        <label>Delivery Location</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="rfqHeader.delivery_location">
                    </div>
                    <div class="col-md-3">
                        <label>Currency</label>
                        <x-select2 id="currency-select2" wire:model.defer="rfqHeader.currency">
                            @foreach($currency_dd as $row)
                            <option value="{{ $row->currency }}">
                                {{ $row->currency }}
                            </option>
                            @endforeach
                        </x-select2>
                        @error('currency') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Total Base Price</label>
                        <input class="form-control form-control-sm text-right" type="text" readonly wire:model.defer="rfqHeader.total_base_price">
                    </div>
                    <div class="col-md-3">
                        <label>Total Final Price</label>
                        <input class="form-control form-control-sm text-right" type="text" readonly wire:model="rfqHeader.total_final_price">
                    </div>
                    <div class="col-md-3">
                        <label>CR%</label>
                        <input class="form-control form-control-sm text-right" type="text" readonly wire:model.defer="rfqHeader.cr">
                    </div>
                    <div class="col-md-3">
                        <label>C/R Amount</label>
                        <input class="form-control form-control-sm text-right" type="text" readonly wire:model.defer="rfqHeader.cramt">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label>Create On</label>
                        <input class="form-control form-control-sm" type="text" readonly wire:model.defer="rfqHeader.create_on">
                    </div>
                    <div class="col-md-3">
                        <label>Change On</label>
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
                <a class="nav-link {{ $currentTab == 'Quotation' ? 'active' : '' }}" id="pills-Quotation-tab" data-toggle="pill" href="#pills-Quotation" role="tab" 
                    aria-controls="pills-Quotation" aria-selected="false">Quotation Detail</a>
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
            <div class="tab-pane fade {{ $currentTab == 'item' ? 'show active' : '' }}" id="pills-lineitem" role="tabpanel" aria-labelledby="pills-lineitem-tab" wire:ignore.self>
                <div class="row">
                    <div class="col-md-6">
                        <label>Supplier</label>
                        <select class="form-control form-control-sm" wire:model="tabLineItem.selectSupplierItem">
                            <option value="">--- Please Select ---</option>
                            @foreach($supplierForAssign_dd as $row)
                            <option value="{{ $row->supplier }}">
                                {{ $row->supplier }} : {{ $row->supplier_name }}
                            </option> 
                            @endforeach
                        </select>
                        @error('selectSupplierItem') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mt-auto">
                        <button wire:click.prevent="assignSupplier" class="btn btn-sm btn-danger"><i class="fas fa-plus-square mr-1"></i>APPLY</button>
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
                                <th scope="col">UoM</th>
                                <th scope="col">Delivery Date</th>
                                <th scope="col">Total Price</th>
                                <th scope="col">Final Price</th>
                                <th scope="col">Currency</th>
                                <th scope="col">C/R Amount</th>
                                <th scope="col">C/R %</th>
                                <th scope="col">Supplier</th>
                                <th scope="col">e-Decision</th>
                                <th scope="col">PO No.</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($itemList as $row)
                            <tr>
                                <td>
                                    <div d-inline ml-2 mt-auto>
                                        <input wire:model="selectedRows" type="checkbox" value="{{ $row->id }}"
                                            id="{{ $row->line_no }}">
                                        <span>{{ $row->line_no }}</span>
                                    </div>
                                </td>
                                <td scope="col">{{ $row->partno }}</td>
                                <td scope="col">{{ $row->description }}</td>                                
                                <td scope="col">{{ $row->status }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row->qty, 0) }}</td>
                                <td scope="col" class="text-center">{{ $row->uom }}</td>
                                <td scope="col" class="text-center">{{ \Carbon\Carbon::parse( $row->delivery_date)->format('d-M-Y') }} </td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row->total_price_lc, 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format( $row->final_price_lc, 2) }}</td>
                                <td scope="col" class="text-center">{{ $row->currency }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row->cr, 2) }}</td>
                                <td scope="col" class="text-right pr-2">{{ number_format($row->cramt, 2) }}</td>
                                <td scope="col">{{ $row->supplier }}</td>
                                <td scope="col">{{ $row->edecision }}</td>
                                <td scope="col">{{ $row->pono }}</td>
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

            <div class="tab-pane fade {{ $currentTab == 'Suppliers' ? 'show active' : '' }}" id="pills-Suppliers" role="tabpanel" aria-labelledby="pills-Suppliers-tab" wire:ignore.self>
                <div class="row">
                    <div class="col-md-6">
                        <label>Supplier</label>
                        <select class="form-control form-control-sm" wire:model="tabSupplier.selectSupplier">
                            <option value="">--- Please Select ---</option>
                            @foreach($supplier_dd as $row)
                            <option value="{{ $row->supplier }}">
                                {{ $row->supplier }} : {{ $row->supplier_name }}
                            </option> 
                            @endforeach
                        </select>
                        @error('selectSupplier') <span class="text-red">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 mt-auto">
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
                                <td scope="col">{{ $row->location }}</td>
                                <td scope="col">{{ $row->po_currency }}</td>
                                <td scope="col">{{ $row->contact_person }}</td>
                                <td scope="col">{{ $row->telphone_number }}</td>
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
        </div>
        {{-- Tab Content End--}} 

        <div class="row mt-5">
            <div class="col-md-12">
                <hr width="100%">
            </div>
        </div>
    </div>
</div>
