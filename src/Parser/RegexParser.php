<?php
namespace ES\Parser\Parser;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;
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
     * @param string $string
     * @param int $offset
     * @return Result
     */
    public function match($string, $offset = 0)
    {
        if ($offset >= strlen($string)) {
            throw new FailureException('Unexpected EOF', $offset);
        }

        $match = null;
        if (preg_match($this->regex, substr($string, $offset), $match, PREG_OFFSET_CAPTURE) === 1) {
            list($text, $position) = $match[0];
            if ($position > 0) {
                throw new RuntimeException(sprintf(
                    'Matched position for regex [%d] was greater than offset [%d]',
                    $position,
                    $offset
                ));
            }

            return new Result\StringResult($text);
        }

        throw new FailureException(sprintf('Unable to match "%s"', $this->regex), $offset);
    }
}
