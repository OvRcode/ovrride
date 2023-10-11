<?php

namespace YaySMTPAmazonSES\Aws3\Aws\Api;

/**
 * Represents a map shape.
 */
class MapShape extends \YaySMTPAmazonSES\Aws3\Aws\Api\Shape
{
    /** @var Shape */
    private $value;
    /** @var Shape */
    private $key;
    public function __construct(array $definition, \YaySMTPAmazonSES\Aws3\Aws\Api\ShapeMap $shapeMap)
    {
        $definition['type'] = 'map';
        parent::__construct($definition, $shapeMap);
    }
    /**
     * @return Shape
     * @throws \RuntimeException if no value is specified
     */
    public function getValue()
    {
        if (!$this->value) {
            if (!isset($this->definition['value'])) {
                throw new \RuntimeException('No value specified');
            }
            $this->value = \YaySMTPAmazonSES\Aws3\Aws\Api\Shape::create($this->definition['value'], $this->shapeMap);
        }
        return $this->value;
    }
    /**
     * @return Shape
     */
    public function getKey()
    {
        if (!$this->key) {
            $this->key = isset($this->definition['key']) ? \YaySMTPAmazonSES\Aws3\Aws\Api\Shape::create($this->definition['key'], $this->shapeMap) : new \YaySMTPAmazonSES\Aws3\Aws\Api\Shape(['type' => 'string'], $this->shapeMap);
        }
        return $this->key;
    }
}
