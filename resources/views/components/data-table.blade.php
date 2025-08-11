@props([
    'headers' => [],
    'rows' => [],
    'actions' => true,
    'searchable' => true,
    'filterable' => true,
    'pagination' => null,
    'emptyMessage' => 'لا توجد بيانات',
    'tableId' => 'data-table'
])

<div class="data-table-container">
    @if($searchable || $filterable)
    <div class="table-controls">
        @if($searchable)
        <div class="search-wrapper">
            <div class="search-input-wrapper">
                <svg class="search-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                    <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" class="search-input" placeholder="{{ __('Search...') }}" id="search-{{ $tableId }}">
            </div>
        </div>
        @endif

        @if($filterable)
        <div class="filters-wrapper">
            <select class="filter-select" id="filter-{{ $tableId }}">
                <option value="">{{ __('All') }}</option>
                <option value="active">{{ __('Active') }}</option>
                <option value="inactive">{{ __('Inactive') }}</option>
            </select>
        </div>
        @endif
    </div>
    @endif

    <div class="table-responsive">
        <table class="data-table" id="{{ $tableId }}">
            <thead>
                <tr>
                    @foreach($headers as $header)
                    <th>{{ $header }}</th>
                    @endforeach
                    @if($actions)
                    <th class="actions-header">{{ __('Actions') }}</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $row)
                <tr class="data-row" data-id="{{ $row['id'] ?? '' }}">
                    @foreach($headers as $key => $header)
                    <td class="data-cell" data-label="{{ $header }}">
                        @if(isset($row[$key]))
                            @if($key === 'avatar')
                                <div class="cell-avatar">
                                    <img src="{{ $row[$key] }}" alt="Avatar" class="avatar-img">
                                </div>
                            @elseif($key === 'status')
                                <span class="status-badge status-{{ $row[$key] ? 'success' : 'danger' }}">
                                    {{ $row[$key] ? __('Active') : __('Inactive') }}
                                </span>
                            @elseif($key === 'role')
                                <span class="role-badge role-{{ $row[$key] }}">
                                    {{ __(ucfirst($row[$key])) }}
                                </span>
                            @elseif($key === 'email')
                                <div class="email-cell">
                                    <span class="email-text">{{ $row[$key] }}</span>
                                    @if(isset($row['email_verified_at']))
                                    <svg class="verified-badge" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    @endif
                                </div>
                            @elseif($key === 'created_at')
                                <div class="date-cell">
                                    <div class="date-text">{{ $row[$key]->format('M d, Y') }}</div>
                                    <div class="time-text">{{ $row[$key]->format('H:i') }}</div>
                                </div>
                            @else
                                <span class="cell-text">{{ $row[$key] }}</span>
                            @endif
                        @endif
                    </td>
                    @endforeach

                    @if($actions)
                    <td class="actions-cell">
                        <div class="actions-dropdown">
                            <button class="actions-toggle" onclick="toggleActions('{{ $tableId }}-{{ $row['id'] ?? $loop->index }}')">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                    <path d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>
                            <div class="actions-menu" id="actions-{{ $tableId }}-{{ $row['id'] ?? $loop->index }}">
                                @if(isset($row['edit_url']))
                                <a href="{{ $row['edit_url'] }}" class="action-item">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                        <path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    {{ __('Edit') }}
                                </a>
                                @endif

                                @if(isset($row['toggle_url']))
                                <form action="{{ $row['toggle_url'] }}" method="POST" class="action-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-item">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                            <path d="M8 7V3a4 4 0 118 0v4m-4 6v6m-4-6h8m-8 6h8"/>
                                        </svg>
                                        {{ $row['is_active'] ? __('Deactivate') : __('Activate') }}
                                    </button>
                                </form>
                                @endif

                                @if(isset($row['delete_url']))
                                <form action="{{ $row['delete_url'] }}" method="POST" class="action-form" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                                                                <button type="submit" class="action-item text-danger">
                                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                {{ __('Delete') }}
                                            </button>
                                </form>
                                @endif
                            </div>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="empty-state">
                        <div class="empty-content">
                            <svg class="empty-icon" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="48" height="48">
                                <path d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                            </svg>
                            <h3>{{ __('No Data Found') }}</h3>
                            <p>{{ $emptyMessage }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pagination)
    <div class="pagination-wrapper">
        {{ $pagination }}
    </div>
    @endif
</div>

<script>
function toggleActions(menuId) {
    // إغلاق جميع القوائم المفتوحة
    document.querySelectorAll('.actions-menu.show').forEach(menu => {
        if (menu.id !== 'actions-' + menuId) {
            menu.classList.remove('show');
        }
    });

    // تبديل حالة القائمة الحالية
    const menu = document.getElementById('actions-' + menuId);
    menu.classList.toggle('show');
}

// إغلاق القوائم عند النقر خارجها
document.addEventListener('click', function(event) {
    if (!event.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// البحث في الجدول
document.getElementById('search-{{ $tableId }}')?.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('#{{ $tableId }} tbody tr');

    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});

// فلترة الجدول
document.getElementById('filter-{{ $tableId }}')?.addEventListener('change', function() {
    const filterValue = this.value;
    const rows = document.querySelectorAll('#{{ $tableId }} tbody tr');

    rows.forEach(row => {
        if (!filterValue) {
            row.style.display = '';
            return;
        }

        const statusCell = row.querySelector('.status-badge');
        if (statusCell) {
            const status = statusCell.textContent.toLowerCase();
            const isActive = status.includes('active');

            if (filterValue === 'active' && isActive) {
                row.style.display = '';
            } else if (filterValue === 'inactive' && !isActive) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
});
</script>
