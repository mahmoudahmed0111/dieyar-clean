@extends('dashboard.layouts.main')

@section('title', __('trans.dashboard'))

@section('content')


<!-- Main Statistics Grid -->
<div class="stats-grid">
    <div class="glass-card stat-card primary">
        <div class="stat-icon-wrapper">
            <i class="fa fa-users fa-2x"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['users'] }}</div>
            <div class="stat-label">{{ __('trans.total_users') }}</div>
        </div>
    </div>
    <div class="glass-card stat-card success">
        <div class="stat-icon-wrapper">
            <i class="fa fa-broom fa-2x"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['cleaners'] }}</div>
            <div class="stat-label">{{ __('trans.cleaners') }}</div>
        </div>
    </div>
    <div class="glass-card stat-card info">
        <div class="stat-icon-wrapper">
            <i class="fa fa-home fa-2x"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['chalets'] }}</div>
            <div class="stat-label">{{ __('trans.chalets') }}</div>
        </div>
    </div>
    <div class="glass-card stat-card warning">
        <div class="stat-icon-wrapper">
            <i class="fa fa-boxes fa-2x"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['inventory'] }}</div>
            <div class="stat-label">{{ __('trans.inventory_items') }}</div>
        </div>
    </div>
    <div class="glass-card stat-card danger">
        <div class="stat-icon-wrapper">
            <i class="fa fa-exclamation-triangle fa-2x"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['damages'] }}</div>
            <div class="stat-label">{{ __('trans.damages') }}</div>
            <div class="stat-change negative">{{ __('trans.pending') }}: {{ $stats['damages_pending'] }} | {{ __('trans.fixed') }}: {{ $stats['damages_fixed'] }}</div>
        </div>
    </div>
    <div class="glass-card stat-card info">
        <div class="stat-icon-wrapper">
            <i class="fa fa-bug fa-2x"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['pest_controls'] }}</div>
            <div class="stat-label">{{ __('trans.pest_controls') }}</div>
        </div>
    </div>
    <div class="glass-card stat-card success">
        <div class="stat-icon-wrapper">
            <i class="fa fa-tools fa-2x"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['maintenance'] }}</div>
            <div class="stat-label">{{ __('trans.maintenance_requests') }}</div>
            <div class="stat-change">{{ __('trans.pending') }}: {{ $stats['maintenance_pending'] }} | {{ __('trans.done') }}: {{ $stats['maintenance_done'] }}</div>
        </div>
    </div>
    <div class="glass-card stat-card primary">
        <div class="stat-icon-wrapper">
            <i class="fa fa-broom fa-2x"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['deep_cleanings'] }}</div>
            <div class="stat-label">{{ __('trans.deep_cleanings') }}</div>
            <div class="stat-change">{{ __('trans.this_month') }}: {{ $stats['deep_cleanings_this_month'] }}</div>
        </div>
    </div>
    <div class="glass-card stat-card info">
        <div class="stat-icon-wrapper">
            <i class="fa fa-broom fa-2x"></i>
        </div>
        <div class="stat-content">
            <div class="stat-number">{{ $stats['regular_cleanings'] }}</div>
            <div class="stat-label">{{ __('trans.regular_cleanings') }}</div>
            <div class="stat-change">{{ __('trans.this_month') }}: {{ $stats['regular_cleanings_this_month'] }}</div>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Actions -->
<div class="dashboard-grid">
    <!-- Recent Activity -->
    <div class="glass-card activity-card">
        <div class="card-header">
                <h3>{{ __('trans.recent_activity') }}</h3>
            <a href="#" class="view-all">{{ __('trans.view_all') }}</a>
        </div>
        <div class="activity-list">
            <div class="activity-item">
                <div class="activity-icon success">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div class="activity-content">
                    <p>{{ __('trans.chalet_no_3_cleaning_completed') }}</p>
                    <span class="activity-time">{{ __('trans.5_minutes_ago') }}</span>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon info">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <div class="activity-content">
                    <p>{{ __('trans.new_user_registered') }}</p>
                    <span class="activity-time">{{ __('trans.15_minutes_ago') }}</span>
                </div>
            </div>
            <div class="activity-item">
                <div class="activity-icon warning">
                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                </div>
                <div class="activity-content">
                    <p>{{ __('trans.new_maintenance_request_for_chalet_no_1') }}</p>
                    <span class="activity-time">{{ __('trans.1_hour_ago') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="glass-card quick-actions-card">
        <div class="card-header">
            <h3>{{ __('trans.quick_actions') }}</h3>
        </div>
        <div class="quick-actions-grid">
            <button class="quick-action-btn">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 4v16m8-8H4"/>
                </svg>
                <span>{{ __('trans.add_user') }}</span>
            </button>
            <button class="quick-action-btn">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <span>{{ __('trans.add_property') }}</span>
            </button>
            <button class="quick-action-btn">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>{{ __('trans.new_report') }}</span>
            </button>
            <button class="quick-action-btn">
                <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>{{ __('trans.request_maintenance') }}</span>
            </button>
        </div>
    </div>
</div>
@endsection
