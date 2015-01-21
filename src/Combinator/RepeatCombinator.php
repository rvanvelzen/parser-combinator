<?php
namespace ES\Parser\Combinator;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;

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
     * @param string $string
     * @param int $offset
     * @return Result
     */
    public function match($string, $offset = 0)
    {
        $result = (new Result\RepeatResult())
            ->setMin($this->min);
        /** @var FailureException|null $failure */
        $failure = null;

        $matches = 0;
        try {
            while ($matches < $this->max) {
                $match = $this->parser->match($string, $offset + $result->getLength());

                $result->addResult($match);

                ++$matches;
            }
        } catch (FailureException $ex) {
            if ($ex->isMoreUsefulThan($failure)) {
                $failure = $ex;
            }
        }

        if ($matches < $this->min) {
            if (!$failure) {
                $failure = new FailureException(sprintf(
                    'Unable to match at least %d repetitions, only matched %d',
                    $this->min,
                    $matches
                ), $offset);
            }

            throw $failure;
        }

        if (!$matches) {
            $result = new Result\EmptyResult();
        }

        if ($failure) {
            $result->setFailure($failure);
        }

        return $result;
    }
}
