<!DOCTYPE html>
<html lang="en">
    <head>
        @include('_snippets/head')
    </head>
    <body>
        @include('_snippets/menu')

        <div class="container-fluid">
            <div id="content">
                @yield('content')
            </div>
        </div>

        @section('custom_footer')
        @endsection

        @include('_snippets/google_analytics')
    </body>
</html>
