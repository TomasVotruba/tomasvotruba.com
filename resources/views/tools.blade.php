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
                    <a href="{{ $tool->getLink() }}" class="btn btn-sm btn-success
                     mt-3 float-end">Check it</a>

                    <h2 class="mt-3 mb-5">{{ $tool->getName() }}</h2>

                    <p>
                        <em>Why use?</em>
                    </p><p>
                        {{ $tool->getWhy() }}
                    </p>

                    <br>

                    <p>
                        <em>Best time to start using?</em>
                    </p>
                    <p>
                        {{ $tool->getWhen() }}
                    </p>
                </div>

                    @if ($tool->getComposer())
                        <div class="card-body border-top bg-primary-subtle">
                        <div class="form-floating m-2">
                            <textarea class="form-control border border-primary">{{ $tool->getComposer() }}</textarea>
                            <label>Install</label>
                        </div>
                        </div>
                    @endif
            </div>
            </div>

        @endforeach

        </div>
    </div>
@endsection
