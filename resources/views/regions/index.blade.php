<x-app-layout>

<div class="max-w-7xl mx-auto px-8 pt-10 pb-12">

    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8">

        <div>
            <h1 class="text-3xl font-semibold text-gray-800">
                Regions
            </h1>
            <p class="text-gray-500 mt-1">
                Manage geographical regions for your asset inventory
            </p>
        </div>

        <a href="{{ route('regions.create') }}"
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

            Add Region

        </a>

    </div>


    @if(session('success'))
        <div class="mb-6 bg-green-100 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif


    <!-- Table Container -->
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border">

        <table class="min-w-full">

            <thead class="bg-gray-50 border-b">

                <tr>
                    <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">
                        ID
                    </th>

                    <th class="text-left px-6 py-4 text-sm font-semibold text-gray-600">
                        Region Name
                    </th>

                    <th class="text-right px-6 py-4 text-sm font-semibold text-gray-600">
                        Actions
                    </th>
                </tr>

            </thead>

            <tbody class="divide-y">

                @foreach($regions as $region)

                <tr class="hover:bg-gray-50 transition">

                    <td class="px-6 py-4 text-gray-700">
                        {{ $region->id }}
                    </td>

                    <td class="px-6 py-4 font-medium text-gray-800">
                        {{ $region->name }}
                    </td>

                    <td class="px-6 py-4 text-right">

                        <a href="{{ route('regions.edit',$region->id) }}"
                           class="text-indigo-600 hover:text-indigo-800 font-medium mr-5">
                           Edit
                        </a>

                        <form action="{{ route('regions.destroy',$region->id) }}"
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

                @endforeach

            </tbody>

        </table>

    </div>

</div>

</x-app-layout>