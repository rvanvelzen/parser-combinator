<?php
namespace ES\Parser\Parser;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;
use RuntimeException;

class RegexParser extends Parser
{
    /** @var string */
    private $regex;

    /**
     * @param string $regex
     */
    public function __construct($regex)
    {
        $this->regex = $regex;
    }

    /**
     * @param Input $input
     * @param int $offset
     * @return Result[]
     */
    protected function match(Input $input, $offset)
    {
        if ($offset >= $input->getLength()) {
            throw new FailureException('Unexpected EOF', $offset);
        }

        $match = null;
        if (preg_match($this->regex, $input->getSubstring($offset), $match, PREG_OFFSET_CAPTURE) === 1) {
            list($text, $position) = $match[0];
            if ($position > 0) {
                throw new RuntimeException(sprintf(
                    'Matched position for regex [%d] was greater than offset [%d]',
                    $position,
                    $offset
                ));
            }

            return [$this->expandResult(new Result\StringResult($text))];
        }

        throw new FailureException(sprintf('Unable to match "%s"', $this->regex), $offset);
    }
}
