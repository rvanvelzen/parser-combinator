<?php
namespace ES\Parser\Parser;

use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;

class ProxyParser extends Parser
{
    /** @var Parser */
    private $parser;

    /**
     * @param Parser $parser
     * @return $this
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;
        return $this;
    }

    /**
     * @param callable $action
     * @return $this
     */
    public function setAction(callable $action)
    {
        return $this->parser->setAction($action);
    }

    /**
     * @param Input $input
     * @param int $offset
     * @return Result
     */
    protected function match(Input $input, $offset)
    {
        return $this->parser->match($input, $offset);
    }
}
