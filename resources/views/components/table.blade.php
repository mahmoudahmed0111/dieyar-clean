<div class="users-table-card">
    <div class="table-responsive">
        <table class="table users-table">
            <thead>
                <tr>
                    @foreach($columns as $col)
                        <th>{{ $col }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                    <tr>
                        @foreach(array_keys($columns) as $key)
                            <td>{!! $row[$key] ?? '' !!}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) }}" class="text-center">{{ $emptyMessage ?? __('No data found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if(isset($pagination))
        <div class="pagination-wrapper">
            {!! $pagination !!}
        </div>
    @endif
</div>
