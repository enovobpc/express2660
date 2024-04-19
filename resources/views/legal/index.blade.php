@section('title')
Avisos Legais | 
@stop

@section('metatags')
<meta name="robots" content="noindex,nofollow">
@stop

@section('content')
<section class="page-header" style="margin-top: 18vh;">
    <div class="container">
        <div class="row">
            <div class="col-sm-12">
                <h1>Avisos Legais</h1>
            </div>
        </div>
    </div>
</section>
<section>
    <div class="container">
        <div class="row">
            <div class="col-sm-3">
                @include('legal.partials.sidebar')
            </div>
            <div class="col-sm-9">
               @include($include)
               <div class="spacer-50"></div>
            </div>
        </div>
    </div>
</section>
@stop

@section('scripts')
<script>
$('#{{$slug}}').addClass('active')
</script>
@stop
