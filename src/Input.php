<?php
namespace ES\Parser;

class Input
{
    /** @var string */
    private $input;
    /** @var int */
    private $length;

    /**
     * @param string $input
     */
    public function __construct($input)
    {
        $this->input = $input;
        $this->length = strlen($input);
    }

    /**
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @param int $from
     * @param int|null $to
     * @return string
     */
    public function getSubstring($from, $to = null)
    {
        return substr($this->input, $from, $to);
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }
}
