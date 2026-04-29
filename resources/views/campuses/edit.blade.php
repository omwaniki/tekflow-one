<x-app-layout>

<div class="max-w-3xl mx-auto px-6 py-8 space-y-6">

    <!-- Header -->
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Edit Campus</h2>
        <p class="text-gray-500">Update campus details</p>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <form method="POST" action="{{ route('campuses.update', $campus->id) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Campus Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Campus Name</label>
                <input type="text" name="name" value="{{ old('name', $campus->name) }}"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500" required>
            </div>

            <!-- Region -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Region</label>
                <select name="region_id"
                    class="w-full rounded-lg border-gray-300 focus:border-gray-500 focus:ring-gray-500" required>

                    <option value="">Select region</option>

                    @foreach($regions as $region)
                        <option value="{{ $region->id }}"
                            {{ $campus->region_id == $region->id ? 'selected' : '' }}>
                            {{ $region->name }}
                        </option>
                    @endforeach

                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">

                <a href="{{ route('campuses.index') }}"
                    class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>

                <button type="submit"
                    class="px-5 py-2 rounded-lg bg-gray-900 text-white hover:bg-black transition">
                    Update Campus
                </button>

            </div>

        </form>

    </div>

</div>

</x-app-layout>