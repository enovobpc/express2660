<div class="modal" id="modal-duplicate-recipients">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close pull-right" data-dismiss="modal">
                    <span class="fs-15" aria-hidden="true"><i class="fas fa-times"></i></span>
                    <span class="sr-only">@trans('Fechar')</span>
                </button>
                <h4 class="modal-title">@trans('Moradas frequentes duplicadas')</h4>
            </div>
            <div class="modal-body">
                <table class="table table-condensed m-b-0">
                    <tbody>
                    <?php
                    $duplicateRecipients = $duplicateRecipients->sortByDesc('count')
                    ?>
                    @foreach($duplicateRecipients as $duplicate)
                        <tr>
                            <td class="w-1">
                                @if($duplicate->count <= 2)
                                <span class="label bg-light-blue">{{ $duplicate->count }}x</span>
                                @elseif($duplicate->count == 3)
                                <span class="label label-warning">{{ $duplicate->count }}x</span>
                                @elseif($duplicate->count == 4)
                                <span class="label bg-orange">{{ $duplicate->count }}x</span>
                                @elseif($duplicate->count >= 5)
                                <span class="label bg-red">{{ $duplicate->count }}x</span>
                                @endif
                            </td>
                            <td>
                                <div class="lh-1-1 text-uppercase">
                                    {{ $duplicate->name }}
                                    <br/>
                                    <small class="text-muted">
                                        {{ $duplicate->address }}<br/>
                                        {{ $duplicate->zip_code }} {{ $duplicate->city }}
                                    </small>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                </table>
            </div>
            <div class="modal-footer">
                <div class="pull-right">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@trans('Fechar')</button>
                </div>
            </div>
        </div>
    </div>
</div>