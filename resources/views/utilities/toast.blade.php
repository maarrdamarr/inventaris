@if(session('success') || session('error') || session('warning') || session('info'))
<script>
  $(function(){
    const opt = {
      toast: true,
      position: 'top-end',
      showConfirmButton: false,
      timer: 2500,
      timerProgressBar: true
    };
    @if(session('success'))
      Swal.fire({...opt, icon:'success', title: @json(session('success'))});
    @endif
    @if(session('error'))
      Swal.fire({...opt, icon:'error', title: @json(session('error'))});
    @endif
    @if(session('warning'))
      Swal.fire({...opt, icon:'warning', title: @json(session('warning'))});
    @endif
    @if(session('info'))
      Swal.fire({...opt, icon:'info', title: @json(session('info'))});
    @endif
  });
}</script>
@endif

