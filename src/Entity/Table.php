<?php
declare(strict_types=1);
/**
 * User: Fabien Sanchez
 * Date: 08/02/2019
 * Time: 10:11
 */

namespace App\Entity;


class Table
{
    /**
     * name
     * @var string
     */
    private $name;

    /**
     * fields
     * @var \App\Entity\Field[]
     */
    private $fields = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function addField(Field $field)
    {
        $this->fields[] = $field;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        /** @var string $out */
        $out = 'object ' . $this->name . '{' . PHP_EOL;
        foreach ($this->fields as $field) {
            $out .= "\t" . $field . PHP_EOL;
        }
        $out .= '}' . PHP_EOL;
        return $out;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

}