@base('layout/layout_base')

@block('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-md-9">
                <h1>
                    I help PHP Companies <br>
                    Change Fast&nbsp;and&nbsp;Safely
                </h1>
            </div>

            <div class="col-4 col-md-3">
                <a href="{{ route('about') }}">
                    <img src="{{ asset('assets/images/tomas_votruba.jpg') }}" class="mt-auto rounded-circle shadow">
                </a>
            </div>
        </div>

        <br>

        <div class="clearfix"></div>

        <h2 class="mb-4">
            What I wrote recently?
        </h2>

        <div class="text-bigger">
            @foreach ($last_posts as $post)
                @php /** @var $post \TomasVotruba\Blog\ValueObject\Post */ @endphp
                <div class="mb-4 row">
                    <div class="col-12">
                        <a href="{{ route(\TomasVotruba\Website\ValueObject\RouteName::POST_DETAIL, ['slug' =>  $post->getSlug()]) }}" class="pt-3 pr-3">{{ $post->getTitle()|raw }}</a>
                    </div>

                    <div class="small text-secondary col-12 pt-2">
                        {{ $post->getDateTime()->format("Y-m-d") }}
                    </div>
                </div>
            @endfor


            <a href="{{ route(\TomasVotruba\Website\ValueObject\RouteName::BLOG) }}" class="btn btn-warning pull-right mt-4">Discover more Posts </a>
        </div>

        <br>
        <br>
        <br>
        <hr>
        <br>
        <br>

        {# my dad raised me with quotes, at first I didn't understand them,
        as I was older, I ralized the Truth - it's a coding standard for Life #}

        <blockquote class="blockquote text-center">
            "{{ $quote->getContent()|raw }}"
        </blockquote>
    </div>
@endblock
