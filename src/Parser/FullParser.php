<?php
namespace ES\Parser\Parser;

use BadMethodCallException;
use ES\Parser\Assertion\EndAssertion;
use ES\Parser\Combinator\ConcatenationCombinator;
use ES\Parser\Input;
use ES\Parser\Parser;
use ES\Parser\Result;

class FullParser extends Parser
{
    /** @var Parser */
    private $parser;

    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = new ConcatenationCombinator([
            $parser,
            new EndAssertion()
        ]);

        $this->parser->setAction(function (array $result) {
            return $result[0];
        });
    }

    /**
     * @param Input $input
     * @param int $offset
     * @return Result[]
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
