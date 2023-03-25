@php
    /** @var $posts \App\Entity\Post[] */
@endphp

<feed xmlns="http://www.w3.org/2005/Atom">
    <id>{{ action(\App\Http\Controllers\RssController::class) }}</id>
    <link href="{{ action(\App\Http\Controllers\RssController::class) }}" />
    <title>
        <![CDATA[ {{ \App\Enum\Design::BLOG_TITLE }} ]]>
    </title>
    <description/>
    <language/>

    <updated>{{ $most_recent_post_date_time_stamp }}</updated>

    @foreach ($posts as $post)
        <entry>
            <title><![CDATA[ {{ $post->getClearTitle() }} ]]></title>
            <link rel="alternate" href="{{ $post->getAbsoluteUrl() }}" />
            <id>{{ $post->getAbsoluteUrl() }}</id>

            <author>
                <name><![CDATA[ Tomas Votruba ]]></name>
            </author>

            <summary type="html">
                <![CDATA[ {!! $post->getPerex() !!} ]]>
            </summary>

            <updated>{{ $post->getDateTime()->format('D, d M Y H:i:s +0000') }}</updated>
        </entry>
    @endforeach

</feed>
