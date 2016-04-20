<?php

declare(strict_types = 1);

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
     * @param int $tableCount1
     * @param int $tableCount2
     * @param int $limit
     * @param int $offset
     *
     * @return array<string,int>
     *
     * @api
     *
     * @quality:method [B]
     */
    public static function getTwoTablePagination(int $tableCount1, int $tableCount2, int $limit, int $offset = 0): array
    {
        $offset1 = $offset > $tableCount1 ? $tableCount1 : $offset;
        $limit1 = min($limit, $tableCount1 - $offset1);
        if ($offset1 + $limit1 < $tableCount1) {
            $offset2 = 0;
            $limit2 = 0;
        } elseif ($offset >= $tableCount1 + $tableCount2) {
            $offset2 = $tableCount2;
            $limit2 = 0;
        } elseif ($offset < $tableCount1) {
            $offset2 = 0;
            $limit2 = $offset1 + $limit - $tableCount1;
        } else {
            $offset2 = $offset - $tableCount1;
            $limit2 = $limit;
        }
        return [
            'limit1'  => $limit1,
            'offset1' => $offset1,
            'limit2'  => $limit2,
            'offset2' => $offset2,
        ];
    }

    /**
     * @param int $limit how many records to get (e.g. LIMIT part of SQL queries)
     * @param int $offset how many records to skip (e.g. OFFSET part of SQL queries)
     * @param int|null $total how many total records were found (e.g. SELECT COUNT(*) FROM)
     *
     * @throws \InvalidArgumentException if the values are negative
     *
     * @api
     *
     * @quality:method [B]
     */
    public function __construct(int $limit, int $offset = 0, int $total = null)
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
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     *
     * @api
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return bool
     *
     * @api
     */
    public function hasPortion(): bool
    {
        return $this->total ? $this->offset < $this->total : (bool)$this->limit;
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
