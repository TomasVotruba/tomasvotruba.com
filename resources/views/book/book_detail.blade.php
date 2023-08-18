@extends('layout/layout_base')

@php /** @var $book \App\Entity\Book */ @endphp

@section('content')
    <div class="container-fluid" id="blog">
        <h1>{{ $title }}</h1>

        <div class="float-right text-center ml-5">
            <a href="{{ $book->getLeanpubLink() }}" target="blank">
                <img src="{{ $book->getCoverImage() }}" style="width: 16em; padding: .5em;margin:0 0 .5em 0"
                     class="shadow" alt="{{ $book->getTitle() }} Book Cover">
            </a>

            <br>

            <a
                href="{{ $book->getLeanpubLink() }}"
                target="blank"
                @class([
                    'btn btn-lg mt-5 mb-5 btn-success',
                ])
            >
                Buy a Copy
            </a>
        </div>

        <div>{!! $book->getLongDescription() !!}</div>
    </div>
@endsection
