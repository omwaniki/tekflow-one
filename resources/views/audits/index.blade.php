<x-app-layout>

<div class="space-y-6">

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Audits</h2>
            <p class="text-gray-500">Verify assets across campuses</p>
        </div>

        @can('create audits')
        <a href="{{ route('audits.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700">
            + New Audit
        </a>
        @endcan
    </div>

    {{-- MAIN CARD --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

        {{-- TOP BAR --}}
        <div class="flex items-center justify-between p-4 border-b bg-gray-50">

            <div></div>

            <input type="text" id="search"
                   placeholder="Search audits..."
                   class="border rounded-lg px-3 py-2 text-sm w-64">

        </div>

        {{-- TABLE --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">

                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Created</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                {{-- 🔥 IMPORTANT --}}
                <tbody id="table-body" class="divide-y">

                    @forelse($audits as $audit)

                        <tr 
                            @if(!auth()->user()->can('edit audits'))
                                onclick="window.location='{{ route('audits.dashboard', $audit->id) }}'"
                                class="hover:bg-gray-50 cursor-pointer"
                            @else
                                class="hover:bg-gray-50"
                            @endif
                        >

                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $audit->name }}
                            </td>

                            <td class="px-6 py-4 capitalize">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $audit->status === 'active' 
                                        ? 'bg-green-100 text-green-700' 
                                        : 'bg-gray-100 text-gray-700' }}">
                                    {{ $audit->status }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-gray-600">
                                {{ $audit->created_at->format('d M Y') }}
                            </td>

                            <td class="px-6 py-4 text-right">
                                @can('edit audits')

                                    <div class="flex justify-end gap-3">

                                        <a href="{{ route('audits.dashboard', $audit->id) }}"
                                           class="text-blue-600 hover:underline text-sm">
                                            Dashboard
                                        </a>

                                        <a href="{{ route('audits.edit', $audit->id) }}"
                                           class="text-gray-600 hover:underline text-sm">
                                            Edit
                                        </a>

                                        <form action="{{ route('audits.destroy', $audit->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this audit?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="text-red-600 hover:underline text-sm">
                                                Delete
                                            </button>
                                        </form>

                                    </div>

                                @else
                                    <span class="text-blue-600 font-semibold">
                                        View →
                                    </span>
                                @endcan
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-10 text-gray-400">
                                No audits found
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

        {{-- PAGINATION --}}
        <div id="pagination" class="p-4">
            {{ $audits->links() }}
        </div>

    </div>

</div>

{{-- 🔥 LIVE SEARCH SCRIPT --}}
<script>
let debounceTimer;

const searchInput = document.getElementById('search');
const tableBody = document.getElementById('table-body');
const pagination = document.getElementById('pagination');

searchInput.addEventListener('keyup', function () {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        fetch(`{{ route('audits.index') }}?search=${this.value}`, {
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