<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 08/02/2019
 * Time: 13:36
 */

namespace App\Parser;


use App\Collection\Liaisons;
use App\Collection\Tables;
use App\Entity\Field;

class PgSqlParser implements Parsable
{
    private $pdo;
    private $tables;
    private $liaisons;

    /**
     * OciParser constructor.
     * @param string $host
     * @param string $dbname
     * @param string $user
     * @param string $password
     */
    public function __construct(string $host, string $dbname, string $user, string $password, int $port)
    {
        $this->pdo = new \PDO("pgsql:host=$host;dbname=$dbname;port=$port", $user, $password);
        $this->tables = new Tables();
        $this->liaisons = new Liaisons();
    }

    /**
     * @return Tables
     */
    public function getTables(): Tables
    {
        return $this->tables;
    }

    public function parse(): void
    {
        $reqSql = <<<SQL
select 
col.table_name, col.column_name, col.data_type, col.column_default, 
colcons.constraint_name,
lower(substring(cons.constraint_type from 1 for 1)) as constraint_type
from information_schema.columns col
LEFT OUTER JOIN information_schema.constraint_column_usage colcons
	ON col.table_name = colcons.table_name
		AND col.column_name = colcons.column_name
LEFT OUTER JOIN information_schema.table_constraints cons
	ON colcons.constraint_name = cons.constraint_name
where col.table_schema = 'public'
SQL;
        $columns = $this->pdo->query($reqSql)->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            $table = $this->tables->get($column['table_name']);
            switch ($column['constraint_type']) {
                case 'p':
                    $table->addField(new Field($column['column_name'], Field::PK));
                    break;
                case 'u':
                    $table->addField(new Field($column['column_name'], Field::UC));
                    break;
                default:
                    $table->addField(new Field($column['column_name']));
            }
        }

        $reqSql = <<<SQL
SELECT DISTINCT
    -- tc.table_schema, 
    -- tc.constraint_name, 
    tc.table_name, 
    -- kcu.column_name, 
    -- ccu.table_schema AS foreign_table_schema,
    ccu.table_name AS foreign_table_name
    -- ccu.column_name AS foreign_column_name 
FROM 
    information_schema.table_constraints AS tc 
    JOIN information_schema.key_column_usage AS kcu
      ON tc.constraint_name = kcu.constraint_name
      AND tc.table_schema = kcu.table_schema
    JOIN information_schema.constraint_column_usage AS ccu
      ON ccu.constraint_name = tc.constraint_name
      AND ccu.table_schema = tc.table_schema
WHERE tc.table_schema = 'public' 
AND   tc.constraint_type = 'FOREIGN KEY'
SQL;
        $liaisons = $this->pdo->query($reqSql)->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($liaisons as $liaison) {
            $this->liaisons->add(  $liaison['table_name'], $liaison['foreign_table_name']);
        }

    }

    /**
     * @return \App\Collection\Liaisons
     */
    public function getLiaisons(): Liaisons
    {
        return $this->liaisons;
    }
}