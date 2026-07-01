<div>
    <p class="text-slate-600 dark:text-slate-400 mb-4">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')
        @foreach([
            ['id' => 'update_password_current_password', 'name' => 'current_password', 'label' => __('Current Password'), 'error' => 'current_password'],
            ['id' => 'update_password_password', 'name' => 'password', 'label' => __('New Password'), 'error' => 'password'],
            ['id' => 'update_password_password_confirmation', 'name' => 'password_confirmation', 'label' => __('Confirm Password'), 'error' => 'password_confirmation'],
        ] as $field)
        <div>
            <label for="{{ $field['id'] }}" class="block text-sm font-medium mb-1">{{ $field['label'] }}</label>
            <input type="password" id="{{ $field['id'] }}" name="{{ $field['name'] }}" autocomplete="{{ $field['name'] === 'current_password' ? 'current-password' : 'new-password' }}"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm @error($field['error'], 'updatePassword') border-red-500 @enderror">
            @error($field['error'], 'updatePassword')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        @endforeach
        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">{{ __('Save') }}</button>
            @if (session('status') === 'password-updated')<span class="text-sm text-emerald-600 dark:text-emerald-400">{{ __('Saved.') }}</span>@endif
        </div>
    </form>
</div>
