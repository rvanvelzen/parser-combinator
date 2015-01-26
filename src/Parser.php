<?php
namespace ES\Parser;

abstract class Parser
{
    /** @var callable */
    private $action;

    public function parse($string)
    {
        $state = new Input($string);
        foreach ($this->match($state, 0) as $match) {
            return $match;
        }
    }

    /**
     * @param Input $input
     * @param int $offset
     * @return Result[]
     */
    abstract protected function match(Input $input, $offset);

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
