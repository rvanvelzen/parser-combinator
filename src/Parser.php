<?php
namespace ES\Parser;

abstract class Parser
{
    /**
     * @param string $string
     * @param int $offset
     * @return Result
     */
    abstract public function match($string, $offset = 0);
}
