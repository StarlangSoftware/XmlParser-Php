<?php

namespace olcaytaner\XmlParser;

class XmlElement
{
    private string $name = "";
    private string $pcData = "";
    private array $attributes;
    private ?XmlElement $parent;
    private XmlElement $firstChild;
    private XmlElement $nextSibling;

    /**
     * Constructor for xml element. Allocates memory and initializes an element.
     * @param string $name Name of the element
     * @param XmlElement $parent Parent of the Xml Element
     */
    function __construct(string $name, XmlElement $parent = NULL)
    {
        $this->name = $name;
        $this->parent = $parent;
        $this->attributes = [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPcData(): string
    {
        return $this->pcData;
    }

    public function getFirstChild(): XmlElement
    {
        return $this->firstChild;
    }

    public function getNextSibling(): XmlElement
    {
        return $this->nextSibling;
    }

    public function getParent(): ?XmlElement
    {
        return $this->parent;
    }

    /**
     * Sets the value of an attribute to a given value
     * @param string $attributeName Name of the attribute
     * @param string $attributeValue New attribute value
     */
    public function setAttributeValue(string $attributeName, string $attributeValue): void
    {
        for ($i = 0; $i < sizeof($this->attributes); $i++) {
            if ($this->attributes[$i]->getName() == $attributeName) {
                $this->attributes[$i]->setValue($attributeValue);
            }
        }
    }

    /**
     * Finds the attribute with the given name of an Xml element
     * @param string $attributeName Name of the attribute
     * @return string If the Xml element has such an attribute returns its value, otherwise it returns NULL
     */
    public function getAttributeValue(string $attributeName): string
    {
        for ($i = 0; $i < sizeof($this->attributes); $i++) {
            if ($this->attributes[$i]->getName() == $attributeName) {
                return $this->attributes[$i]->getValue();
            }
        }
        return "";
    }

    public function attributeSize(): int
    {
        return sizeof($this->attributes);
    }

    public function getAttribute(int $index): XmlAttribute
    {
        return $this->attributes[$index];
    }

    public function setNextSibling(XmlElement $nextSibling): void
    {
        $this->nextSibling = $nextSibling;
    }

    public function setFirstChild(XmlElement $firstChild): void
    {
        $this->firstChild = $firstChild;
    }

    public function addAttribute(XmlAttribute $attribute): void
    {
        $this->attributes[] = $attribute;
    }

    public function setPcData(string $pcData): void
    {
        $this->pcData = $pcData;
    }

    public function hasAttributes(): bool
    {
        return sizeof($this->attributes) > 0;
    }
}