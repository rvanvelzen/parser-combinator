<?php
namespace ES\Parser\Parser;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;

class StringParser extends Parser
{
    const CASE_SENSITIVE = 'sensitive';
    const CASE_INSENSITIVE = 'ignore';

    /** @var string */
    private $string;
    /** @var int */
    private $length;
    /** @var string */
    private $mode;

    /**
     * @param string $string
     * @param string $mode
     */
    public function __construct($string, $mode = self::CASE_SENSITIVE)
    {
        $this->string = $string;
        $this->length = strlen($string);
        $this->mode = $mode;
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

        $caseInsensitive = $this->mode === self::CASE_INSENSITIVE;
        if (substr_compare($string, $this->string, $offset, $this->length, $caseInsensitive) === 0) {
            return new Result\StringResult($this->string);
        }

        throw (new FailureException('Unable to match', $offset))
            ->setExpecting($this->string);
    }
}
