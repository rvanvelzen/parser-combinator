<?php
namespace ES\Parser\Parser;

use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;

class EmptyParser extends Parser
{
    /**
     * @param Input $input
     * @param int $offset
     * @return Result
     */
    protected function match(Input $input, $offset)
    {
        return $this->expandResult(new Result\EmptyResult());
    }
}
