<?php

namespace Tests\Unit;

use App\Support\Youtube;
use PHPUnit\Framework\TestCase;

class YoutubeTest extends TestCase
{
    public function test_watch_v_not_first_query_param(): void
    {
        $id = 'dQw4w9WgXcQ';
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/'.$id,
            Youtube::toEmbedUrl('https://www.youtube.com/watch?feature=share&si=xx&v='.$id)
        );
    }

    public function test_youtube_nocookie_embed_input(): void
    {
        $id = 'abcdefghijk';
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/'.$id,
            Youtube::toEmbedUrl('https://www.youtube-nocookie.com/embed/'.$id.'?rel=0')
        );
    }

    public function test_live_url(): void
    {
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/jfKfPfyJRdk',
            Youtube::toEmbedUrl('https://www.youtube.com/live/jfKfPfyJRdk')
        );
    }

    public function test_extract_from_markdown_line(): void
    {
        $id = 'dQw4w9WgXcQ';
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/'.$id,
            Youtube::toEmbedUrl('Lihat video: https://www.youtube.com/watch?v='.$id.' (wajib)')
        );
    }

    public function test_html_entity_ampersand_in_query(): void
    {
        $id = 'dQw4w9WgXcQ';
        $encoded = 'https://www.youtube.com/watch?v='.$id.'&amp;feature=youtu.be';
        $this->assertSame(
            'https://www.youtube-nocookie.com/embed/'.$id,
            Youtube::toEmbedUrl($encoded)
        );
    }

    public function test_rejects_non_youtube(): void
    {
        $this->assertNull(Youtube::toEmbedUrl('https://vimeo.com/123456'));
    }

    public function test_to_watch_url(): void
    {
        $this->assertSame(
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            Youtube::toWatchUrl('https://youtu.be/dQw4w9WgXcQ')
        );
    }
}
