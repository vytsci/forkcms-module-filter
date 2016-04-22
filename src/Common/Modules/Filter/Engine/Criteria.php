<?php

namespace Common\Modules\Filter\Engine;

use Common\Core\Model as CommonModel;

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
     * @var string
     */
    private $value;

    /**
     * @param $name
     * @param $columns
     * @param $operator
     * @param \SpoonFormElement $field
     */
    function __construct($name, $columns, $operator, \SpoonFormAttributes $field = null, $value = null)
    {
        $this
            ->setName($name)
            ->setColumns($columns)
            ->setOperator($operator)
            ->setField($field)
            ->setValue($value);
    }

    /**
     * @return bool
     */
    public function isFilled()
    {
        if (
            $this->hasField()
            && $this->field->isSubmitted()
            && $this->field->getValue() != ''
            && $this->field->getValue() != '-1'
        ) {
            return true;
        }

        return isset($this->value);
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
        if (!is_array($columns)) {
            $columns = array($columns);
        }

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
     * @return bool
     */
    public function hasField()
    {
        return isset($this->field);
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

    /**
     * @return \SpoonFormElement
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        if ($this->hasField() && $this->isFilled()) {
            return $this->field->getValue();
        }

        return $this->value;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
