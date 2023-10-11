<?php

namespace YaySMTPAmazonSES\Aws3\Aws\Handler\GuzzleV5;

use YaySMTPAmazonSES\Aws3\GuzzleHttp\Stream\StreamDecoratorTrait;
use YaySMTPAmazonSES\Aws3\GuzzleHttp\Stream\StreamInterface as GuzzleStreamInterface;

/**
 * Adapts a Guzzle 5 Stream to a PSR-7 Stream.
 *
 * @codeCoverageIgnore
 */
class PsrStream implements \YaySMTPAmazonSES\Aws3\Psr\Http\Message\StreamInterface {
  use StreamDecoratorTrait;
  /** @var GuzzleStreamInterface */
  private $stream;
  public function __construct(\YaySMTPAmazonSES\Aws3\GuzzleHttp\Stream\StreamInterface $stream) {
    $this->stream = $stream;
  }
  public function rewind() {
    $this->stream->seek(0);
  }
  public function getContents() {
    return $this->stream->getContents();
  }
}
