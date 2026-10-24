@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-10 text-center">
        <h1 class="text-4xl font-extrabold mb-4 text-gray-800 tracking-wide">Artikel</h1>
        <p class="text-gray-600 text-base mb-4">
            Temukan artikel informatif dan terbaru yang telah kami sajikan untuk menambah wawasan Anda.
        </p>

        @if ($articles->isEmpty())
            <p class="text-gray-500 italic">No articles available.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7 border-t border-gray-400 pt-8">
                @foreach ($articles as $article)
                    <div
                        class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-md hover:shadow-xl hover:-translate-y-1 transition-all border border-gray-100 overflow-hidden">

                        @if ($article->image)
                            <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}"
                                class="w-full h-52 object-cover">
                        @endif

                        <div class="p-8">
                            <div class="mb-3">
                                <p class="text-2xl font-extrabold text-gray-700 text-left mb-2">
                                    {{ $article->user->name }}
                                </p>
                                <p class="text-xs text-gray-500 text-left">
                                    {{ $article->created_at->format('d M Y') }}
                                </p>
                            </div>

                            <h2 class="text-xl font-semibold text-gray-900 leading-tight mb-3 text-right">

                                <a href="{{ route('articles.show', $article->slug) }}"
                                    class="inline-block px-4 py-2 text-sm border border-black text-gray-700 rounded-lg font-semibold hover:bg-green-50 transition">
                                    Baca Selengkapnya
                                </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
@endsection
