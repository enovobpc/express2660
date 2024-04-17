<div class="box no-border">
    <div class="box-body p-t-0">
        <div class="row">
            <div class="col-sm-12">
                <h4 class="section-title">Dados Fiscais</h4>
            </div>
            <div class="col-sm-5">
                <table class="table table-condensed">
                    <tr>
                        <td class="w-120px">{{ Form::label('company_name', 'Designação Social', ['class' => 'control-label']) }}</td>
                        <td colspan="2">{{ Form::text('company_name', Setting::get('company_name'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('vat', 'Contribuínte', ['class' => 'control-label']) }}</td>
                        <td colspan="2">{{ Form::text('vat', Setting::get('vat'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('company_capital', 'Capital Social', ['class' => 'control-label']) }}</td>
                        <td class="w-100px">{{ Form::text('company_capital', Setting::get('company_capital'), ['class' =>'form-control']) }}</td>
                        <td>{{ Form::text('company_conservatory', Setting::get('company_conservatory'), ['class' =>'form-control', 'placeholder' => 'Conservatória']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('company_permit', 'Nº Alvará', ['class' => 'control-label']) }}</td>
                        <td colspan="2">{{ Form::text('company_permit', Setting::get('company_permit'), ['class' =>'form-control']) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-4">
                <table class="table table-condensed">
                    <tr>
                        <td class="w-100px">{{ Form::label('company_address', 'Morada', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('company_address', Setting::get('company_address') ? Setting::get('company_address') : Setting::get('address_1'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('company_zip_code', 'Código Postal', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('company_zip_code', Setting::get('company_zip_code') ? Setting::get('company_zip_code') : Setting::get('zip_code_1'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('company_city', 'Localidade', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('company_city', Setting::get('company_city') ? Setting::get('company_city') : Setting::get('city_1'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('company_country', 'País', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::select('company_country', trans('country'), Setting::get('company_country') ? Setting::get('company_country') : 'pt', ['class' =>'form-control select2']) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-3">
                <table class="table table-condensed">
                    <tr>
                        <td class="w-60px">{{ Form::label('company_phone', 'Telefone', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('company_phone', Setting::get('company_phone') ? Setting::get('company_phone') : Setting::get('phone_1'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('company_mobile', 'Telemóvel', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('company_mobile', Setting::get('company_mobile') ? Setting::get('company_mobile') : Setting::get('mobile_1'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('company_email', 'E-mail', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('company_email', Setting::get('company_email') ? Setting::get('company_email') : Setting::get('email_1'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>{{ Form::label('company_website', 'Website', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('company_website', Setting::get('company_website'), ['class' =>'form-control']) }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-8">
                <h4 class="section-title">Formulário de Contacto Website</h4>
                <table class="table table-condensed">
                    <tr>
                        <td class="w-100px">{{ Form::label('contact_form_email', 'Enviar para', ['class' => 'control-label']) }}</td>
                        <td>{{ Form::text('contact_form_email', Setting::get('contact_form_email'), ['class' =>'form-control']) }}</td>
                    </tr>
                </table>
                <div class="row">
                    <div class="col-sm-6">
                        <h4 class="section-title">Contactos Principais Website</h4>
                        <table class="table table-condensed">
                            <tr>
                                <td class="w-80px">Telefone</td>
                                <td>{{ Form::text('support_phone_1', Setting::get('support_phone_1'), ['class' =>'form-control']) }}</td>
                            </tr>
                            <tr>
                                <td>Telemóvel</td>
                                <td>{{ Form::text('support_mobile_1', Setting::get('support_mobile_1'), ['class' =>'form-control']) }}</td>
                            </tr>
                            <tr>
                                <td>E-mail</td>
                                <td>{{ Form::text('support_email_1', Setting::get('support_email_1'), ['class' =>'form-control']) }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        <h4 class="section-title">Contactos Secundários Website</h4>
                        <table class="table table-condensed">
                            <tr>
                                <td class="w-80px">Telefone</td>
                                <td>{{ Form::text('support_phone_2', Setting::get('support_phone_2'), ['class' =>'form-control']) }}</td>
                            </tr>
                            <tr>
                                <td>Telemóvel</td>
                                <td>{{ Form::text('support_mobile_2', Setting::get('support_mobile_2'), ['class' =>'form-control']) }}</td>
                            </tr>
                            <tr>
                                <td>E-mail</td>
                                <td>{{ Form::text('support_email_2', Setting::get('support_email_2'), ['class' =>'form-control']) }}</td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <h4 class="section-title">Redes Sociais Website</h4>
                <table class="table table-condensed">
                    <tr>
                        <td class="w-80px">Facebook</td>
                        <td>{{ Form::text('facebook', Setting::get('facebook'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>Instagram</td>
                        <td>{{ Form::text('instagram', Setting::get('instagram'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>Twitter</td>
                        <td>{{ Form::text('twitter', Setting::get('twitter'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>Linkedin</td>
                        <td>{{ Form::text('linkedin', Setting::get('linkedin'), ['class' =>'form-control']) }}</td>
                    </tr>
                    <tr>
                        <td>Youtube</td>
                        <td>{{ Form::text('youtube', Setting::get('youtube'), ['class' =>'form-control']) }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-sm-12">
                <h4 class="section-title">Moradas do Website</h4>
                <table class="table table-condensed">
                    <tr>
                        <th class="w-100px vertical-align-middle">Nº Delega.</th>
                        <th>{{ Form::select('total_delegations', [1=>1,2=>2,3=>3,4 =>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10], Setting::get('total_delegations'), ['class' =>'form-control select2']) }}</th>
                    </tr>
                    <tr>
                        <th class="w-100px">Designação</th>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <th>{{ Form::text('delegation_name_'.$i, Setting::get('delegation_name_'.$i), ['class' =>'form-control']) }}</th>
                        @endfor
                    </tr>
                    <tr>
                        <td>Morada</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('address_'.$i, Setting::get('address_'.$i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>Código Postal</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('zip_code_'.$i, Setting::get('zip_code_'.$i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>Localidade</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('city_'.$i, Setting::get('city_'.$i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>Telefone</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('phone_'.$i, Setting::get('phone_' . $i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>Telemóvel</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('mobile_'.$i, Setting::get('mobile_' . $i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>E-mail</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('email_'.$i, Setting::get('email_' . $i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>Horário</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('horary_'.$i, Setting::get('horary_' . $i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                </table>

                <h4 class="section-title">Coordenadas GPS e Google Maps</h4>
                <table class="table table-condensed">
                    <tr>
                        <td class="w-130px">Latitude Google</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('maps_latitude_' . $i, Setting::get('maps_latitude_' . $i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>Longitude Google</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('maps_longitude_' . $i, Setting::get('maps_longitude_' . $i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>Google Maps URL</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('maps_url_' . $i, Setting::get('maps_url_' . $i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>Latitude GPS</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('gps_latitude_' . $i, Setting::get('gps_latitude_' . $i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                    <tr>
                        <td>Longitude GPS</td>
                        @for($i = 1 ; $i <= Setting::get('total_delegations') ; $i++)
                            <td>{{ Form::text('gps_longitude_' . $i, Setting::get('gps_longitude_' . $i), ['class' =>'form-control']) }}</td>
                        @endfor
                    </tr>
                </table>
            </div>
            <div class="col-sm-12">
                {{ Form::hidden('collections_email', Setting::get('collections_email')) }}
                {{ Form::hidden('collections_phone', Setting::get('collections_phone')) }}
                {{ Form::hidden('commercial_email', Setting::get('commercial_email')) }}
                {{ Form::hidden('commercial_phone', Setting::get('commercial_phone')) }}
                {{ Form::hidden('support_phone', Setting::get('support_phone')) }}
                {{ Form::hidden('support_email01', Setting::get('support_email01')) }}
                {{ Form::hidden('support_email02', Setting::get('support_email02')) }}
                {{ Form::submit('Gravar', array('class' => 'btn btn-primary' ))}}
            </div>
        </div>
    </div>
</div>
