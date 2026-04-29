<x-app-layout>

<div class="space-y-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Assign Asset</h2>
            <p class="text-gray-500">Assign an asset to a user</p>
        </div>
    </div>

    <!-- CARD -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">

        <form method="POST" action="{{ route('assignments.store') }}">
            @csrf

            <!-- 🔹 Asset Selection -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Asset</label>
                <select name="asset_id"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option>Select Asset</option>
                    @foreach($assets as $asset)
                        <option value="{{ $asset->id }}">
                            {{ $asset->serial_number }} - {{ $asset->model }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 🔹 Assignment Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assigned To Name</label>
                    <input type="text" name="assigned_to_name"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="assigned_to_email"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="assigned_to_type"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="staff">Staff</option>
                        <option value="student">Student</option>
                        <option value="agent">Agent</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Campus -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Campus</label>
                    <select name="campus_id"
                        class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option>Select Campus</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- 🔹 Notes -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea name="notes" rows="3"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- 🔹 Actions -->
            <div class="flex justify-end gap-3 pt-4 border-t">
                <a href="{{ route('assignments.index') }}"
                    class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800">
                    Cancel
                </a>

                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium shadow">
                    Assign Asset
                </button>
            </div>

        </form>

    </div>

</div>

</x-app-layout>