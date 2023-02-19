<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0"
     xmlns:content="https://purl.org/rss/1.0/modules/content/"
     xmlns:dc="https://purl.org/dc/elements/1.1/"
     xmlns:atom="https://www.w3.org/2005/Atom"
>
    <channel>
        <title>Tomas Votruba writes about PHP and education</title>
        <link>https://tomasvotruba.com/</link>
        <description>PHP, Communities and Communication posts by Tomas Votruba</description>
        <pubDate>{{ "now"|date('r') }}</pubDate>
        <atom:link href="https://tomasvotruba.com/rss.xml" rel="self" type="application/rss+xml" />

        <lastBuildDate>{{ $most_recent_post_date_time|date('r') }}</lastBuildDate>

        {{-- https://stackoverflow.com/a/29161205/1348344 --}}

        @foreach($posts as $post)
            @php
                $post_absolute_url = 'https://tomasvotruba.com'. path(\TomasVotruba\Website\ValueObject\RouteName::POST_DETAIL, ['slug' => $post->getSlug()]);
            @endphp

        <item>
            <title><![CDATA[ {{ $post->getTitle()|replace({'&nbsp;':' '}) }} ]]></title>
            <link>{{ $post_absolute_url }}</link>
            <description><![CDATA[ {{ $post->getPerex()|markdown|raw }} ]]></description>
            <content:encoded><![CDATA[ {{ $post->getHtmlContent()|raw }} ]]></content:encoded>
            <guid isPermaLink="false">{{ $post_absolute_url }}</guid>
            <dc:creator><![CDATA[ Tomas Votruba ]]></dc:creator>

            {{-- @see https://wordpress.stackexchange.com/a/229773 --}}
            <pubDate>{{ $post->getDateTime()|date('D, d M Y H:i:s +0000') }}</pubDate>
            <lastBuildDate>{{ $post->getDateTime()|date('D, d M Y H:i:s +0000') }}</lastBuildDate>

            <comments>{{ $post_absolute_url }}#disqus_thread</comments>
        </item>
        {% endfor %}
    </channel>
</rss>
