<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
     xmlns:content="https://purl.org/rss/1.0/modules/content/"
     xmlns:dc="https://purl.org/dc/elements/1.1/"
     xmlns:atom="https://www.w3.org/2005/Atom"
>
    <channel>
        <title>Tomas Votruba writes about PHP and education</title>
        <link>https://tomasvotruba.com/</link>
        <description>{{ \App\Enum\Design::BLOG_TITLE }}</description>
        <pubDate>{{ date('r') }}</pubDate>
        <atom:link href="https://tomasvotruba.com/rss.xml" rel="self" type="application/rss+xml"/>
        <lastBuildDate>{{ $most_recent_post_date_time_stamp }}</lastBuildDate>

        @php
            /** @var $posts \App\Entity\Post[] */
        @endphp

        @foreach ($posts as $post)
            <item>
                <title><![CDATA[ {{ $post->getClearTitle() }} ]]></title>
                <link>{{ $post->getAbsoluteUrl() }}</link>
                <description><![CDATA[ {!! $post->getPerex() !!} ]]></description>
                <content><![CDATA[ {!! $post->getContent() !!} ]]></content>
                <guid isPermaLink="false">{{ $post->getAbsoluteUrl() }}</guid>
                <dc:creator><![CDATA[ Tomas Votruba ]]></dc:creator>

                {{-- @see https://wordpress.stackexchange.com/a/229773 --}}
                <pubDate>{{ $post->getDateTime()->format('D, d M Y H:i:s +0000') }}</pubDate>
                <lastBuildDate>{{ $post->getDateTime()->format('D, d M Y H:i:s +0000') }}</lastBuildDate>
            </item>
        @endforeach
    </channel>
</rss>
