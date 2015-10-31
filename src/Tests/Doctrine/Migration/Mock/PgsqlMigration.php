<?php

namespace OctoLab\Common\Tests\Doctrine\Migration\Mock;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOPgSql\Driver;
use OctoLab\Common\Doctrine\Migration\DriverBasedMigration;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class PgsqlMigration extends DriverBasedMigration
{
    /**
     * @return $this
     */
    public function mock()
    {
        $this->connection = new Connection([], new Driver());
        return $this;
    }

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
        $this->queries[] = 'some test up migration...';
    }

    protected function postPdoPgsqlUp()
    {
        $this->queries = [];
    }

    protected function prePdoPgsqlDown()
    {
        $this->queries[] = 'some test down migration...';
    }

    protected function postPdoPgsqlDown()
    {
        $this->queries = [];
    }
}
