<!DOCTYPE html>
<html lang="en">
    <head>
        @include('_snippets/head')
    </head>
    <body>
        @include('snippets/menu')

        <div class="container-fluid">
            <div id="content">
                @block('content')
                @endblock
            </div>
        </div>

        @block('custom_footer')
        @endblock

        @include('_snippets/google_analytics')
    </body>
</html>
