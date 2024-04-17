@if(!hasModule('human_resources'))
    @include('admin.partials.denied_message')
@else
    <div class="box no-border">
        <div class="box-body">
            {{ Form::open(['route' => ['admin.users.cards.store', $user->id]]) }}
            <table class="table table-condensed table-dashed table-cards">
                <thead>
                    <tr>
                        <td class="bg-gray-light bold">@trans('Documento/Certificado')</td>
                        <td class="bg-gray-light bold w-150px">@trans('Nº de Documento')</td>
                        <td class="bg-gray-light bold w-140px">@trans('Data Emissão')</td>
                        <td class="bg-gray-light bold w-140px">@trans('Data Validade')</td>
                        <td class="bg-gray-light bold w-140px">@trans('Aviso Expiração')</td>
                        <td class="bg-gray-light bold w-200px">@trans('Notas/Obs')</td>
                        <td class="bg-gray-light bold w-1"></td>
                    </tr>
                </thead>
                <tbody>
                    @include('admin.users.users.partials.cards.default_doc_row')
                    @foreach($customCards as $card)
                        @include('admin.users.users.partials.cards.doc_row')
                    @endforeach
                    <tr style="display: none">
                        <td class="vertical-align-middle">
                            {{ Form::text('name[]', null, ['class' => 'form-control rq-field', 'placeholder' => 'Nome do cartão/certificado']) }}
                        </td>
                        <td>
                            {{ Form::text('card_no[]', null, ['class' => 'form-control rq-field']) }}
                        </td>
                        <td>
                            <div class="input-group">
                                {{ Form::text('issue_date[]', null, ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                {{ Form::text('validity_date[]', null, ['class' => 'form-control datepicker']) }}
                                <div class="input-group-addon">
                                    <i class="fas fa-calendar-alt"></i>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="input-group">
                                {{ Form::text('notification_days[]', null, ['class' => 'form-control']) }}
                                <div class="input-group-addon" style="border: none;">
                                    @trans('dias antes')
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ Form::text('obs[]', null, ['class' => 'form-control']) }}
                        </td>
                        <td class="vertical-align-middle">
                            <a href="#" class="text-red btn-delete-card">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <button type="submit" class="btn btn-sm btn-primary">@trans('Gravar')</button>
            <button type="button" class="btn btn-sm btn-default btn-add-card"><i class="fas fa-plus"></i> @trans('Adicionar outro documento ou certificado')</button>
            {{ Form::close() }}
        </div>
    </div>
@endif