<?php

declare(strict_types = 1);

namespace OctoLab\Common\Doctrine\Util;

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
     * @param int $limit how many records to get (e.g. LIMIT part of SQL queries)
     * @param int $offset how many records to skip (e.g. OFFSET part of SQL queries)
     * @param int|null $total how many total records were found (e.g. SELECT COUNT(*) FROM)
     *
     * @throws \InvalidArgumentException if the values are negative
     *
     * @api
     */
    public function __construct(int $limit, int $offset = 0, int $total = null)
    {
        if (min($limit, $offset, (int)$total) < 0) {
            throw new \InvalidArgumentException('Values must be unsigned.');
        }
        $this->total = $total ?: PHP_INT_MAX;
        $this->limit = min($limit, $this->total);
        $this->offset = $offset;
    }

    /**
     * @param int $table1Count
     * @param int $table2Count
     * @param int $limit
     * @param int $offset
     *
     * @return array<string,int>
     *
     * @api
     */
    public static function getTwoTablePagination(int $table1Count, int $table2Count, int $limit, int $offset = 0): array
    {
        \assert('$table1Count >= 0 && $table2Count >= 0 && $limit >= 0 && $offset >= 0');
        $offset1 = min($offset, $table1Count);
        $limit1 = min($limit, $table1Count - $offset1);
        $offset2 = min($offset - $offset1, $table2Count);
        $limit2 = min($limit - $limit1, $table2Count - $offset2);
        return [
            'limit1' => $limit1,
            'offset1' => $offset1,
            'limit2' => $limit2,
            'offset2' => $offset2,
        ];
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
        return $this->offset < $this->total;
    }

    /**
     * @return Limiter
     *
     * @api
     */
    public function nextPortion(): Limiter
    {
        \assert('$this->limit > 0');
        if ($this->limit) {
            $this->offset = min($this->offset + $this->limit, $this->total);
            $this->limit = min($this->limit, $this->total - $this->offset);
        }
        return $this;
    }
}
