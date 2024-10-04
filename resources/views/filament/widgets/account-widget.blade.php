@php
    $user = filament()->auth()->user();
@endphp

<x-filament-widgets::widget class="fi-account-widget">
    <x-filament::section class="shadow-xl">
        <div class="flex items-center gap-x-3">

            <div class="flex-1 space-y-8"> <!-- Menambahkan space-y-2 untuk jarak -->
                <h2 class="flex text-xl flex-1 font-semibold leading-6 text-gray-950 dark:text-white">
                    <span class="text-blue-600">Selamat Datang</span>
                    <img src="{{ asset('images/icon/smiley-icon.svg') }}" class="ml-2 w-5 h-5" alt="">
                </h2>

                <p class="text-5xl font-semibold text-gray-500 dark:text-gray-400">
                    @if (auth()->user()->isAdmin())
                        Administrator
                    @else
                        {{ filament()->getUserName($user) }}
                    @endif
                </p>
            </div>

            @if (auth()->user()->isAdmin())
                <img src="{{ asset('images/icon/icon-dashboard-admin.png') }}" alt="">
            @else
                <img src="{{ asset('images/icon/icon-dashboard-user.png') }}" alt="">
            @endif
            {{-- <form
                action="{{ filament()->getLogoutUrl() }}"
                method="post"
                class="my-auto"
            >
                @csrf

                <x-filament::button
                    color="gray"
                    icon="heroicon-m-arrow-left-on-rectangle"
                    icon-alias="panels::widgets.account.logout-button"
                    labeled-from="sm"
                    tag="button"
                    type="submit"
                >
                    {{ __('filament-panels::widgets/account-widget.actions.logout.label') }}
                </x-filament::button>
            </form> --}}
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
