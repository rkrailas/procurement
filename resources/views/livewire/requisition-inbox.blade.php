<div>
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="color: #C3002F;">Requisition Inbox</h1>
                </div>
                <div class="col-sm-6">
                    {{-- <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Purchase Requisition</li>
                        <li class="breadcrumb-item active" style="color: #C3002F;">Requisition Inbox</li>
                    </ol> --}}
                </div>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="container-fluid">
        <div class="card shadow-none border rounded">
            <div class="card-body p-1">
                <div class="row">
                    <div class="col-md-3">
                        <label for="ordertype">Doc Type</label>
                        <select class="form-control form-control-sm" id="ordertype" wire:model.defer="doctype">
                            <option value="">--- All ---</option>
                            @foreach($doctype_dd as $row)
                            <option value="{{ $row->doc_type_no }}">
                                {{ $row->doc_type_no }} : {{ $row->description }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        {{-- 8/2/2022 > Hide by CR No.4
                        <label for="site">Status</label>
                        <select class="form-control form-control-sm" id="site" wire:model.defer="status">
                            <option value="">--- All ---</option>
                            @foreach($status_dd as $row)
                            <option value="{{ $row->status_no }}">
                                {{ $row->status_no }} : {{ $row->description }}
                            </option>
                            @endforeach
                        </select> --}}
                    </div>
                    <div class="col-6 text-right mt-auto">
                        <button wire:click.prevent="resetSearch" class="btn btn-sm btn-light">CLEAR</button>
                        <button wire:click.prevent="search" class="btn btn-sm btn-danger">APPLY</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Search End-->

    <!-- Grid -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <table class="table table-md table-hover nissanTB">
                        <thead>
                            <th scope="col">#</th>
                            <th scope="col">Doc Type
                                <span wire:click="sortBy('b.doc_type_no')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'b.doc_type_no' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'b.doc_type_no' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Document Number
                                <span wire:click="sortBy('a.ref_doc_no')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'a.ref_doc_no' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'a.ref_doc_no' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Requester
                                <span wire:click="sortBy('d.requestor')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'd.requestor' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'd.requestor' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Requested For
                                <span wire:click="sortBy('d.requested_for')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'd.requested_for' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'd.requested_for' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Requested For Company
                                <span wire:click="sortBy('d.company')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'd.company' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'd.company' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Status
                                <span wire:click="sortBy('c.status_no')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'c.status_no' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'c.status_no' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Approval Type
                                <span wire:click="sortBy('a.approval_type')" class="float-right text-sm"
                                    style="cursor: pointer;">
                                    <i
                                        class="fa fa-arrow-up {{ $sortBy === 'a.approval_type' && $sortDirection === 'asc' ? '' : 'text-dark'}}"></i>
                                    <i
                                        class="fa fa-arrow-down {{ $sortBy === 'a.approval_type' && $sortDirection === 'desc' ? '' : 'text-dark'}}"></i>
                                </span>
                            </th>
                            <th scope="col">Submitted Date
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
                            @foreach ($workflow_list as $row)
                            <tr>
                                <td scope="col">{{ $loop->iteration + $workflow_list->firstitem()-1 }}</td>                                
                                <td scope="col">{{ $row->doctype }} </td>
                                <td scope="col">{{ $row->ref_doc_no }} </td>
                                <td scope="col">{{ $row->requestor }} </td>
                                <td scope="col">{{ $row->requested_for }} </td>
                                <td scope="col">{{ $row->company }} </td>
                                <td scope="col">{{ $row->status }} </td>
                                <td scope="col">{{ $row->approval_type }} </td>
                                <td scope="col" class="text-center">{{ \Carbon\Carbon::parse($row->create_on)->format('d-M-Y') }} </td>
                                <td>
                                    <center>
                                        <a href="" wire:click.prevent="approvePR('{{ $row->ref_doc_no }}')">
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
                    {{ $workflow_list->links() }}
                </div>
            </div>
        </div>
    </div>
    <!-- Grid End-->

</div>
