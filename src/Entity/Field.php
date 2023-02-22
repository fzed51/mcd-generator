<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 08/02/2019
 * Time: 10:14
 */

namespace App\Entity;


class Field
{
    const PK = '#';
    const UC = '-';
    const FK = '+';
    /**
     * name
     * @var string
     */
    private $name;
    /**
     * type
     * @var string
     */
    private $type;

    public function __construct(string $name, string $type = '')
    {

        $this->name = $name;
        $this->type = $type;
    }

    public function __toString()
    {
        return (empty($this->type) ? '' : $this->type . ' ') . $this->name;
    }
}
