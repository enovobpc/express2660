<div class="col-lg-2 col-md-3 col-sm-3">
    <div class="form-group form-group-sm">
        <div class="input-group">
            <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
            <input name="start" value="{{ Request::get('startDate') }}" class="form-control datepicker" placeholder="De:"> 
        </div>
    </div>
</div>
<div class="col-lg-2 col-md-3 col-sm-3">
    <div class="input-group form-group-sm">
        <span class="input-group-addon"><i class="fas fa-calendar-alt"></i></span>
        <input name="end" value="{{ Request::get('endDate') }}" class="form-control datepicker" placeholder="Até:"> 
    </div>
</div>
<div class="col-md-2 col-sm-3">
    <button type="button" class="btn btn-sm btn-default btn-block active-analytics-filter">
        <i class="fas fa-filter"></i> Filtrar
    </button>
</div>