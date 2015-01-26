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
     * @return Result[]
     */
    protected function match(Input $input, $offset)
    {
        $failure = null;
        $any = false;

        foreach ($this->parsers as $parser) {
            try {
                foreach ($parser->match($input, $offset) as $match) {
                    yield $match;
                    $any = true;
                }
            } catch (FailureException $ex) {
                if ($ex->isMoreUsefulThan($failure)) {
                    $failure = $ex;
                }
            }
        }

        if (!$any) {
            if (!$failure) {
                throw new \RuntimeException('Unexpected non-match');
            }

            throw $failure;
        }
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
