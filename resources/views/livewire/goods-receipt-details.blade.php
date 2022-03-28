<div>
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                Inventory Management > Goods Receipts (GR)
            </div>
            <div class="row mb-2">
                <div class="col-sm-12">
                    <h1 class="m-0" style="color: #C3002F;">Goods Receipt for Purchase Order/Blanket Purchase Order</h1>
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
                            <div class="col-12">
                                <h5 class="card-title"><b>Document Header Data</b></h5>
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-3">
                                <label for="poNumber">PO/BPO Document Number</label>
                                <input type="text" class="form-control" name="poNumber" id="poNumber" readonly wire:model="poNumber">
                            </div>
                            <div class="col-3">
                                <label for="poReleasedDate">PO Released Date</label>
                                <input type="text" class="form-control" name="poReleasedDate" id="poReleasedDate" readonly wire:model="poReleasedDate">
                            </div>

                            <div class="col-3">
                                <label for="orderUntil">Order Valid Until</label>
                                <input type="text" class="form-control" name="orderUntil" id="orderUntil" readonly wire:model="orderUntil">
                            </div>

                            <div class="col-3">
                                <label for="requestor">Requestor</label>
                                <input type="text" class="form-control" name="requestor" id="requestor" readonly wire:model="requestor">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <label for="currency">Currency</label>
                                <input type="text" class="form-control" id="currency" readonly>
                            </div>
                            <div class="col-3">
                                <label for="orderTypeAndDes">Order Type and Description</label>
                                <input type="text" class="form-control" id="orderTypeAndDes" readonly>
                            </div>

                            <div class="col-3">
                                <label for="siteName">Site and Name</label>
                                <input type="text" class="form-control" id="siteName" readonly>
                            </div>

                            <div class="col-3">
                                <label for="supplierName">Supplier ID and Name </label>
                                <input type="text" class="form-control" id="supplierName" readonly>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <label for="smeSupplier">SME Supplier</label>
                                <input type="text" class="form-control" name="smeSupplier" id="smeSupplier" readonly wire:model="smeSupplier">
                            </div>
                            <div class="col-3">
                                <label for="supplierBranch">Supplier Branch</label>
                                <input type="text" class="form-control" name="supplierBranch" id="supplierBranch" maxlength="50" wire:model="supplierBranch">
                            </div>

                            <div class="col-3">
                                <label for="invoiceName">Invoice Number <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" name="invoiceName" id="invoiceName" maxlength="20" wire:model="invoiceName">
                            </div>

                            <div class="col-3">
                                <label for="invoiceDate">Invoice Date <small class="text-danger">*</small></label>
                                <input type="date" class="form-control" name="invoiceDate" id="invoiceDate" wire:model="invoiceDate">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <label for="actualDate">Actual Delivery Date <small class="text-danger">*</small></label>
                                <input type="date" class="form-control" name="actualDate" id="actualDate" readonly wire:model="actualDate">
                            </div>
                            <div class="col-3">
                                <label for="billDate">Bill of Lading Date <small class="text-danger">*</small></label>
                                <input type="date" class="form-control" name="billDate" id="billDate" maxlength="50" wire:model="billDate">
                            </div>

                            <div class="col-3">
                                <label for="receiver">Receiver <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" name="receiver" id="receiver" maxlength="50" wire:model="receiver">
                            </div>


                        </div>


                        <div class="row">
                            <div class="col-6">
                                <label for="remark">Remark </label>
                                <input type="text" class="form-control" name="remark" id="remark" wire:model="remark">
                            </div>

                            <div class="col-6">
                                <label for="file">Add Attachment </label>
                                <input type="file" class="form-control" name="file" id="file" required wire:model="file">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-3">
                                <label for="departmentCode">Department Code (Cost Center) </label>
                                <input type="text" class="form-control" name="departmentCode" id="departmentCode" maxlength="10" readonly wire:model="departmentCode">
                            </div>


                            <div class="col-3">
                                <label for="file">Invoice Type <small class="text-danger">*</small></label>
                                <select class="form-control" name="invoiceType" id="invoiceType" required wire:model="invoiceType">

                                </select>

                            </div>

                            <div class="col-3">
                                <label for="taxCode">Tax Code</label>
                                <input type="text" class="form-control" name="taxCode" id="taxCode" maxlength="2" wire:model="taxCode">
                            </div>

                            <div class="col-3">
                                <label for="amountExcludeVat">Amount exclude VAT <small class="text-danger">*</small></label>
                                <input type="text" class="form-control" name="amountExcludeVat" id="amountExcludeVat" maxlength="12" required wire:model="amountExcludeVat">
                            </div>

                        </div>

                        <div class="row">
                            <div class="col-3">
                                <label for="vatAmount">VAT Amount</label>
                                <input type="text" class="form-control" name="vatAmount" id="vatAmount" maxlength="12" readonly wire:model="vatAmount">
                            </div>


                            <div class="col-3">
                                <label for="totalAmount">Total Amount<small class="text-danger">*</small></label>
                                <select class="form-control" name="totalAmount" id="totalAmount" readonly wire:model="totalAmount">

                                </select>

                            </div>



                        </div>

                    </div>
                </div>

            </div>

        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">

                    <div class="card-body">
                        <div class="row">
                            <div class="col-2" style="text-align: center; align-content: center;">
                                <h5><b>Document Item Data</b></h5>
                            </div>
                            <div class="col-1 text-left" style="display: flex;justify-content: center;align-items: center;">
                                <button class="btn btn-danger btn-block" style="border-radius: 10px;">Receive</button>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-12" style="overflow-x:auto;">
                                <table class="table table-hover">
                                    <thead>
                                        <tr style="background-color: #C3002F;overflow:hidden; white-space:nowrap" class="text-white">
                                            <th>Line No.</th>
                                            <th>Part No</th>
                                            <th>Description</th>
                                            <th>PO Qty.</th>
                                            <th>PO Unit Price</th>
                                            <th>Purchase UoM</th>
                                            <th>Actual Unit Price</th>
                                            <th>Quantity *</th>
                                            <th>Amount</th>
                                            <th>PO/BPO Remaining Quantity</th>
                                            <th>Location No.</th>
                                            <th>Lot/Batch No.</th>
                                            <th>Manufac. Date</th>
                                            <th>GL Account</th>
                                            <th>Internal Order</th>
                                        </tr>
                                    </thead>
                                    <tbody>


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