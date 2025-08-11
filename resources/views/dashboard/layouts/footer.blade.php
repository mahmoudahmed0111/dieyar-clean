<footer class="footer">
    {{-- <div class="footer-content">
        <div class="footer-section">
            <h4>{{ __('Quick Info') }}</h4>
            <p>{{ __('Last Update') }}: {{ now()->format('Y-m-d H:i') }}</p>
            <p>{{ __('System Version') }}: v1.0.0</p>
        </div>
        <div class="footer-section">
            <h4>{{ __('Quick Links') }}</h4>
            <a href="#" class="footer-link">{{ __('Technical Support') }}</a>
            <a href="#" class="footer-link">{{ __('User Guide') }}</a>
        </div>
        <div class="footer-section">
            <h4>{{ __('Status') }}</h4>
            <div class="status-item">
                <span class="status-dot online"></span>
                {{ __('Database Connected') }}
            </div>
            <div class="status-item">
                <span class="status-dot online"></span>
                {{ __('Server Running Normal') }}
            </div>
        </div>
    </div> --}}
    <div class="footer-bottom">
        &copy; {{ date('Y') }} {{ $settings->name }}. {{ __('All rights reserved') }}.
        <span class="footer-version">v1.0.0</span>
    </div>
</footer>
