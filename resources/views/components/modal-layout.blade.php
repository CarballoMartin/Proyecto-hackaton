@props(['show'])

<div wire:ignore.self>
    @if ($show)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 bg-opacity-75" x-data @click.self="$wire.closeModal()">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl" @click.away="$wire.closeModal()">
                @if (isset($header))
                    <div class="px-6 py-4 border-b">
                        {{ $header }}
                    </div>
                @endif

                {{-- Se quito el formulario y se agrego $body --}}
                @if (isset($body))
                    <div class="px-6 py-4">
                        {{ $body }}
                    </div>
                @endif

                @if (isset($footer))
                    <div class="px-6 py-4 bg-gray-50 text-right space-x-4">
                        {{ $footer }}
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>