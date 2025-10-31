@props(['header' => null])

<x-layouts.app>
    @if ($header)
        <div class="my-4 mx-2">
            {{ $header }}
        </div>
    @endif

    <div>
        {{ $slot }}
    </div>
</x-layouts.app>
