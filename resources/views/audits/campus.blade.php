<x-app-layout>

<div class="max-w-7xl mx-auto px-8 pt-10 pb-12">
    <div class="mb-6 text-sm text-gray-500 flex items-center gap-2">
        <a href="{{ route('audits.index') }}">Audits</a>
        <span>/</span>
        <a href="{{ route('audits.dashboard', $audit->id) }}">
            {{ $audit->name }}
        </a>
        <span>/</span>
        <span class="text-gray-800 font-medium">
            {{ $campus->name }}
        </span>
    </div>

    <!-- HEADER -->
    <div class="mb-8">
        <h1 class="text-3xl font-semibold text-gray-800">
            {{ $campus->name }} — Audit Overview
        </h1>
        <p class="text-gray-500 mt-1">
            {{ $audit->name }}
        </p>
    </div>

    <!-- SUMMARY CARDS -->
    <div class="grid grid-cols-4 gap-6 mb-8">

        <!-- Total -->
        <div class="bg-white p-6 rounded-xl border">
            <p class="text-sm text-gray-500">Total Assets</p>
            <p class="text-2xl font-semibold mt-2">{{ $total }}</p>
        </div>

        <!-- Verified -->
        <div class="bg-white p-6 rounded-xl border">
            <p class="text-sm text-gray-500">Verified</p>
            <p class="text-2xl font-semibold mt-2 text-green-600">
                {{ $verified }}
            </p>
        </div>

        <!-- Missing -->
        <div class="bg-white p-6 rounded-xl border">
            <p class="text-sm text-gray-500">Missing</p>
            <p class="text-2xl font-semibold mt-2 text-red-600">
                {{ $missing }}
            </p>
        </div>

        <!-- Progress -->
        <div class="bg-white p-6 rounded-xl border">
            <p class="text-sm text-gray-500">Progress</p>
            <p class="text-2xl font-semibold mt-2">
                {{ $progress }}%
            </p>

            @php
                $color = match(true) {
                    $progress == 100 => 'bg-green-500',
                    $progress > 0 => 'bg-amber-500',
                    default => 'bg-red-500',
                };
            @endphp

            <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                <div class="{{ $color }} h-2 rounded-full transition-all duration-500"
                    style="width: {{ $progress > 0 ? max($progress, 2) : 2 }}%"></div>
            </div>
        </div>

    </div>

    <!-- ACTIONS -->
    <div class="flex gap-4 mb-8">

        <a href="{{ route('audits.verify', $audit->id) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Go to Verification
        </a>

        <button class="px-4 py-2 bg-gray-200 rounded-lg">
            Export (Coming Soon)
        </button>

    </div>

    <!-- VERIFICATION TABLE -->
    <div class="bg-white rounded-xl border">

        <div class="p-6 border-b">
            <h2 class="text-lg font-semibold text-gray-800">
                Asset Verification
            </h2>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-left">
                <tr>
                    <th class="p-4">Asset</th>
                    <th class="p-4">Serial</th>
                    <th class="p-4">Status</th>
                </tr>
            </thead>

            <tbody>
                @foreach($records as $record)
                <tr class="border-t">
                    <td class="p-4">
                        {{ trim(($record->asset->brand ?? '') . ' ' . ($record->asset->model ?? '')) ?: ($record->asset->device_type ?? '—') }}
                    </td>

                    <td class="p-4">
                        {{ $record->asset->serial_number ?? '—' }}
                    </td>

                    <td class="p-4">
                        @if(is_null($record->found))
                            <span class="text-gray-400">Pending</span>
                        @elseif($record->found)
                            <span class="text-green-600 font-medium">Found</span>
                        @else
                            <span class="text-red-600 font-medium">Missing</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</div>

</x-app-layout>