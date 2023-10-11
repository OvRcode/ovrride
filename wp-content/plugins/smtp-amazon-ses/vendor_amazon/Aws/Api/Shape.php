<?php

namespace YaySMTPAmazonSES\Aws3\Aws\Api;

/**
 * Base class representing a modeled shape.
 */
class Shape extends \YaySMTPAmazonSES\Aws3\Aws\Api\AbstractModel
{
    /**
     * Get a concrete shape for the given definition.
     *
     * @param array    $definition
     * @param ShapeMap $shapeMap
     *
     * @return mixed
     * @throws \RuntimeException if the type is invalid
     */
    public static function create(array $definition, \YaySMTPAmazonSES\Aws3\Aws\Api\ShapeMap $shapeMap)
    {
        static $map = ['structure' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\StructureShape', 'map' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\MapShape', 'list' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\ListShape', 'timestamp' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\TimestampShape', 'integer' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\Shape', 'double' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\Shape', 'float' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\Shape', 'long' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\Shape', 'string' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\Shape', 'byte' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\Shape', 'character' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\Shape', 'blob' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\Shape', 'boolean' => 'YaySMTPAmazonSES\\Aws3\\Aws\\Api\\Shape'];
        if (isset($definition['shape'])) {
            return $shapeMap->resolve($definition);
        }
        if (!isset($map[$definition['type']])) {
            throw new \RuntimeException('Invalid type: ' . print_r($definition, true));
        }
        $type = $map[$definition['type']];
        return new $type($definition, $shapeMap);
    }
    /**
     * Get the type of the shape
     *
     * @return string
     */
    public function getType()
    {
        return $this->definition['type'];
    }
    /**
     * Get the name of the shape
     *
     * @return string
     */
    public function getName()
    {
        return $this->definition['name'];
    }
}
