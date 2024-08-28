@extends('layout/layout_base')

@section('content')
    <div class="container">
        <h1>{{ $title }}</h1>

        <div class="text-center">
            <img src="{{ asset('assets/images/tomas_votruba.jpg') }}"
                 class="rounded-circle shadow homepage-face margin-auto" alt="Face of Tomas Votruba">
        </div>

        <br>
        <div class="clearfix"></div>
        <br>

        <div class="text-bigger">
            <p>
                I'm a PHP trainer, legacy code cleaner, blogger and <a
                    href="https://github.blog/2020-09-03-introducing-the-github-stars-program" target="blank">open-source
                    developer</a>.
            </p>

            <p>
                I love to connect with people and improve their everyday lives.
            </p>

            <p>
                My passion and daily work is tidying up code and empowering weakest parts. By removing frictions, the
                code becomes stable, easy to understand and even self-repairing.
            </p>

            <br>
            <div class="clearfix"></div>
            <br>

            <div class="text-center">
                <img src="{{ asset('assets/images/logo/rector.svg' )}}" class="mb-5 margin-auto" alt=""
                     style="max-width: 5em">
            </div>

            <p>
                To make this happen faster and in scale, I created <a href="http://github.com/rectorphp/rector"
                                                                      target="blank">Rector</a> - a PHP CLI tool for
                instant upgrades and automated refactoring. It's catching up pretty well among PHP community around the
                world - from Symfony to Drupal.
            </p>
            <p>
                I connected with Matthias Noback and <strong>we wrote a book about Rector</strong>:<br>
                <a href="{{ action(\App\Http\Controllers\RectorBookController::class,  ['slug' => 'rector-the-power-of-automated-refactoring']) }}">Rector
                    - The Power of Automated Refactoring</a>
            </p>

            <div class="clearfix mt-5"></div>
        </div>
    </div>
@endsection
