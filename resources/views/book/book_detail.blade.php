@extends('layout/layout_base')

@php /** @var $book \TomasVotruba\Website\ValueObject\Book */ @endphp

@section('content')
    <div class="container-fluid" id="blog">
        <h1>{{ $title }}</h1>

        <div class="float-right text-center ml-5">
            <a href="{{ $book->getLeanpubLink() }}" target="blank">
                <img src="{{ $book->getCoverImage() }}" style="width: 16em; padding: .5em;margin:0 0 .5em 0" class="shadow" alt="{{ $book->getTitle() }} Book Cover">
            </a>

            <br>

            <a
                href="{{ $book->getLeanpubLink() }}"
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

        <div>{!! $book->getLongDescription() !!}</div>
    </div>
@endsection
