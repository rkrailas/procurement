@props(['id', 'error', 'readonly'=>''])

<input {{ $attributes }} type="text" class="form-control form-control-sm datetimepicker-input @error($error) is-invalid @enderror" id="{{ $id }}"
    {{ $readonly == 'true' ? 'readonly' : '' }}
    data-toggle="datetimepicker" data-target="#{{ $id }}"
    onchange="this.dispatchEvent(new InputEvent('input'))"
    />

@push('js')
<script type="text/javascript">
    $('#{{ $id }}').datetimepicker({
        format: 'YYYY-MM-DD'
    });
</script>
@endpush