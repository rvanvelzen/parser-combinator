<?php
namespace ES\Parser\Result;

use ES\Parser\Result;

class RepeatResult extends GroupResult
{
    /** @var int */
    private $min;

    /**
     * @return Result|null
     */
    public function nextOption()
    {
        $current = $this->getResults();
        if (count($current) > $this->min) {
            $result = new static();
            $result->setMin($this->min);

            foreach (array_slice($current, 0, -1) as $sub) {
                $result->addResult($sub);
            }

            return $result;
        }

        return parent::nextOption();
    }


    /**
     * @param int $min
     * @return $this
     */
    public function setMin($min)
    {
        $this->min = $min;
        return $this;
    }
}
