<x-app-layout>

<div class="max-w-3xl space-y-6">

    <div>
        <h2 class="text-2xl font-bold text-gray-800">Invite Agent</h2>
        <p class="text-gray-500">Send onboarding invite to an agent</p>
    </div>

    <div class="bg-white p-6 rounded-2xl border shadow-sm">

        <form method="POST" action="{{ route('agents.sendInvite') }}" class="space-y-4">
            @csrf

            <input type="text" name="name" placeholder="Full Name"
                   class="w-full border rounded-lg px-4 py-2" required>

            <input type="email" name="email" placeholder="Email"
                   class="w-full border rounded-lg px-4 py-2" required>

            <select name="campus_id" class="w-full border rounded-lg px-4 py-2">
                <option value="">Select Campus</option>
                @foreach($campuses as $campus)
                    <option value="{{ $campus->id }}">
                        {{ $campus->name }}
                    </option>
                @endforeach
            </select>

            <input type="text" name="role" placeholder="Role"
                   class="w-full border rounded-lg px-4 py-2">

            <div class="flex justify-end">
                <button class="bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Send Invite
                </button>
            </div>

        </form>

    </div>

</div>

</x-app-layout>