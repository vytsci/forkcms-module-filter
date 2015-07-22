<?php

namespace Common\Modules\Filter;

use Symfony\Component\HttpFoundation\Request;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Header as BackendHeader;
use Backend\Core\Engine\Form as BackendForm;

/**
 * Class Filter
 * @package Backend\Modules\Filter\Engine
 */
class Filter
{
    /**
     * @var BackendHeader
     */
    protected $header;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var BackendForm
     */
    protected $frm;

    /**
     * @var
     */
    private $name;

    /**
     * @var array
     */
    private $criteria = array();

    /**
     * @var string
     */
    private $query;

    /**
     * @param string $name
     * @param null $action
     * @param string $method
     * @param bool $useToken
     * @param bool $useGlobalError
     */
    public function __construct(
        $name = 'filter',
        $action = null,
        $method = 'get',
        $useToken = true,
        $useGlobalError = true
    )
    {
        $this->name = $name;
        $this->header = BackendModel::getContainer()->get('header');
        $this->request = BackendModel::getContainer()->get('request');
        $this->frm = new BackendForm($name, $action, $method, $useToken, $useGlobalError);
    }

    /**
     * @param $name
     * @param $columns
     * @param string $operator
     * @return $this
     */
    public function addTextCriteria($name, $columns, $operator = Helper::OPERATOR_EQUAL)
    {
        $this->criteria[] = new Criteria(
            $name,
            $columns,
            $operator,
            $this->frm->addText($name, $this->request->get($name))
        );

        return $this;
    }

    /**
     * @param $name
     * @param $columns
     * @param string $operator
     * @return $this
     */
    public function addCheckboxCriteria($name, $columns, $operator = Helper::OPERATOR_EQUAL)
    {
        $this->criteria[] = new Criteria(
            $name,
            $columns,
            $operator,
            $this->frm->addCheckbox($name, (bool)$this->request->get($name))
        );

        return $this;
    }

    /**
     * @param $name
     * @param $columns
     * @param $values
     * @param string $operator
     * @return $this
     */
    public function addDropdownCriteria($name, $columns, $values, $operator = Helper::OPERATOR_EQUAL)
    {
        $this->criteria[] = new Criteria(
            $name,
            $columns,
            $operator,
            $this->frm->addDropdown($name, $values, $this->request->get($name))
        );

        return $this;
    }

    /**
     * @param $name
     * @param $columns
     * @param $values
     * @param string $operator
     * @return $this
     */
    public function addRadiobuttonCriteria($name, $columns, $values, $operator = Helper::OPERATOR_EQUAL)
    {
        $this->criteria[] = new Criteria(
            $name,
            $columns,
            $operator,
            $this->frm->addRadiobutton($name, $values)
        );

        return $this;
    }

    /**
     * @param $query
     * @return string
     */
    public function getQuery($query)
    {
        if (empty($this->query)) {
            $this->query = $query;

            $queryFilter = false === stripos($this->query, ' where ')?' WHERE ':' AND ';
            $queryFilterParts = array();

            /**
             * @var $criteria Criteria
             */
            foreach ($this->criteria as $criteria) {
                if ($criteria->getField()->isFilled()) {
                    $parts = array();
                    foreach ($criteria->getColumns() as $column) {
                        $value = Helper::getOperatorBasedValue(
                            $criteria->getOperator(),
                            $criteria->getField()->getValue()
                        );
                        $parts[] = "{$column} {$criteria->getOperator()} {$value}";
                    }

                    $queryFilterPart = implode(' OR ', $parts);
                    $queryFilterParts[] = count($parts) > 1?'(' . $queryFilterPart . ')':$queryFilterPart;
                }
            }

            if (!empty($queryFilterParts)) {
                $queryFilter .= implode(' AND ', $queryFilterParts);

                $positionGroupBy = stripos($this->query, 'GROUP BY');

                if ($positionGroupBy === false) {
                    $this->query = $this->query . $queryFilter;
                } else {
                    $this->query =
                        substr($this->query, 0, $positionGroupBy)
                        . $queryFilter . ' '
                        . substr($this->query, $positionGroupBy);
                }
            }
        }

        return $this->query;
    }

    /**
     * Parse the form
     *
     * @param \SpoonTemplate $tpl The template instance wherein the form will be parsed.
     */
    public function parse(\SpoonTemplate $tpl)
    {
        $this->frm->parse($tpl);
    }
}
