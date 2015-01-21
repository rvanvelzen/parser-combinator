<?php
namespace ES\Parser\Parser;

class CharacterRangeParser extends CharacterSetParser
{
    /**
     * @param string $from
     * @param string $to
     */
    public function __construct($from, $to)
    {
        parent::__construct(range($from, $to));
    }
}
