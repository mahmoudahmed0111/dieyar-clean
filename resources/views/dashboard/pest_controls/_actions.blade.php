<a href="{{ route('dashboard.pest_controls.edit', $row) }}" class="btn btn-sm btn-primary" title="{{ __('trans.edit') }}"><i class="fa fa-edit"></i></a>
<form action="{{ route('dashboard.pest_controls.destroy', $row) }}" method="POST" class="d-inline delete-form" style="display:inline-block">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('trans.delete') }}"><i class="fa fa-trash"></i></button>
</form>
