<a href="{{ route('dashboard.users.edit', $user) }}" class="btn btn-sm btn-primary" title="{{ __('trans.edit') }}"><i class="fa fa-edit"></i></a>
@if($user->id !== auth()->id())
    <form action="{{ route('dashboard.users.destroy', $user) }}" method="POST" class="d-inline delete-form" style="display:inline-block">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="{{ __('trans.delete') }}"><i class="fa fa-trash"></i></button>
    </form>
@endif
