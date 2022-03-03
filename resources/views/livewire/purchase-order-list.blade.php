<div>
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="color: #C3002F;">Purchase Order List</h1>
                </div>
                <div class="col-sm-6">

                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="container">
        <div class="card shadow-none border rounded">
            <div class="card-body p-1">
                <div class="row">
                    <div class="col-md-3">
                        <label>Purchase Order No.</label>
                        <input class="form-control form-control-sm" type="text" id="pono" maxlength="100" wire:model.defer="pono">
                    </div>
                    <div class="col-md-3">
                        <label for="ordertype">Order Type</label>
                        <select class="form-control form-control-sm" wire:model="ordertype">
                            <option value="">--- Please Select ---</option>
                            {{-- @foreach($ordertype_dd as $row)
                            <option value="{{ $row->ordertype }}">
                                {{ $row->ordertype }} : {{ $row->description }}
                            </option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Site</label>
                        <select class="form-control form-control-sm" wire:model="site">
                            <option value="">--- Please Select ---</option>
                            {{-- @foreach($site_dd as $row)
                            <option value="{{ $row->site }}">
                                {{ $row->site }} : {{ $row->delivery_location }}
                            </option>
                            @endforeach --}}
                        </select>
                    </div>
                    <div class="col-3">
                        <label>Staus</label>
                        <select class="form-control form-control-sm" wire:model="status">
                            <option value="">--- Please Select ---</option>
                            {{-- @foreach($status_dd as $row)
                            <option value="{{ $row->status }}">
                                {{ $row->status }} : {{ $row->description }}
                            </option>
                            @endforeach --}}
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-3">
                        <label>RFQ No.</label>
                        <x-select2-multiple id="rfqno-select2" wire:model.defer="rfqno">
                            {{-- @foreach($rfqno_dd as $row)
                            <option value="{{ $row->rfqno_dd }}">
                                {{ $row->rfqno_dd }}
                            </option>
                            @endforeach --}}
                        </x-select2-multiple>
                    </div>
                    <div class="col-3">
                        <label>Purchase Requisiton No.</label>
                        <x-select2-multiple id="prno-select2" wire:model.defer="prno">
                            {{-- @foreach($prno_dd as $row)
                            <option value="{{ $row->prno }}">
                                {{ $row->prno }}
                            </option>
                            @endforeach --}}
                        </x-select2-multiple>
                    </div>
                    <div class="col-3">
                        <label>Supplier</label>
                        <x-select2-multiple id="supplier-select2" wire:model.defer="supplier">
                            {{-- @foreach($supplier_dd as $row)
                            <option value='{{ $row->id }}'>
                                {{ $row->fullname }}
                            </option>
                            @endforeach --}}
                        </x-select2-multiple>
                    </div>
                    <div class="col-3">
                        <label>Buyer</label>
                        <x-select2-multiple id="buyer-select2" wire:model.defer="buyer">
                            {{-- @foreach($buyer_dd as $row)
                            <option value='{{ $row->buyer }}'>
                                {{ $row->fullname }}
                            </option>
                            @endforeach --}}
                        </x-select2-multiple>
                    </div>                    
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <label class="">Create Date From</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="createdate_from" id="createdate_from" :error="'date'"
                                required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="">Create Date To</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="createdate_to" id="createdate_to" :error="'date'"
                                required />
                        </div>
                    </div>
                    <div class="col-md-6 mt-auto">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button wire:click.prevent="resetSearch" class="btn btn-sm btn-light mr-1">CLEAR</button>
                            <button wire:click.prevent="searchPO" class="btn btn-sm btn-danger">APPLY</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table PO -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12 d-flex justify-content-end">
                    <button wire:click.prevent="popupSelectOrderType" class="btn btn-sm btn-danger"><i
                            class="fa fa-plus-circle" mb-1></i>
                        Create PO</button>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col">
                    <table class="table table-md table-hover nissanTB">
                        <thead>
                            <th scope="col">#</th>
                            <th scope="col">PO No.
                                <span wire:click="sortBy('poh.pono')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'poh.pono' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'poh.pono' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Order Type
                                <span wire:click="sortBy('ort.description')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'ort.description' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'ort.description' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">PR No.
                                <span wire:click="sortBy('prh.prno')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'prh.prno' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'prh.prno' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">RFQ No.
                                <span wire:click="sortBy('rfq.rfqno')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'rfq.rfqno' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'rfq.rfqno' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Status
                                <span wire:click="sortBy('poh.status')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'poh.status' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'poh.status' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Site
                                <span wire:click="sortBy('poh.site')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'poh.site' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'poh.site' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Part No.
                                <span wire:click="sortBy('buyer.name')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'part.partno' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'part.partno' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Description
                                <span wire:click="sortBy('buyer.name')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'part.partno' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'part.partno' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Total Net Value
                                <span wire:click="sortBy('poh.total_netvalue')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'poh.total_netvalue' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'poh.total_netvalue' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Total Gross Value
                                <span wire:click="sortBy('poh.total_grossvalue')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'poh.total_grossvalue' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'poh.total_grossvalue' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Currency
                                <span wire:click="sortBy('poh.currency')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'poh.currency' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'poh.currency' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Requested For
                                <span wire:click="sortBy('req_f.name')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'req_f.name' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'req_f.name' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Requestor
                                <span wire:click="sortBy('req.name')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'req.name' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'req.name' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Buyer
                                <span wire:click="sortBy('buyer.name')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'buyer.name' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'buyer.name' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Create On
                                <span wire:click="sortBy('poh.request_date')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'poh.request_date' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'poh.request_date' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col"></th>
                        </thead>
                        <tbody>
                            {{-- @foreach ($po_list as $row)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $po_list->firstitem()-1 }}</td>
                                <td scope="col">{{ $row->pono }} </td>
                                <td scope="col">{{ $row->order_type }} </td>
                                <td scope="col">{{ $row->prno }} </td>
                                <td scope="col">{{ $row->rfqno }} </td>
                                <td scope="col">{{ $row->status }} </td>
                                <td scope="col">{{ $row->site }} </td>
                                <td scope="col">{{ $row->partno }} </td>
                                <td scope="col">{{ $row->description }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->total_netvalue, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->total_grossvalue, 2) }} </td>
                                <td scope="col">{{ $row->currency }} </td>
                                <td scope="col">{{ $row->requested_for }} </td>
                                <td scope="col">{{ $row->requestor }} </td>
                                <td scope="col">{{ $row->buyer }} </td>
                                <td scope="col" class="text-center">{{ \Carbon\Carbon::parse($row->create_on)->format('d-M-Y') }} </td>
                                <td>
                                    <center>
                                        <a href="" wire:click.prevent="edit('{{ $row->pono }}')">
                                            <i class="fa fa-edit mr-2"></i>
                                        </a>
                                    </center>
                                </td>
                            </tr>
                            @endforeach --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                {{-- <div class="col-md-12 mb-1">
                    {{ $po_list->links() }}
                </div> --}}
            </div>
        </div>
    </div>
    
    <!-- Modal-->
    <div class="modal" id="orderTypeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Select Order Type</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="ordertype">Order Type</label>
                    <select class="form-control form-control-sm" wire:model.defer="selectedOrderType">
                        <option value="">--- Select ---</option>
                        {{-- @foreach($ordertype_dd as $row)
                        <option value="{{ $row->ordertype }}">
                            {{ $row->ordertype }} : {{ $row->description }}
                        </option>
                        @endforeach --}}
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" wire:click.prevent="createPO">Create PO</button>
                    <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>


@push('js')

<script>
    window.addEventListener('show-orderTypeModal', event => {
        $('#orderTypeModal').modal('show');
    })

    window.addEventListener('hide-orderTypeModal', event => {
        $('#orderTypeModal').modal('hide');
    })

    window.addEventListener('clear-select2', event => {
        // clearSelect2('ordertype-select2');
        // clearSelect2('site-select2');
        // clearSelect2('requestor-select2');
        // clearSelect2('requestedfor-select2');
        // clearSelect2('buyer-select2');
        // clearSelect2('status-select2');
    })
</script>

@endpush