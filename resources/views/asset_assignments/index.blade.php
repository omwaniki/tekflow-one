<x-app-layout>

<div class="space-y-6">

    <!-- ✅ SUCCESS MESSAGE -->
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- ✅ HEADER -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Asset Assignments</h2>
            <p class="text-gray-500">Track and manage who holds each asset</p>
        </div>

        <a href="{{ route('assignments.create') }}"
        class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            + Assign Asset
        </a>
    </div>

    <!-- ✅ CARD -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        <!-- 🔥 TOOLBAR (MATCH AGENTS STYLE) -->
        <div class="flex items-center justify-between p-4 border-b bg-gray-50">

            <div class="flex items-center gap-3">
                <!-- (Future: bulk return or bulk actions) -->
            </div>

            <input type="text" id="search"
                   placeholder="Search assignments..."
                   class="border rounded-lg px-3 py-2 text-sm w-64">
        </div>

        <!-- TABLE -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Asset</th>
                        <th class="px-6 py-3">Serial</th>
                        <th class="px-6 py-3">Assigned To</th>
                        <th class="px-6 py-3">Campus</th>
                        <th class="px-6 py-3">Assigned Date</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody id="table-body" class="divide-y">

                    @forelse($assignments as $assignment)

                    <tr class="hover:bg-gray-50">

                        <!-- Asset -->
                        <td class="px-6 py-4 font-medium text-gray-800">
                            {{ $assignment->asset->device_type ?? 'Asset' }}
                        </td>

                        <!-- Serial -->
                        <td class="px-6 py-4 text-gray-600">
                            {{ $assignment->asset->serial_number }}
                        </td>

                        <!-- Assigned To -->
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-800">
                                {{ $assignment->assigned_to_name }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $assignment->assigned_to_email }}
                            </div>
                        </td>

                        <!-- Campus -->
                        <td class="px-6 py-4 text-gray-600">
                            {{ $assignment->campus->name ?? '-' }}
                        </td>

                        <!-- Date -->
                        <td class="px-6 py-4 text-gray-600">
                            {{ optional($assignment->assigned_at)->format('d M Y') }}
                        </td>

                        <!-- Status -->
                        <td class="px-6 py-4">
                            @if($assignment->status === 'active')
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                    Active
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                    Returned
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-3">

                                @if($assignment->status === 'active')
                                <form method="POST" action="{{ route('assignments.return', $assignment->id) }}">
                                    @csrf
                                    <button class="text-red-600 hover:underline text-sm">
                                        Return
                                    </button>
                                </form>
                                @endif

                            </div>
                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-400">
                            No assignments found
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>
        </div>

        <!-- PAGINATION -->
        <div id="pagination" class="p-4">
            {{ $assignments->links() }}
        </div>

    </div>

</div>

<!-- 🔥 JS (LIVE SEARCH like agents) -->
<script>
let debounceTimer;

const searchInput = document.getElementById('search');
const tableBody = document.getElementById('table-body');
const pagination = document.getElementById('pagination');

searchInput.addEventListener('keyup', function () {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        fetch(`{{ route('assignments.index') }}?search=${this.value}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            tableBody.innerHTML = data.table;
            pagination.innerHTML = data.pagination;
        });
    }, 300);
});
</script>

</x-app-layout>