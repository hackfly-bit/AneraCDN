<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <x-theme-fouc />
    <title>{{ config('app.name', 'Laravel CDN') }} — File Management & CDN</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-slate-50 text-slate-900 dark:bg-slate-950 dark:text-slate-100">
    <div class="min-h-screen flex flex-col">
        {{-- Nav --}}
        <header class="border-b border-slate-200/80 dark:border-slate-800 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm sticky top-0 z-10">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2 font-semibold text-lg">
                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-indigo-600 text-white text-sm font-bold">CDN</span>
                    <span>{{ config('app.name', 'Laravel CDN') }}</span>
                </a>
                @if (Route::has('login'))
                    <nav class="flex items-center gap-3 text-sm">
                        <x-theme-toggle />
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">Log in</a>
                        @endauth
                    </nav>
                @endif
            </div>
        </header>

        {{-- Hero --}}
        <main class="flex-1">
            <section>
                <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-28">
                    <div class="max-w-2xl">
                        <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400 mb-3">Internal file platform</p>
                        <h1 class="text-4xl sm:text-5xl font-bold tracking-tight text-slate-900 dark:text-white leading-tight">
                            Upload, store &amp; serve files with control
                        </h1>
                        <p class="mt-5 text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                            Centralized CDN for your team — manage uploads, public &amp; private files, image optimization, and REST API access from one dashboard.
                        </p>
                        <div class="mt-8 flex flex-wrap gap-3">
                            @auth
                                <a href="{{ route('dashboard.upload') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition shadow-sm">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    Upload files
                                </a>
                                <a href="{{ route('dashboard.files') }}" class="inline-flex items-center px-5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-600 font-medium hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                    Browse files
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition shadow-sm">
                                    Sign in
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </section>

            {{-- Features --}}
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
                <h2 class="text-2xl font-semibold text-center mb-10">What you can do</h2>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="p-6 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-600 dark:text-indigo-400 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        </div>
                        <h3 class="font-semibold mb-1">Drag &amp; drop upload</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Multi-file upload with folders and public/private visibility.</p>
                    </div>
                    <div class="p-6 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center text-purple-600 dark:text-purple-400 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="font-semibold mb-1">Image optimization</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Automatic thumbnails and WebP conversion on upload.</p>
                    </div>
                    <div class="p-6 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <h3 class="font-semibold mb-1">Role-based access</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Admin and user roles with granular file permissions.</p>
                    </div>
                    <div class="p-6 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center text-amber-600 dark:text-amber-400 mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        </div>
                        <h3 class="font-semibold mb-1">REST API</h3>
                        <p class="text-sm text-slate-600 dark:text-slate-400">Sanctum-authenticated API for programmatic file management.</p>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-slate-200 dark:border-slate-800 py-6">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-sm text-slate-500 dark:text-slate-400">
                &copy; {{ date('Y') }} {{ config('app.name', 'Laravel CDN') }}
            </div>
        </footer>
    </div>
</body>
</html>
