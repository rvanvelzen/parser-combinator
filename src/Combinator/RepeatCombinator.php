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
     * @return Result[]
     */
    protected function match(Input $input, $offset)
    {
        /** @var Result\GroupResult[] $result */
        $result = [new Result\GroupResult()];
        $matchCount = 0;

        if ($this->min === 0) {
            yield $this->expandResult($result[0]);
        }

        while ($matchCount < $this->max) {
            $new = [];
            foreach ($result as $old) {
                try {
                    $matches = $this->parser->match($input, $offset + $old->getLength());
                    foreach ($matches as $match) {
                        $sub = clone $old;
                        $sub->addResult($match);
                        $new[] = $sub;

                        if ($matchCount + 1 >= $this->min) {
                            yield $this->expandResult($sub);
                        }
                    }
                } catch (FailureException $ex) {

                }
            }

            if (!$new) {
                break;
            }

            $result = $new;
            ++$matchCount;
        }

        if ($matchCount < $this->min) {
            throw new FailureException(sprintf(
                'Unable to match at least %d repetitions, only matched %d',
                $this->min,
                $matchCount
            ), $offset);
        }
    }
}
