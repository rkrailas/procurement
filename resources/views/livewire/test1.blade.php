<div>
    <div class="row">
        <div class="col-6">
            <label>Requestor</label>
            <x-select2-multiple id="requestor-select2" wire:model.defer="requestor">
                @foreach($requestor_dd as $row)
                <option value='{{ $row->id }}'>
                    {{ $row->fullname }}
                </option>
                @endforeach
            </x-select2-multiple>
        </div>
        <div class="col-6">
            <label>Requestor for</label>
            <x-select2-multiple id="requestorfor-select2" wire:model.defer="requestorfor">
                @foreach($requestorfor_dd as $row)
                <option value='{{ $row->id }}'>
                    {{ $row->fullname }}
                </option>
                @endforeach
            </x-select2-multiple>
        </div>
    </div>

    <div class="row">
        <div class="col-6">
            <div wire:ignore>
                <x-select2-page-multiple id="requestor2-select2" url="/dataforselect2" wire:model.defer="requestor2">
                </x-select2-multiple>
            </div>
        </div>
        <div class="col-6">
            <button type="button" class="btn btn-primary" wire:click="test">Test</button>
        </div>
    </div>


</div>
