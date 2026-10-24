@extends('layouts.app')

@section('content')
    <section class="bg-white py-14">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="bg-white rounded-3xl shadow-xl p-10 border border-gray-200">



                <h1 class="text-5xl font-extrabold mb-4 text-gray-900 leading-tight tracking-tight text-center">
                    {{ $article->title }}
                </h1>
                <p class="text-center text-gray-600 text-lg max-w-2xl mx-auto mt-2">
                    Informasi inspiratif & edukatif untuk membantu pengalaman belanja lebih cerdas.
                </p>

                <div class="mt-10 bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-md flex gap-4 items-center mb-5">
                    @php
                        $profilePhoto = $article->user->profile_photo ?? null;
                    @endphp

                    <div class="w-12 h-12 rounded-full overflow-hidden border border-gray-300 shadow bg-gray-200">
                        @if ($profilePhoto)
                            <img src="{{ asset('storage/' . $profilePhoto) }}" class="w-full h-full object-cover">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($article->user->name) }}&size=128&background=random&color=fff"
                                class="w-full h-full object-cover">
                        @endif
                    </div>

                    <div>
                        <p class="font-semibold text-gray-800 text-lg">{{ $article->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $article->created_at->format('F d, Y') }}</p>
                    </div>
                </div>

                @if ($article->image)
                    <div class="overflow-hidden rounded-2xl shadow-lg hover:scale-[1.02] transition duration-500 mb-10">
                        <img src="{{ asset('storage/' . $article->image) }}" class="w-full h-[330px] object-cover">
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                    <div class="md:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                        @if ($article->thumbnail)
                            <div
                                class="w-full mb-5 h-60 overflow-hidden rounded-xl shadow border hover:scale-[1.02] transition">
                                <img src="{{ asset('storage/' . $article->thumbnail) }}" class="w-full h-full object-cover">
                            </div>
                        @endif

                        <div class="prose prose-lg max-w-none text-gray-800 leading-relaxed">
                            {!! $article->description !!}
                        </div>
                    </div>

                    <div class="space-y-6">

                        <div
                            class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-xl border border-gray-200 shadow-md">
                            <h3 class="font-bold text-gray-800 text-lg mb-4 border-b border-gray-200 pb-2">Bagikan Artikel
                            </h3>

                            @php
                                $url = urlencode(request()->fullUrl());
                                $title = urlencode($article->title);
                            @endphp

                            <div class="grid gap-3">

                                <a href="https://wa.me/?text={{ $title }}%20{{ $url }}" target="_blank"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M12 2a10 10 0 00-8.94 14.36L2 22l5.78-1.9A10 10 0 1012 2zm0 18a8 8 0 01-4.28-1.22l-.31-.2-3.43 1.13 1.14-3.27-.21-.32A8 8 0 1112 20zm4.26-5.47c-.23-.12-1.37-.68-1.58-.76s-.37-.12-.53.12-.62.76-.76.92-.28.17-.51.05a6.54 6.54 0 01-1.92-1.19 7.12 7.12 0 01-1.31-1.62c-.14-.23 0-.35.1-.47s.23-.28.35-.42a1.56 1.56 0 00.23-.39.43.43 0 00-.02-.41c-.06-.12-.53-1.27-.73-1.74s-.39-.4-.53-.41h-.45a.86.86 0 00-.62.29A2.62 2.62 0 007 9.83 4.57 4.57 0 007.92 12a10.62 10.62 0 003.61 3.61 7.87 7.87 0 002.47 1c.26.04.5.03.68-.02a2.1 2.1 0 001.37-1.1 1.72 1.72 0 00.12-1c-.05-.09-.21-.15-.41-.25z" />
                                    </svg> WhatsApp
                                </a>

                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ $url }}" target="_blank"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.988H8.078V12h2.36V9.797c0-2.332 1.393-3.622 3.522-3.622.999 0 2.043.177 2.043.177v2.248h-1.151c-1.136 0-1.493.704-1.493 1.425V12h2.543l-.406 2.89h-2.137v6.988C18.343 21.128 22 16.991 22 12z" />
                                    </svg>Facebook
                                </a>

                                <a href="https://twitter.com/intent/tweet?url={{ $url }}&text={{ $title }}"
                                    target="_blank"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                        viewBox="0 0 24 24">
                                        <path
                                            d="M24 4.557a9.83 9.83 0 01-2.828.775A4.932 4.932 0 0023.337 3.1a9.864 9.864 0 01-3.127 1.195A4.916 4.916 0 0016.616 3c-2.733 0-4.95 2.216-4.95 4.95 0 .388.045.765.13 1.13C7.728 8.94 4.1 6.897 1.671 3.882a4.92 4.92 0 00-.67 2.49c0 1.72.875 3.236 2.207 4.125a4.903 4.903 0 01-2.24-.618v.063c0 2.403 1.71 4.405 3.977 4.864a4.996 4.996 0 01-2.224.085c.627 1.956 2.445 3.379 4.6 3.418a9.869 9.869 0 01-6.102 2.105c-.398 0-.79-.024-1.175-.07A13.93 13.93 0 007.548 21c9.142 0 14.307-7.72 14.307-14.416 0-.22-.005-.437-.015-.653A10.326 10.326 0 0024 4.557z" />
                                    </svg>
                                    Twitter
                                </a>

                                <button
                                    onclick="navigator.clipboard.writeText('{{ request()->fullUrl() }}'); alert('Link berhasil disalin!')"
                                    class="flex items-center justify-center gap-2 px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition">
                                    🔗 Copy Link
                                </button>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-10">
                    <a href="{{ route('articles.index') }}"
                        class="inline-flex items-center px-5 py-3 text-sm font-medium text-white bg-black rounded-lg hover:bg-gray-200 transition">
                        Kembali ke Artikel
                    </a>
                </div>

            </div>
        </div>
        </div>
    </section>
@endsection
