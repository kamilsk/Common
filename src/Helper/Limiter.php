<?php

namespace OctoLab\Common\Helper;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class Limiter
{
    /** @var int */
    private $limit;
    /** @var int */
    private $offset;
    /** @var int */
    private $total;

    /**
     * @param int $limit
     * @param int $offset
     * @param int|null $total
     */
    public function __construct($limit, $offset = 0, $total = null)
    {
        $this->limit = $total ? min($limit, $total) : $limit;
        $this->offset = $offset;
        $this->total = $offset + $total;
    }

    /**
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return bool
     */
    public function hasPortion()
    {
        return $this->total ? $this->offset < $this->total : (bool) $this->limit;
    }

    /**
     * @return $this
     */
    public function nextPortion()
    {
        if ($this->limit) {
            $this->offset += $this->limit;
            if ($this->total) {
                $this->limit = min($this->limit, $this->total - $this->offset);
            }
        }
        return $this;
    }
}
