<ul class="datatable-filters list-inline hide pull-left" data-target="#datatable-contacts">
    <li>
        <a href="{{ route('account.contacts.create') }}"
           class="btn btn-sm btn-black pull-right"
           data-toggle="modal"
           data-target="#modal-remote">
            <i class="fas fa-plus"></i> {{ trans('account/global.word.new') }}
        </a>
    </li>
</ul>
<table id="datatable-contacts" class="table table-condensed table-hover">
    <thead>
    <tr>
        <th></th>
        <th>{{ trans('account/global.word.name') }}</th>
        <th>{{ trans('account/global.word.phone') }}</th>
        <th>{{ trans('account/global.word.email') }}</th>
        <th class="w-30px"></th>
    </tr>
    </thead>
    <tbody></tbody>
</table>