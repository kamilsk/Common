<?php

namespace OctoLab\Common\Util;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class Math
{
    /**
     * @param int $tableCount1
     * @param int $tableCount2
     * @param int $limit
     * @param int $offset
     *
     * @return array
     */
    public function getTwoTablePagination($tableCount1, $tableCount2, $limit, $offset = 0)
    {
        $offset1 = $offset > $tableCount1 ? $tableCount1 : $offset;
        $limit1 = min($limit, $tableCount1 - $offset1);
        if ($offset1 + $limit1 < $tableCount1) {
            $offset2 = 0;
            $limit2 = 0;
        } elseif ($offset >= $tableCount1 + $tableCount2) {
            $offset2 = $tableCount2;
            $limit2 = 0;
        } else {
            if ($offset < $tableCount1) {
                $offset2 = 0;
                $limit2 = $offset1 + $limit - $tableCount1;
            } else {
                $offset2 = $offset - $tableCount1;
                $limit2 = $limit;
            }
        }
        return [
            'limit1'  => $limit1,
            'offset1' => $offset1,
            'limit2'  => $limit2,
            'offset2' => $offset2,
        ];
    }
}
