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

        <div class="row">

        @foreach ($tools as $tool)
            <div class="col-12 col-lg-6">
                <div class="card mb-5 shadow">
                <div class="card-body ps-4 pe-4">
                    <a name="{{ $tool->getSlug() }}"></a>

                    <a href="{{ $tool->getLink() }}" class="me-2" style="text-decoration: none;">
                        <div class="badge text-bg-dark">GitHub</div>
                    </a>

                    @if ($tool->getPost())
                        <div class="badge text-bg-success me-2">
                            <a href="{{ $tool->getPost() }}" style="color: white">Read Blog Post</a>
                        </div>
                    @endif

                    @if ($tool->isPhpstanExtension())
                        <div class="badge text-bg-warning">PHPStan extension</div>
                    @endif

                    <h2 class="mt-5" style="font-size: 2em; margin-bottom: 1.1em">{{ $tool->getName() }}</h2>

                    <p class="mt-5">
                        <em class="text-secondary">Why use?</em>
                    </p>
                    <p>
                        {{ $tool->getWhy() }}
                    </p>

                    <br>

                    <p>
                        <em class="text-secondary">Best time to start using?</em>
                    </p>
                    <p>
                        {{ $tool->getWhen() }}
                    </p>
                </div>

                    <div class="card-body border-top border-primary border-bottom bg-primary-subtle">
                        <div class="form-floating m-2">
                            <textarea class="form-control border border-primary">{{ $tool->getComposer() }}</textarea>
                            <label>Install</label>
                        </div>
                    </div>

                    @if ($tool->getTryCommands())
                        <div class="card-body mt-2 mb-2 ms-2">
                            <em class="text-secondary">First commands to try</em>
                        </div>

                        @foreach ($tool->getTryCommands() as $label => $tryCommand)
                            <div class="card-body border-top border-success bg-success-subtle">
                                <div class="form-floating m-2">
                                    <textarea class="form-control border border-success">{{ $tryCommand }}</textarea>
                                    <label>{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if ($tool->getPhpstanContents())
                        <div class="card-body mt-2 mb-2 ms-2">
                            <em class="text-secondary">Copy-paste to <code>phpstan.neon</code></em>
                        </div>

                        <div class="card-body border-top border-success bg-success-subtle">
                            <div class="m-2">
                                <textarea class="form-control border border-success autoresize">{{ $tool->getPhpstanContents() }}</textarea>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        @endforeach

        </div>
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
