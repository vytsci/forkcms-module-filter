<?php

namespace Common\Modules\Filter;

/**
 * Class Criteria
 * @package Backend\Modules\Filter\Engine
 */
class Criteria
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $columns;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var \SpoonFormElement
     */
    private $field;

    /**
     * @param $name
     * @param $columns
     * @param $operator
     * @param \SpoonFormElement $field
     */
    function __construct($name, $columns, $operator, \SpoonFormElement $field)
    {
        $this
            ->setName($name)
            ->setColumns($columns)
            ->setOperator($operator)
            ->setField($field);
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param $column
     * @return $this
     */
    public function addColumn($column)
    {
        $this->columns[] = $column;

        return $this;
    }

    /**
     * @param $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @param $operator
     * @return $this
     * @throws \Exception
     */
    public function setOperator($operator)
    {
        if (!Helper::isValidOperator($operator)) {
            throw new \Exception("Given operator '{$operator}' is not valid operator");
        }

        $this->operator = $operator;

        return $this;
    }

    /**
     * @return \SpoonFormElement
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }
}
