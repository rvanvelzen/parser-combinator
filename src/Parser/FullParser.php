<?php
namespace ES\Parser\Parser;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;

class FullParser extends Parser
{
    /** @var Parser */
    private $parser;

    /**
     * @param Parser $parser
     */
    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * @param string $string
     * @param int $offset
     * @return Result
     * @throws FailureException
     */
    public function match($string, $offset = 0)
    {
        $result = $this->parser->match($string, $offset);
        if ($result->getLength() === strlen($string)) {
            return $result;
        } else {
            if (($failure = $result->getFailure())) {
                throw $failure;
            }

            throw new FailureException('Parser ended before EOF', $result->getLength());
        }
    }
}
