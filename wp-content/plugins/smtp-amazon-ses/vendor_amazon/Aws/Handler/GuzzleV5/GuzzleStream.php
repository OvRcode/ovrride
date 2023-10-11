<?php

namespace YaySMTPAmazonSES\Aws3\Aws\Handler\GuzzleV5;

use YaySMTPAmazonSES\Aws3\GuzzleHttp\Stream\StreamDecoratorTrait;
use YaySMTPAmazonSES\Aws3\GuzzleHttp\Stream\StreamInterface as GuzzleStreamInterface;
use YaySMTPAmazonSES\Aws3\Psr\Http\Message\StreamInterface as Psr7StreamInterface;
/**
 * Adapts a PSR-7 Stream to a Guzzle 5 Stream.
 *
 * @codeCoverageIgnore
 */
class GuzzleStream implements \YaySMTPAmazonSES\Aws3\GuzzleHttp\Stream\StreamInterface
{
    use StreamDecoratorTrait;
    /** @var Psr7StreamInterface */
    private $stream;
    public function __construct(\YaySMTPAmazonSES\Aws3\Psr\Http\Message\StreamInterface $stream)
    {
        $this->stream = $stream;
    }
}
