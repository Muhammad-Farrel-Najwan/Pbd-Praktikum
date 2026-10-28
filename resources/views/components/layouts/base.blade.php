@props([
    'role' => 'admin',
    'roleLabel' => 'Administrator',
    'title' => 'RedStock',
    'homeRoute' => 'login',
    'navigation' => [],
    'breadcrumb' => null,
    'topbarTitle' => null,
])

@php
    $currentRoute = request()->route()?->getName();

    $isActive = function (array $item) use ($currentRoute): bool {
        if (! $currentRoute || empty($item['active'])) {
            return false;
        }

        foreach ((array) $item['active'] as $pattern) {
            if (request()->routeIs($pattern)) {
                return true;
            }
        }

        return false;
    };
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        document.documentElement.classList.add('js');
    </script>

    <title>{{ $title }} | RedStock</title>

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body @class(['rs-body', 'rs-role-' . $role])>
    <a class="rs-skip-link" href="#rs-main-content">Lewati ke konten utama</a>

    <div class="rs-bg" aria-hidden="true">
        <span class="rs-bg-grid"></span>
        <span class="rs-bg-glow rs-bg-glow-a"></span>
        <span class="rs-bg-glow rs-bg-glow-b"></span>
        <span class="rs-bg-scanlines"></span>
    </div>

    <div class="rs-app">
        <aside class="rs-sidebar" id="rs-sidebar" aria-label="Navigasi {{ $roleLabel }}">
            <a href="{{ route($homeRoute) }}" class="rs-brand" aria-label="RedStock, menuju dashboard {{ $roleLabel }}">
                <span class="rs-brand-mark" aria-hidden="true">R</span>

                <span class="rs-brand-text">
                    <span class="rs-brand-name">RED<span>STOCK</span></span>
                    <span class="rs-brand-caption">{{ $roleLabel }} // Inventory OS</span>
                </span>
            </a>

            <nav class="rs-nav" aria-label="Menu utama {{ $roleLabel }}">
                @foreach ($navigation as $item)
                    @if (! empty($item['section']))
                        <p class="rs-nav-section">{{ $item['section'] }}</p>
                    @else
                        @php
                            $active = $isActive($item);
                            $disabled = $item['disabled'] ?? false;
                            $href = '#';

                            if (! $disabled) {
                                if (! empty($item['route']) && \Illuminate\Support\Facades\Route::has($item['route'])) {
                                    $href = route($item['route']);
                                } elseif (! empty($item['url'])) {
                                    $href = $item['url'];
                                }
                            }
                        @endphp

                        @if ($disabled)
                            <span class="rs-nav-link is-disabled" aria-disabled="true" title="{{ $item['label'] }} belum tersedia">
                                <x-ui.icon :name="$item['icon'] ?? 'circle'" class="rs-nav-icon" />

                                <span class="rs-nav-label">{{ $item['label'] }}</span>

                                @if (! empty($item['badge']))
                                    <span class="rs-nav-badge">{{ $item['badge'] }}</span>
                                @endif
                            </span>
                        @else
                            <a href="{{ $href }}"
                                @class(['rs-nav-link', 'is-active' => $active])
                                @if ($active) aria-current="page" @endif>
                                <x-ui.icon :name="$item['icon'] ?? 'circle'" class="rs-nav-icon" />

                                <span class="rs-nav-label">{{ $item['label'] }}</span>

                                @if (! empty($item['badge']))
                                    <span class="rs-nav-badge">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        @endif
                    @endif
                @endforeach
            </nav>

            <div class="rs-sidebar-footer">
                <div class="rs-account">
                    <span class="rs-avatar" aria-hidden="true">
                        {{ strtoupper(substr(session('username', 'U'), 0, 1)) }}
                    </span>

                    <span class="rs-account-text">
                        <span class="rs-account-name">{{ session('username', 'User') }}</span>
                        <span class="rs-account-role">{{ $roleLabel }}</span>
                    </span>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <button type="submit" class="rs-logout">
                        <x-ui.icon name="logout" :size="16" />
                        Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="rs-scrim" id="rs-scrim" aria-hidden="true"></div>

        <div class="rs-main">
            <header class="rs-topbar">
                <div class="rs-topbar-left">
                    <button type="button" class="rs-icon-btn rs-menu-toggle" id="rs-menu-toggle"
                        aria-label="Buka menu" aria-controls="rs-sidebar" aria-expanded="false">
                        <x-ui.icon name="menu" />
                    </button>

                    <div>
                        <nav class="rs-breadcrumb" aria-label="Breadcrumb">
                            @if ($breadcrumb)
                                {!! $breadcrumb !!}
                            @else
                                RedStock / <span>{{ $roleLabel }}</span>
                            @endif
                        </nav>

                        <div class="rs-topbar-title">{{ $topbarTitle ?? $roleLabel }}</div>
                    </div>
                </div>

                <div class="rs-topbar-right">
                    <span class="rs-status-chip">
                        <span class="rs-status-dot" aria-hidden="true"></span>
                        System Online
                    </span>

                    <span class="rs-avatar rs-avatar-sm" aria-hidden="true">
                        {{ strtoupper(substr(session('username', 'U'), 0, 1)) }}
                    </span>
                </div>
            </header>

            <main class="rs-content" id="rs-main-content">
                {{ $slot }}

                <footer class="rs-footer">
                    <span>&copy; {{ now()->year }} RedStock</span>
                    <span>Neon Core // Inventory OS</span>
                </footer>
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
