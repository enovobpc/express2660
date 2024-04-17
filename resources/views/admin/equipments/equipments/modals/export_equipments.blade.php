{{ Form::open(['route' => ['admin.equipments.filter.export.file', $group], 'class' => 'form-export']) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
            <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body">
    <div class="row row-5">
        <div class="col-sm-12">
            <div class="form-group is-required">
                {{ Form::label('date_start', 'Data Ínicio', ['class' => 'control-label']) }}
            <div class="input-group">
                {{ Form::text('date_start', $dateStart, ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 0 0 5px;']) }}
                <div class="input-group-addon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
            </div>
            <div class="form-group is-required">
                {{ Form::label('date_end', 'Data Fim', ['class' => 'control-label', 'style' => 'padding: 10px 0 0 0;']) }}
            <div class="input-group">
                {{ Form::text('date_end', $dateEnd, ['class' => 'form-control datepicker', 'required', 'style' => 'padding: 0 0 0 5px;']) }}
                    <div class="input-group-addon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer" style="padding: 10px 0 0 0;">
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    <button type="submit" class="btn btn-primary">Exportar</button>
</div>
{{ Form::close() }}

<script>

$('.datepicker').datepicker(Init.datepicker());

$('.form-export').on('submit', function(e){
    e.preventDefault();
    
    var $form = $(this);
    var $submitBtn = $form.find('button[type=submit]');
    $submitBtn.button('Exportar...');
    // var fileName = "Exportar.xls";
    
    // $.ajax({
    //     'url': $form.attr('action'),
    //     'type': 'GET',
    //     'data': {
            
    //     },
    // })
    // .then((resp) => {
        
    // });
    
    var request = new XMLHttpRequest();
    request.open("POST", $form.attr('action'), true);
    request.setRequestHeader(
        "Content-Type",
        "application/json; charset=UTF-8"
    );
    request.responseType = "blob";
    request.onload = function (e) {
      if (this.status === 200) {
        var blob = this.response;
        
        var fileName = request.getResponseHeader('content-disposition').split('filename="')[1].split('"')[0];
        if (window.navigator.msSaveOrOpenBlob) {
          window.navigator.msSaveBlob(blob, fileName);
        } else {
          var downloadLink = window.document.createElement("a");
          var contentTypeHeader = request.getResponseHeader("Content-Type");
          downloadLink.href = window.URL.createObjectURL(
            new Blob([blob], { type: contentTypeHeader })
          );
          downloadLink.download = fileName;
          document.body.appendChild(downloadLink);
          downloadLink.click();
          document.body.removeChild(downloadLink);
        }
        
        $('#modal-remote-xs').modal('hide');
      }
      
      $submitBtn.button('reset');
    };

    request.send(JSON.stringify({
        'date_start': $('input[name=date_start]').val(),
        'date_end': $('input[name=date_end]').val()
    }));
    
});
   


</script>
