<?php
namespace ES\Parser\Parser;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;

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
     * @param Input $input
     * @param int $offset
     * @return Result
     */
    protected function match(Input $input, $offset)
    {
        if ($offset >= $input->getLength()) {
            throw (new FailureException('Unexpected EOF', $offset))
                ->setExpecting($this->string);
        }

        $comparison = $input->getSubstring($offset, $this->length);
        if ($this->mode === self::CASE_INSENSITIVE) {
            $match = strcasecmp($this->string, $comparison) === 0;
        } else {
            $match = $this->string === $comparison;
        }

        if ($match) {
            return $this->expandResult(new Result\StringResult($this->string));
        }

        throw (new FailureException('Unable to match', $offset))
            ->setExpecting($this->string);
    }
}
