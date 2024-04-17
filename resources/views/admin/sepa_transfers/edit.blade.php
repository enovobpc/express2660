<?php $hash = str_random(5); ?>
{{ Form::model($payment, $formOptions) }}
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">
        <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
        <span class="sr-only">Fechar</span>
    </button>
    <h4 class="modal-title">{{ $action }}</h4>
</div>
<div class="modal-body {{ $hash }}">
    <div class="row row-5" style="margin: -15px -15px 0;
    background: #eee;
    padding: 10px 10px 0;
    border-bottom: 1px solid #ddd;">
        <div class="col-sm2 col-lg-1">
            <div class="form-group">
                {{ Form::label('code', 'ID') }}
                {{ Form::text('code', null, ['class' => 'form-control', 'required', $payment->edit_mode ? : 'disabled']) }}
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group is-required">
                {{ Form::label('name', 'Descrição') }}
                {{ Form::text('name', null, ['class' => 'form-control', 'required', $payment->edit_mode ? : 'disabled']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('bank_id', 'Banco') }}
                {{ Form::select('bank_id', ['' => ''] + $banks, null, ['class' => 'form-control select2', 'disabled']) }}
            </div>
        </div>
        <div class="col-sm-2">
            <div class="form-group">
                {{ Form::label('type', 'Tipo') }}
                {{ Form::select('type', ['' => ''] + trans('admin/billing.sepa-types'), null, ['class' => 'form-control select2', 'disabled']) }}
            </div>
        </div>
        <div class="col-sm-4">
            <ul class="list-unstyled list-inline pull-right">
                <li>
                    <h3 class="m-0 text-right p-r-10">
                        <small>Transações</small><br/>
                        <span class="sepa-transactions-count">{{ $payment->transactions_count ? $payment->transactions_count : 0 }}</span>
                    </h3>
                </li>
                <li>
                    <h3 class="m-0 text-right">
                        <small>Montante Total</small><br/>
                        <b class="sepa-transactions-total">{{ money($payment->transactions_total, '€') }}</b>
                    </h3>
                </li>
            </ul>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-12">
            @if($payment->edit_mode)
            <a href="{{ route('admin.sepa-transfers.groups.create', $payment->id) }}"
               data-toggle="modal"
               data-target="#modal-remote"
               class="btn btn-xs btn-success pull-right m-t-10">
                <i class="fas fa-plus"></i> Adicionar
            </a>
            @endif
            <h4 class="text-blue bold">Lotes  <small>({{ @$payment->groups->count() }})</small></h4>
            <div class="clearfix"></div>
                <?php
                $groups = $payment->groups;
                ?>
            <div class="sepa-groups-list" style="border: 1px solid #ccc;
    height: 100px;
    overflow: auto;
    border-radius: 5px;">
                @include('admin.sepa_transfers.partials.groups_list')
            </div>
        </div>
    </div>
    <div class="row row-5">
        <div class="col-sm-12">
            <?php
            $group = null;
            $transactions = [];
            if(@$payment->exists && !$payment->groups->isEmpty()) {
                $group = $payment->groups->first();
                $transactions = $group->transactions;
            }
            ?>
            <div class="sepa-transactions-list">
                @include('admin.sepa_transfers.partials.transactions_list')
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div class="pull-left text-left" style="width: 80%">
    </div>
    <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
    @if($payment->edit_mode)
        <button type="submit" class="btn btn-primary" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A guardar...">Guardar</button>
        <button type="button" class="btn btn-success btn-conclude" data-loading-text="<i class='fas fa-spin fa-circle-notch'></i> A finalizar..."><i class="fas fa-check"></i> Finalizar</button>
    @endif
</div>
{{ Form::hidden('conclude', '0') }}
{{ Form::close() }}
<style>
    .table-services td {
        padding: 1px !important;
    }
    .table tr {
        cursor: pointer;
    }

    .table tr.active td{
        background: #add5ff !important;
    }
</style>
<script>
    $('.datepicker').datepicker(Init.datepicker());
    $('.select2').select2(Init.select2());
    $('input').iCheck(Init.iCheck())

    $(document).on('click', '.{{ $hash }} .sepa-groups-list tr[data-url]', function(){
        $('.{{ $hash }} .sepa-transactions-list .table-transactions').html('<div class="text-center m-t-90"><i class="fas fa-spin fa-circle-notch"></i> A carregar lista...</div>');

        $('.{{ $hash }} .sepa-groups-list tr[data-url]').removeClass('active');
        $(this).addClass('active');

        var url = $(this).data('url');
        $.get(url, function(data){
            if(data.result) {
                $('.{{ $hash }} .sepa-transactions-list').html(data.html)
            } else {
                $('.{{ $hash }} .sepa-transactions-list .table-transactions').html('<div class="text-center m-t-90 text-red"><i class="fas fa-exclamation-triangle"></i> Erro ao carregar lista.</div>')
            }
        })
    });


    $('.btn-conclude').on('click', function (e) {
        e.preventDefault();

        var $form = $(this).closest('form');
        bootbox.confirm({
            title: "Finalizar transferência SEPA",
            message: "<h4>Confirma a finalização da edição? Após finalizar não poderá editar a transferência.</h4>",
            buttons: {
                confirm: {
                    label: "Finalizar",
                    className: "btn-success"
                },
                cancel: {
                    label: "Cancelar",
                    className: "btn-default"
                }
            },
            callback: function(result) {
                if (result) {
                    $('[name="conclude"]').val(1);
                    $form.submit();
                }
            }
        });
    })


    /**
     * Remove transaction line
     */
    $(document).on('click', ".{{ $hash }} .remove-group-line",function(e) {
        e.preventDefault();

        var $this = $(this);

        bootbox.confirm({
            title: "Remover lote",
            message: "<h4>Confirma a remoção do lote de transações?</h4>",
            buttons: {
                confirm: {
                    label: "Remover",
                    className: "btn-danger"
                },
                cancel: {
                    label: "Cancelar",
                    className: "btn-default"
                }
            },
            callback: function(result) {
                if (result) {
                    $.ajax({
                        url: $this.attr('href'),
                        type: "DELETE",
                        success: function (data) {
                            if (data.result) {
                                Growl.success(data.feedback);
                                $('.modal .sepa-groups-list').html(data.html_groups)
                                $('.modal .sepa-transactions-list').html(data.html)
                                $('.modal .sepa-transactions-count').html(data.transactions_count)
                                $('.modal .sepa-transactions-total').html(data.transactions_total)
                                $('#modal-remote').modal('hide');

                                $(document).find('.btn-add-transaction').hide();
                            } else {
                                Growl.error(data.feedback);
                            }
                        }
                    }).fail(function () {
                        Growl.error500()
                    }).always(function () {
                    });
                }
            }
        });
    })


    /**
     * Remove transaction line
     */
    $(document).on('click', ".{{ $hash }} .remove-transaction-line",function(e) {
        e.preventDefault();

        var $this = $(this);

        bootbox.confirm({
            title: "Remover transação",
            message: "<h4>Confirma a remoção da transação?</h4>",
            buttons: {
                confirm: {
                    label: "Remover",
                    className: "btn-danger"
                },
                cancel: {
                    label: "Cancelar",
                    className: "btn-default"
                }
            },
            callback: function(result) {

                if (result) {
                    $.ajax({
                        url: $this.attr('href'),
                        type: "DELETE",
                        success: function (data) {
                            if (data.result) {
                                Growl.success(data.feedback);
                                $('.modal .sepa-groups-list').html(data.html_groups)
                                $('.modal .sepa-transactions-list').html(data.html)
                                $('.modal .sepa-transactions-count').html(data.transactions_count)
                                $('.modal .sepa-transactions-total').html(data.transactions_total)
                                $('#modal-remote').modal('hide');
                            } else {
                                Growl.error(data.feedback);
                            }
                        }
                    }).fail(function () {
                        Growl.error500()
                    }).always(function () {
                    });
                }
            }
        });
    })

</script>

