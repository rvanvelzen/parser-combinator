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
     * @return string
     */
    public function exportTree()
    {
        $output = [];
        foreach ($this->results as $result) {
            $output[] = preg_replace('/^/m', '  ', $result->exportTree());
        }
        return implode("\n", $output);
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

    public function getClean()
    {
        $refreshed = [];
        foreach ($this->results as $result) {
            $new = $result->getClean();

            if ($new && !$new instanceof EmptyResult) {
                $refreshed[] = $new;
            }
        }

        if (!$refreshed) {
            return new EmptyResult();
        }

        if (count($refreshed) === 1) {
            return $refreshed[0];
        }

        $this->results = $refreshed;
        return $this;
    }
}
