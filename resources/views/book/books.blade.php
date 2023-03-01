@extends('layout/layout_base')

@section('content')
    <div class="container-fluid" id="blog">
        <h1>{{ $title }}</h1>

        <div class="row">
            @foreach ($books as $book)
                @php /** @var $book \App\Entity\Book */ @endphp

                <div class="text-center col-12 col-md-6 mb-5 mt-4">
                    <a href="{{ route('book-detail', ['slug' => $book->getSlug()]) }}">
                        <img src="{{ $book->getCoverImage() }}" style="width: 17em; padding: .5em;margin:0 0 1em 0"
                             class="shadow" alt="Rector book cover">
                    </a>

                    <br>

                    <a
                            href="{{ route('book-detail', ['slug' => $book->getSlug()]) }}"
                            target="blank"
                            @class([
                                'btn btn-lg mt-4 mb-2',
                                'btn-success' => $book->isFinished(),
                                'btn-warning' => ! $book->isFinished(),
                            ])
                    >
                        @if ($book->isFinished())
                            Buy a Copy
                        @else
                            Become Early Adopter
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
