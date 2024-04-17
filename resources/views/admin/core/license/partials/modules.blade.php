<div class="row row-5">
    <div style="margin-top: -15px">
    @foreach($modulesGrouped as $groupName => $modules)
        <div class="col-sm-12">
            <h4 class="m-t-5 m-b-10 bold">{{ $groupName ? $groupName : 'Sem nome' }}</h4>

            <?php

            $count = $modules->count();

            $columns = 4;
            if($count == 5) {
                $columns = 3;
            }

            $rowsPerColumn = round($count / $columns);
            $i = 0;
            ?>
            <div class="row row-5">
                <div class="col-sm-3">
                @foreach($modules as $module)
                    <?php $i++; ?>
                    <div>
                        <label style="font-weight: normal">
                            {{ Form::checkbox('modules[]', $module->module, in_array($module->module, @$activeModules)) }}
                            {{ @$module->name }}
                        </label>
                    </div>

                    @if($i == $rowsPerColumn)
                        </div>
                        <div class="col-sm-3">
                    <?php $i = 0; ?>
                    @endif
                @endforeach
                </div>
            </div>
            <hr style="margin: 10px 0 5px 0; border-color: #999"/>
        </div>
    @endforeach
    </div>
</div>