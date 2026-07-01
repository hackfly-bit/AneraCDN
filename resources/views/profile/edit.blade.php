@extends('layouts.dashboard')

@section('title', 'Profile')

@section('content')
<div class="max-w-3xl space-y-6">
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <h2 class="font-semibold">Informasi Profile</h2>
        </div>
        <div class="p-5">@include('profile.partials.update-profile-information-form')</div>
    </div>

    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <h2 class="font-semibold">Ubah Password</h2>
        </div>
        <div class="p-5">@include('profile.partials.update-password-form')</div>
    </div>

    <div class="rounded-xl border border-red-200 dark:border-red-900 bg-white dark:bg-slate-900 shadow-sm">
        <div class="px-5 py-4 border-b border-red-200 dark:border-red-900 bg-red-600 text-white rounded-t-xl">
            <h2 class="font-semibold">Hapus Akun</h2>
        </div>
        <div class="p-5">@include('profile.partials.delete-user-form')</div>
    </div>
</div>
@endsection
