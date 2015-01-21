<?php
namespace ES\Parser\Combinator;

use ES\Parser\Parser;
use ES\Parser\Result;

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
     * @param string $string
     * @param int $offset
     * @return Result
     */
    public function match($string, $offset = 0)
    {
        $result = new Result\GroupResult();

        foreach ($this->parsers as $parser) {
            $match = $parser->match($string, $offset);

            $result->addResult($match);
            $offset += $match->getLength();
        }

        return $result;
    }
}
