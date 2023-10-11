<?php

namespace YaySMTPAmazonSES\Aws3\Aws\Api\Parser;

use YaySMTPAmazonSES\Aws3\Aws\Api\Service;
use YaySMTPAmazonSES\Aws3\Aws\Result;
use YaySMTPAmazonSES\Aws3\Aws\CommandInterface;
use YaySMTPAmazonSES\Aws3\Psr\Http\Message\ResponseInterface;
/**
 * @internal Implements JSON-RPC parsing (e.g., DynamoDB)
 */
class JsonRpcParser extends \YaySMTPAmazonSES\Aws3\Aws\Api\Parser\AbstractParser
{
    use PayloadParserTrait;
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
    public function __invoke(\YaySMTPAmazonSES\Aws3\Aws\CommandInterface $command, \YaySMTPAmazonSES\Aws3\Psr\Http\Message\ResponseInterface $response)
    {
        $operation = $this->api->getOperation($command->getName());
        $result = null === $operation['output'] ? null : $this->parser->parse($operation->getOutput(), $this->parseJson($response->getBody()));
        return new \YaySMTPAmazonSES\Aws3\Aws\Result($result ?: []);
    }
}
