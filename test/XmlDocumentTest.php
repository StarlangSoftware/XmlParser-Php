<?php

namespace olcaytaner\XmlParser;

use PHPUnit\Framework\TestCase;

class XmlDocumentTest extends TestCase
{
    public function testRead()
    {
        $doc = new XmlDocument("../test.xml");
        $doc->parse();
        $root = $doc->getFirstChild();
        $this->assertEquals("frameset", $root->getName());
        $firstChild = $root->getFirstChild();
        $this->assertEquals("role", $firstChild->getName());
        $this->assertEquals("ali veli \"deneme yapmak\" = anlamında > bir deyim", $firstChild->getPcData());
        $secondChild = $firstChild->getNextSibling();
        $this->assertEquals("perceiver, alien \"x3\" to whom?", $secondChild->getAttributeValue("descr"));
        $this->assertEquals("PAG", $secondChild->getAttributeValue("f"));
        $this->assertEquals("2", $secondChild->getAttributeValue("n"));
    }
}
