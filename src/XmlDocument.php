<?php

namespace olcaytaner\XmlParser;

class XmlDocument
{
    private string $fileName;
    private ?XmlElement $root = null;
    private XmlTokenType $lastReadTokenType = XmlTokenType::XML_END;
    private string $data;
    private int $position;
    private string $nextChar;

    /**
     * Creates an empty xml document.
     * @param string $fileName Name of the xml file
     */
    public function __construct(string $fileName)
    {
        $this->fileName = $fileName;
        $this->position = 0;
    }

    private function readChar(): string
    {
        $ch = $this->data[$this->position];
        $this->position++;
        return $ch;
    }

    /**
     * Reads a token character by character from xml file.
     * @param string $previousChar Previous character read
     * @param bool $extraAllowed If true, space or slash is allowed in the token, otherwise it is not allowed
     * @param bool $quotaAllowed If true, quota is allowed in the token, otherwise it is not allowed
     * @return string Token read
     */
    private function readToken(string $previousChar, bool $extraAllowed = false, bool $quotaAllowed = false): string
    {
        $ch = $previousChar;
        $buffer = "";
        while (($ch != "'" || $extraAllowed) && ($ch != "\"" || $quotaAllowed) && ($ch != "=" || $quotaAllowed) && ($ch != " " || $extraAllowed) && ($ch != "/" || $extraAllowed) && ($ch != null) && ($ch != '<') && ($ch != '>' || $quotaAllowed)) {
            $buffer = $buffer . $ch;
            $ch = $this->readChar();
        }
        $this->nextChar = $ch;
        return $buffer;
    }

    /**
     * Parses a tag like <mytag> or </mytag>
     * @return string Token read
     */
    private function parseTag(): string
    {
        $ch = $this->readChar();
        if ($ch == "/") {
            $this->lastReadTokenType = XmlTokenType::XML_CLOSING_TAG_WITHOUT_ATTRIBUTES;
            $ch = $this->readChar();
        } else {
            $this->lastReadTokenType = XmlTokenType::XML_OPENING_TAG_WITH_ATTRIBUTES;
        }
        $token = $this->readToken($ch);
        $ch = $this->nextChar;
        if ($ch == ">" && $this->lastReadTokenType == XmlTokenType::XML_OPENING_TAG_WITH_ATTRIBUTES) {
            $this->lastReadTokenType = XmlTokenType::XML_OPENING_TAG_WITHOUT_ATTRIBUTES;
        }
        if ($this->lastReadTokenType == XmlTokenType::XML_CLOSING_TAG_WITHOUT_ATTRIBUTES && $ch != ">") {
            $this->lastReadTokenType = XmlTokenType::XML_END;
            return "";
        } else {
            return $token;
        }
    }

    /**
     * Parses an attribute value like "attribute value" or 'attribute value'
     * @return string Attribute value read
     */
    private function parseAttributeValue(): string
    {
        $ch = $this->readChar();
        if ($ch == "\"") {
            $this->lastReadTokenType = XmlTokenType::XML_ATTRIBUTE_VALUE;
            return "";
        }
        $token = $this->readToken($ch, true);
        $ch = $this->nextChar;
        if ($ch != "\"") {
            $this->lastReadTokenType = XmlTokenType::XML_END;
            return "";
        }
        $this->lastReadTokenType = XmlTokenType::XML_ATTRIBUTE_VALUE;
        return $token;
    }

    /**
     * Parses a tag like />
     * @return ""
     */
    private function parseEmptyTag(): string
    {
        $ch = $this->readChar();
        if ($ch != ">") {
            $this->lastReadTokenType = XmlTokenType::XML_END;
        } else {
            $this->lastReadTokenType = XmlTokenType::XML_CLOSING_TAG_WITH_ATTRIBUTES;
        }
        return "";
    }

    private function getNextToken(XmlTextType $xmlTextType): string
    {
        $ch = $this->readChar();
        while ($ch == " " || $ch == "\t" || $ch == "\n") {
            $ch = $this->readChar();
        }
        switch ($ch) {
            case  "<":
                return $this->parseTag();
            case "\"":
                if ($xmlTextType == XmlTextType::XML_TEXT_VALUE) {
                    $token = $this->readToken($ch, true, true);
                    $ch = $this->nextChar;
                    $this->lastReadTokenType = XmlTokenType::XML_TEXT;
                    $this->position--;
                    return $token;
                } else {
                    return $this->parseAttributeValue();
                }
            case  "/":
                return $this->parseEmptyTag();
            case  "=":
                if ($xmlTextType == XmlTextType::XML_TEXT_VALUE) {
                    $token = $this->readToken($ch, true, true);
                    $ch = $this->nextChar;
                    $this->lastReadTokenType = XmlTokenType::XML_TEXT;
                    $this->position--;
                    return $token;
                } else {
                    $this->lastReadTokenType = XmlTokenType::XML_EQUAL;
                }
                break;
            case  ">":
                if ($xmlTextType == XmlTextType::XML_TEXT_VALUE) {
                    $token = $this->readToken($ch, true, true);
                    $ch = $this->nextChar;
                    $this->lastReadTokenType = XmlTokenType::XML_TEXT;
                    $this->position--;
                    return $token;
                } else {
                    $this->lastReadTokenType = XmlTokenType::XML_OPENING_TAG_FINISH;
                }
                return "";
            case null:
                $this->lastReadTokenType = XmlTokenType::XML_END;
                return "";
            default  :
                if ($xmlTextType == XmlTextType::XML_TEXT_VALUE) {
                    $token = $this->readToken($ch, true, true);
                    $ch = $this->nextChar;
                } else {
                    $token = $this->readToken($ch, true);
                    $ch = $this->nextChar;
                }
                $this->lastReadTokenType = XmlTokenType::XML_TEXT;
                $this->position--;
                return $token;
        }
        return "";
    }

