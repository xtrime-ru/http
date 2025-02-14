<?php declare(strict_types=1);

namespace Amp\Http;

use PHPUnit\Framework\TestCase;

class ParseFieldValueComponentsTest extends TestCase
{
    public function test(): void
    {
        self::assertSame([
            ['no-cache', ''],
            ['no-store', ''],
            ['must-revalidate', ''],
        ], $this->parse('no-cache, no-store, must-revalidate'));

        self::assertSame([
            ['public', ''],
            ['max-age', '31536000'],
        ], $this->parse('public, max-age=31536000'));

        self::assertSame([
            ['private', 'foo, bar'],
            ['max-age', '31536000'],
        ], $this->parse('private="foo, bar", max-age=31536000'));

        self::assertNull($this->parse('private="foo, bar, max-age=31536000'));

        self::assertSame([
            ['private', 'foo"bar'],
            ['max-age', '31536000'],
        ], $this->parse('private="foo\"bar", max-age=31536000'));

        self::assertSame([
            ['private', 'foo""bar'],
            ['max-age', '31536000'],
        ], $this->parse('private="foo\"\"bar", max-age=31536000'));

        self::assertSame([
            ['private', 'foo\\'],
            ['bar', ''],
        ], $this->parse('private="foo\\\\", bar'));

        self::assertSame([
            ['private', 'foo'],
            ['private', 'bar'],
        ], $this->parse('private="foo", private=bar'));
    }

    private function parse(string $headerValue): ?array
    {
        return parseFieldValueComponents($this->createMessage(['cache-control' => $headerValue]), 'cache-control');
    }

    private function createMessage(array $headers): HttpMessage
    {
        return new class($headers) extends HttpMessage {
            public function __construct(array $headers)
            {
                $this->replaceHeaders($headers);
            }
        };
    }
}
