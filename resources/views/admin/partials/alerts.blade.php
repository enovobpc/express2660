<script>
    $(document).ready(function(){
        @if (Session::has('success'))
            $.bootstrapGrowl("<i class='fas fa-check'></i> {{ Session::get('success') }}&nbsp;&nbsp;", {type: 'success', align: 'center', width: 'auto', delay: 8000});
        @endif

        @if (Session::has('error'))
            $.bootstrapGrowl("<i class='fas fa-exclamation-circle'></i> {{ Session::get('error') }}&nbsp;&nbsp;", {type: 'error', align: 'center', width: 'auto', delay: 8000});
        @endif

        @if (Session::has('warning'))
            $.bootstrapGrowl("<i class='fas fa-exclamation-triangle'></i> {{ Session::get('warning') }}&nbsp;&nbsp;", {type: 'warning', align: 'center', width: 'auto', delay: 8000});
        @endif

        @if (Session::has('info'))
            $.bootstrapGrowl("<i class='fas fa-info-circle'></i> {{ Session::get('info') }}&nbsp;&nbsp;", {type: 'info', align: 'center', width: 'auto', delay: 8000});
        @endif
    })
</script>
