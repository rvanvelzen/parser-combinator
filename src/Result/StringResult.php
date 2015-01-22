<?php
namespace ES\Parser\Result;

use ES\Parser\Result;

class StringResult extends Result
{
    /** @var string */
    private $string;
    /** @var int */
    private $length = 0;

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->string = $string;
        $this->length = strlen($string);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->string;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @return mixed
     */
    public function getSemanticValue()
    {
        $action = $this->getAction();
        if ($action) {
            return $action($this->string);
        }

        return $this->string;
    }
}
