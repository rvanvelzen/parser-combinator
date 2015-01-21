<?php
namespace ES\Parser\Combinator;

use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;

class ConcatenationCombinator extends Parser
{
    /** @var Parser[] */
    private $parsers = [];

    /**
     * @param Parser[] $parsers
     */
    public function __construct(array $parsers = [])
    {
        foreach ($parsers as $parser) {
            $this->addParser($parser);
        }
    }

    /**
     * @param Parser $parser
     * @return $this
     */
    public function addParser(Parser $parser)
    {
        $this->parsers[] = $parser;
        return $this;
    }

    /**
     * @param Input $input
     * @param int $offset
     * @return Result
     */
    protected function match(Input $input, $offset)
    {
        $result = new Result\GroupResult();

        foreach ($this->parsers as $parser) {
            $match = $parser->match($input, $offset);

            $result->addResult($match);
            $offset += $match->getLength();
        }

        return $this->expandResult($result);
    }
}
