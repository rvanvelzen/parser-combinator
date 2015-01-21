<?php
namespace ES\Parser\Parser;

use BadMethodCallException;
use ES\Parser\Assertion\EndAssertion;
use ES\Parser\Combinator\LookaheadCombinator;
use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;

class FullParser extends Parser
{
    /** @var Parser */
    private $parser;

    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = new LookaheadCombinator(
            LookaheadCombinator::POSITIVE,
            $parser,
            new EndAssertion()
        );
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

    /**
     * @param callable $action
     * @return $this
     */
    public function setAction(callable $action)
    {
        throw new BadMethodCallException('FullParser does not support semantic actions');
    }
}
