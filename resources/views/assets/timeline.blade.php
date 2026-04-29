<x-app-layout>

<div class="space-y-6">

    <!-- HEADER -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            Asset Timeline — {{ $asset->serial_number }}
        </h2>
        <p class="text-gray-500">Full lifecycle of this asset</p>
    </div>

    <!-- CARD -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <div class="relative border-l-2 border-gray-200 ml-3">

            @forelse($timeline as $event)

                <div class="mb-6 ml-4">

                    <!-- DOT -->
                    <div class="absolute -left-2.5 w-4 h-4 rounded-full
                        {{ $event['color'] == 'purple' ? 'bg-purple-500' : 'bg-blue-500' }}">
                    </div>

                    <!-- CONTENT -->
                    <div class="bg-gray-50 rounded-lg px-4 py-3">

                        <div class="text-sm font-medium text-gray-800">
                            {{ $event['label'] }}
                        </div>

                        <div class="text-xs text-gray-500 mt-1">
                            {{ $event['meta'] }} • 
                            {{ \Carbon\Carbon::parse($event['date'])->format('d M Y') }}
                        </div>

                    </div>

                </div>

            @empty

                <div class="text-gray-400 text-sm">
                    No timeline history yet
                </div>

            @endforelse

        </div>

    </div>

</div>

</x-app-layout>