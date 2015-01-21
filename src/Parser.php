<?php
namespace ES\Parser;

abstract class Parser
{
    /** @var callable */
    private $action;

    /**
     * @param string $string
     * @param int $offset
     * @return Result
     */
    abstract public function match($string, $offset = 0);

    /**
     * @param callable $action
     * @return $this
     */
    public function setAction(callable $action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param Result $result
     * @return Result
     */
    protected function expandResult(Result $result)
    {
        $result->setAction($this->action);
        return $result;
    }
}
