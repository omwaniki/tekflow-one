<x-app-layout>

<div class="max-w-7xl mx-auto px-8 pt-10 pb-12">

    <!-- Breadcrumb -->
    <x-breadcrumbs :links="[
        ['label' => 'Dashboard', 'url' => route('dashboard')],
        ['label' => 'Regions', 'url' => route('regions.index')],
        ['label' => 'Create']
    ]" />

    <!-- Page Header -->
    <div class="mb-8">

        <h1 class="text-3xl font-semibold text-gray-800">
            Create Region
        </h1>

        <p class="text-gray-500 mt-1">
            Add a new region to organize campuses and assets
        </p>

    </div>


    <!-- Form Card -->
    <div class="bg-white shadow-sm rounded-xl border p-6">

        <form method="POST" action="{{ route('regions.store') }}">
            @csrf

            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Region Name
                </label>

                <input
                    type="text"
                    name="name"
                    placeholder="Example: Kenya"
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    required>

            </div>

            <button
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium shadow-sm">

                Save Region

            </button>

        </form>

    </div>

</div>

</x-app-layout>