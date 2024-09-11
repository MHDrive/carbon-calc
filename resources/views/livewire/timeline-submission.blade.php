@php
    $media = $media ?? false;
    $pending = $pending ?? false;
    $diterima = $diterima ?? false;
    $ditolak = $ditolak ?? false;
@endphp
<div class="container mx-auto">
    <div class="flex justify-evenly items-center px-20">
        <div class="text-center">
            <div
                class="w-10 h-10 rounded-full {{ $media ? 'bg-success-300' : 'bg-gray-300' }} mx-auto flex items-center justify-center">
                @if ($media)
                    <x-heroicon-o-check-circle class="w-8 h-8" />
                @else
                    <x-hugeicons-file-add class="w-8 h-8" />
                @endif
            </div>
            <p class="mt-2 whitespace-nowrap">Upload Berkas</p>
        </div>

        <div class="w-72 h-7 border-t-2 {{ $media ? 'border-success-300' : 'border-gray-300' }}">
        </div>

        <div class="text-center">
            <div
                class="w-10 h-10 rounded-full {{ $pending ? 'bg-warning-300' : ($diterima ? 'bg-success-300' : ($ditolak ? 'bg-red-300' : 'bg-gray-300')) }} mx-auto flex items-center justify-center">
                @if ($ditolak)
                    <x-heroicon-o-x-circle class="w-8 h-8" />
                @elseif ($diterima)
                    <x-heroicon-o-check-circle class="w-8 h-8" />
                @else
                    <x-hugeicons-file-sync class="w-8 h-8" />
                @endif
            </div>
            <p class="mt-2 whitespace-nowrap">Menunggu Verifikasi</p>
        </div>

        <div
            class="w-72 h-7 border-t-2 {{ $diterima ? 'border-success-300' : ($ditolak ? 'border-red-300' : 'border-gray-300') }}">
        </div>

        <div class="text-center">
            <div
                class="w-10 h-10 rounded-full {{ $diterima ? 'bg-success-300' : ($ditolak ? 'bg-red-300' : 'bg-gray-300') }} mx-auto flex items-center justify-center">
                @if ($diterima)
                    <x-heroicon-o-check-circle class="w-8 h-8" />
                @elseif ($ditolak)
                    <x-heroicon-o-x-circle class="w-8 h-8" />
                @else
                    <x-hugeicons-file-view class="w-8 h-8" />
                @endif
            </div>
            <p class="mt-2 whitespace-nowrap">Ditolak/Diterima</p>
        </div>
    </div>
</div>
