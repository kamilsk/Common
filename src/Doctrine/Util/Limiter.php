<?php

namespace OctoLab\Common\Doctrine\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Limiter
{
    /** @var int */
    private $limit;
    /** @var int */
    private $offset;
    /** @var int */
    private $total;

    /**
     * @param int $limit how many records to get (e.g. LIMIT part of SQL queries)
     * @param int $offset how many records to skip (e.g. OFFSET part of SQL queries)
     * @param int|null $total how many total records were found (e.g. SELECT COUNT(*) FROM)
     *
     * @throws \InvalidArgumentException if the values are negative
     *
     * @api
     */
    public function __construct($limit, $offset = 0, $total = null)
    {
        $this->limit = $total ? min($limit, $total) : $limit;
        $this->offset = $offset;
        $this->total = $total ?: PHP_INT_MAX;
        if ($this->limit < 0 || $this->offset < 0 || $this->total < 0) {
            throw new \InvalidArgumentException('Values must be unsigned.');
        }
    }

    /**
     * @return int
     *
     * @api
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return int
     *
     * @api
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return bool
     *
     * @api
     */
    public function hasPortion()
    {
        return $this->total ? $this->offset < $this->total : (bool) $this->limit;
    }

    /**
     * @return $this
     *
     * @api
     */
    public function nextPortion()
    {
        if ($this->limit) {
            $this->offset = min($this->offset + $this->limit, $this->total);
            $this->limit = min($this->limit, $this->total - $this->offset);
        }
        return $this;
    }
}
