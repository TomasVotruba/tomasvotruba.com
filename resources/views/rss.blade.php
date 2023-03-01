@php
<<<<<<< HEAD
    /** @var $posts \App\Entity\Post[] */
@endphp
=======
    /** @var $posts \TomasVotruba\Website\Entity\Post[] */
@endphp

<?xml version="1.0" encoding="UTF-8" ?>
>>>>>>> 2861e0a805 (mics)

<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
     xmlns:content="https://purl.org/rss/1.0/modules/content/"
     xmlns:dc="https://purl.org/dc/elements/1.1/"
     xmlns:atom="https://www.w3.org/2005/Atom"
>
    <channel>
        <title>Tomas Votruba writes about PHP and education</title>
<<<<<<< HEAD
        <link>
        https://tomasvotruba.com/</link>
        <description>{{ \App\Enum\Design::BLOG_TITLE }}</description>
=======
        <link>https://tomasvotruba.com/</link>
        <description>PHP, Communities  posts by Tomas Votruba</description>
>>>>>>> 2861e0a805 (mics)
        <pubDate>{{ "now"|date('r') }}</pubDate>
        <atom:link href="https://tomasvotruba.com/rss.xml" rel="self" type="application/rss+xml"/>

        <lastBuildDate>{{ $most_recent_post_date_time_stamp }}</lastBuildDate>

<<<<<<< HEAD
        @foreach ($posts as $post)
=======
        {{-- https://stackoverflow.com/a/29161205/1348344 --}}

        @foreach($posts as $post)
>>>>>>> 2861e0a805 (mics)
            @php
                $post_absolute_url = route(\App\Enum\RouteName::POST_DETAIL, ['slug' => $post->getSlug()]);
            @endphp

            <item>
                <title><![CDATA[ {{ $post->getClearTitle() }} ]]></title>
                <link>{{ $post_absolute_url }}</link>
                <description><![CDATA[ {!! $post->getPerex() !!} ]]></description>
                <content><![CDATA[ {!! $post->getContent() !!} ]]></content>
                <guid isPermaLink="false">{{ $post_absolute_url }}</guid>
                <dc:creator><![CDATA[ Tomas Votruba ]]></dc:creator>

                {{-- @see https://wordpress.stackexchange.com/a/229773 --}}
                <pubDate>{{ $post->getDateTime()->format('D, d M Y H:i:s +0000') }}</pubDate>
                <lastBuildDate>{{ $post->getDateTime()->format('D, d M Y H:i:s +0000') }}</lastBuildDate>
            </item>
        @endforeach
    </channel>
</rss>
