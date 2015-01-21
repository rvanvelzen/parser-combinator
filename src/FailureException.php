<?php
namespace ES\Parser;

use Exception;
use RuntimeException;

class FailureException extends RuntimeException
{
    /** @var int */
    private $offset;
    /** @var string[] */
    private $expecting = [];

    /**
     * @param string $message
     * @param int $offset
     * @param Exception $previous
     */
    public function __construct($message, $offset, Exception $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->offset = $offset;
    }

    /**
     * @return string
     */
    public function getDisplayMessage()
    {
        return sprintf('%s%s at offset %d', $this->getMessage(), $this->getHint(), $this->getOffset());
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param string[]|string $strings
     * @return $this
     */
    public function setExpecting($strings) {
        $this->expecting = (array)$strings;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getExpecting()
    {
        return $this->expecting;
    }

    /**
     * @return string|null
     */
    public function getHint()
    {
        $expecting = $this->getExpecting();
        if (!$expecting || count($expecting) > 5) {
            return null;
        }

        if (count($expecting) === 1) {
            $hint = reset($expecting);
        } else {
            $hint = sprintf('one of %s', implode(', ', $expecting));
        }

        return sprintf(' (expecting %s)', $hint);
    }

    /**
     * @param FailureException $ex
     * @return bool
     */
    public function isMoreUsefulThan(FailureException $ex = null)
    {
        if (!$ex) {
            return true;
        }

        if ($this->getOffset() == $ex->getOffset()) {
            $mine = $this->getExpecting();
            $other = $ex->getExpecting();

            return $mine && (!$other || (count($mine) < count($other)));
        }

        return $this->getOffset() > $ex->getOffset();
    }
}
