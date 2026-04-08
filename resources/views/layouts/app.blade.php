<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Attendance System')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .glass-card {
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(18px);
        }
        .text-glow {
            text-shadow: 0 2px 12px rgba(255, 255, 255, 0.15);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen antialiased">
    <div class="bg-[radial-gradient(circle_at_top,_rgba(96,165,250,0.25),_transparent_25%),radial-gradient(circle_at_20%_20%,_rgba(168,85,247,0.18),_transparent_18%),linear-gradient(to_bottom,_#020617,_#0f172a)] min-h-screen">
        @unless(View::hasSection('hideNavbar'))
            <header class="border-b border-white/10 py-5">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-white no-underline">
                        <span class="inline-flex h-12 w-12 items-center justify-center rounded-3xl bg-indigo-500/20 text-xl font-semibold">AS</span>
                        <div>
                            <p class="text-lg font-semibold">Attendance System</p>
                            <p class="text-sm text-slate-300/80">Sleek attendance tracking for admins and students</p>
                        </div>
                    </a>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-sm">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-2xl bg-white/10 px-4 py-3 text-white hover:bg-white/15 transition">Dashboard</a>
                        @if (auth()->user()->role !== 'admin')
                            <a href="{{ route('attendance.index') }}" class="rounded-2xl bg-white/10 px-4 py-3 text-white hover:bg-white/15 transition">Attendance</a>
                        @endif
                        <a href="{{ route('leave.index') }}" class="rounded-2xl bg-white/10 px-4 py-3 text-white hover:bg-white/15 transition">Leave</a>
                        <a href="{{ route('tasks.index') }}" class="rounded-2xl bg-white/10 px-4 py-3 text-white hover:bg-white/15 transition">Tasks</a>
                        @if (auth()->user()->role === 'admin')
                            <a href="{{ route('admin.students') }}" class="rounded-2xl bg-white/10 px-4 py-3 text-white hover:bg-white/15 transition">Admin</a>
                        @endif
                        @if (auth()->user()->role !== 'admin')
                            <a href="{{ route('profile.edit') }}" class="rounded-2xl bg-white/10 px-4 py-3 text-white hover:bg-white/15 transition">Profile</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="rounded-2xl bg-rose-500 px-4 py-3 text-white hover:bg-rose-600 transition">Logout</button>
                        </form>
                    @else
                        {{-- <a href="{{ route('login') }}" class="rounded-2xl bg-white/10 px-4 py-3 text-white hover:bg-white/15 transition">Login</a>
                        <a href="{{ route('register') }}" class="rounded-2xl bg-indigo-500 px-4 py-3 text-white hover:bg-indigo-600 transition">Register</a> --}}
                    @endauth
                </div>
            </div>
        </header>
        @endunless

        <main class="mx-auto max-w-7xl px-4 py-8">
            @if (session('success'))
                <div class="mb-6 rounded-[2rem] border border-emerald-300/30 bg-emerald-500/10 p-5 text-emerald-200 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-6 rounded-[2rem] border border-rose-300/30 bg-rose-500/10 p-5 text-rose-200 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    @yield('scripts')
</body>
</html>
