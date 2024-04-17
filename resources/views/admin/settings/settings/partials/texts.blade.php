<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="row">
            <div class="col-sm-12">
                <ul class="nav nav-tabs">
                    <li role="presentation" class="active">
                        <a href="#legal-texts-geral" role="tab" data-toggle="tab">Condições Gerais</a>
                    </li>
                    <li role="presentation">
                        <a href="#legal-texts-presentation" role="tab" data-toggle="tab">Apresentação Empresa</a>
                    </li>
                    <li role="presentation">
                        <a href="#legal-texts-charge" role="tab" data-toggle="tab">Instruções Carga</a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="legal-texts-geral">
                        <table class="table table-condensed no-border m-b-0">
                            <td style="padding: 0; margin-top: -1px">
                                <h4 class="section-title">Condições Gerais do Serviço</h4>
                                {{ Form::textarea('prices_table_general_conditions', Setting::get('prices_table_general_conditions'), ['class' =>'form-control ckeditor-conditions', 'id' => 'ckeditor-conditions']) }}
                            </td>
                        </table>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="legal-texts-presentation">
                        <h4 class="section-title">Apresentação Empresa</h4>
                        <ul class="nav nav-tabs">
                            <li role="presentation" class="active">
                                <a href="#prices_table_presentation-pt" role="tab" data-toggle="tab"><i class="flag-icon flag-icon-pt"></i> Português</a>
                            </li>
                            <li role="presentation">
                                <a href="#prices_table_presentation-en" role="tab" data-toggle="tab"><i class="flag-icon flag-icon-gb"></i> English</a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane active" id="prices_table_presentation-pt">
                                <table class="table table-condensed no-border m-b-0">
                                    <td style="padding: 0; margin-top: -1px">
                                        {{ Form::textarea('prices_table_presentation_pt', Setting::get('prices_table_presentation_pt'), ['class' =>'form-control ckeditor-presentation', 'id' => 'ckeditor-presentation', 'style' => 'border-top: none']) }}
                                    </td>
                                </table>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="prices_table_presentation-en">
                                <table class="table table-condensed no-border m-b-0">
                                    <td style="padding: 0; margin-top: -1px">
                                        {{ Form::textarea('prices_table_presentation_en', Setting::get('prices_table_presentation_en'), ['class' =>'form-control ckeditor-presentation-en', 'id' => 'ckeditor-presentation-en', 'style' => 'border-top: none']) }}
                                    </td>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="legal-texts-charge">
                        <table class="table table-condensed no-border m-b-0">
                            <td style="padding: 0; margin-top: -1px">
                                <h4 class="section-title">Apresentação Instruções de Carga</h4>
                                <ul class="nav nav-tabs">
                                    <li role="presentation" class="active">
                                        <a href="#charging_instructions_presentation-pt" role="tab" data-toggle="tab"><i class="flag-icon flag-icon-pt"></i> Português</a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#charging_instructions_presentation-en" role="tab" data-toggle="tab"><i class="flag-icon flag-icon-gb"></i> English</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="charging_instructions_presentation-pt">
                                        <table class="table table-condensed no-border m-b-0">
                                            <td style="padding: 0; margin-top: -1px">
                                                {{ Form::textarea('charging_instructions_presentation_pt', Setting::get('charging_instructions_presentation_pt'), ['class' =>'form-control budget-geral-answer', 'id' => 'budget-geral-answer', 'style' => 'border-top: none']) }}
                                            </td>
                                        </table>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="charging_instructions_presentation-en">
                                        <table class="table table-condensed no-border m-b-0">
                                            <td style="padding: 0; margin-top: -1px">
                                                {{ Form::textarea('charging_instructions_presentation_en', Setting::get('charging_instructions_presentation_en'), ['class' =>'form-control budget-geral-answer', 'id' => 'budget-geral-answer-en', 'style' => 'border-top: none']) }}
                                            </td>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </table>
                    </div>
                </div>

            </div>
            <div class="col-sm-12">
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
            </div>
        </div>
    </div>
</div>

