<div>
    <div>
        @if ($paginator->hasPages())

        <ul class="flex justify-between">

            {{-- Prev --}}
            {{-- @dump($paginator->onFirstPage()) --}}

            @if ($paginator->onFirstPage())
            <li class="w-16 px-2 py-1 text-center rounded border bg-gray-200">Prev</li>
            @else
            <li class="w-16 px-2 py-1 text-center rounded border shadow bg-white cursor-pointer" wire:click="previousPage">Prev</li>
            @endif
            {{-- Prev End --}}


            {{-- Numbers --}}
            @foreach ($elements as $element)
            <div class="flex">
                @if (is_array($element))
                @foreach ($element as $page => $url)
                @if ($page == $paginator->currentPage())
                <li class="mx-2 w-10 px-2 py-1 text-center rounded border shadow bg-blue-500 text-white cursor-pointer" wire:click="gotoPage({{ $page }})">
                    {{ $page }}
                </li>
                @else
                <li class="mx-2 w-10 px-2 py-1 text-center rounded border shadow bg-white cursor-pointer" wire:click="gotoPage({{ $page }})">
                    {{ $page }}
                </li>
                @endif
                @endforeach
                @endif
            </div>
            @endforeach
            {{-- Numbers End --}}


            {{-- Next --}}
            @if ($paginator->hasMorePages())
            <li class="w-16 px-2 py-1 text-center rounded border shadow bg-white cursor-pointer" wire:click="nextPage">Next</li>
            @else
            <li class="w-16 px-2 py-1 text-center rounded border bg-gray-200">Next</li>
            @endif
            {{-- Next End --}}

        </ul>

        @endif
    </div>
</div>