<?php
namespace ES\Parser\Combinator;

use BadMethodCallException;
use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;

class OrCombinator extends Parser
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
        /** @var Result $longest */
        $longest = new Result\EmptyResult();
        $failure = null;

        foreach ($this->parsers as $parser) {
            try {
                $match = $parser->match($string, $offset);
                if ($match->getLength() > $longest->getLength()) {
                    $longest = $match;
                }
            } catch (FailureException $ex) {
                if ($ex->isMoreUsefulThan($failure)) {
                    $failure = $ex;
                }
            }
        }

        if ($failure) {
            $longest->setFailure($failure);
        }

        if (!$longest->getLength() && $longest->getFailure()) {
            throw $longest->getFailure();
        }

        return $longest;
    }

    /**
     * @param callable $action
     * @return $this
     */
    public function setAction(callable $action)
    {
        throw new BadMethodCallException('OrCombinator does not support semantic actions');
    }
}
