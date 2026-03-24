<div class="w-full max-w-md rounded-2xl border border-stone-200 bg-white p-6 shadow-sm sm:p-8">
    <div class="mb-6 space-y-1">
        <h1 class="text-2xl font-semibold tracking-tight text-stone-950">Sign in</h1>
        <p class="text-sm text-stone-600">Enter your email and password to continue.</p>
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <ul class="space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="login" class="space-y-5">
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-stone-700">Email</label>
            <input
                id="email"
                type="email"
                wire:model="email"
                autocomplete="email"
                class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
            >
            @error('email')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="space-y-2">
            <label for="password" class="block text-sm font-medium text-stone-700">Password</label>
            <input
                id="password"
                type="password"
                wire:model="password"
                autocomplete="current-password"
                class="block w-full rounded-xl border border-stone-300 bg-white px-3 py-2.5 text-sm text-stone-950 shadow-sm outline-none transition focus:border-stone-400 focus:ring-2 focus:ring-stone-200"
            >
            @error('password')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-3 text-sm text-stone-600">
            <input
                type="checkbox"
                wire:model="remember"
                class="h-4 w-4 rounded border-stone-300 text-stone-900 focus:ring-2 focus:ring-stone-200"
            >
            <span>Remember me</span>
        </label>

        <button
            type="submit"
            class="inline-flex w-full items-center justify-center rounded-xl bg-stone-950 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-stone-800 focus:outline-none focus:ring-2 focus:ring-stone-300"
        >
            Sign in
        </button>
    </form>
</div>
