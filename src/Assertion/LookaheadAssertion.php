<?php
namespace ES\Parser\Assertion;

use BadMethodCallException;
use ES\Parser\FailureException;
use ES\Parser\Input;
use ES\Parser\Parser;
use ES\Parser\Result;

class LookaheadAssertion extends Parser
{
    const POSITIVE = 'positive';
    const NEGATIVE = 'negative';

    /** @var string */
    private $type;
    /** @var Parser */
    private $lookahead;

    /**
     * @param string $type
     * @param Parser $lookahead
     */
    public function __construct($type, Parser $lookahead)
    {
        $this->type = $type;
        $this->lookahead = $lookahead;
    }

    /**
     * @param Input $input
     * @param int $offset
     * @return Result
     */
    protected function match(Input $input, $offset)
    {
        try {
            foreach ($this->lookahead->match($input, $offset) as $oops) {
                if ($this->type === self::NEGATIVE) {
                    throw new FailureException('Unexpected match for negative lookahead', $offset);
                } else {
                    break;
                }
            }
        } catch (FailureException $failure) {
            if ($this->type === self::POSITIVE) {
                throw $failure;
            }
        }

        return [$this->expandResult(new Result\EmptyResult())];
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
