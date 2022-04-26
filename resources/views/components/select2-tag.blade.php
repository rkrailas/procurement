@props(['id'])

{{-- {{ $attributes }} --}}
<div wire:ignore>
    <select id="{{ $id }}" class="form-control"> 
        <option value="">Not Specified</option>
        {{$slot}}
    </select>
</div>

@once
    @push('styles')
        <!-- Select2 -->
        <link rel="stylesheet" href="{{ asset('backend/plugins/select2/css/select2.min.css') }}">
        <link rel="stylesheet" href="{{ asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
    @endpush
    @push('js')
        <!-- Select2 -->
        <script src="{{ asset('backend/plugins/select2/js/select2.full.min.js') }}"></script>
    @endpush
@endonce

@push('js')
    <script>        
        $(function(){
            $('#{{ $id }}').select2({
                theme: 'bootstrap4',
                tags: true
            }).on('change',function(){         
                @this.set('{{ $attributes["wire:model.defer"] }}', $(this).val());
            });
        })
    </script>
@endpush