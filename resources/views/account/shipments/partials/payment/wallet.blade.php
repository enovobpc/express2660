<table style="width: 100%">
    <tbody>
        <tr>
            <td class="text-center"> 
                <div class="mbway-success text-center">
                    <h4 class="m-t-0 text-green"><i class="fas fa-check"></i> Pagamento recebido</h4>
                    <p>Pagamento recebido com sucesso.</p>
                    <h4 class="bold">Saldo de Conta</h4>
                    <p style="line-height: 27px;">
                        Disponível: <span class="wallet-amount">{{ money($response['wallet'], Setting::get('app_currency')) }}</span><br/>
                    </p>
                </div>
            </td>
        </tr>
    </tbody>
</table>

<script>


</script>