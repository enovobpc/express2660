@if($categorySummary)
<div>
    <h4 class="bold" style="margin-top: 50px">Resumo por Categoria</h4>
    <table class="table table-bordered table-pdf m-b-5">
        <tr>
            <th>Categoria</th>
            @foreach($allStatus as $status)
                <th style="width: 30px">{{ $status }}</th>
            @endforeach
            <th style="width: 30px">Total</th>
        </tr>
        @foreach($categories as $categoryName => $category)
            <?php $rowTotal = 0; ?>
            <tr>
                <td>{{ @$categoryName }}</td>
                @foreach($allStatus as $statusKey => $statusName)
                    <?php
                    $rowValue = @$category[$statusKey] ? @$category[$statusKey] : '-';
                    $rowTotal+= @$category[$statusKey];
                    ?>
                    <td style="text-align: center">{{ $rowValue }}</td>
                @endforeach
                <td style="text-align: center; font-weight: bold">{{ $rowTotal }}</td>
            </tr>
        @endforeach
    </table>
    <div class="clearfix"></div>
    <br/>
    <h4 class="bold" style="margin-top: 10px">Resumo de equipamentos</h4>
</div>
@endif

<div>
    @if(!$groupResults)
        <table class="table table-bordered table-pdf m-b-5" style="border: none;">
            <tr>
                <th style="width: 170px">Artigo</th>
                <th>Categoria</th>
                <th>Localização</th>
                <th style="width: 80px"></th>
                <th style="width: 70px; text-align: center">Total</th>
            </tr>
            <?php $rowsTotal = 0 ?>
            @foreach($equipments as $equipment)
                <?php $rowsTotal+= $equipment->stock_total; ?>
                <tr>
                    <td>{{ @$equipment->name }}</td>
                    <td>{{ @$equipment->category->name }}</td>
                    <td>{{ @$equipment->location->name }}</td>
                    <td>{{ trans('admin/equipments.products.status.'. @$equipment->status) }}</td>
                    <td style="text-align: center; font-weight: bold">{{ money($equipment->stock_total) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="border: none;" colspan="4"></td>
                <td style="text-align: center; font-weight: bold">{{ money($rowsTotal) }}</td>
            </tr>
        </table>
    @elseif($groupResults == 'location' || $groupResults == 'category')

        @foreach($equipments as $groupName => $groupDetais)
        @if($groupResults == 'location')
            <h5 style="margin: 0 0 5px; font-weight: bold">{{ @$groupDetais->first()->location->code }} - {{ $groupName }}</h5>
        @else
            <h5 style="margin: 0 0 5px; font-weight: bold">{{ $groupName }}</h5>
        @endif
        <table class="table table-bordered table-pdf m-b-5" style="border: none;">
            <tr>
                <th style="width: 170px">Artigo</th>
                <th>Categoria</th>
                <th>Localização</th>
                <th style="width: 80px">Estado</th>
                <th style="width: 70px; text-align: center">Total</th>
            </tr>
            <?php $rowsTotal = 0 ?>
            @foreach($groupDetais as $equipment)
                <?php $rowsTotal+= $equipment->stock_total; ?>
                <tr>
                    <td>{{ @$equipment->name }}</td>
                    <td>{{ @$equipment->category->name }}</td>
                    <td>{{ @$equipment->location->name }}</td>
                    <td>{{ trans('admin/equipments.products.status.'. @$equipment->status) }}</td>
                    <td style="text-align: center; font-weight: bold">{{ money($equipment->stock_total) }}</td>
                </tr>
            @endforeach
            <tr>
                <td style="border: none;" colspan="4"></td>
                <td style="text-align: center; font-weight: bold">{{ money($rowsTotal) }}</td>
            </tr>
        </table>
        @endforeach

    @elseif($groupResults == 'location-category')

        @foreach($equipments as $locationName => $locationDetails)

            <div style="background: #333; font-weight: bold;">
                <h5 style="margin: 3px 5px; color: #fff">{{ $locationName }}</h5>
            </div>

            <div style="margin-left: 15px; margin-top: 5px">
                @foreach($locationDetails as $categoryName => $categoryProducts)
                <h5 style="margin: 0; font-weight: bold">{{ $categoryName }}</h5>
                <table class="table table-bordered table-pdf m-b-10" style="border: none">
                    <tr>
                        <th style="width: 170px; background: #999">Artigo</th>
                        <th style="background: #999">Categoria</th>
                        <th style="background: #999">Localização</th>
                        <th style="width: 80px; background: #999">Estado</th>
                        <th style="width: 70px; background: #999; text-align: center">Total</th>
                    </tr>
                    <?php $rowsTotal = 0 ?>
                    @foreach($categoryProducts as $equipment)
                        <?php $rowsTotal+= $equipment->stock_total; ?>
                        <tr>
                            <td>{{ @$equipment->name }}</td>
                            <td>{{ @$equipment->category->name }}</td>
                            <td>{{ @$equipment->location->name }}</td>
                            <td>{{ trans('admin/equipments.status.'. @$equipment->status) }}</td>
                            <td style="text-align: center; font-weight: bold">{{ money($equipment->stock_total) }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="border: none;" colspan="4"></td>
                        <td style="text-align: center; font-weight: bold">{{ money($rowsTotal) }}</td>
                    </tr>
                </table>
                @endforeach
            </div>
        @endforeach
    @endif

    <div class="clearfix"></div>
</div>