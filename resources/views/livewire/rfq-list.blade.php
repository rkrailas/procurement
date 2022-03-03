<div>
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="color: #C3002F;">Request For Quotation List</h1>
                </div>
                <div class="col-sm-6 text-right">
                    {{-- <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Purchase Requisition</li>
                        <li class="breadcrumb-item active" style="color: #C3002F;">Request For Quotation List</li>
                    </ol> --}}
                    <button wire:click.prevent="goto_prlist" class="btn btn-sm btn-danger">Go to PR List</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="container">
        <div class="card shadow-none border rounded">
            <div class="card-body p-1">
                <div class="row">
                    <div class="col">
                        <label for="prno">PR No.</label>
                        <input class="form-control" type="text" id="prno" maxlength="10" required
                            wire:model.defer="prno">
                    </div>
                    <div class="col">
                        <label>Buyer</label>
                        <x-select2-multiple id="buyer-select2" wire:model.defer="buyer">
                            @foreach($buyer_dd as $row)
                            <option value='{{ $row->buyer }}'>
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2-multiple>
                    </div>
                    <div class="col">
                        <label>Buyer Group</label>
                        <x-select2-multiple id="buyergroup-select2" wire:model.defer="buyer_group">
                            @foreach($buyergroup_dd as $row)
                            <option value='{{ $row->buyer_group }}'>
                                {{ $row->buyer_group }}
                            </option>
                            @endforeach
                        </x-select2-multiple>
                    </div>
                    <div class="col">
                        <label class="">Create Date From</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="createon_from" id="createon_from" :error="'date'"
                                required />
                        </div>
                    </div>
                    <div class="col">
                        <label class="">Create Date To</label>
                        <div class="input-group mb-1">
                            <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fas fa-calendar"></i>
                                </span>
                            </div>
                            <x-datepicker wire:model.defer="createon_to" id="createon_to" :error="'date'"
                                required />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col">
                        <label>RFQ No.</label>
                        <input class="form-control" type="text" id="rfqno" maxlength="10" required
                            wire:model.defer="rfqno">
                    </div>
                    <div class="col">
                        <label>Requested For</label>
                        <x-select2-multiple id="requestedfor-select2" wire:model.defer="requested_for">
                            @foreach($requestedfor_dd as $row)
                            <option value='{{ $row->id }}'>
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2-multiple>
                    </div>
                    <div class="col">
                        <label>Requester</label>
                        <x-select2-multiple id="requestor-select2" wire:model.defer="requester">
                            @foreach($requestor_dd as $row)
                            <option value='{{ $row->id }}'>
                                {{ $row->fullname }}
                            </option>
                            @endforeach
                        </x-select2-multiple>
                    </div>
                    <div class="col">
                        <label>Site</label>
                        <x-select2-multiple id="site-select2" wire:model.defer="site">
                            @foreach($site_dd as $row)
                            <option value='{{ $row->site }}'>
                                {{ $row->site }}
                            </option>
                            @endforeach
                        </x-select2-multiple>
                    </div>
                    <div class="col-md">
                        <label>Staus</label>
                        <x-select2-multiple id="status-select2" wire:model.defer="status" >
                            @foreach($status_dd as $row)
                            <option value="{{ $row->status }}">
                                {{ $row->status }} : {{ $row->description }}
                            </option>
                            @endforeach
                        </x-select2-multiple>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button wire:click.prevent="resetSearch" class="btn btn-sm btn-light mr-1">CLEAR</button>
                        <button wire:click.prevent="searchPR" class="btn btn-sm btn-danger">APPLY</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- GRID -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <table class="table table-md table-hover nissanTB">
                        <thead>
                            <th scope="col">#</th>
                            <th scope="col">RFQ No.
                                <span wire:click="sortBy('a.rfqno')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'a.rfqno' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'a.rfqno' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Order Type
                                <span wire:click="sortBy('c.ordertype')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'c.ordertype' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'c.ordertype' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">PR No.
                                <span wire:click="sortBy('a.prno')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'a.prno' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'a.prno' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Status
                                <span wire:click="sortBy('a.status')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'a.status' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'a.status' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Site
                                <span wire:click="sortBy('c.site')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'c.site' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'c.site' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Total Base Price 
                                <span wire:click="sortBy('a.total_base_price')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'a.total_base_price' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'a.total_base_price' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Total Final Price 
                                <span wire:click="sortBy('a.total_final_price')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'a.total_final_price' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'a.total_final_price' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Currency
                                <span wire:click="sortBy('a.currency')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'a.currency' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'a.currency' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Requested For
                                <span wire:click="sortBy('c.requested_for')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'c.requested_for' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'c.requested_for' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Requester
                                <span wire:click="sortBy('c.requestor')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'c.requestor' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'c.requestor' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">buyer
                                <span wire:click="sortBy('c.buyer')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'c.buyer' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'c.buyer' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Create On
                                <span wire:click="sortBy('a.create_on')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'a.create_on' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'a.create_on' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col"></th>
                        </thead>
                        <tbody>
                            @foreach ($rfq_list as $row)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $rfq_list->firstitem()-1 }}</td>
                                <td scope="col">{{ $row->rfqno }} </td>
                                <td scope="col">{{ $row->ordertype }} </td>
                                <td scope="col">{{ $row->prno }} </td>
                                <td scope="col">{{ $row->rfqstatus }} </td>
                                <td scope="col">{{ $row->site }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->total_base_price, 2) }} </td>
                                <td scope="col" class="text-right">{{ number_format($row->total_final_price, 2) }} </td>
                                <td scope="col">{{ $row->currency }} </td>
                                <td scope="col">{{ $row->requested_for }} </td>
                                <td scope="col">{{ $row->requestor }} </td>
                                <td scope="col">{{ $row->buyer }} </td>
                                <td scope="col" class="text-center">{{ \Carbon\Carbon::parse($row->create_on)->format('d-M-Y') }} </td>
                                <td>
                                    <center>
                                        <a href="" wire:click.prevent="edit('{{ $row->rfqno }}')">
                                            <i class="fa fa-edit mr-2"></i>
                                        </a>
                                    </center>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-1">
                    {{ $rfq_list->links() }}
                </div>
            </div>
        </div>
    </div>

</div>

@push('js')
<script>
    window.addEventListener('clear-select2', event => {
        clearSelect2('buyer-select2');
        clearSelect2('buyergroup-select2');
        clearSelect2('requestor-select2');
        clearSelect2('requestedfor-select2');
        clearSelect2('site-select2');
        clearSelect2('status-select2');
    })
</script>

@endpush