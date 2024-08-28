@extends('layout/layout_base')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-10 offset-0 offset-md-1">
                <div class="card">
                    <div class="card-body p-5 shadow">
                        <div class="d-flex mb-5">
                            <div style="max-width: 5.5em">
                                <img src="{{ asset('assets/images/tomas_votruba.jpg') }}" class="rounded-circle shadow">
                            </div>

                            <div class="d-block ms-4 mt-4">
                                <h2 class="mt-0" style="letter-spacing: .02em; font-size: 2em">Tomas Votruba</h2>
                            </div>
                        </div>

                        <div class="text-bigger">
                            <p>
                                I help PHP companies to modernize their codebase,<br> increase profits and attract new talent.
                            </p>

                            <p>
                                To make this happen faster and at scale, I made <a href="http://github.com/rectorphp/rector">Rector</a> - software that handles instant upgrades and automated refactoring.
                            </p>
                            <p>
                                We provide <a href="https://getrector.com/hire-team">an upgrade service</a> to save you time and money.
                            </p>

                            <hr>

                            <div style="font-size: .9em">
                                <a href="https://www.linkedin.com/in/tomas-votruba/">LinkedIn</a>

                                <span class="ps-2 pe-2">•</span>

                                <a href="https://x.com/votrubaT">Twitter</a>

                                <span class="ps-2 pe-2">•</span>

                                <a href="https://github.com/tomasVotruba/">GitHub</a>
                            </div>
                        </div>
                    </div>
                </div>

                <h1>
                </h1>
            </div>
        </div>

        {{-- my dad raised me with quotes, at first I didn't understand them,
        as I was older, I realized the Truth - it's a coding standard for Life --}}

        <blockquote class="blockquote text-center mt-0">
            "{!! $quote !!}"
        </blockquote>

        <hr>

        <a name="posts"></a>

        <h2 class="mb-5">
            Learn about PHP in Posts
        </h2>

        @include('_snippets/post/post_list', ['posts' => $posts])


    </div>
@endsection
