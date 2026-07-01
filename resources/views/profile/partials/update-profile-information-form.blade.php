<div>
    <p class="text-slate-600 dark:text-slate-400 mb-4">{{ __("Update your account's profile information and email address.") }}</p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')
        <div>
            <label for="name" class="block text-sm font-medium mb-1">{{ __('Name') }}</label>
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm @error('name') border-red-500 @enderror">
            @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="email" class="block text-sm font-medium mb-1">{{ __('Email') }}</label>
            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm @error('email') border-red-500 @enderror">
            @error('email')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="mt-2 rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-950/50 p-3 text-sm">
                {{ __('Your email address is unverified.') }}
                <button type="submit" form="send-verification" class="text-indigo-600 dark:text-indigo-400 underline">{{ __('Click here to re-send the verification email.') }}</button>
            </div>
            @if (session('status') === 'verification-link-sent')
            <p class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">{{ __('A new verification link has been sent to your email address.') }}</p>
            @endif
            @endif
        </div>
        <div class="flex items-center gap-3">
            <button type="submit" class="inline-flex px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">{{ __('Save') }}</button>
            @if (session('status') === 'profile-updated')<span class="text-sm text-emerald-600 dark:text-emerald-400">{{ __('Saved.') }}</span>@endif
        </div>
    </form>
</div>
