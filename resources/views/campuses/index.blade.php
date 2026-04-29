<x-app-layout>

<div class="max-w-7xl mx-auto px-8 pt-10 pb-12">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">

        <div>
            <h1 class="text-3xl font-semibold text-gray-800">
                Campuses
            </h1>
            <p class="text-gray-500 mt-1">
                Manage campuses within each region
            </p>
        </div>

        <a href="{{ route('campuses.create') }}"
           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-xl shadow-sm transition duration-200">

            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-5 h-5"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke="currentColor">

                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M12 4v16m8-8H4"/>

            </svg>

            Add Campus

        </a>

    </div>


    @if(session('success'))
        <div class="mb-6 bg-green-100 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif


    <!-- Table -->
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border">

        <table class="min-w-full">

            <thead class="bg-gray-50 border-b">

                <tr>

                    <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">
                        Campus
                    </th>

                    <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">
                        Region
                    </th>

                    <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">
                        Actions
                    </th>

                </tr>

            </thead>

            <tbody class="divide-y">

                @forelse($campuses as $campus)

                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $campus->name }}
                    </td>

                    <td class="px-6 py-4 text-gray-700">
                        {{ $campus->region->name ?? '-' }}
                    </td>

                    <td class="px-6 py-4 text-right">

                        <a href="{{ route('campuses.edit',$campus->id) }}"
                           class="text-indigo-600 hover:text-indigo-800 font-medium mr-5">
                           Edit
                        </a>

                        <form action="{{ route('campuses.destroy',$campus->id) }}"
                              method="POST"
                              class="inline">

                            @csrf
                            @method('DELETE')

                            <button class="text-red-600 hover:text-red-800 font-medium">
                                Delete
                            </button>

                        </form>

                    </td>

                </tr>

                @empty

                <tr>
                    <td colspan="3" class="text-center text-gray-500 py-6">
                        No campuses found.
                    </td>
                </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</div>

</x-app-layout>