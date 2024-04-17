<div class="row row-5">
    <div class="col-lg-6 col-md-4 hidden-xs hidden-sm">
        <h4 class="m-t-5 m-b-15">Páginas mais vistas</h4>
    </div>
    @include('admin.website.visits.partials.date_filter')
</div>

<table class="table" id="datatable-analytics-result">
    <thead>
        <tr>
            <th>Página</th>
            <th class="text-center w-1">Visitas</th>
            <th class="text-center w-1">Sessões</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3"><i class="fas fa-spin fa-circle-notch"></i> A carregar...</td>
        </tr>
    </tbody>
</table>