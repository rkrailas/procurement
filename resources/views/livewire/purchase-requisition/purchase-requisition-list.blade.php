<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Purchase Requisition List</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Purchase Requisition</li>
                        <li class="breadcrumb-item active">Purchase Requisition List</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Session Search -->
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <label for="prno">Purchase Requisition No.</label>
                <input class="form-control form-control-sm" type="text" id="prno" maxlength="100" required wire:model.defer="prno">
            </div>
            <div class="col-md-3">
                <label for="ordertype">Order Type</label>
                <select class="form-control form-control-sm" id="ordertype" wire:model.defer="ordertype">
                    <option value="">--- All ---</option>
                    @foreach($ordertype_dd as $row)
                    <option value="{{ $row->ordertype }}">
                        {{ $row->ordertype }} : {{ $row->description }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label for="site">Site No</label>
                <select class="form-control form-control-sm" id="site" wire:model.defer="site">
                    <option value="">--- All ---</option>
                    @foreach($site_dd as $row)
                    <option value="{{ $row->site }}">
                        {{ $row->site }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-2">
                <label class="">Request Date From</label>
                <div class="input-group mb-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-calendar"></i>
                        </span>
                    </div>
                    <x-datepicker wire:model.defer="requestdate_from" id="requestdate_from"
                        :error="'date'" required/>
                </div>
            </div>
            <div class="col-2">
                <label class="">Request Date To</label>
                <div class="input-group mb-1">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <i class="fas fa-calendar"></i>
                        </span>
                    </div>
                    <x-datepicker wire:model.defer="requestdate_to" id="requestdate_to"
                        :error="'date'" required/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-3">
                <label>Requestor</label>
                <x-select2 id="requestor-select2" wire:model.defer="requestor">
                    <option value=" ">--- All ---</option>
                    @foreach($requestor_dd as $row)
                    <option value='{{ $row->id }}'>
                        {{ $row->fullname }}
                    </option>
                    @endforeach
                </x-select2>
            </div>
            <div class="col-3">
                <label>Requested For</label>
                <x-select2 id="requestedfor-select2" wire:model.defer="requested_for">
                    <option value=" ">--- All ---</option>
                    @foreach($requestedfor_dd as $row)
                    <option value='{{ $row->id }}'>
                        {{ $row->fullname }}
                    </option>
                    @endforeach
                </x-select2>
            </div>
            <div class="col-3">
                <label>Buyer</label>
                <x-select2 id="buyer-select2" wire:model.defer="buyer">
                    <option value=" ">--- All ---</option>
                    @foreach($buyer_dd as $row)
                    <option value='{{ $row->id }}'>
                        {{ $row->fullname }}
                    </option>
                    @endforeach
                </x-select2>
            </div>
            <div class="col-md-3">
                <label for="status">Staus</label>
                <select class="form-control form-control-sm" id="status" wire:model.defer="status">
                    <option value="">--- All ---</option>
                    @foreach($status_dd as $row)
                    <option value="{{ $row->status }}">
                        {{ $row->status }} : {{ $row->description }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <button wire:click.prevent="resetSearch" class="btn btn-sm btn-secondary">CLEAR</button>
                        <button wire:click.prevent="searchPR" class="btn btn-sm btn-primary">APPLY</button>
                    </div>
                    <button wire:click.prevent="popupSelectOrderType" class="btn btn-sm btn-success"><i class="fa fa-plus-circle" mb-1></i>
                        Create PR</button>
                </div>
                
                
            </div>
        </div>
    </div>

    <!-- Table PR -->
    <div class="content">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col">
                    <table class="table table-md table-hover">
                        <thead>
                            <th scope="col">#</th>
                            <th scope="col">PR No.
                                <span wire:click="sortBy('prh.prno')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortBy === 'prh.prno' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                    <i class="fa fa-arrow-down {{ $sortBy === 'prh.prno' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Type
                                <span wire:click="sortBy('ort.description')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortBy === 'ort.description' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                    <i class="fa fa-arrow-down {{ $sortBy === 'ort.description' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Requested For
                                <span wire:click="sortBy('req.name')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortBy === 'req.name' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                    <i class="fa fa-arrow-down {{ $sortBy === 'req.name' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                </span>
                            </th>                                    
                            <th scope="col">Site
                                <span wire:click="sortBy('prh.site')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortBy === 'prh.site' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                    <i class="fa fa-arrow-down {{ $sortBy === 'prh.site' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Status
                                <span wire:click="sortBy('prh.status')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortBy === 'prh.status' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                    <i class="fa fa-arrow-down {{ $sortBy === 'prh.status' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Total Budget
                                {{-- <span wire:click="sortBy('sales.transactiondate')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortBy === 'sales.transactiondate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                    <i class="fa fa-arrow-down {{ $sortBy === 'sales.transactiondate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                </span> --}}
                            </th>
                            <th scope="col">Total Price
                                {{-- <span wire:click="sortBy('sales.transactiondate')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortBy === 'sales.transactiondate' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                    <i class="fa fa-arrow-down {{ $sortBy === 'sales.transactiondate' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                </span> --}}
                            </th>
                            <th scope="col">Req. Date
                                <span wire:click="sortBy('prh.request_date')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortBy === 'prh.request_date' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                    <i class="fa fa-arrow-down {{ $sortBy === 'prh.request_date' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Buyer
                                <span wire:click="sortBy('buyer.name')" class="float-right text-sm" style="cursor: pointer;">
                                    <i class="fa fa-arrow-up {{ $sortBy === 'buyer.name' && $sortDirection === 'asc' ? '' : 'text-muted'}}"></i>
                                    <i class="fa fa-arrow-down {{ $sortBy === 'buyer.name' && $sortDirection === 'desc' ? '' : 'text-muted'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Actions</th>
                        </thead>
                        <tbody>
                            @foreach ($pr_list as $row)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $pr_list->firstitem()-1  }}</td>
                                <td scope="col">{{ $row->prno }} </td>
                                <td scope="col">{{ $row->order_type }} </td>
                                <td scope="col">{{ $row->requested_for }} </td>
                                <td scope="col">{{ $row->site }} </td>
                                <td scope="col">{{ $row->status }} </td>
                                <td scope="col">{{ number_format(0,2) }} </td>
                                <td scope="col">{{ number_format(0,2) }} </td>
                                <td scope="col">{{ \Carbon\Carbon::parse($row->request_date)->format('Y-m-d') }} </td>
                                <td scope="col">{{ $row->buyer }} </td>
                                <td>
                                    <center>
                                        <a href="" wire:click.prevent="edit('{{ $row->prno }}')">
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
                <div class="col-10 d-flex justify-content-start align-items-baseline">{{ $pr_list->links() }} <span
                        class="ml-2">{{ number_format($pr_list->Total(),0) }} items</span>
                    <div class="col">
                        <select class="form-control form-control-sm" style="width: 80px;" wire:model.lazy="numberOfPage">
                            <option value="10" selected>10</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal --}}
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
                <select class="form-control form-control-sm" id="ordertype" wire:model.defer="selectedOrderType">
                    <option value="">--- Select ---</option>
                    @foreach($ordertype_dd as $row)
                    <option value="{{ $row->ordertype }}">
                        {{ $row->ordertype }} : {{ $row->description }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" wire:click.prevent="createPR">Create PR</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
        clearSelect2('requestor-select2');
        clearSelect2('requestedfor-select2');
        clearSelect2('buyer-select2');
    })
</script>

@endpush