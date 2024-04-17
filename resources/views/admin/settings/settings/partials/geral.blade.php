<div style="display: none">
    {{ Form::text('vat_rate_normal', Setting::get('vat_rate_normal')) }}
</div>
<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="row">
            <div class="col-sm-8">
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="section-title">Modos de funcionamento da Aplicação</h4>
                        <table class="table table-condensed m-0" style="border-bottom: 1px solid #eee">
                            <tr>
                                <td>{{ Form::label('app_mode', 'Tipo de transporte', ['class' => 'control-label']) }}</td>
                                <td class="w-60">{{ Form::select('app_mode', trans('admin/global.app-modes'), Setting::get('app_mode'), ['class' =>'form-control select2']) }}</td>
                            </tr>
                            {{--<tr>
                                <td>{{ Form::label('expenses_mode', 'Modo dos encargos', ['class' => 'control-label']) }}</td>
                                <td class="w-40">{{ Form::select('expenses_mode', ['' => 'Simples', 'advanced' => 'Avançado'], Setting::get('expenses_mode'), ['class' =>'form-control select2']) }}</td>
                            </tr>--}}
                        </table>
                        <h4 class="section-title">Preferências Regionais</h4>
                        <table class="table table-condensed">
                            <tr>
                                <td>{{ Form::label('app_country', 'Região do Sistema', ['class' => 'control-label']) }}</td>
                                <td class="w-60">{{ Form::select('app_country', trans('admin/localization.countries'), Setting::get('app_country'), ['class' =>'form-control select2country']) }}</td>
                            </tr>
                            <tr>
                                <td>{{ Form::label('app_locale', 'Idioma do Sistema', ['class' => 'control-label']) }}</td>
                                <td>{{ Form::select('app_locale', trans('locales'), Setting::get('app_locale') ? Setting::get('app_locale') : ['pt'], ['class' =>'form-control select2country']) }}</td>
                            </tr>
                            {{-- <tr>
                                <td>{{ Form::label('app_locales[]', 'Línguas ativas', ['class' => 'control-label']) }}</td>
                                <td>{{ Form::select('app_locales[]', trans('locales'), Setting::get('app_locales') ? Setting::get('app_locales') : ['pt'], ['class' =>'form-control select2', 'multiple']) }}</td>
                            </tr> --}}
                            <tr>
                                <td>{{ Form::label('app_currency', 'Moeda e Faturação', ['class' => 'control-label']) }}</td>
                                <td>{{ Form::select('app_currency', trans('admin/localization.currencies'), Setting::get('app_currency'), ['class' =>'form-control select2']) }}</td>
                            </tr>
                            <tr>
                                <td>{{ Form::label('shipments_volumes_mesure_unity', 'Unidade de medida', ['class' => 'control-label']) }}</td>
                                <td>
                                    {{ Form::select('shipments_volumes_mesure_unity', ['cm' => 'cm (centímetro)', 'm' => 'm (metro)', 'in' => 'in (Inches)'], Setting::get('shipments_volumes_mesure_unity'), ['class' =>'form-control select2']) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <h4 class="section-title">
                            <a href="?tab=customization"
                               data-toggle="tooltip"
                               title="Altere a côr da aplicação só para a sua conta sem afetar outros utilizadores."
                               style="margin: -4px;" class="btn btn-xs btn-primary pull-right">
                                Personalizar para mim
                            </a>
                            Design e Usabilidade
                        </h4>
                        <table class="table table-condensed m-0">
                            <tr style="border-bottom: 1px solid #eee">
                                <td>{{ Form::label('app_skin', 'Côr da aplicação', ['class' => 'control-label']) }}</td>
                                <td class="w-1">
                                    <div class="{{ Setting::get('app_skin') }}">
                                        <div class="skin-preview skin-master" data-current-skin="{{ Setting::get('app_skin') }}" style="height: 22px; width: 22px; margin: 6px -3px 0 0;"></div>
                                    </div>
                                </td>
                                <td class="w-200px">{{ Form::select('app_skin', trans('admin/global.skins'), Setting::get('app_skin'), ['class' =>'form-control select2']) }}</td>
                            </tr>
                        </table>
                        <table class="table table-condensed m-0">
                            <tr style="border-bottom: 1px solid #eee">
                                <td>{{ Form::label('shipments_limit_search', 'Limitar pesquisas aos últimos', ['class' => 'control-label']) }}</td>
                                <td class="w-120px">
                                    <div class="input-group">
                                        {{ Form::number('shipments_limit_search', Setting::get('shipments_limit_search'), ['class' =>'form-control number', 'maxlength' => 2, 'min' => 0, 'max'=> 12]) }}
                                        <span class="input-group-addon" id="basic-addon2">meses</span>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table class="table table-condensed">
                            <tr>
                                <td>{{ Form::label('datatable_search_enter', 'Pesquisar tabelas só quando pressionar "Enter"', ['class' => 'control-label']) }}</td>
                                <td class="check">{{ Form::checkbox('datatable_search_enter', 1, Setting::get('datatable_search_enter'), ['class' => 'ios'] ) }}</td>
                            </tr>
                            <tr>
                                <td>{{ Form::label('modals_backdrop', 'Permitir fechar a janela ao clicar fora', ['class' => 'control-label']) }}</td>
                                <td class="check">{{ Form::checkbox('modals_backdrop', 1, Setting::get('modals_backdrop'), ['class' => 'ios'] ) }}</td>
                            </tr>
                            <tr>
                                <td>{{ Form::label('fixed_menu', 'Fixar menu lateral (todos utilizadores)', ['class' => 'control-label']) }}</td>
                                <td class="check">{{ Form::checkbox('fixed_menu', 1, Setting::get('fixed_menu'), ['class' => 'ios'] ) }}</td>
                            </tr>
                        </table>
                        <h4 class="section-title">Segurança</h4>
                        <table class="table table-condensed">
                            <tr>
                                <td>{{ Form::label('multiple_logins', 'Permitir multiplos logins na mesma conta', ['class' => 'control-label']) }} {!! tip('Ativando a opção, o sistema autoriza que mais do que um utilizador possa estar a usar a mesma conta em simultâneo.') !!}</td>
                                <td class="check">{{ Form::checkbox('multiple_logins', 1, Setting::get('multiple_logins'), ['class' => 'ios'] ) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Recursos Humanos</h4>
                <table class="table table-condensed m-0">
                    <tr>
                        <td>{{ Form::label('rh_max_holidays', 'Total de dias de Férias/Ano', ['class' => 'control-label']) }}</td>
                        <td style="width: 60px">{{ Form::text('rh_max_holidays', Setting::get('rh_max_holidays') ? Setting::get('rh_max_holidays') : '22', ['class' =>'form-control']) }}</td>
                    </tr>
                </table>
                <table class="table table-condensed" style="border-top: 1px solid #eee">
                    <tr>
                        <td class="w-120px">{{ Form::label('rh_workingdays', 'Dias de Trabalho', ['class' => 'control-label']) }}</td>
                        <td>
                            {{ Form::select('rh_workingdays[]', trans('datetime.weekday-tiny'), Setting::get('rh_workingdays') ? Setting::get('rh_workingdays') : [1,2,3,4,5,6], ['class' =>'form-control select2', 'multiple']) }}
                        </td>
                    </tr>
                </table>

                    <h4 class="section-title">Códigos postais área de Atuação</h4>
                    {{ Form::textarea('postal_codes_of_operation', Setting::get('postal_codes_of_operation'), ['class' =>'form-control', 'rows' => 5]) }}

                    <h4 class="section-title">Atividades dos colaboradores (um por linha) <i class="fas fa-info-circle" data-toggle="tooltip" title="Uma atividade por linha. Para definir as atividade em Português e Inglês deverá colocar uma linha inicial com a descrição PT ou EN. Tal como demonstrado no exemplo."></i></h4>  
                    {{ Form::textarea('collaborators_activities', Setting::get('collaborators_activities'), ['class' =>'form-control', 'rows' => 5, 'placeholder' => "Digite as atividades dos colaboradores.\nExemplos: \nPT\nCondutor de veículo ligeiro\nCondutor de veículo pesado de mercadorias \nEN\na driver of a heavy goods vehicle (HGV)\na bus/coach driver" ]) }}


            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <hr/>
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
            </div>
        </div>
    </div>
</div>

