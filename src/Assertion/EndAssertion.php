<?php
namespace ES\Parser\Assertion;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;

class EndAssertion extends Parser
{
    /**
     * @param Input $input
     * @param int $offset
     * @return Result
     */
    protected function match(Input $input, $offset)
    {
        if ($offset < $input->getLength()) {
            throw (new FailureException('Expected end-of-string', $offset))
                ->setExpecting('$eos');
        }

        return $this->expandResult(new Result\EmptyResult());
    }
}