    /**
     * Parses given xml document
     */
    public function parse(): void
    {
        $textType = XmlTextType::XML_TEXT_ATTRIBUTE;
        $siblingClosed = false;
        $parent = NULL;
        $sibling = NULL;
        $xmlAttribute = NULL;
        $current = NULL;
        $myfile = fopen($this->fileName, "r");
        $this->data = fread($myfile, filesize($this->fileName));
        fclose($myfile);
        $token = $this->getNextToken($textType);
        while ($this->lastReadTokenType != XmlTokenType::XML_END) {
            switch ($this->lastReadTokenType) {
                case XmlTokenType::XML_OPENING_TAG_WITH_ATTRIBUTES:
                case XmlTokenType::XML_OPENING_TAG_WITHOUT_ATTRIBUTES:
                    if ($parent == null){
                        $current = new XmlElement($token);
                    } else {
                        $current = new XmlElement($token, $parent);
                    }
                    if ($parent != null) {
                        if ($sibling != null && $siblingClosed) {
                            $sibling->setNextSibling($current);
                            $sibling = $current;
                        } else {
                            $parent->setFirstChild($current);
                        }
                    } else {
                        if ($this->root == null) {
                            $this->root = $current;
                        }
                    }
                    $parent = $current;
                    $siblingClosed = false;
                    if ($this->lastReadTokenType == XmlTokenType::XML_OPENING_TAG_WITH_ATTRIBUTES) {
                        $textType = XmlTextType::XML_TEXT_ATTRIBUTE;
                    } else {
                        $textType = XmlTextType::XML_TEXT_VALUE;
                    }
                    break;
                case XmlTokenType::XML_OPENING_TAG_FINISH:
                    $textType = XmlTextType::XML_TEXT_VALUE;
                    $siblingClosed = false;
                    break;
                case XmlTokenType::XML_CLOSING_TAG_WITH_ATTRIBUTES:
                    $sibling = $current;
                    $parent = $current->getParent();
                    $textType = XmlTextType::XML_TEXT_VALUE;
                    $siblingClosed = true;
                    break;
                case XmlTokenType::XML_CLOSING_TAG_WITHOUT_ATTRIBUTES:
                    if ($token == $current->getName()) {
                        $sibling = $current;
                        $parent = $current->getParent();
                    } else {
                        if ($token == $current->getParent()->getName()) {
                            $sibling = $parent;
                            $parent = $current->getParent()->getParent();
                            $current = $current->getParent();
                        }
                    }
                    $siblingClosed = true;
                    $textType = XmlTextType::XML_TEXT_VALUE;
                    break;
                case XmlTokenType::XML_ATTRIBUTE_VALUE:
                    if ($token != "") {
                        $token = $this->replaceEscapeCharacters($token);
                        $xmlAttribute->setValue($token);
                    } else {
                        $xmlAttribute->setValue("");
                    }
                    $current->addAttribute($xmlAttribute);
                    $textType = XmlTextType::XML_TEXT_ATTRIBUTE;
                    break;
                case XmlTokenType::XML_EQUAL:
                    $textType = XmlTextType::XML_TEXT_NOT_AVAILABLE;
                    break;
                case XmlTokenType::XML_TEXT:
                    if ($textType == XmlTextType::XML_TEXT_ATTRIBUTE) {
                        $xmlAttribute = new XmlAttribute($token);
                    } else {
                        if ($textType == XmlTextType::XML_TEXT_VALUE) {
                            $token = $this->replaceEscapeCharacters($token);
                            $current->setPcData($token);
                        }
                    }
                    break;
                default:
                    break;
            }
            $token = $this->getNextToken($textType);
        }
    }

    public function getFirstChild(): ?XmlElement
    {
        return $this->root;
    }

    private function replaceEscapeCharacters(string $token): string
    {
        $result = $token;
        while (str_contains($result,"&quot;")){
            $result = str_replace("&quot;", "\"", $result);
        }
        while (str_contains($result,"&amp;")){
            $result = str_replace("&amp;", "&", $result);
        }
        while (str_contains($result,"&lt;")){
            $result = str_replace("&lt;", "<", $result);
        }
        while (str_contains($result,"&gt;")){
            $result = str_replace("&gt;", ">", $result);
        }
        while (str_contains($result,"&apos;")){
            $result = str_replace("&apos;", "'", $result);
        }
        return $result;
    }
}