@section('title')
    Google Analytics
@stop

@section('content-header')
    <i class="fab fa-google"></i> | Google Analytics
@stop

@section('content')
<div class="row">
    <div class="col-sm-8">
        @include('admin.website.visits.partials.visits_graph')
    </div>
    <div class="col-md-4">
        @include('admin.website.visits.partials.top_pages')
    </div>
</div>
@include('admin.website.visits.partials.panel')
@endsection

@section('scripts')
    {{ Html::script('vendor/chart.js/dist/Chart.min.js') }}
<script>
    $(document).ready(function () {
        
        $('.analytics-menu .active a').trigger('click');
        
        var analyticsChartData = {
            labels: [{!! @$analyticsGraphData['labels'] !!}],
            datasets: [{
                label: 'Visitas',
                data: [{{ @$analyticsGraphData['visits'] }}],
                backgroundColor: [{!! $analyticsGraphData['background'] !!}],
                borderWidth: 1
            }]
        }    

        var analyticsChartOptions = {
            scales: {
                yAxes: [{
                    ticks: { beginAtZero:true}
                }]
            },
            legend: { display: false }
        }

        var analyticsChart = $("#analyticsChart");
        var analyticsChart = new Chart(analyticsChart, {
            type: 'bar',
            data: analyticsChartData,
            options: analyticsChartOptions
        });
    });
    
    
    /**
     * Change graph filter
     */
    $(document).on('change', 'select[name=filter-analytics]', function(){
    
        var url = "{{ route('admin.website.visits.index') }}?tab={{ $tab }}";
        var start = $(this).val();

        if (start != 'custom') {
            $('#filter-analytics-search').hide()
            url = Url.updateParameter(url, 'filter', start);
            url = Url.updateParameter(url, 'start', start);
            window.location = url;
        } else {
            $('#filter-analytics-search').show()
            $('input[name=start],input[name=end]').val('');
        }
    });
    
    /*
     * Graph custom filter
     */
    $(document).on('click', '#submit-filter-analytics', function(e){
        e.preventDefault();
        var url = "{{ route('admin.website.visits.index') }}?tab={{ $tab }}";
        var filter = $('select[name=filter-analytics]').val();
        var start = $('input[name=start]').val();
        url = Url.updateParameter(url, 'filter', filter);
        url = Url.updateParameter(url, 'start', start);
        if ("{{ $tab }}" == "monthly") {
            var end = $('input[name=end]').val();
                url = Url.updateParameter(url, 'end', end);
        }
        window.location = url;
    })

    /**
     * Datatable for top pages
     */
    $('#datatable-top-pages').DataTable({
        dom: "<'row'<'col-sm-12'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12'p>>",
        pageLength: 10,
        processing: false,
        serverSide: false,
        stateSave: false,
        oLanguage: {
            "sProcessing": "A processar...",
            "sLoadingRecords": "A carregar...",
            "sLengthMenu": "Ver _MENU_",
            "sZeroRecords": "Não foram encontrados resultados",
            "sEmptyTable": "Não existem registos para apresentar.",
            "sSearch": "<i class='fas fa-search text-muted'></i>",
            "oPaginate": {
                "sFirst": "<i class='fas fa-angle-double-left'></i>",
                "sPrevious": "<i class='fas fa-angle-left'></i>",
                "sNext": "<i class='fas fa-angle-right'></i>",
                "sLast": "<i class='fas fa-angle-double-right'></i>",
            }
        },
        data: {!! $topPages !!},
        order: [[2, "desc"]],
        columns: [
            { data: 'pos', orderable: false, class:'text-center'},
            { data: 'url'},
            { data: 'pageViews', class:'text-right'},
        ],
    });
    
    /**
     * Active filter 
     */
    $(document).on('click', '.active-analytics-filter', function(e){
        e.preventDefault();
        loadAnalyticsContent($('.analytics-menu .active a').attr('href'))
    });
    
    $(document).on('click', '.load-analytics', function(e){
        e.preventDefault();
        
        var $this = $(this);
        var url = $this.attr('href');
        
        $('.load-analytics').parent().removeClass('active');
        $this.parent().addClass('active');
        
        loadAnalyticsContent(url);
    })
    
    
    function loadAnalyticsContent(url) {
        
        var start = $('.analytics-result [name=start]').val();
        var end   = $('.analytics-result [name=end]').val();
        
        if(start > end) {
            tmp = start;
            start = end;
            end = tmp;
        }
        
        $('#datatable-analytics-result tbody').html('<tr><td><i class="fas fa-spin fa-circle-notch"></i> A carregar...</td></tr>')
        
        $('#datatable-analytics-result_paginate').remove();
        
        $.post(url, {startDate: start, endDate: end},function (data) {
            
            $('.analytics-result').html(data.html)
            
            $('.datepicker').datepicker(Init.datepicker());
            
            $('#datatable-analytics-result').DataTable({
                dom: "<'row'<'col-sm-12'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-12'p>>",
                pageLength: 10,
                processing: false,
                serverSide: false,
                stateSave: false,
                order: [[1, "desc"]],
                data: data.data,
            });
        }).fail(function(){
            $('#datatable-analytics-result tbody').html('<tr><td class="text-red"><i class="fas fa-exclamation-circle"></i> Erro ao obter informação do Google.</td></tr>')
        });
    }
</script>
@stop