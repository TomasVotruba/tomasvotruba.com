<div id="menu">
    <div class="container-fluid">
        <a href="{{ route(\App\Enum\RouteName::HOMEPAGE) }}">Home</a>
        <span class="ps-2 pe-2">•</span>

        <a href="{{ route(\App\Enum\RouteName::BLOG) }}">Blog</a>
        <span class="ps-2 pe-2">•</span>

        <a href="{{ route(\App\Enum\RouteName::BOOKS) }}">Books</a>
        <span class="ps-2 pe-2">•</span>

        <a href="{{ route(\App\Enum\RouteName::CONTACT) }}">Contact</a>
        <span class="ps-2 pe-2">•</span>

        <a href="{{ route(\App\Enum\RouteName::ABOUT) }}">About Me</a>
        <span class="ps-2 pe-2">•</span>

        <a href="https://twitter.com/votrubaT" target="blank">Twitter</a>
        <span class="ps-2 pe-2">•</span>

        <a href="{{ route(\App\Enum\RouteName::RSS) }}" target="blank">RSS</a>
    </div>
</div>
