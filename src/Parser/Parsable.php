<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 08/02/2019
 * Time: 13:50
 */

namespace App\Parser;


interface Parsable
{
    /**
     * @return \App\Collection\Tables
     */
    public function getTables(): \App\Collection\Tables;    /**
     * @return \App\Collection\Liaisons
     */
    public function getLiaisons(): \App\Collection\Liaisons;

    public function parse(): void;
}