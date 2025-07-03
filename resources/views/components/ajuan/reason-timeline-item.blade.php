@props([
    'icon' => null,
    'description' => '',
    'status' => '',
    'dateText' => '',
    'createdBy' => '',
    'last' => false,
])

<li>
    <div class="relative pb-8">
        @unless ($last)
            <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
        @endunless
        <div class="relative flex space-x-3">
            <div>
                <span class="flex size-8 items-center justify-center rounded-full bg-gray-500 ring-8 ring-white">
                    {!! $icon !!}
                </span>
            </div>
            <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                <div>
                    <p class="text-sm text-gray-500">{{ $description }}</p>
                </div>
                <div class="whitespace-nowrap text-right text-sm text-gray-500">
                    <span class="mr-2">{{ $createdBy }}</span>
                    <p class="mr-2">{{ $status }}</p>
                    <time>{{ $dateText }}</time>
                </div>
            </div>
        </div>
    </div>
</li>
