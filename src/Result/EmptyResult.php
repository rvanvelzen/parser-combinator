<?php
namespace ES\Parser\Result;

use ES\Parser\Result;

class EmptyResult extends Result
{
    /**
     * @return int
     */
    public function getLength()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function exportTree()
    {
        return '';
    }
}
