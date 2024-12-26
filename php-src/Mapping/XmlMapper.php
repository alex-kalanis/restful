<?php

namespace kalanis\Restful\Mapping;


use DOMDocument;
use DOMNode;
use kalanis\Restful\Exceptions\InvalidArgumentException;
use kalanis\Restful\Mapping\Exceptions\MappingException;
use Nette\Utils\Arrays;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Traversable;


/**
 * XmlMapper
 * @package kalanis\Restful\Mapping
 */
class XmlMapper implements IMapper
{

    protected const ITEM_ELEMENT = 'item';

    private DOMDocument $xml;

    public function __construct(
        private string $rootElement = 'root',
    )
    {
    }

    /**
     * Get XML root element
     * @return string
     */
    public function getRootElement(): string
    {
        return $this->rootElement;
    }

    /**
     * Set XML root element
     */
    public function setRootElement(string $rootElement): self
    {
        $this->rootElement = $rootElement;
        return $this;
    }

    /**
     * Parse traversable or array resource data to XML
     * @param string|object|iterable<string|int, mixed> $data
     * @param bool $prettyPrint
     * @throws InvalidArgumentException
     * @return string
     */
    public function stringify(iterable|string|object $data, bool $prettyPrint = true): string
    {
        if (!is_string($data) && !is_array($data) && !($data instanceof Traversable)) {
            throw new InvalidArgumentException('Data must be of type string, array or Traversable');
        }

        if ($data instanceof Traversable) {
            $data = iterator_to_array($data);
        }

        $this->xml = new DOMDocument('1.0', 'UTF-8');
        $this->xml->formatOutput = $prettyPrint;
        $this->xml->preserveWhiteSpace = $prettyPrint;
        $root = $this->xml->createElement($this->rootElement);
        $this->xml->appendChild($root);
        $this->toXml($data, $root, self::ITEM_ELEMENT);
        $stored = $this->xml->saveXML();
        if (false === $stored) {
            throw new \RuntimeException('Storing XML failed');
        }
        return $stored;
    }

    /**
     * @param array<string|int, mixed>|string $data
     * @param DOMNode $xml
     * @param string $previousKey
     */
    private function toXml(array|string $data, DOMNode $xml, string $previousKey): void
    {
        if (is_iterable($data)) {
            foreach ($data as $key => $value) {
                $node = $xml;
                if (is_int($key)) {
                    $node = $this->xml->createElement($previousKey);
                    $xml->appendChild($node);
                } elseif (!Arrays::isList($value)) {
                    $node = $this->xml->createElement($key);
                    $xml->appendChild($node);
                }
                $this->toXml($value, $node ?: $xml, is_string($key) ? $key : $previousKey);
            }
        } else {
            $xml->appendChild($this->xml->createTextNode($data));
        }
    }

    /**
     * Parse XML to array
     * @param string $data
     * @throws  MappingException If XML data is not valid
     * @return string|object|iterable<string|int, string|int|float|bool|null>
     *
     */
    public function parse(mixed $data): iterable|string|object
    {
        return $this->fromXml(strval($data));
    }

    /**
     * @param string $data
     * @throws  MappingException If XML data is not valid
     * @return iterable<string|int, string|int|float|bool|null>
     *
     */
    private function fromXml(string $data): iterable
    {
        try {
            $useErrors = libxml_use_internal_errors(true);
            $xml = simplexml_load_string($data, null, LIBXML_NOCDATA);
            if (false === $xml) {
                $error = libxml_get_last_error();
                if ($error) {
                    throw new MappingException('Input is not valid XML document: ' . $error->message . ' on line ' . $error->line);
                } else {
                    throw new MappingException('Total parser failure. Document not valid and cannot get last error.');
                }
            }
            libxml_clear_errors();
            libxml_use_internal_errors($useErrors);

            $data = Json::decode(Json::encode((array) $xml), true);
            return $data ? $this->normalize((array) $data) : [];
        } catch (JsonException $e) {
            throw new MappingException('Error in parsing response: ' . $e->getMessage());
        }
    }

    /**
     * Normalize data structure to accepted form
     * @param array<string|int, mixed> $value
     * @return array<string|int, string|int|float|bool|null>
     */
    private function normalize(array $value): array
    {
        if (isset($value['@attributes'])) {
            unset($value['@attributes']);
        }
        if (0 === count($value)) {
            return [];
        }

        foreach ($value as $key => $node) {
            if (!is_array($node)) {
                continue;
            }
            $value[$key] = $this->normalize($node);
        }
        return $value;
    }
}
