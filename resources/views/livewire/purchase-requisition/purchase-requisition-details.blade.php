<div>
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <!-- ปุ่มซ่อนเมนู -->
                    <div class="float-left d-none d-sm-inline">
                        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                    </div>
                    <h1 class="m-0 text-dark">Purchase Requisition Details</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">Purchase Requisition</li>
                        <li class="breadcrumb-item active">Purchase Requisition Details</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between mb-2" style="font-size: 20px;">
                    <div>
                        Order Type : 
                        <span style="color: blue">{{ $headerData['ordertype'] }}</span>
                    </div>
                    <div>
                        Status : 
                        <span style="color: blue">{{ $headerData['status'] }}</span>
                    </div>
                </div>
            </div> 
        </div>

        {{-- Header --}}
        <div class="row">
            <div class="col-md-3">
                <label for="prno">Requestor</label>
                <input class="form-control form-control-sm" type="text" id="requestor" maxlength="100" readonly wire:model.defer="headerData.requestor">
            </div>
            <div class="col-md-3">
                <label for="ordertype">Requested For</label>
                <select class="form-control form-control-sm" id="requested_for" wire:model="headerData.requested_for">
                    <option value="">--- Please Select ---</option>
                    @foreach($buyer_dd as $row)
                    <option value="{{ $row->id }}">
                        {{ $row->fullname }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="ordertype">Delivery Address</label>
                <select class="form-control form-control-sm" id="delivery_address" wire:model.defer="headerData.delivery_address">
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
                    <x-datepicker wire:model.defer="request_date" id="request_date"
                        :error="'date'" required/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <label for="prno">Company</label>
                <input class="form-control form-control-sm" type="text" id="company" maxlength="100" readonly wire:model="headerData.company">
            </div>
            <div class="col-md-3">
                <label for="prno">Site</label>
                <input class="form-control form-control-sm" type="text" id="site" maxlength="100" readonly wire:model="headerData.site">
            </div>
            <div class="col-md-3">
                <label for="prno">Function</label>
                <input class="form-control form-control-sm" type="text" id="functions" maxlength="100" readonly wire:model.defer="headerData.functions">
            </div>
            <div class="col-md-3">
                <label for="prno">Department</label>
                <input class="form-control form-control-sm" type="text" id="department" maxlength="100" readonly wire:model.defer="headerData.department">
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <label for="prno">Division</label>
                <input class="form-control form-control-sm" type="text" id="division" maxlength="100" readonly wire:model.defer="headerData.division">
            </div>
            <div class="col-md-3">
                <label for="prno">Section</label>
                <input class="form-control form-control-sm" type="text" id="section" maxlength="100" readonly wire:model.defer="headerData.section">
            </div>
            <div class="col-md-3">
                <label for="prno">Email</label>
                <input class="form-control form-control-sm" type="text" id="email" maxlength="100" readonly wire:model.defer="headerData.email">
            </div>
            <div class="col-md-3">
                <label for="prno">Phone</label>
                <input class="form-control form-control-sm" type="text" id="phone" maxlength="100" readonly wire:model.defer="headerData.phone">
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <label for="ordertype">Buyer</label>
                <select class="form-control form-control-sm" id="buyer" wire:model.defer="headerData.buyer">
                    <option value="">--- Please Select ---</option>
                    @foreach($buyer_dd as $row)
                    <option value="{{ $row->id }}">
                        {{ $row->fullname }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label for="ordertype">Cost Center (Department Code)</label>
                <select class="form-control form-control-sm" id="requested_for" wire:model.defer="headerData.requested_for">
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
                <input class="form-control form-control-sm" type="text" id="edecision" maxlength="100" wire:model.defer="headerData.edecision">
            </div>
        </div>

        {{-- Blanket Request --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <label style="color: blue">Blanket Request</label>
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
                    <x-datepicker wire:model.defer="headerData.valid_until" id="valid_until"
                        :error="'date'" required/>
                </div>
            </div>
            <div class="col-md-3">
                <label for="date_to_notify">Days to Notify before BPO Expiry</label>
                <input class="form-control form-control-sm" type="number" step="1" id="date_to_notify" wire:model.defer="headerData.date_to_notify">
            </div>
            <div class="col-md-6">
                <label>Notify when remaining value is below</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" wire:model.defer="headerData.notify_below_10">
                        <label class="form-check-label">10%</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" wire:model.defer="headerData.notify_below_25">
                        <label class="form-check-label">25%</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" wire:model.defer="headerData.notify_below_35">
                        <label class="form-check-label">35%</label>
                    </div>
                </div>
                
            </div>
        </div>

        {{-- Line items --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <label style="color: blue">Line items</label>
                <hr width="100%">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-sm">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">First</th>
                        <th scope="col">Last</th>
                        <th scope="col">Handle</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row">1</th>
                        <td>Mark</td>
                        <td>Otto</td>
                        <td>@mdo</td>
                      </tr>
                      <tr>
                        <th scope="row">2</th>
                        <td>Jacob</td>
                        <td>Thornton</td>
                        <td>@fat</td>
                      </tr>
                      <tr>
                        <th scope="row">3</th>
                        <td colspan="2">Larry the Bird</td>
                        <td>@twitter</td>
                      </tr>
                    </tbody>
                  </table>
            </div>
        </div>


        {{-- Actions --}}
        <div class="row mt-3">
            <div class="col-md-12">
                <label style="color: blue">Actions</label>
                <hr width="100%">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between">
                    <div>
                        <button wire:click.prevent="" class="btn btn-sm btn-success">Release for Sourcing</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-success">Release for PO</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-info">Print</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-secondary">Cancel</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-danger">Delete</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-warning">Re-Open</button>
                        <button wire:click.prevent="" class="btn btn-sm btn-success">Converet to PO</button>
                    </div>                
                    <button wire:click.prevent="" class="btn btn-sm btn-primary">Save</button>
                </div>
                
            </div>
        </div>
    </div>



</div>
