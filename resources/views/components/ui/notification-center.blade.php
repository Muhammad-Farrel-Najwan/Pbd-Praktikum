@props([
    'groups' => [],
    'enabled' => false,
])

@php
    $total = collect($groups)->sum('count');
@endphp

@if ($enabled)
    <div class="rs-notif" data-notification-center>
        <button
            type="button"
            class="rs-icon-btn rs-notif-toggle"
            data-notification-toggle
            aria-label="Notifikasi{{ $total > 0 ? ', ' . $total . ' perlu tindakan' : '' }}"
            aria-controls="rs-notification-panel"
            aria-expanded="false"
        >
            <x-ui.icon name="bell" />

            @if ($total > 0)
                <span class="rs-notif-badge" aria-hidden="true">{{ $total > 99 ? '99+' : $total }}</span>
            @endif
        </button>

        <div
            class="rs-notif-panel"
            id="rs-notification-panel"
            data-notification-panel
            aria-label="Daftar notifikasi"
            hidden
        >
            <div class="rs-notif-head">
                <div>
                    <strong>Notifikasi</strong>
                    <span>{{ $total > 0 ? $total . ' perlu tindakan' : 'Tidak ada tindakan tertunda' }}</span>
                </div>
            </div>

            @if (count($groups) > 0)
                <ul class="rs-notif-list">
                    @foreach ($groups as $group)
                        <li>
                            <a href="{{ $group['url'] }}" class="rs-notif-item">
                                <span @class(['rs-notif-icon', 'is-' . $group['tone']])>
                                    <x-ui.icon :name="$group['icon']" />
                                </span>

                                <span class="rs-notif-body">
                                    <span class="rs-notif-title">{{ $group['title'] }}</span>
                                    <span class="rs-notif-desc">{{ $group['description'] }}</span>
                                </span>

                                <span class="rs-notif-count">{{ $group['count'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="rs-notif-empty">
                    <x-ui.icon name="check" />
                    <span>Semua proses sudah beres.</span>
                </div>
            @endif
        </div>
    </div>
@endif
