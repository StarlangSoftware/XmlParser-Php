<?php

namespace olcaytaner\XmlParser;

class XmlAttribute
{
    private string $name;
    private string $value;

    /**
     * Constructor for xml attribute. Initializes the attribute.
     * @param string $name Name of the attribute
     */
    function __construct(string $name)
    {
        $this->name = $name;
    }

    function getName(): string
    {
        return $this->name;
    }

    function getValue(): string
    {
        return $this->value;
    }

    function setValue($value): void
    {
        $this->value = $value;
    }

    function __toString()
    {
        return $this->name . "=\"" . $this->value . "\"";
    }
}