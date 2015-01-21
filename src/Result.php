<?php
namespace ES\Parser;

abstract class Result
{
    /** @var FailureException */
    private $failure;
    /** @var callable */
    private $action;

    /**
     * @return int
     */
    abstract public function getLength();

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
     * @param callable $action
     * @return $this
     */
    public function setAction(callable $action = null)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return callable|null
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return mixed
     */
    abstract public function getSemanticValue();
}
