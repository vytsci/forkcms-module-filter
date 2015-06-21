<?php

namespace Common\Modules\Filter;

/**
 * Class Helper
 * @package Common\Modules\Filter
 */
class Helper
{
    const OPERATOR_EQUAL = '=';
    const OPERATOR_EQUAL_NOT = '<>';
    const OPERATOR_EQUAL_LESS = '<=';
    const OPERATOR_EQUAL_GREATER = '>=';
    const OPERATOR_LESS = '<';
    const OPERATOR_GREATER = '>';
    const OPERATOR_PATTERN = 'LIKE';
    const OPERATOR_PATTERN_NOT = 'NOT LIKE';
    const OPERATOR_BOOL = 'IS';
    const OPERATOR_BOOL_NOT = 'IS NOT';

    /**
     * @param $operator
     * @param $value
     * @return string
     */
    public static function getOperatorBasedValue($operator, $value)
    {
        switch ($operator) {
            case self::OPERATOR_PATTERN:
            case self::OPERATOR_PATTERN_NOT:
                $value = "'%{$value}%'";
                break;
            case self::OPERATOR_BOOL:
            case self::OPERATOR_BOOL_NOT:
                $value = $value == 'null'?"NULL":"'{$value}'";
                break;
            default:
                $value = "'{$value}'";
        }

        return $value;
    }

    /**
     * @param $operator
     * @return bool
     */
    public static function isValidOperator($operator)
    {
        $allowed = array(
            self::OPERATOR_EQUAL,
            self::OPERATOR_EQUAL_NOT,
            self::OPERATOR_EQUAL_LESS,
            self::OPERATOR_EQUAL_GREATER,
            self::OPERATOR_LESS,
            self::OPERATOR_GREATER,
            self::OPERATOR_PATTERN,
            self::OPERATOR_PATTERN_NOT,
            self::OPERATOR_BOOL,
            self::OPERATOR_BOOL_NOT
        );

        return in_array($operator, $allowed);
    }
}
