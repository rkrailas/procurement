@push('js')
<script>
  window.addEventListener('popup-success',function(e) {
        Swal.fire({
            title:  e.detail.title,
            text: e.detail.text,
            icon: 'success',
        });
    });
</script>
@endpush


{{-- ็How to use
===In Controller===
if($data){
    $this->dispatchBrowserEvent('popup-alert', [
        'title' => 'บันทึกสำเร็จ',
    ]);
}else{
    //Some Process
} --}}