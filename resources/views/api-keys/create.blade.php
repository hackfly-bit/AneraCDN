@extends('layouts.dashboard')

@section('title', 'Buat API Key Baru')

@section('toolbar')
<a href="{{ route('api-keys.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-sm font-medium hover:bg-slate-100 dark:hover:bg-slate-800">Kembali</a>
@endsection

@section('content')
<div class="max-w-xl">
    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
        <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <h2 class="font-semibold">Buat API Key Baru</h2>
        </div>
        <form method="POST" action="{{ route('api-keys.store') }}" class="p-5 space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium mb-1">Nama API Key</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100 @error('name') border-red-500 @enderror">
                @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="expires_at" class="block text-sm font-medium mb-1">Tanggal Kadaluarsa (Opsional)</label>
                <input type="date" name="expires_at" id="expires_at" value="{{ old('expires_at') }}"
                    class="w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 dark:text-slate-100 @error('expires_at') border-red-500 @enderror">
                @error('expires_at')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Kosongkan jika tidak ingin ada batas waktu kadaluarsa.</p>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('api-keys.index') }}" class="inline-flex px-4 py-2 rounded-lg border border-slate-300 dark:border-slate-600 text-sm hover:bg-slate-100 dark:hover:bg-slate-800">Batal</a>
                <button type="submit" class="inline-flex px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700">Buat API Key</button>
            </div>
        </form>
    </div>
</div>
@endsection
