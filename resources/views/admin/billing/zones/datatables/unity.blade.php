<div class="text-center">
        @if($row->unity == 'country')
            País
        @elseif($row->unity == 'zip_code')
            Códigos Postais
        @elseif($row->unity == 'distance')
            Dist. do remetente
        @elseif($row->unity == 'pack_type')
            Tipo Embalagem
        @elseif($row->unity == 'pack_zip_code')
            Cod. Postal + Emb.
        @elseif($row->unity == 'pack_matrix')
            Cod. Postal + Matriz
        @elseif($row->unity == 'matrix')
            Matriz Cod. Postais
        @else
            Zonas por Rota
        @endif
</div>