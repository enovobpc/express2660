<div class="text-center">
    @if(@$row->user->name)
    {{ @$row->user->name }}
    @else
    <span class="text-info"><i class="fas fa-info-circle"></i> Ainda sem responsável</span>
    @endif
</div>