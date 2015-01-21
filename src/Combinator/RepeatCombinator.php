<?php
namespace ES\Parser\Combinator;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;

class RepeatCombinator extends Parser
{
    const INFINITE = null;

    /** @var Parser */
    private $parser;
    /** @var int */
    private $min;
    /** @var int|null */
    private $max;

    /**
     * @param Parser $parser
     * @param int $min
     * @param int|null $max
     */
    public function __construct(Parser $parser, $min = 0, $max = self::INFINITE)
    {
        $this->parser = $parser;

        $this->min = $min;
        $this->max = $max === self::INFINITE ? PHP_INT_MAX : $max;
    }

    /**
     * @param Input $input
     * @param int $offset
     * @return Result
     */
    protected function match(Input $input, $offset)
    {
        $result = new Result\GroupResult();

        $matches = 0;
        try {
            while ($matches < $this->max) {
                $match = $this->parser->match($input, $offset + $result->getLength());

                $result->addResult($match);

                ++$matches;
            }
        } catch (FailureException $ex) {
            // actually handling error cases follows
        }

        if ($matches < $this->min) {
            throw new FailureException(sprintf(
                'Unable to match at least %d repetitions, only matched %d',
                $this->min,
                $matches
            ), $offset);
        }

        if (!$matches) {
            $result = new Result\EmptyResult();
        }

        return $this->expandResult($result);
    }
}
