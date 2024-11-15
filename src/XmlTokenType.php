<?php

namespace olcaytaner\XmlParser;

enum XmlTokenType
{
    case XML_OPENING_TAG_WITH_ATTRIBUTES;
    case XML_OPENING_TAG_WITHOUT_ATTRIBUTES;
    case XML_OPENING_TAG_FINISH;
    case XML_CLOSING_TAG_WITH_ATTRIBUTES;
    case XML_CLOSING_TAG_WITHOUT_ATTRIBUTES;
    case XML_ATTRIBUTE_VALUE;
    case XML_EQUAL;
    case XML_TEXT;
    case XML_END;
}