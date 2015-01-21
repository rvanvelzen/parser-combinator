<?php
namespace ES\Parser\Assertion;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;

class EndAssertion extends Parser
{
    /**
     * @param string $string
     * @param int $offset
     * @return Result
     */
    public function match($string, $offset = 0)
    {
        if ($offset < strlen($string)) {
            throw (new FailureException('Expected end-of-string', $offset))
                ->setExpecting('$eos');
        }

        return new Result\EmptyResult();
    }
}
