<nav class="text-sm text-gray-500 mb-4">

    <ol class="flex items-center space-x-2">

        @foreach ($links as $link)

            <li class="flex items-center">

                @if (!$loop->first)
                    <span class="mx-2 text-gray-400">/</span>
                @endif

                @if (isset($link['url']))
                    <a href="{{ $link['url'] }}"
                       class="hover:text-gray-700">
                        {{ $link['label'] }}
                    </a>
                @else
                    <span class="text-gray-700 font-medium">
                        {{ $link['label'] }}
                    </span>
                @endif

            </li>

        @endforeach

    </ol>

</nav>