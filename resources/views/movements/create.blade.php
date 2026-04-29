<x-app-layout>

<div class="space-y-6">

    <!-- HEADER -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Move Asset</h2>
        <p class="text-gray-500">Transfer, store, or reallocate an asset</p>
    </div>

    <!-- CARD -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <form method="POST" action="{{ route('movements.store') }}">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- ASSET -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Asset</label>
                    <select name="asset_id" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500">

                        <option value="">Select Asset</option>

                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}"
                                data-campus="{{ $asset->campus->name ?? '' }}">
                                {{ $asset->serial_number }} 
                                ({{ $asset->campus->name ?? 'No Campus' }})
                            </option>
                        @endforeach

                    </select>
                </div>

                <!-- FROM CAMPUS (AUTO FILLED) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Campus</label>
                    <input type="text" id="fromCampus"
                        class="w-full border-gray-300 rounded-lg bg-gray-100 text-gray-600"
                        placeholder="Auto-filled"
                        readonly>
                </div>

                <!-- MOVEMENT TYPE -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Movement Type</label>
                    <select name="movement_type" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500">

                        <option value="">Select Type</option>
                        <option value="transfer">Transfer</option>
                        <option value="storage_in">Storage In</option>
                        <option value="storage_out">Storage Out</option>
                        <option value="loan_out">Loan Out</option>
                        <option value="loan_return">Loan Return</option>
                        <option value="reallocation">Reallocation</option>

                    </select>
                </div>

                <!-- TO CAMPUS -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Campus</label>
                    <select name="to_campus_id" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500">

                        <option value="">Select Destination</option>

                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">
                                {{ $campus->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <!-- DATE -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Movement Date</label>
                    <input type="date" name="movement_date" required
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500">
                </div>

                <!-- REASON -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason / Notes</label>
                    <textarea name="reason" rows="3"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500"></textarea>
                </div>

            </div>

            <!-- ACTIONS -->
            <div class="mt-6 flex justify-end gap-3">

                <a href="{{ route('movements.index') }}"
                   class="px-4 py-2 border rounded-lg text-sm text-gray-600 hover:bg-gray-100">
                    Cancel
                </a>

                <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm hover:bg-blue-700">
                    Save Movement
                </button>

            </div>

        </form>

    </div>

</div>

<!-- 🔥 AUTO-FILL FROM CAMPUS -->
<script>
const assetSelect = document.querySelector('select[name="asset_id"]');
const fromCampusInput = document.getElementById('fromCampus');

assetSelect.addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    const campus = selected.getAttribute('data-campus');

    fromCampusInput.value = campus || 'N/A';
});
</script>

</x-app-layout>