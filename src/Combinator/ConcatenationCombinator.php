<?php
namespace ES\Parser\Combinator;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;
use ES\Parser\Input;

class ConcatenationCombinator extends Parser
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
        /** @var Result\GroupResult[] $result */
        $result = [new Result\GroupResult()];
        $failure = null;

        foreach ($this->parsers as $parser) {
            $new = [];
            foreach ($result as $old) {
                try {
                    $matches = $parser->match($input, $offset + $old->getLength());
                    foreach ($matches as $match) {
                        $sub = clone $old;
                        $sub->addResult($match);
                        $new[] = $sub;
                    }
                } catch (FailureException $ex) {
                    if ($ex->isMoreUsefulThan($failure)) {
                        $failure = $ex;
                    }
                }
            }
            $result = $new;
        }

        if (!$result) {
            if (!$failure) {
                throw new \RuntimeException('Unexpected non-match');
            }

            throw $failure;
        }

        foreach ($result as $sub) {
            yield $this->expandResult($sub);
        }
    }
}
