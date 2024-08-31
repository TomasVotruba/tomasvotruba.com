@extends('layout/layout_base')

@section('content')
    <div class="container">
        <h1>Share board</h1>

        @foreach ($randomPosts as $post)
            <textarea class="form-control mb-4 p-2" style="height: 8em">https://tomasvotruba.com/blog/{{ $post->getSlug() }}
            </textarea>
        @endforeach
    </div>
@endsection
