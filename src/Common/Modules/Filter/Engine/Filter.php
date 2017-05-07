<?php

namespace Common\Modules\Filter\Engine;

use Symfony\Component\HttpFoundation\Request;
use Backend\Core\Engine\Model as BackendModel;
use Backend\Core\Engine\Header as BackendHeader;
use Common\Core\Model as CommonModel;
use Common\Core\Form as CommonForm;

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
     * @var CommonForm
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
     * @var bool
     */
    private $idle = true;

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
    ) {
        $this->name = $name;
        $this->header = CommonModel::getContainer()->get('header');
        $this->request = CommonModel::getContainer()->get('request');
        $this->frm = new CommonForm($name, $action, $method, $useToken, $useGlobalError);
    }

    /**
     * @param $form
     *
     * @return $this
     */
    public function setForm($form)
    {
        $this->frm = $form;

        return $this;
    }

    /**
     * @param $name
     * @return Criteria
     * @throws \Exception
     */
    public function getCriteria($name)
    {
        if (!isset($this->criteria[$name])) {
            throw new \Exception('Such criteria does not exist');
        }

        return $this->criteria[$name];
    }

    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function addCriteria(Criteria $criteria)
    {
        $this->criteria[$criteria->getName()] = $criteria;

        return $this;
    }

    /**
     * @param $name
     * @param $columns
     * @param string $operator
     * @return $this
     */
    public function addTextCriteria($name, $columns, $operator = Helper::OPERATOR_EQUAL)
    {
        $this->criteria[$name] = new Criteria(
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
     * @param null $value
     * @param string $operator
     *
     * @return $this
     */
    public function addDateCriteria($name, $columns, $value = null, $operator = Helper::OPERATOR_EQUAL)
    {
        $this->criteria[$name] = new Criteria(
            $name,
            $columns,
            $operator,
            $this->frm->addDate($name, $this->request->get($name, $value))
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
        $this->criteria[$name] = new Criteria(
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
    public function addDropdownCriteria($name, $columns, $values = array(), $operator = Helper::OPERATOR_EQUAL)
    {
        $this->criteria[$name] = new Criteria(
            $name,
            $columns,
            $operator,
            $this->frm->addDropdown($name, $values, $this->request->get($name))->setDefaultElement('-', '-1')
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
        $this->criteria[$name] = new Criteria(
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
    public function getQuery($query = null)
    {
        if (empty($this->query)) {
            $queryGenerated = $query;

            $positionOfLastFrom = strripos($queryGenerated, ' from ');

            $queryFilter = false === stripos($queryGenerated, ' where ', $positionOfLastFrom) ? ' WHERE ' : ' AND ';
            $queryFilterParts = array();

            /**
             * @var $criteria Criteria
             */
            foreach ($this->criteria as $criteria) {
                if ($criteria->isFilled()) {
                    $parts = array();
                    foreach ($criteria->getColumns() as $column) {
                        $value = Helper::getOperatorBasedValue(
                            $criteria->getOperator(),
                            $criteria->getValue()
                        );
                        $parts[] = "{$column} {$criteria->getOperator()} {$value}";
                    }

                    $queryFilterPart = implode(' OR ', $parts);
                    $queryFilterParts[] = count($parts) > 1 ? '('.$queryFilterPart.')' : $queryFilterPart;
                }
            }

            if (!empty($queryFilterParts)) {
                $queryFilter .= implode(' AND ', $queryFilterParts);

                $positionGroupBy = stripos($queryGenerated, 'GROUP BY');
                $positionOrderBy = stripos($queryGenerated, 'ORDER BY');
                $positionHaving = stripos($queryGenerated, 'HAVING');

                $position = false;
                $positions = array();
                if ($positionGroupBy !== false) {
                    $positions[] = $positionGroupBy;
                }
                if ($positionOrderBy !== false) {
                    $positions[] = $positionOrderBy;
                }
                if ($positionHaving !== false) {
                    $positions[] = $positionHaving;
                }
                if (!empty($positions)) {
                    $position = min($positions);
                }

                if ($position === false) {
                    $queryGenerated = $queryGenerated.$queryFilter;
                } else {
                    $queryGenerated =
                        substr($queryGenerated, 0, $position)
                        .$queryFilter.' '
                        .substr($queryGenerated, $position);
                }

                $this->idle = false;
            }

            if (isset($query)) {
                $this->query = $queryGenerated;
            }
        }

        return $this->query;
    }

    /**
     * @return bool
     */
    public function isIdle()
    {
        return (bool)$this->idle;
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
