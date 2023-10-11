<?php

namespace YaySMTPAmazonSES\Aws3\Aws\Api\Serializer;

use YaySMTPAmazonSES\Aws3\Aws\Api\Service;
use YaySMTPAmazonSES\Aws3\Aws\Api\StructureShape;

/**
 * @internal
 */
class RestXmlSerializer extends \YaySMTPAmazonSES\Aws3\Aws\Api\Serializer\RestSerializer {
  /** @var XmlBody */
  private $xmlBody;
  /**
   * @param Service $api      Service API description
   * @param string  $endpoint Endpoint to connect to
   * @param XmlBody $xmlBody  Optional XML formatter to use
   */
  public function __construct(\YaySMTPAmazonSES\Aws3\Aws\Api\Service $api, $endpoint, \YaySMTPAmazonSES\Aws3\Aws\Api\Serializer\XmlBody $xmlBody = null) {
    parent::__construct($api, $endpoint);
    $this->xmlBody = $xmlBody ?: new \YaySMTPAmazonSES\Aws3\Aws\Api\Serializer\XmlBody($api);
  }
  protected function payload(\YaySMTPAmazonSES\Aws3\Aws\Api\StructureShape $member, array $value, array &$opts) {
    $opts['headers']['Content-Type'] = 'application/xml';
    $opts['body'] = (string) $this->xmlBody->build($member, $value);
  }
}
