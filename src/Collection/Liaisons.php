<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 08/02/2019
 * Time: 15:59
 */

namespace App\Collection;


class Liaisons
{

    private $liaisons = [];

    public function add(string $tableName_o, string $tableName_t)
    {
        $liaison = $this->liaisons[$tableName_o] ?? [];
        if (!in_array($tableName_t, $liaison)) {
            $liaison[] = $tableName_t;
            $this->liaisons[$tableName_o] = $liaison;
        }
    }

    public function render(): string
    {
        $liaisons = $this->liaisons;
        uasort($liaisons, function ($itema, $itemB) {
            return count($itema) <=> count($itemB);
        });
        $out = "";
        foreach ($liaisons as $origin => $liaison) {
            foreach ($liaison as $target) {
                $out .= $origin . ' --> ' . $target . PHP_EOL;
            }
        }
        return $out;
    }

}