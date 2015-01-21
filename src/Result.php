<?php
namespace ES\Parser;

abstract class Result
{
    /** @var FailureException */
    private $failure;

    /**
     * @return int
     */
    abstract public function getLength();

    /**
     * @return string
     */
    abstract public function exportTree();

    /**
     * @return FailureException
     */
    public function getFailure()
    {
        return $this->failure;
    }

    /**
     * @param FailureException $failure
     * @return $this
     */
    public function setFailure(FailureException $failure)
    {
        if ($failure->isMoreUsefulThan($this->failure)) {
            $this->failure = $failure;
        }

        return $this;
    }

    /**
     * Prune empty nodes.
     */
    public function getClean()
    {
        return $this;
    }
}
