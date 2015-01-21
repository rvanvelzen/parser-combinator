<?php
namespace ES\Parser\Parser;


use ES\Parser\Parser;
use ES\Parser\Result;

class EmptyParser extends Parser
{
    /**
     * @param string $string
     * @param int $offset
     * @return Result
     */
    public function match($string, $offset = 0)
    {
        return new Result\EmptyResult();
    }
}
