<?php

namespace YaySMTPAmazonSES\Aws3\Aws\Api\Parser;

use YaySMTPAmazonSES\Aws3\Aws\CommandInterface;
use YaySMTPAmazonSES\Aws3\Aws\Exception\AwsException;
use YaySMTPAmazonSES\Aws3\Psr\Http\Message\ResponseInterface;
use YaySMTPAmazonSES\Aws3\GuzzleHttp\Psr7;
/**
 * @internal Decorates a parser and validates the x-amz-crc32 header.
 */
class Crc32ValidatingParser extends \YaySMTPAmazonSES\Aws3\Aws\Api\Parser\AbstractParser
{
    /** @var callable */
    private $parser;
    /**
     * @param callable $parser Parser to wrap.
     */
    public function __construct(callable $parser)
    {
        $this->parser = $parser;
    }
    public function __invoke(\YaySMTPAmazonSES\Aws3\Aws\CommandInterface $command, \YaySMTPAmazonSES\Aws3\Psr\Http\Message\ResponseInterface $response)
    {
        if ($expected = $response->getHeaderLine('x-amz-crc32')) {
            $hash = hexdec(\YaySMTPAmazonSES\Aws3\GuzzleHttp\Psr7\hash($response->getBody(), 'crc32b'));
            if ($expected != $hash) {
                throw new \YaySMTPAmazonSES\Aws3\Aws\Exception\AwsException("crc32 mismatch. Expected {$expected}, found {$hash}.", $command, ['code' => 'ClientChecksumMismatch', 'connection_error' => true, 'response' => $response]);
            }
        }
        $fn = $this->parser;
        return $fn($command, $response);
    }
}
