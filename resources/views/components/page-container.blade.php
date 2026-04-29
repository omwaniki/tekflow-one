<div class="max-w-6xl mx-auto px-8 pt-10 pb-14">

    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-800">
            {{ $title }}
        </h1>

        @isset($subtitle)
        <p class="text-gray-500 mt-1">
            {{ $subtitle }}
        </p>
        @endisset
    </div>

    {{ $slot }}

</div>