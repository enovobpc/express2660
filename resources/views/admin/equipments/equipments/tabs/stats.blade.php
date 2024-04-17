<div class="row">
    <div class="col-sm-12">
        <div class="">
            <div class="box-header">
                <div class="box-header bg-gray">
                    <div class="pull-right">
                        <form action="{{ route('admin.equipments.index', ['tab' => 'stats']) }}" method="get">
                            {{ Form::hidden('tab', 'stats') }}
                            <button type="submit" class="btn btn-sm btn-default pull-right"
                                    style="margin: -5px 0 -7px 4px; border: none; height: 32px; padding: 5px 10px;"><i class="fas fa-search"></i></button>
                            <div class="input-group input-group-sm w-160px pull-right" style="margin: -6px;">
                                {{ Form::select('location', ['' => 'Localização'] + $locations, fltr_val(Request::all(), 'location'), ['class' => 'form-control input-sm filter-datatable w-20px select2']) }}
                            
                            </div>
                        </form>
                    </div>
                <h4 class="box-title">Categoria de Equipamento por Localização</h4>
            </div>
            <div class="box-body p-0" style="overflow-x: auto">
                <table class="table table-bordered m-b-0" style="margin-top: -1px">
                    <tr>
                        <th style="min-width: 200px; border-bottom: 1px solid #ccc" class="bg-gray-light">Localização</th>
                        @foreach($allCategories as $category)
                            <th style="width: 50px; border-bottom: 1px solid #ccc; text-align: center; font-weight: bold" class="bg-gray-light">
                                <span data-toggle="tooltip" title="{{ $category->name }}">
                                    {{ $category->code }}
                                </span>
                            </th>
                        @endforeach
                        <th style="width: 50px; border-bottom: 1px solid #ccc; font-weight: bold" class="bg-gray-light">Total</th>
                    </tr>
                    <?php $tableTotal = 0; ?>
                    @foreach($stats['categoryLocation'] as $locationId => $locationCategories)
                        <?php
                        $rowTotal = 0;
                        $locationName = @$locations[$locationId];
                        ?>
                        <tr>
                            <td>{{ @$locationName }}</td>
                            @foreach($allCategories as $category)
                                <?php
                                $rowValue = @$locationCategories[$category->id] ? @$locationCategories[$category->id] : '';
                                $rowTotal+=  @$locationCategories[$category->id] ? $rowValue : 0;
                                ?>
                                <td style="text-align: center">
                                    @if($rowValue)
                                    <span data-toggle="tooltip" title="{{ $category->name }} - {{ $locationName }}">
                                        <a href="{{ route('admin.equipments.locations.show', [$locationId, 'category' => $category->id]) }}" data-toggle="modal" data-target="#modal-remote-lg">
                                            <b>{{ $rowValue }}</b> <i class="fas fa-external-link-square-alt"></i>
                                        </a>
                                    </span>
                                    @endif
                                </td>
                            @endforeach
                            <td style="text-align: center; font-weight: bold">
                                <span data-toggle="tooltip" title="{{ $locationName }}">
                                {{ $rowTotal }}
                                </span>
                            </td>
                        </tr>
                            <?php $tableTotal+= $rowTotal; ?>
                    @endforeach
                    <tr>
                        <td colspan="{{ count($allCategories) + 1 }}" class="text-right">Total</td>
                        <td style="text-align: center; font-weight: bold">
                            {{ $tableTotal }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="m-t-15">
            <div class="box-header bg-gray">
                <h4 class="box-title">Resumo por Categoria</h4>
            </div>
            <div class="box-body p-0" style="max-height: 265px;overflow-y: auto;border-bottom: 1px solid #ddd;">
                <table class="table table-bordered table-pdf m-b-5">
                    <tr>
                        <th style="border-bottom: 1px solid #ccc" class="bg-gray-light">Categoria</th>
                        @foreach($allStatus as $status)
                            <th style="width: 30px; border-bottom: 1px solid #ccc" class="bg-gray-light">{{ $status }}</th>
                        @endforeach
                        <th style="width: 30px; border-bottom: 1px solid #ccc" class="bg-gray-light">Total</th>
                    </tr>
                    <?php $tableTotal = []; ?>
                    @foreach($stats['categories'] as $categoryName => $category)
                        <?php $rowTotal = 0; ?>
                        <tr>
                            <td>{{ @$categoryName }}</td>
                            @foreach($allStatus as $statusKey => $statusName)
                                <?php
                                $rowValue = @$category[$statusKey] ? @$category[$statusKey] : '-';
                                $rowTotal+= @$category[$statusKey];
                                $tableTotal[$statusKey] = @$tableTotal[$statusKey] + @$category[$statusKey]
                                ?>
                                <td style="text-align: center">{{ $rowValue }}</td>
                            @endforeach
                            <td style="text-align: center; font-weight: bold">{{ $rowTotal }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td style="border-bottom: none; border-left: none"></td>
                        <?php $total = 0; ?>
                        @foreach($allStatus as $statusKey => $statusName)
                            <?php $total+= @$tableTotal[$statusKey]; ?>
                            <td style="text-align: center; font-weight: bold">{{ @$tableTotal[$statusKey] }}</td>
                        @endforeach
                        <td style="text-align: center; font-weight: bold">{{ $total }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="m-t-15">
            <?php
            $minDt = new Date();
            $minDt = $minDt->subDays(30)->format('Y-m-d');
            ?>
            <div class="box-header bg-gray">
                <div class="pull-right">
                    <form action="{{ route('admin.equipments.index', ['tab' => 'stats']) }}" method="get">
                        {{ Form::hidden('tab', 'stats') }}
                        <button type="submit" class="btn btn-sm btn-default pull-right"
                                style="margin: -5px 0 -7px 4px; border: none;height: 28px; padding: 5px 10px;"><i class="fas fa-search"></i></button>
                        <div class="input-group input-group-sm w-240px pull-right" style="margin: -6px;">
                            {{ Form::text('stats_date_min', fltr_val(Request::all(), 'stats_date_min', $minDt), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Início', 'autocomplete' => 'field-1']) }}
                            <span class="input-group-addon">até</span>
                            {{ Form::text('stats_date_max', fltr_val(Request::all(), 'stats_date_max', date('Y-m-d')), ['class' => 'form-control datepicker filter-datatable w-20px', 'placeholder' => 'Fim', 'autocomplete' => 'field-1']) }}
                        </div>
                    </form>
                </div>
                <h4 class="box-title">Resumo de movimentos</h4>

            </div>
            <div class="box-body p-0" style="max-height: 265px;overflow-y: auto;border-bottom: 1px solid #ddd;">
                <table class="table table-bordered table-pdf m-b-5">
                    <tr>
                        <th style="border-bottom: 1px solid #ccc" class="bg-gray-light">Categoria</th>
                        @foreach(trans('admin/equipments.equipments.actions') as $key => $value)
                        <th style="width: 30px; border-bottom: 1px solid #ccc" class="bg-gray-light">{{ $value }}</th>
                        @endforeach
                        <th style="width: 30px; border-bottom: 1px solid #ccc" class="bg-gray-light">Total</th>
                    </tr>
                    @foreach($allCategories as $category)
                        <?php $rowTotal = 0; ?>
                        <tr>
                            <td>{{ $category->name }}</td>
                            @foreach(trans('admin/equipments.equipments.actions') as $key => $value)
                                <?php $rowTotal+= @$stats['history'][$category->id][$key];?>
                                <td style="text-align: center">
                                    @if(@$stats['history'][$category->id][$key])
                                    <a href="{{ route('admin.equipments.categories.history', [$category->id, 'action' => $key, 'min_date' => Request::get('stats_date_min'), 'max_date' => Request::get('stats_date_max')]) }}"
                                       data-toggle="modal"
                                       data-target="#modal-remote-lg">
                                        <b>{{ @$stats['history'][$category->id][$key] }}</b> <i class="fas fa-external-link-square-alt"></i>
                                    </a>
                                    @endif
                                </td>
                            @endforeach
                            <td style="text-align: center; font-weight: bold">{{ $rowTotal }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>