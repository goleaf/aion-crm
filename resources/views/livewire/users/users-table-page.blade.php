@php($users = $users ?? [])

<div class="overflow-hidden rounded-2xl border border-stone-200 bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-stone-200 text-left text-sm">
            <thead class="bg-stone-50">
                <tr>
                    <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Username</th>
                    <th scope="col" class="px-4 py-3 font-medium text-stone-700 sm:px-6">Password</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($users as $user)
                    <tr>
                        <td class="px-4 py-3 text-stone-950 sm:px-6">{{ data_get($user, 'name') }}</td>
                        <td class="px-4 py-3 font-mono text-stone-700 sm:px-6">{{ data_get($user, 'password') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="px-4 py-6 text-center text-stone-500 sm:px-6">No users available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
