<?php
namespace ES\Parser\Combinator;

use BadMethodCallException;
use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;

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
     * @param string $type
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
     * @param Input $input
     * @param int $offset
     * @return Result
     */
    protected function match(Input $input, $offset)
    {
        $result = $this->parser->match($input, $offset);
        /** @var FailureException|null $failure */
        $failure = null;

        try {
            $this->lookahead->match($input, $offset + $result->getLength());
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

    /**
     * @param callable $action
     * @return $this
     */
    public function setAction(callable $action)
    {
        throw new BadMethodCallException('LookaheadCombination does not support semantic actions');
    }
}
