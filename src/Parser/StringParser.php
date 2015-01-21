<?php
namespace ES\Parser\Parser;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;

class StringParser extends Parser
{
    /** @var string */
    private $string;
    /** @var int */
    private $length;

    /**
     * @param string $string
     */
    public function __construct($string)
    {
        $this->string = $string;
        $this->length = strlen($string);
    }

    /**
     * @param string $string
     * @param int $offset
     * @return Result
     */
    public function match($string, $offset = 0)
    {
        if ($offset >= strlen($string)) {
            throw (new FailureException('Unexpected EOF', $offset))
                ->setExpecting($this->string);
        }

        if (substr_compare($string, $this->string, $offset, $this->length) === 0) {
            return new Result\StringResult($this->string);
        }

        throw (new FailureException('Unable to match', $offset))
            ->setExpecting($this->string);
    }
}
