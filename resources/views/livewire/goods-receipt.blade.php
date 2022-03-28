<div>
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                Inventory Management > Goods Receipts (GR)
            </div>
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0" style="color: #C3002F;">Goods Receipt for Purchase Order/Blanket Purchase Order (Inventory/Non Inventory)</h1>
                </div>

            </div>
        </div>
    </div>

    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div clss="col-12">
                                <h4>Purchase Order No. <small class="text-red">*</small></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div clss="col-6">
                                <input style="height: 100%;" type="text" class="form-control form-control-sm" wire:model="purchase_order" maxlength="20">
                            </div>
                            <div class="col-2">
                                <button type="submit" class="btn-block btn btn-danger" wire:click="previewItem">Preview Item</button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12" style="background-color: #EAEAEA; border-radius: 5px; align-items: center;">
                                <b style="margin-left: 5px;"> Header Data</b>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table class="table table-hover">
                                    <thead>
                                        <tr style="background-color: #C3002F;" class="text-white">
                                            <th>Company</th>
                                            <th>Site</th>
                                            <th>PO No.</th>
                                            <th>Order Type</th>
                                            <th>Supplier</th>
                                            <th>Currency</th>
                                            <th>Cost Center</th>
                                            <th>Requester</th>
                                            <th>Requested For</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if(!empty($poHeader) )

                                        @foreach($poHeader as $header)
                                        <tr>
                                            <td>{{ $header->company }}</td>
                                            <td>{{ $header->stie }}</td>
                                            <td>{{ $header->pono }}</td>
                                            <td>{{ $header->order_type }}</td>
                                            <td>{{ $header->supplier }}</td>
                                            <td>{{ $header->po_currency }}</td>
                                            <td>{{ $header->cost_center }}</td>
                                            <td>{{ $header->requester }}</td>
                                            <td>{{ $header->requested_for }}</td>

                                        </tr>
                                        @endforeach

                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12" style="background-color: #EAEAEA; border-radius: 5px; align-items: center;">
                                <b style="margin-left: 5px;"> Item Data</b>
                            </div>
                        </div>

                        <div class="row">
                            <div clss="col-12">
                                <h4>Line No.</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div clss="m-l-1 col-6">
                                <input style="height: 100%;" type="email" class="form-control form-control-sm" id="email">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <table class="table table-hover">
                                    <thead>
                                        <tr style="background-color: #C3002F;" class="text-white">
                                            <th>GR Item*</th>
                                            <th>Line No.</th>
                                            <th>Part No.</th>
                                            <th>Description</th>
                                            <th>PR Qty.</th>

                                            <th>Purchase UoM</th>
                                            <th>Cost Center</th>

                                            <th>PO Unit Price</th>

                                            <th>Total Price of Item</th>
                                            <th>Currency</th>
                                            <th>Non stock control</th>

                                            <th>PO Item Status</th>
                                            <th>GL Account</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        @if(!empty($poItems) )
                                        @foreach($poItems as $item)
                                        <tr style="background-color: #C3002F;" class="text-white">
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->line_no }}</td>
                                            <td>{{ $item->partno }}</td>
                                            <td>{{ $item->description }}</td>
                                            <td>{{ $item->qty }}</td>

                                            <td>null</td>
                                            <td>null</td>

                                            <td>{{ $item->unit_price }}</td>

                                            <td>null</td>
                                            <td>null</td>
                                            <td>null</td>

         
                                            <td>{{ $item->status }}</td>
                                            <td>{{ $item->gl_account }}</td>
                                           
                                        </tr>
                                        @endforeach
                                        @endif

                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>