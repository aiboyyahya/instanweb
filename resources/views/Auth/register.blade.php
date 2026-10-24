@extends('layouts.app')

@section('title', 'Daftar - InstanWeb')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 border border-gray-100">

        <h1 class="text-2xl font-semibold text-gray-800 mb-6 text-center">
            Bergabung dengan InstanWeb
        </h1>

        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 p-3 rounded mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.email') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-300 focus:border-indigo-400 p-2" />
                @error('name')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-300 focus:border-indigo-400 p-2" />
                @error('email')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" required
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-300 focus:border-indigo-400 p-2" />
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:ring-indigo-300 focus:border-indigo-400 p-2" />
                @error('password_confirmation')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">
                Daftar
            </button>
        </form>

        <div class="my-6 flex items-center gap-3">
            <div class="w-full border-t border-gray-200"></div>
            <span class="text-xs text-gray-500">atau</span>
            <div class="w-full border-t border-gray-200"></div>
        </div>

        <a href="{{ route('google.redirect') }}"
            class="w-full inline-flex items-center justify-center gap-3 border rounded-lg py-2 px-4 hover:shadow-sm transition">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                <path fill="#4285F4" d="M21.35 11.1H12v2.8h5.35c-.23 1.4-1.14 2.6-2.43 3.4v2.8h3.93c2.3-2.12 3.65-5.23 3.65-8.99 0-.6-.06-1.18-.18-1.75z"/>
                <path fill="#34A853" d="M12 22c2.7 0 4.96-.9 6.62-2.45l-3.93-2.8c-.88.6-2.03.95-2.69.95-2.06 0-3.81-1.38-4.44-3.24H3.06v2.03C4.7 19.96 8.07 22 12 22z"/>
                <path fill="#FBBC05" d="M7.56 13.46a6.24 6.24 0 010-3.92V7.51H3.06a9.98 9.98 0 000 8.98l4.5-2.03z"/>
                <path fill="#EA4335" d="M12 6.6c1.47 0 2.78.5 3.82 1.48l2.86-2.86C16.96 3.7 14.7 3 12 3 8.07 3 4.7 5.04 3.06 7.97l4.5 2.03C8.19 7.98 9.94 6.6 12 6.6z"/>
            </svg>
            <span class="text-sm font-medium">Daftar dengan Google</span>
        </a>

        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun?
            <a href="{{ route('login.form') }}" class="text-indigo-600 hover:underline">Masuk</a>
        </p>

    </div>
</div>
@endsection
