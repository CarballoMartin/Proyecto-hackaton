@props(['style' => session('flash.bannerStyle', 'success'), 'message' => session('flash.banner')])

<div x-data="{
    show: false,
    style: '{{ $style }}',
    message: '{{ $message }}',
    timer: null
}" x-init="
        if (message) {
            show = true;
            timer = setTimeout(() => { show = false; message = null; }, 3000);
        }
        $watch('message', (newMessage) => {
            if (newMessage) {
                show = true;
                clearTimeout(timer);
                timer = setTimeout(() => { show = false; message = null; }, 3000);
            }
        });
    " class="fixed top-0 left-0 right-0 z-50 flex justify-center pt-2" x-show="show && message"
    x-on:banner-message.window="
        style = event.detail.style;
        message = event.detail.message;
    " x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-full"
    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-full"
    style="z-index: 100;"
    x-cloak
    wire:ignore.self>

    <div :class="{ 'bg-indigo-500': style == 'success', 'bg-red-700': style == 'danger', 'bg-yellow-500': style == 'warning', 'bg-gray-500': style != 'success' && style != 'danger' && style != 'warning'}"
        class="rounded-lg shadow-lg max-w-md w-full mx-2">
        <div class="px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <span class="flex p-2 rounded-lg"
                        :class="{ 'bg-indigo-600': style == 'success', 'bg-red-600': style == 'danger', 'bg-yellow-600': style == 'warning' }">
                        <svg x-show="style == 'success'" class="size-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg x-show="style == 'danger'" class="size-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        <svg x-show="style != 'success' && style != 'danger' && style != 'warning'"
                            class="size-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        <svg x-show="style == 'warning'" class="size-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.5" fill="none" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4v.01 0 0 " />
                        </svg>
                    </span>

                    <p class="ms-3 font-medium text-sm text-white truncate" x-text="message"></p>
                </div>

                <button type="button" class="-me-1 flex p-2 rounded-md focus:outline-none transition"
                    :class="{ 'hover:bg-indigo-600 focus:bg-indigo-600': style == 'success', 'hover:bg-red-600 focus:bg-red-600': style == 'danger', 'hover:bg-yellow-600 focus:bg-yellow-600': style == 'warning'}"
                    aria-label="Dismiss" x-on:click="show = false">
                    <svg class="size-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>