<div>
   <div class="container">
       <div class="row">
           <div class="col">
               <div wire:ignore class="form-group">
                    <label>Select Team Members</label>
                    {{-- <select wire:model="testSelect2" class="select2" id="select2" multiple style="width: 500px;">
                        @foreach ($choice_dd as $row)
                            <option value="{{ $row->choice }}">{{ $row->choice }}</option>
                        @endforeach
                    </select> --}}

                    <select wire:model="testSelect2" class="select2" id="select2" multiple style="width: 500px;" disabled>
                        @foreach ($choice_dd as $row)
                            <option value="{{ $row->choice }}">{{ $row->choice }}</option>
                        @endforeach
                    </select>
               </div>
           </div>
       </div>
       <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary" wire:click.prevent="InsertVar">Insert Data</button>
                <button class="btn btn-primary" wire:click.prevent="getVar">Get Data</button>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form autocomplete="off" enctype="multipart/form-data" wire:submit.prevent="addAttachment">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-9">
                            <div class="custom-file">
                                <input wire:model="attachment_file" type="file" class="custom-file-input" id="customFile" multiple>
                                    @error('attachment_file.*')
                                    <div class="alert alert-danger" role="alert">
                                        The attachment file must not be greater than 5120 kilobytes
                                    </div>
                                    @enderror
                                {{-- <label class="custom-file-label" for="customFile">
                                    @if ($attachment_file)
                                    @foreach ($attachment_file as $file)
                                    {{ $file->getClientOriginalName() }} ({{ $this->formatSizeUnits($file->getSize()) }}) <br/>
                                    @endforeach
                                    @else
                                    Browse Files
                                    @endif
                                </label> --}}
                                <label class="custom-file-label" for="customFile">Browse Files</label>

                                @if ($attachment_file)
                                    @foreach ($attachment_file as $k => $file)
                                    {{ $file->getClientOriginalName() }} ({{ $this->formatSizeUnits($file->getSize()) }}) 
                                    <a href="" wire:click.prevent="confirmDelete('{{ $k }}')">
                                        <i class="fas fa-times text-center mr-1" style="color: red"></i>
                                    </a>
                                    @if ($file->getSize() > $maxSize)
                                    <span class="text-danger">File size is too large.</span>
                                    @endif
                                    <br/>
                                    @endforeach
                                @endif
                                
                            </div>
                        </div>
                        <div class="col-md-3 text-left">
                            <button type="submit" class="btn btn-danger"><i class="fas fa-cloud-upload-alt mr-1"></i>Upload</button>
                            <span style="vertical-align:bottom; color:red">max file size 5 mb</span> 
                        </div>
                    </div>
                </form>
            </div>
        </div>
   </div>
</div>

@once
    @push('styles')
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">

        <!-- Customize hight = form-control-sm -->
        {{-- <style>
            .select2-container .select2-selection--multiple {
                height: auto !important;
            }
        </style> --}}
    @endpush
@endonce

@once
    @push('js')
        <!-- Select2 -->
        <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>
    @endpush
@endonce

@push('js')
    <script>        
        $(function(){
            $('.select2').select2({
                theme: 'bootstrap4',
            }).on('change', function () {
                @this.set('testSelect2', $(this).val());
            });
        });

        window.addEventListener('bindToSelect2', event => {
            // $( document ).ready(function() {
            //     alert( "ready!" );
            // });
            $(event.detail.selectName).html(" ");
            $(event.detail.selectName).append(event.detail.newOption);
        });
    </script>

@endpush
