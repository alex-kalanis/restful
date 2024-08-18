<?php

namespace Tests\Picabo\Restful\Mapping;

require_once __DIR__ . '/../../../bootstrap.php';

use Picabo\Restful\Mapping\XmlMapper;
use Tester;
use Tester\Assert;
use Tests\TestCase;

/**
 * Test: Tests\Picabo\Restful\Mapping\XmlMapper.
 *
 * @testCase Tests\Picabo\Restful\Mapping\XmlMapperTest
 * @author Drahomír Hanák
 * @package Tests\Picabo\Restful\Mapping
 */
class XmlMapperTest extends TestCase
{

    /** @var XmlMapper */
    private $mapper;

    public function testConvertDataArrayToXml(): void
    {
        $xml = $this->mapper->stringify(array('node' => 'value'));
        $dom = Tester\DomQuery::fromXml($xml);
        Assert::true($dom->has('root'));
        Assert::true($dom->has('root node'));
    }

    public function testConvertArrayListWithNumericIndexesToXml(): void
    {
        $data = array('hello', 'world');
        $xml = $this->mapper->stringify($data);

        $dom = Tester\DomQuery::fromXml($xml);
        $items = $dom->find('root item');
        Assert::equal(count($items), 2);
        Assert::equal((string)$items[0], 'hello');
        Assert::equal((string)$items[1], 'world');
    }

    public function testConvertArrayListWithNumericIndexesUsingParentKeyToXml(): void
    {
        $data = array(
            'user' => array(
                array('id' => 1, 'name' => 'Tester'),
                array('id' => 2, 'name' => 'Test')
            )
        );
        $xml = $this->mapper->stringify($data);

        $dom = Tester\DomQuery::fromXml($xml);
        $items = $dom->find('root user');
        Assert::equal(count($items), 2);
        Assert::equal((string)$items[0]->name, 'Tester');
        Assert::equal((string)$items[1]->name, 'Test');
    }

    public function testConvertArrayListWithNumericIndexesInAnotherArrayListUsingLastStringParentKeyToXml(): void
    {
        $data = array(
            'user' => array(
                array(
                    array('id' => 1, 'name' => 'Tester'),
                    array('id' => 2, 'name' => 'Test')
                )
            )
        );
        $xml = $this->mapper->stringify($data);

        $dom = Tester\DomQuery::fromXml($xml);
        $items = $dom->find('root user user');
        Assert::equal(count($items), 2);
        Assert::equal((string)$items[0]->name, 'Tester');
        Assert::equal((string)$items[1]->name, 'Test');
    }

    public function testSetCustomItemElementName(): void
    {
        $data = array('hello', 'world');
        $this->mapper->setRootElement('base');
        $xml = $this->mapper->stringify($data);
        $dom = Tester\DomQuery::fromXml($xml);

        $items = $dom->find('base item');
        Assert::equal(count($items), 2);
        Assert::equal((string)$items[0], 'hello');
        Assert::equal((string)$items[1], 'world');
    }

    public function testConvertXmlToDataArray(): void
    {
        $array = $this->mapper->parse('<?xml version="1.0" encoding="utf-8" ?><root><node>value</node></root>');
        Assert::equal($array['node'], 'value');
    }

    public function testConvertsXmlRecursivelyToArray(): void
    {
        $array = $this->mapper->parse('<?xml version="1.0" encoding="UTF-8"?>
			<envelope>
			   <user>
			     <name>test</name>
			     <phone>500</phone>
			  </user>
			</envelope>');
        Assert::equal($array['user']['name'], 'test');
    }

    public function testThrowsMappingExceptionIfInvalidXMLisGiven(): void
    {
        Assert::throws(function () {
            $this->mapper->parse('<?xml version="1.0" encoding="UTF-8"?>
				<envelope>
				   <user>
				     <name>test
				     <phone>500</phone>
				  </user>
				</envelope>');
        }, 'Picabo\Restful\Mapping\MappingException');
    }

    public function testConvertsEmptyElementsToEmptyString(): void
    {
        $array = $this->mapper->parse('<?xml version="1.0" encoding="UTF-8"?>
			<envelope>
			   <user>
			     <name>test</name>
			     <phone></phone>
			     <friends></friends>
			  </user>
			</envelope>');
        Assert::equal($array['user']['phone'], '');
        Assert::equal($array['user']['friends'], '');
    }

    public function testRemoveAttributes(): void
    {
        $array = $this->mapper->parse('<?xml version="1.0" encoding="UTF-8"?>
			<envelope>
			   <user id="5">
			     <name>test</name>
			     <age type="int"></age>
			  </user>
			</envelope>');
        Assert::same($array['user'], array(
            'name' => 'test',
            'age' => ''
        ));
    }

    public function testParseEmptyDataset(): void
    {
        $data = $this->mapper->parse('<?xml version="1.0" encoding="UTF-8"?><root></root>');
        Assert::same(count($data), 0);
    }

    public function testResetDocumentContentOnEveryCall(): void
    {
        $data = array('node' => 'value');
        $this->mapper->stringify($data);
        $xml = $this->mapper->stringify($data);

        $dom = Tester\DomQuery::fromXml($xml);
        $nodes = $dom->find('root node');
        Assert::equal(count($nodes), 1);
    }

    public function testDoesNotConvertAccentsToXMLentities(): void
    {
        $data = array('node' => 'ěščřžýáíé');
        $xml = $this->mapper->stringify($data, FALSE);

        Assert::equal(trim($xml), "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<root><node>ěščřžýáíé</node></root>");
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->mapper = new XmlMapper('root');
    }

}

(new XmlMapperTest())->run();
