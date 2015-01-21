<?php
namespace ES\Parser\Combinator;

use BadMethodCallException;
use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;

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
     * @param Input $input
     * @param int $offset
     * @return Result
     */
    protected function match(Input $input, $offset)
    {
        /** @var Result|null $longest */
        $longest = null;
        $failure = null;

        foreach ($this->parsers as $parser) {
            try {
                $match = $parser->match($input, $offset);
                if (!$longest || $match->getLength() > $longest->getLength()) {
                    $longest = $match;
                }
            } catch (FailureException $ex) {
                if ($ex->isMoreUsefulThan($failure)) {
                    $failure = $ex;
                }
            }
        }

        if (!$longest && $failure) {
            throw $failure;
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
