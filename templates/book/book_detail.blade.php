@base('layout/layout_base')

@php /** @var $book \TomasVotruba\Website\ValueObject\Book */ @endphp

@block('content')
    <div class="container-fluid" id="blog">
        <h1>{{ raw($title) }}</h1>

        <div class="float-right text-center ml-5">
            <a href="{{ $book->getLeanpubLink() }}" target="blank">
                <img src="{{ $book->getCoverImage() }}" style="width: 16em; padding: .5em;margin:0 0 .5em 0" class="shadow" alt="{{ $book->getTitle() }} Book Cover">
            </a>

            <br>

            <a href="{{ $book->getLeanpubLink() }}" target="blank" class="btn @if ($book->isFinished())btn-success@else btn-warning@endif btn-lg mt-4 mb-2">
                @if ($book->isFinished())
                    Buy a Copy
                @else
                    Become Early Adopter
                @endif
            </a>
        </div>

        <div>
            {{ $book->getLongDescription()|raw }}
        </div>
    </div>
@endblock
