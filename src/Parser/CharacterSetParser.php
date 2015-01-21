<?php
namespace ES\Parser\Parser;

use ES\Parser\FailureException;
use ES\Parser\Parser;
use ES\Parser\Result;

class CharacterSetParser extends Parser
{
    /** @var string[] */
    private $characters;

    /**
     * @param string[]|string $characters
     */
    public function __construct($characters)
    {
        if (!is_array($characters)) {
            $characters = str_split($characters);
        }

        $this->characters = array_map('strval', $characters);
    }

    /**
     * @param string[]|string $characters
     * @return $this
     */
    public function getWithoutCharacters($characters)
    {
        if (!is_array($characters)) {
            $characters = str_split($characters);
        }
        $characters = array_map('strval', $characters);

        $new = array_diff($this->characters, $characters);
        return new self($new);
    }

    /**
     * @param string $string
     * @param int $offset
     * @return Result
     */
    public function match($string, $offset = 0)
    {
        if ($offset >= strlen($string)) {
            throw (new FailureException('Unexpected EOF', $offset))
                ->setExpecting($this->characters);
        }

        $char = $string[$offset];
        if (in_array($char, $this->characters)) {
            return $this->expandResult(new Result\StringResult($char));
        }

        throw (new FailureException('Unable to match', $offset))
            ->setExpecting($this->characters);
    }
}
