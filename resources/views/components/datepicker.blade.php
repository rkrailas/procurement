@props(['id', 'error', 'readonly'=>''])

<input {{ $attributes }} type="text" class="form-control form-control-sm datetimepicker-input @error($error) is-invalid @enderror" id="{{ $id }}"
    {{ $readonly == 'true' ? 'readonly' : '' }}
    data-toggle="datetimepicker" data-target="#{{ $id }}"
    onchange="this.dispatchEvent(new InputEvent('input'))"
    autocomplete="off"
    />

@push('js')
<script type="text/javascript">
    $('#{{ $id }}').datetimepicker({
        format: 'YYYY-MM-DD',
        //format: 'DD-MMM-YYYY',
        // icons: {
        //     today: 'todayText',
        //     up: 'glyphicon glyphicon-chevron-up',
        //     up: "fa fa-arrow-up",
        //     down: "fa fa-arrow-down",
        //     previous: " fa fa-arrow-left",
        //     next: "fa fa-arrow-right"
        //     },
        // showTodayButton: true,
        // ignoreReadonly: true,
        // allowInputToggle: true,
        // useCurrent: true,
    });
</script>
@endpush