<?php
namespace ES\Parser\Result;

use ES\Parser\FailureException;
use ES\Parser\Result;

class GroupResult extends Result
{
    /** @var Result[] */
    private $results = [];

    /**
     * @return int
     */
    public function getLength()
    {
        return array_reduce($this->results, function ($sum, Result $result) {
            return $sum + $result->getLength();
        }, 0);
    }

    /**
     * @return FailureException
     */
    public function getFailure()
    {
        $failure = parent::getFailure();
        foreach ($this->results as $result) {
            if ($ex = $result->getFailure()) {
                if ($ex->isMoreUsefulThan($failure)) {
                    $failure = $ex;
                }
            }
        }

        return $failure;
    }

    /**
     * @param Result $result
     * @return $this
     */
    public function addResult(Result $result)
    {
        $this->results[] = $result;
        return $this;
    }

    /**
     * @return Result[]
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return mixed
     */
    public function getSemanticValue()
    {
        $params = [];
        foreach ($this->getResults() as $sub) {
            $params[] = $sub->getSemanticValue();
        }

        $action = $this->getAction();
        if ($action) {
            return $action($params);
        }

        return $params;
    }
}
