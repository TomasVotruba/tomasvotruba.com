@extends('layout/layout_base')

@php
    /** @var \App\ValueObject\Tool[] $tools */
@endphp

@section('content')
    <div class="container">
        <h1>Tools - What and When to use</h1>

        <div class="mt-4 mb-4">
            <p>
                There is a dozen of tools me and my friends use daily. <strong>We save time, effort and automate safety</strong>. <strong>CI works for us</strong>.
                <br>
                We have better sleep and more time for fun work. Yet, you might have missed them.
            </p>
            <p>
                I made this tiny page to put them all in single place and make it easy to use for everyone. Lazy coding!
            </p>
        </div>

        <br>
        <br>

        <div class="row">

        @foreach ($tools as $tool)
            <div class="col-12 col-md-8 offset-md-2">
                <div class="card mb-5 shadow">
                    <div class="card-body ps-4 pe-4">
                        <a name="{{ $tool->getSlug() }}"></a>

                        <div style="float: left; margin-top: .2em" class="text-secondary">
                            <a href="#{{ $tool->getSlug() }}">
                                #{{ $loop->index + 1 }}
                            </a>
                        </div>

                        <div class="text-end">
                            @if ($tool->isPhpstanExtension())
                                <div class="badge text-bg-warning">PHPStan extension</div>
                            @else
                                <div class="badge text-bg-primary">Standalone CLI tool</div>
                            @endif
                        </div>



                        <h2 class="text-center" style="font-size: 2em; margin: 2.5em 0;">{{ $tool->getName() }}</h2>

                        <p class="mt-5">
                            <em class="text-secondary">Why use?</em>
                        </p>
                        <p>
                            {{ $tool->getWhy() }}

                            @if ($tool->getPost())
                                <span class="ps-2 pe-2">•</span>
                                <a href="{{ $tool->getPost() }}">Read&nbsp;more in a post</a>
                            @endif
                        </p>

                        <br>

                        <p>
                            <em class="text-secondary">Best time to start using?</em>
                        </p>
                        <p>
                            {{ $tool->getWhen() }}
                        </p>

                        <br>

                        <p>
                            <em class="text-secondary">How to install?</em>
                            <textarea class="form-control mt-2" style="height: 2.5em; max-width: 40em">{{ $tool->getComposer() }}</textarea>
                        </p>

                        <br>

                        @if ($tool->getTryCommands())
                            <p>
                                <em class="text-secondary">First commands to try</em>

                                <div class="card-body rounded bg-light border" style="border-color: #DDD;">
                                    @foreach ($tool->getTryCommands() as $label => $tryCommand)
                                        <div class="form-floating m-2">
                                            <textarea class="form-control">{{ $tryCommand }}</textarea>
                                            <label>{{ $label }}</label>

                                            @if (! $loop->last)<br>@endif
                                        </div>
                                    @endforeach
                                </div>
                            </p>
                        @endif

                        @if ($tool->getPhpstanContents())
                            <p>
                                <em class="text-secondary">Copy-paste to <code>phpstan.neon</code></em>

                                <div class="card-body rounded bg-light border" style="border-color: #DDD;">
                                    <textarea class="form-control border autoresize m-2" style="max-width: 32.8em">{{ $tool->getPhpstanContents() }}</textarea>
                                </div>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // select full textarea on a click in it
        document.querySelectorAll("textarea").forEach(function(textarea) {
            textarea.onclick = function() {
                this.select();
            };
        });

        // make textarea as long to fit the contents
        function autoResize(textarea) {
            // Reset height to handle reducing content
            textarea.style.height = 'auto';
            // Set height to match the scroll height (content height)
            textarea.style.height = textarea.scrollHeight + 5 + 'px';
        }

        // Trigger resize for all textareas with the class "autoresize" on page load
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.autoresize').forEach(textarea => {
                autoResize(textarea);
            });
        });
    </script>
@endsection
