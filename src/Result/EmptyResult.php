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
     * @return mixed
     */
    public function getSemanticValue()
    {
        $action = $this->getAction();
        if ($action) {
            return $action(null);
        }

        return null;
    }
}
