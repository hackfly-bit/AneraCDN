<div x-data="{ open: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }">
    <p class="text-slate-600 dark:text-slate-400 mb-4">
        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
    </p>

    <button type="button" @click="open = true" class="inline-flex px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700">
        {{ __('Delete Account') }}
    </button>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" role="dialog">
        <div class="fixed inset-0 bg-slate-900/60" @click="open = false"></div>
        <div class="relative w-full max-w-md rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-xl p-6">
            <h3 class="text-lg font-semibold mb-2">{{ __('Are you sure you want to delete your account?') }}</h3>
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')
                <p class="text-sm text-slate-600 dark:text-slate-400 mb-4">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>
                <input type="password" name="password" placeholder="{{ __('Password') }}"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm mb-4 @error('password', 'userDeletion') border-red-500 @enderror">
                @error('password', 'userDeletion')<p class="text-sm text-red-600 dark:text-red-400 mb-4">{{ $message }}</p>@enderror
                <div class="flex justify-end gap-2">
                    <button type="button" @click="open = false" class="px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-sm">{{ __('Cancel') }}</button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white text-sm font-medium hover:bg-red-700">{{ __('Delete Account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
