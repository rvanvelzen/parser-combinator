<?php
namespace ES\Parser\Combinator;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;

class LookaheadCombinator extends Parser
{
    const POSITIVE = 'positive';
    const NEGATIVE = 'negative';

    /** @var string */
    private $type;
    /** @var Parser */
    private $parser;
    /** @var Parser */
    private $lookahead;

    /**
     * @param Parser $parser
     * @param Parser $lookahead
     */
    public function __construct($type, Parser $parser, Parser $lookahead)
    {
        $this->type = $type;
        $this->parser = $parser;
        $this->lookahead = $lookahead;
    }

    /**
     * @param string $string
     * @param int $offset
     * @return Result
     */
    public function match($string, $offset = 0)
    {
        $result = $this->parser->match($string, $offset);
        /** @var FailureException|null $failure */
        $failure = null;

        try {
            $this->lookahead->match($string, $offset + $result->getLength());
        } catch (FailureException $failure) {
            // handle this right below here
        }

        if ($failure) {
            if ($this->type === self::POSITIVE) {
                throw $failure;
            }
        } elseif ($this->type === self::NEGATIVE) {
            throw new FailureException('Unexpected match for negative lookahead', $offset);
        }

        return $result;
    }
}
