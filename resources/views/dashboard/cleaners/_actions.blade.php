<a href="{{ route('dashboard.cleaners.edit', $cleaner) }}" class="btn btn-sm btn-primary" title="{{ __('trans.edit') }}"><i class="fas fa-edit"></i></a>
<form action="{{ route('dashboard.cleaners.destroy', $cleaner) }}" method="POST" class="d-inline delete-form" style="display:inline-block">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-sm btn-danger" title="{{ __('trans.delete') }}"><i class="fas fa-trash"></i></button>
</form>
<form action="{{ route('dashboard.cleaners.toggle-status', $cleaner) }}" method="POST" class="d-inline" style="display:inline-block">
    @csrf
    @method('PATCH')
    <button type="submit" class="btn btn-sm btn-warning" title="{{ $cleaner->status === 'active' ? __('trans.deactivate') : __('trans.activate') }}">
        <i class="fas fa-{{ $cleaner->status === 'active' ? 'times' : 'check' }}"></i>
    </button>
</form>
