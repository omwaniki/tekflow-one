<x-app-layout>

<div class="space-y-6">

    <!-- ✅ SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- HEADER -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Asset Movements</h2>
            <p class="text-gray-500">Track asset transfers, storage, and reallocations</p>
        </div>

        <a href="{{ route('movements.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            + New Movement
        </a>
    </div>

    <!-- CARD -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <!-- 🔥 TOOLBAR -->
        <div class="flex items-center justify-between p-4 border-b bg-gray-50">

            <!-- FILTER FORM -->
            <form method="GET" class="flex items-center gap-3">

                <!-- TYPE -->
                <select name="movement_type" class="border rounded-lg px-3 py-2 text-sm">
                    <option value="">All Types</option>
                    <option value="transfer" {{ request('movement_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                    <option value="storage_in" {{ request('movement_type') == 'storage_in' ? 'selected' : '' }}>Storage In</option>
                    <option value="storage_out" {{ request('movement_type') == 'storage_out' ? 'selected' : '' }}>Storage Out</option>
                    <option value="loan_out" {{ request('movement_type') == 'loan_out' ? 'selected' : '' }}>Loan Out</option>
                    <option value="loan_return" {{ request('movement_type') == 'loan_return' ? 'selected' : '' }}>Loan Return</option>
                    <option value="reallocation" {{ request('movement_type') == 'reallocation' ? 'selected' : '' }}>Reallocation</option>
                </select>

                <!-- DATE -->
                <input type="date" name="date"
                    value="{{ request('date') }}"
                    class="border rounded-lg px-3 py-2 text-sm">

                <!-- FILTER BUTTON -->
                <button class="bg-gray-800 text-white px-4 py-2 rounded-lg text-sm">
                    Filter
                </button>

                <!-- RESET -->
                <a href="{{ route('movements.index') }}"
                   class="text-sm text-gray-500 hover:underline">
                    Reset
                </a>

            </form>

            <!-- SEARCH (future AJAX) -->
            <input type="text"
                   placeholder="Search movements..."
                   class="border rounded-lg px-3 py-2 text-sm w-64">
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Asset</th>
                        <th class="px-6 py-3">Type</th>
                        <th class="px-6 py-3">From</th>
                        <th class="px-6 py-3">To</th>
                        <th class="px-6 py-3">Reason</th>
                        <th class="px-6 py-3">By</th>
                        <th class="px-6 py-3">Date</th>
                    </tr>
                </thead>

                <tbody class="divide-y">

                    @forelse ($movements as $movement)

                    <tr class="hover:bg-gray-50">

                        <!-- Asset -->
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $movement->asset->serial_number ?? '-' }}
                        </td>

                        <!-- Type -->
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-700">
                                {{ ucfirst(str_replace('_', ' ', $movement->movement_type)) }}
                            </span>
                        </td>

                        <!-- From -->
                        <td class="px-6 py-4 text-gray-600">
                            {{ $movement->fromCampus->name ?? '-' }}
                        </td>

                        <!-- To -->
                        <td class="px-6 py-4 text-gray-600">
                            {{ $movement->toCampus->name ?? '-' }}
                        </td>

                        <!-- Reason -->
                        <td class="px-6 py-4 text-gray-500">
                            {{ $movement->reason ?? '-' }}
                        </td>

                        <!-- By -->
                        <td class="px-6 py-4 text-gray-600">
                            {{ $movement->performedBy->name ?? '-' }}
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 text-gray-500">
                            {{ \Carbon\Carbon::parse($movement->movement_date)->format('d M Y') }}
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-400">
                            No movements recorded yet
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        <!-- PAGINATION -->
        <div class="p-4">
            {{ $movements->links() }}
        </div>

    </div>

</div>

</x-app-layout>