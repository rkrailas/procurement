<div>
    <section style="padding-top: 60px;">
        <div class="container">
            <div class="row" id="test555">
                <div class="col">
                    <label for="prno">Requestor</label>
                    <input class="form-control form-control-sm" type="text" id="requestor_name">
                </div>
                <div class="col">
                    <label class="">Request Date</label>
                    <div class="input-group mb-1">
                        <div class="input-group-prepend">
                            <span class="input-group-text">
                                <i class="fas fa-calendar"></i>
                            </span>
                        </div>
                        <x-datepicker wire:model.defer="prHeader.request_date" id="request_date"
                            :error="'date'"/>
                    </div>
                </div>
                <div class="col">
                    <label>Delivery Address <span style="color: red">*</span></label>
                    <select class="form-control form-control-sm" id="delivery_address">
                        <option value="">--- Please Select ---</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Dropzone File Upload
                        </div>
                        <div class="card-body">
                            <form action="{{ route('dropzone.store') }}" class="dropzone"></form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <button class="btn btn-primary" wire:click="myRandom">Random</button>

                    <a href="PRForm/NM22000030" target="_blank">
                        <button class="btn btn-primary">Print</button>
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>

{{-- @push('js')
<script>
    // var segments = location.href.split('/');
    // var action = segments[3];
    // if (action == 'test1') {
        // var acceptedFileTypes = ".xlsx";
        var fileList = new Array;
        var i = 0;
        var callForDzReset = false;
        $('#dropzonewidget').dropzone({
            url: "dropzoneStore",
            addRemoveLinks: true,
            maxFiles: 4,
            // acceptedFileTypes: ".xlsx",
            maxFilesize: ,
            init: function () {
                this.on("success", function (file, serverFileName) {
                    file.serverFn = serverFileName;
                    fileList[i] = {
                        "serverFileName": serverFileName,
                        "fileName": file.name,
                        "fileId": i
                    };
                    i++;
                });
            }
        });
    // }
</script>
@endpush --}}

@push('js')
<script>
        var CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

        Dropzone.autoDiscover = false;
        var myDropzone = new Dropzone(".dropzone", {
            maxFilesize: 2, //2 mb
            acceptedFiles: ".jpeg,.jpg,.png,.pdf"
        });

        myDropzone.on("sending", function(file,xhr,formData){
            formData.append("_token",CSRF_TOKEN);
        });
        myDropzone.on("success", function(file,response){
            if(response.success == 0){
                alert(response.error);
            }
        });


        $("#test555 :input").attr("disabled", true);

        window.addEventListener('disable-prdetail', event => {
            alert('3333333');
            //$("#PR_Detail :input").attr("disabled", true)
        });
</script>

@endpush
