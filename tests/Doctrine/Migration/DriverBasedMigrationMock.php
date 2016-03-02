<?php

namespace OctoLab\Common\Doctrine\Migration;

use Doctrine\DBAL\Driver;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
final class DriverBasedMigrationMock extends DriverBasedMigration
{
    /**
     * @param string $sql
     * @param array $params
     * @param array $types
     */
    protected function addSql($sql, array $params = [], array $types = [])
    {
    }

    protected function prePdoPgsqlUp()
    {
        $this->queries[] = '[PostgreSQL][Up] test migration';
    }

    protected function postPdoPgsqlUp()
    {
        $this->queries = [];
    }

    protected function prePdoPgsqlDown()
    {
        $this->queries[] = '[PostgreSQL][Down] test migration';
    }

    protected function postPdoPgsqlDown()
    {
        $this->queries = [];
    }

    protected function prePdoMysqlUp()
    {
        $this->queries[] = '[MySQL][Up] test migration';
    }

    protected function postPdoMysqlUp()
    {
        $this->queries = [];
    }

    protected function prePdoMysqlDown()
    {
        $this->queries[] = '[MySQL][Down] test migration';
    }

    protected function postPdoMysqlDown()
    {
        $this->queries = [];
    }
}
