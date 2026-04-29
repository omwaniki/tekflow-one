<x-app-layout>

<div class="max-w-7xl mx-auto px-8 pt-10 pb-12">

    <!-- Breadcrumb -->
    <x-breadcrumbs :links="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Campuses', 'url' => route('campuses.index')],
        ['label' => 'Create']
    ]" />

    <!-- Page Header -->
    <div class="mb-8">

        <h1 class="text-3xl font-semibold text-gray-800">
            Create Campus
        </h1>

        <p class="text-gray-500 mt-1">
            Add a new campus to your asset inventory
        </p>

    </div>


    <!-- Form Card -->
    <div class="bg-white shadow-sm rounded-xl border p-6">

        <form method="POST" action="{{ route('campuses.store') }}">
            @csrf

            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Campus Name
                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Example: Ruiru Campus"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required>

            </div>


            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Region
                </label>

                <select
                    name="region_id"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required>

                    <option value="">Select Region</option>

                    @foreach($regions as $region)
                        <option value="{{ $region->id }}">
                            {{ $region->name }}
                        </option>
                    @endforeach

                </select>

            </div>

            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium shadow-sm">

                Save Campus

            </button>

        </form>

    </div>

</div>

</x-app-layout>