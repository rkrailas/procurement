<div>
    <section style="padding-top: 60px;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            Dropzone File Upload
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('dropzone.store') }}" enctype="multipart/form-data" class="dropzone" id="dropzonewidget">
                            @csrf
                                <div>
                                    <h1 class="text-center">Upload Files By Click In Box</h1>
                                </div>
                                <div class="dz-default dz-message"><span>Drop files here to upload</span></div>

                                
                            </form>
                            <input hidden type="text" name="documents" id="documents">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <button class="btn btn-primary" wire:click="myRandom">Random</button>
                </div>
            </div>
        </div>
    </section>
</div>

@push('js')

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


@endpush
