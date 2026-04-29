<x-app-layout>

<div class="max-w-7xl mx-auto px-8 pt-10 pb-12">

    <!-- Breadcrumb -->
    <x-breadcrumbs :links="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Assets', 'url' => route('assets.index')],
        ['label' => 'Edit']
    ]" />

    <!-- Page Header -->
    <div class="mb-8">

        <h1 class="text-3xl font-semibold text-gray-800">
            Edit Asset
        </h1>

        <p class="text-gray-500 mt-1">
            Update asset details
        </p>

    </div>

    <!-- Form Card -->
    <div class="bg-white shadow-sm rounded-xl border p-6">

        <form method="POST" action="{{ route('assets.update', $asset->id) }}">
            @csrf
            @method('PUT')

            <!-- Asset Name -->
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Asset Name
                </label>

                <input
                    type="text"
                    name="name"
                    value="{{ old('name', $asset->name) }}"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required>

            </div>

            <!-- Campus -->
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Campus
                </label>

                <select
                    name="campus_id"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">

                    <option value="">Select Campus</option>

                    @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}"
                            {{ $asset->campus_id == $campus->id ? 'selected' : '' }}>
                            {{ $campus->name }}
                        </option>
                    @endforeach

                </select>

            </div>

            <!-- Assigned Agent -->
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Assigned Agent
                </label>

                <select
                    name="agent_id"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">

                    <option value="">Unassigned</option>

                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}"
                            {{ $asset->agent_id == $agent->id ? 'selected' : '' }}>
                            {{ $agent->name }}
                        </option>
                    @endforeach

                </select>

            </div>

            <!-- Status -->
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required>

                    <option value="active" {{ $asset->status == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="faulty" {{ $asset->status == 'faulty' ? 'selected' : '' }}>Faulty</option>
                    <option value="retired" {{ $asset->status == 'retired' ? 'selected' : '' }}>Retired</option>

                </select>

            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t">

                <a href="{{ route('assets.index') }}" 
                   class="text-sm text-gray-600 hover:underline">
                    Cancel
                </a>

                <button
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium shadow-sm">

                    Update Asset

                </button>

            </div>

        </form>

    </div>

</div>

</x-app-layout>