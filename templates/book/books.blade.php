@base('layout/layout_base')

@block('content')
    <div class="container-fluid" id="blog">
        <h1>{{ raw($title) }}</h1>

        <div class="row">
            @foreach ($books as $book)
                @php /** @var $book \TomasVotruba\Website\ValueObject\Book */ @endphp
                <div class="text-center col-12 col-md-6 mb-5 mt-4">
                    <a href="{{ route('book-detail', ['slug' => $book->slug]) }}" target="blank">
                        <img src="{{ $book->coverImage }}" style="width: 17em; padding: .5em;margin:0 0 1em 0" class="shadow" alt="Rector book cover">
                    </a>

                    <br>

                    <a href="{{ route('book-detail', ['slug' => $book->slug]) }}" target="blank" class="btn @if ($book->isFinished)btn-success@else btn-warning@endif btn-lg mt-4 mb-2">
                        @if ($book->isFinished)
                            Buy a Copy
                        @else
                            Become Early Adopter
                        @endif
                    </a>
                </div>
            @endfor
        </div>
    </div>
@endblock
