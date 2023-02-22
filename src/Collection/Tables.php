<?php
declare(strict_types=1);

/**
 * User: Fabien Sanchez
 * Date: 08/02/2019
 * Time: 10:08
 */

namespace App\Collection;

class Tables
{
    /**
     * tables
     * @var \App\Entity\Table[]
     */
    private $tables = [];

    /**
     * @param string $tableName
     * @return \App\Entity\Table
     */
    public function get(string $tableName): \App\Entity\Table
    {
        if (array_key_exists($tableName, $this->tables)) {
            return $this->tables[$tableName];
        }
        $newTable = new \App\Entity\Table($tableName);
        return $this->add($newTable);
    }

    /**
     * @param \App\Entity\Table $table
     * @return \App\Entity\Table
     */
    public function add(\App\Entity\Table $table): \App\Entity\Table
    {
        $this->tables[$table->getName()] = $table;
        return $table;
    }

    public function clear(): void
    {
        $this->tables = [];
    }

    public function render()
    {
        $out = PHP_EOL;
        /** @var TYPE_NAME $table */
        foreach ($this->tables as $table) {
            $out .= $table;
        }
        return $out;
    }
}