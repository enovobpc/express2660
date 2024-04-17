{{-- Status --}}
@if (session('status'))
<div class="notice notice-success">
    <strong><i class="fas fa-check-circle"></i></strong> {{ session('status') }}
</div>
@endif


{{-- Success --}}
@if(session('success'))
<div class="notice notice-success">
    <strong><i class="fas fa-check-circle"></i></strong> {{ session('success') }}
</div>
@endif

{{-- Error --}}
@if(session('error'))
<div class="notice notice-danger">
    <strong><i class="fas fa-exclamation-circle"></i></strong> {{ session('error') }}
</div>
@endif

{{-- Warning --}}
@if(session('warning'))
<div class="notice notice-warning">
    <strong><i class="fas fa-exclamation-triangle"></i></strong> {{ session('warning') }}
</div>
@endif

{{-- Info --}}
@if(session('info'))
<div class="notice notice-info">
    <strong><i class="fas fa-info-circle"></i></strong> {{ session('info') }}
</div>
@endif

{{-- Validator --}}
@if(session('errors'))
<div class="notice notice-danger">
    <strong><i class="fas fa-exclamation-circle"></i></strong>
    @foreach($errors->all() as $error)
    {{ $error }} <br>
    @endforeach
</div>
@endif