<div id="menu">
    <div class="container-fluid">
        <a href="{{ action(\App\Http\Controller\HomepageController::class) }}">Home</a>
        <span class="ps-2 pe-2">•</span>

        <a href="{{ action(\App\Http\Controller\BlogController::class) }}">Blog</a>
        <span class="ps-2 pe-2">•</span>

        <a href="{{ action(\App\Http\Controller\BooksController::class) }}">Books</a>
        <span class="ps-2 pe-2">•</span>

        <a href="{{ action(\App\Http\Controller\ContactController::class) }}">Contact</a>
        <span class="ps-2 pe-2">•</span>

        <a href="{{ action(\App\Http\Controller\AboutController::class) }}">About Me</a>
        <span class="ps-2 pe-2">•</span>

        <a href="https://twitter.com/votrubaT" target="blank">Twitter</a>
        <span class="ps-2 pe-2">•</span>

        <a href="{{ action(\App\Http\Controller\RssController::class) }}" target="blank">RSS</a>
    </div>
</div>
