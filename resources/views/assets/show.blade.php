<x-app-layout>

<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">
                Device Details
            </h2>
            <p class="text-gray-500">
                View full device information and history
            </p>
        </div>

        <a href="{{ route('assets.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900">
            ← Back to Assets
        </a>
    </div>

    {{-- DEVICE OVERVIEW --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <div class="flex items-center justify-between">

            <div>
                <h3 class="text-xl font-semibold text-gray-800">
                    {{ $asset->device_type ?? 'Device' }}
                </h3>

                <p class="text-gray-500 text-sm mt-1">
                    Serial: {{ $asset->serial_number ?? '—' }}
                </p>
            </div>

            {{-- STATUS --}}
            <div>
                @if($asset->status === 'active')
                    <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">
                        Active
                    </span>
                @elseif($asset->status === 'faulty')
                    <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-700">
                        Faulty
                    </span>
                @else
                    <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-700">
                        Retired
                    </span>
                @endif
            </div>

        </div>

        {{-- QUICK DETAILS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 text-sm">

            <div>
                <p class="text-gray-500">Campus</p>
                <p class="font-medium text-gray-800">
                    {{ $asset->campus->name ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Assigned To</p>
                <p class="font-medium text-gray-800 flex items-center gap-2">
                    {{ optional($asset->currentAssignment)->assigned_to_name 
                        ?? $asset->assigned_to_name 
                        ?? '—' }}

                    @if($asset->currentAssignment)
                        <span class="px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">
                            Assigned
                        </span>
                    @else
                        <span class="px-2 py-0.5 text-xs rounded-full bg-gray-100 text-gray-600">
                            Unassigned
                        </span>
                    @endif

                </p>
            </div>

            <div>
                <p class="text-gray-500">Email</p>
                <p class="font-medium text-gray-800">
                    {{ optional($asset->currentAssignment)->assigned_to_email 
                        ?? $asset->assigned_to_email 
                        ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Role</p>
                <p class="font-medium text-gray-800">
                    {{ $asset->role ?? '—' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Manufacture Date</p>
                <p class="font-medium text-gray-800">
                    {{ $asset->manufacture_date 
                        ? \Carbon\Carbon::parse($asset->manufacture_date)->format('Y-m-d') 
                        : '—' }}
                </p>
            </div>

            <div>
                <p class="text-gray-500">Device Age</p>
                <p class="font-medium text-gray-800">
                    {{ $asset->age ?? 0 }} years
                </p>
            </div>

        </div>

    </div>

    {{-- 🔥 ASSIGNMENT HISTORY CARD --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            Assignment History
        </h3>

        <!-- 🔥 ASSET TIMELINE -->
        <div class="mt-6">

            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                Asset Timeline
            </h3>

            <div class="relative border-l-2 border-gray-200 ml-4 pl-4">

                @foreach($timeline as $event)

                <div class="mb-6 ml-2 flex gap-3">

                    <!-- ICON -->
                    <div class="mt-1">
                        @if($event['type'] === 'movement')
                            <div class="w-6 h-6 rounded-full bg-purple-100 flex items-center justify-center">
                                📍
                            </div>
                        @else
                            <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center">
                                👤
                            </div>
                        @endif
                    </div>

                    <!-- CONTENT -->
                    <div class="flex-1 bg-gray-50 rounded-xl px-4 py-3">

                        <div class="text-sm font-semibold text-gray-800">
                            {{ $event['label'] }}
                        </div>

                        <div class="text-xs text-gray-500 mt-1 flex items-center gap-2">

                            <span class="px-2 py-0.5 rounded-full text-[11px]
                                {{ $event['type'] === 'movement' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $event['meta'] }}
                            </span>

                            <span>
                                {{ \Carbon\Carbon::parse($event['date'])->format('d M Y') }}
                            </span>

                        </div>

                    </div>

                </div>

                @endforeach

            </div>

        </div>

        <div class="bg-gray-50 rounded-xl border border-gray-200">

            @forelse($asset->assignments as $assignment)

            <div class="flex items-center justify-between px-5 py-4 border-b last:border-0">

                <!-- LEFT -->
                <div>
                    <div class="text-sm font-medium text-gray-800">
                        {{ $assignment->assigned_to_name }}
                    </div>

                    <div class="text-xs text-gray-500">
                        {{ $assignment->assigned_to_email }}
                    </div>

                    <div class="text-xs text-gray-400 mt-1">
                        Assigned:
                        {{ optional($assignment->assigned_at)->format('d M Y') }}
                    </div>

                    @if($assignment->returned_at)
                    <div class="text-xs text-gray-400">
                        Returned:
                        {{ optional($assignment->returned_at)->format('d M Y') }}
                    </div>
                    @endif
                </div>

                <!-- RIGHT -->
                <div class="text-right">
                    @if($assignment->status === 'active')
                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                            Active
                        </span>
                    @else
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                            Returned
                        </span>
                    @endif
                </div>

            </div>

            @empty

            <div class="text-center py-6 text-gray-400 text-sm">
                No assignment history
            </div>

            @endforelse

        </div>

    </div>

    {{-- ACTIONS --}}
    <div class="flex gap-3">

        <a href="{{ route('assets.edit', $asset->id) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
            Edit Device
        </a>

        <form method="POST" action="{{ route('assets.destroy', $asset->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit"
                    onclick="return confirm('Delete this device?')"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                Delete
            </button>
        </form>

    </div>

</div>

</x-app-layout>