<?php

namespace OctoLab\Common\Tests\Doctrine\Migration\Mock;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\PDOMySql\Driver;
use OctoLab\Common\Doctrine\Migration\DriverBasedMigration;

/**
 * @author Kamil Samigullin <kamil@samigullin.info>
 */
class MysqlMigration extends DriverBasedMigration
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

    protected function prePdoMysqlUp()
    {
        $this->queries[] = 'some test up migration...';
    }

    protected function postPdoMysqlUp()
    {
        $this->queries = [];
    }

    protected function prePdoMysqlDown()
    {
        $this->queries[] = 'some test down migration...';
    }

    protected function postPdoMysqlDown()
    {
        $this->queries = [];
    }
}
