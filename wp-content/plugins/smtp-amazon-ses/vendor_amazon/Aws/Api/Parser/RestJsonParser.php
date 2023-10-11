<?php

namespace YaySMTPAmazonSES\Aws3\Aws\Api\Parser;

use YaySMTPAmazonSES\Aws3\Aws\Api\Service;
use YaySMTPAmazonSES\Aws3\Aws\Api\StructureShape;
use YaySMTPAmazonSES\Aws3\Psr\Http\Message\ResponseInterface;
/**
 * @internal Implements REST-JSON parsing (e.g., Glacier, Elastic Transcoder)
 */
class RestJsonParser extends \YaySMTPAmazonSES\Aws3\Aws\Api\Parser\AbstractRestParser
{
    use PayloadParserTrait;
    /** @var JsonParser */
    private $parser;
    /**
     * @param Service    $api    Service description
     * @param JsonParser $parser JSON body builder
     */
    public function __construct(\YaySMTPAmazonSES\Aws3\Aws\Api\Service $api, \YaySMTPAmazonSES\Aws3\Aws\Api\Parser\JsonParser $parser = null)
    {
        parent::__construct($api);
        $this->parser = $parser ?: new \YaySMTPAmazonSES\Aws3\Aws\Api\Parser\JsonParser();
    }
    protected function payload(\YaySMTPAmazonSES\Aws3\Psr\Http\Message\ResponseInterface $response, \YaySMTPAmazonSES\Aws3\Aws\Api\StructureShape $member, array &$result)
    {
        $jsonBody = $this->parseJson($response->getBody());
        if ($jsonBody) {
            $result += $this->parser->parse($member, $jsonBody);
        }
    }
}
