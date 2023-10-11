<?php

namespace YaySMTPAmazonSES\Aws3\Aws\Api\Serializer;

use YaySMTPAmazonSES\Aws3\Aws\Api\Service;
use YaySMTPAmazonSES\Aws3\Aws\Api\StructureShape;

/**
 * Serializes requests for the REST-JSON protocol.
 * @internal
 */
class RestJsonSerializer extends \YaySMTPAmazonSES\Aws3\Aws\Api\Serializer\RestSerializer {
  /** @var JsonBody */
  private $jsonFormatter;
  /** @var string */
  private $contentType;
  /**
   * @param Service  $api           Service API description
   * @param string   $endpoint      Endpoint to connect to
   * @param JsonBody $jsonFormatter Optional JSON formatter to use
   */
  public function __construct(\YaySMTPAmazonSES\Aws3\Aws\Api\Service $api, $endpoint, \YaySMTPAmazonSES\Aws3\Aws\Api\Serializer\JsonBody $jsonFormatter = null) {
    parent::__construct($api, $endpoint);
    $this->contentType = \YaySMTPAmazonSES\Aws3\Aws\Api\Serializer\JsonBody::getContentType($api);
    $this->jsonFormatter = $jsonFormatter ?: new \YaySMTPAmazonSES\Aws3\Aws\Api\Serializer\JsonBody($api);
  }
  protected function payload(\YaySMTPAmazonSES\Aws3\Aws\Api\StructureShape $member, array $value, array &$opts) {
    $opts['headers']['Content-Type'] = $this->contentType;
    $opts['body'] = (string) $this->jsonFormatter->build($member, $value);
  }
}
