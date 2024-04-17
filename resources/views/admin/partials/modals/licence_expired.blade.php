<div class="modal" id="licence-expired" data-backdrop="static" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-red">
                <h4 class="modal-title"><i class="fas fa-lock"></i> LICENÇA EXPIRADA</h4>
            </div>
            <div class="modal-body">
                <h2 class="text-center m-t-0 m-b-5">
                    <i class="fas fa-exclamation-triangle text-red"></i>
                </h2>
                <h4 class="m-t-0 text-center" style="line-height: 24px;">
                    <b>A licença suspensa por falta de pagamentos.</b>
                    <br/>
                    Por favor, regularize a totalidade ou parte do valor em aberto para normalizar a situação.
                </h4>
                <?php
                $licenseData = json_decode(File::get(storage_path() . '/license.json'), true);
                ?>
                @if(!Auth::user()->hasRole('acesso-a-licenca') && @$licenseData['total_unpaid'])
                <hr/>
                <div class="row">
                    <div class="col-sm-6">
                        <table class="table fs-14">
                            <tr>
                                <td style="border-top: 0">Pagamentos em atraso</td>
                                <td style="border-top: 0" class="bold">{{ @$licenseData['count_unpaid'] ? @$licenseData['count_unpaid'] : 1 }}</td>
                            </tr>
                            <tr>
                                <td>Montante em dívida</td>
                                <td class="bold">{{ money(@$licenseData['total_unpaid'], '€') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-sm-6">
                        @if(@$licenseData['mb_ref'])
                        <div class="row row-10">
                            <div class="col-sm-3 text-right">
                                <img class="w-55px" src="{{ asset('assets/img/default/mb.svg') }}">
                            </div>
                            <div class="col-sm-8">
                                <ul class="list-unstyled fs-15">
                                    <li class="m-b-7">Entidade: <b>{{ @$licenseData['mb_entity'] }}</b></li>
                                    <li class="m-b-7">Referência: <b>{{ chunk_split(@$licenseData['mb_ref'], 3, ' ') }}</b></li>
                                    <li>Valor: <b>{{ money(@$licenseData['total_unpaid'], '€') }}</b></li>
                                </ul>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>