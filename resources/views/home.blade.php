@extends('layouts.app')

@section('title', 'Attendance System')
@section('hideNavbar', true)

@section('content')
    <div class="grid grid-cols-1 gap-10 xl:grid-cols-[1.6fr_1fr] items-center">
        <div class="glass-card rounded-[2rem] border border-white/10 p-10 max-w-3xl w-full text-white mx-auto">
            <span class="inline-flex rounded-full bg-white/10 px-4 py-2 text-sm font-semibold text-indigo-100 mb-6">Attendance tracking for students, teachers, HR, and admins</span>

            <h1 class="text-5xl md:text-6xl font-bold text-white leading-tight tracking-tight mb-6">
                🎓 Attendance System
            </h1>

            <p class="text-slate-300 text-lg md:text-xl leading-relaxed max-w-2xl mb-8">
                Manage attendance, submit leave requests, assign tasks, and keep your team connected with fast notifications.
            </p>

            <div class="grid gap-4 sm:grid-cols-2">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-3xl bg-indigo-500 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-indigo-500/20 hover:bg-indigo-600 transition">
                    🔐 Login
                </a>

                <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-3xl bg-sky-500 px-6 py-4 text-base font-semibold text-white shadow-lg shadow-sky-500/20 hover:bg-sky-600 transition">
                    📝 Register as Student
                </a>
            </div>

            <div class="mt-10 grid gap-4 sm:grid-cols-3">
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 text-left">
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-400 mb-2">Fast Setup</p>
                    <p class="font-semibold text-white">Easy login and role-based dashboards.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 text-left">
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-400 mb-2">Smart Attendance</p>
                    <p class="font-semibold text-white">Mark attendance with auto-notifications.</p>
                </div>
                <div class="rounded-3xl border border-white/10 bg-white/5 p-5 text-left">
                    <p class="text-sm uppercase tracking-[0.2em] text-slate-400 mb-2">Leave & Tasks</p>
                    <p class="font-semibold text-white">Submit leave, assign tasks, and stay updated.</p>
                </div>
            </div>
        </div>

        <div class="rounded-[2rem] overflow-hidden border border-white/10 shadow-2xl shadow-slate-950/40">
            <img src="https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8YXR0ZW5kYW5jZSUyMHN5c3RlbXxlbnwwfHwwfHx8MA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                 alt="Attendance System" class="h-full w-full min-h-[420px] object-cover">
        </div>
    </div>
@endsection
