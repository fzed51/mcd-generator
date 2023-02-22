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

class OciParser implements Parsable
{
    private $pdo;
    private $tables;
    private $liaisons;

    /**
     * OciParser constructor.
     * @param string $tnsname
     * @param string $user
     * @param string $password
     */
    public function __construct(string $tnsname, string $user, string $password)
    {
        $this->pdo = new \PDO("oci:dbname=$tnsname", $user, $password);
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
        $columns = $this->pdo->query(<<<SQL
SELECT 
    USER_TAB_COLUMNS.TABLE_NAME,
    USER_TAB_COLUMNS.COLUMN_NAME,
    USER_CONSTRAINTS.CONSTRAINT_TYPE
FROM
    USER_TAB_COLUMNS
    LEFT OUTER JOIN  USER_IND_COLUMNS ON ( 
       USER_TAB_COLUMNS.TABLE_NAME = USER_IND_COLUMNS.TABLE_NAME
       AND USER_TAB_COLUMNS.COLUMN_NAME = USER_IND_COLUMNS.COLUMN_NAME
    )
    LEFT OUTER JOIN USER_INDEXES ON USER_IND_COLUMNS.INDEX_NAME = USER_INDEXES.INDEX_NAME
    LEFT OUTER JOIN USER_CONSTRAINTS ON USER_IND_COLUMNS.INDEX_NAME = USER_CONSTRAINTS.CONSTRAINT_NAME
SQL
        )->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            $table = $this->tables->get($column['TABLE_NAME']);
            switch ($column['CONSTRAINT_TYPE']) {
                case 'P':
                    $table->addField(new Field($column['COLUMN_NAME'], Field::PK));
                    break;
                case 'U':
                    $table->addField(new Field($column['COLUMN_NAME'], Field::UC));
                    break;
                //default:
                //    $table->addField(new Field($column['COLUMN_NAME']));
            }
        }
        $liaisons = $this->pdo->query(<<<SQL
SELECT USER_CONSTRAINTS.TABLE_NAME, R_USER_CONSTRAINTS.TABLE_NAME R_TABLE_NAME
FROM 
USER_CONSTRAINTS
INNER JOIN USER_CONSTRAINTS R_USER_CONSTRAINTS ON USER_CONSTRAINTS.R_CONSTRAINT_NAME = R_USER_CONSTRAINTS.CONSTRAINT_NAME
SQL
        )->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($liaisons as $liaison) {
            $this->liaisons->add($liaison['R_TABLE_NAME'], $liaison['TABLE_NAME']);
        }
    }

    /**
     * @return \App\Collection\Liaisons
     */
    public function getLiaisons(): \App\Collection\Liaisons
    {
        return $this->liaisons;
    }
}