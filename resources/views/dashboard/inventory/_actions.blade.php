<a href="{{ route('dashboard.inventory.edit', $item) }}" class="btn btn-sm btn-primary" title="{{ __('trans.edit') }}"><i class="fa fa-edit"></i></a>
<form action="{{ route('dashboard.inventory.destroy', $item) }}" method="POST" class="d-inline delete-form" style="display:inline-block">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('trans.delete') }}"><i class="fa fa-trash"></i></button>
</form>
