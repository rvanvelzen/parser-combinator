<?php
namespace ES\Parser\Parser;

use ES\Parser\Assertion\EndAssertion;
use ES\Parser\Combinator\LookaheadCombinator;
use ES\Parser\FailureException;
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
        $this->parser = new LookaheadCombinator(
            LookaheadCombinator::POSITIVE,
            $parser,
            new EndAssertion()
        );
    }

    /**
     * @param string $string
     * @param int $offset
     * @return Result
     * @throws FailureException
     */
    public function match($string, $offset = 0)
    {
        return $this->parser->match($string, $offset);
    }
}
