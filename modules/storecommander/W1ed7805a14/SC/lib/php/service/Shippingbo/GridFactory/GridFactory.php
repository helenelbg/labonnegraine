<?php

namespace Sc\service\Shippingbo\GridFactory;

final class GridFactory implements GridFactoryInterface
{
    /**
     * @var mixed
     */
    private $maxRefLength;
    /**
     * @var array|string[]
     */
    private $defaultColValues;

    /**
     * @var array|string[]
     */
    private $displayGroupPosition;
    private $currentPriorityStatus;

    const STATUS_MISSING_REFERENCE = 'missing_reference';
    const STATUS_DUPLICATE_REFERENCE = 'duplicated_reference';
    const STATUS_SKU_TOO_LONG = 'sku_too_long';

    const STATUS_MISSING_COMPONENT = 'missing_component';
    const STATUS_IS_LOCKED = 'is_locked';
    const STATUS_NO_ERROR = 'no_error';
    /**
     * @var int[]|string[]
     */
    private $columnsToDisplay;
    private $entityType;

    /**
     * @return mixed
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    public function __construct($entityType)
    {
        $this->maxRefLength = \Product::$definition['fields']['reference']['size'];
        $this->displayGroupPosition = [];
        $this->columnsToDisplay = [];
        $this->entityType = $entityType;

        $this->defaultColValues = [
            'statusCode' => 'success',
            'cellClass' => '',
            'value' => '',
        ];

        return $this;
    }

    /**
     * @param array|string[] $displayGroupPosition
     */
    public function setDisplayGroupPosition($displayGroupPosition)
    {
        $this->displayGroupPosition = $displayGroupPosition;

        return $this;
    }

    /**
     * @return array|string[]
     */
    public function getDisplayGroupPosition()
    {
        if (!$this->displayGroupPosition)
        {
            return [
                self::STATUS_MISSING_REFERENCE,
                self::STATUS_DUPLICATE_REFERENCE,
                self::STATUS_MISSING_COMPONENT,
                self::STATUS_SKU_TOO_LONG,
                self::STATUS_NO_ERROR,
                self::STATUS_IS_LOCKED,
            ];
        }

        return $this->displayGroupPosition;
    }

    public function getPriorityDefinition()
    {
        return [
            self::STATUS_IS_LOCKED => [
                'statusLabel' => _l('Locked'),
            ],
            self::STATUS_MISSING_REFERENCE => [
                'statusLabel' => _l('Reference is required'),
            ],
            self::STATUS_DUPLICATE_REFERENCE => [
                'statusLabel' => _l('Reference is duplicated'),
            ],
            self::STATUS_MISSING_COMPONENT => [
                'statusLabel' => _l("The product composing this %s doesn't exists in Shippingbo", null, [_l($this->getEntityType())]),
            ],
            self::STATUS_SKU_TOO_LONG => [
                'statusLabel' => _l('SKU is too long, Prestashop limit is %s characters', null, [$this->maxRefLength]),
            ],
            self::STATUS_NO_ERROR => [
                'statusLabel' => _l('No error'),
            ],
        ];
    }

    /**
     * @inherit
     */
    public function processColumns($row)
    {
        $this->currentPriorityStatus = self::STATUS_NO_ERROR;
        $this->columnsToDisplay = $this->getColumnsToDisplay();

        $columns = [];
        foreach ($row as $key => $value)
        {
            if (!isset($this->getColumnsToDisplay()[$key]))
            {
                continue;
            }
            $columns = $this->setStatusInformations($columns, self::STATUS_NO_ERROR);
            $columns[$key] = $this->defaultColValues;
            $columns[$key]['value'] = $value;

            // validate fields
            // TODO 3 : a refactoriser -> handlers ?
            if ($key === 'is_locked')
            {
                $columns[$key] = $this->checkIsLocked($row, $columns, $key);
            }
            if ($key === 'user_ref')
            {
                $columns[$key] = $this->checkUserRef($row, $columns, $key);
            }
            if ($key === 'name')
            {
                $columns[$key] = $this->checkName($row, $columns, $key);
            }
            if ($key === 'reference')
            {
                $columns[$key] = $this->checkReference($row, $columns, $key);
            }
            if ($key === 'source_ref')
            {
                $columns[$key] = $this->checkSourceRef($row, $columns, $key);
            }
            if ($key === 'active')
            {
                $columns[$key]['value'] = (bool) $value ? _l('Yes') : _l('No');
            }
        }

        // filtrage des colonnes à afficher
        return array_merge($this->getColumnsToDisplay(), $columns);
    }

    /**
     * @return int[]|string[]
     */
    public function getColumnsToDisplay()
    {
        return $this->columnsToDisplay;
    }

    /**
     * @param int[]|string[] $columnsToDisplay
     */
    public function setColumnsToDisplay($columnsToDisplay)
    {
        $this->columnsToDisplay = array_flip($columnsToDisplay);

        return $this;
    }

    private function setStatusInformations($columns, $groupCode)
    {
        if ($this->getGroupPriorityByCode($groupCode)['value'] < $this->getGroupPriorityByCode($this->currentPriorityStatus)['value'])
        {
            $this->currentPriorityStatus = $groupCode;
        }
        $columns['statusLabel'] = $this->getStatusLabelByCode($this->currentPriorityStatus);
        if ($this->getDisplayGroupPosition())
        {
            $columns['groupPosition'] = $this->getGroupDisplayPriorityByCode($this->currentPriorityStatus);
        }

        return $columns;
    }

    public function getGroupPriorityByCode($groupCode)
    {
        $groupCodeIndexes = array_flip(array_keys($this->getPriorityDefinition()));
        $groupPriority = $this->defaultColValues;
        $groupPriority['value'] = $groupCodeIndexes[$groupCode];

        return $groupPriority;
    }

    public function getGroupDisplayPriorityByCode($groupCode)
    {
        $groupCodeIndexes = array_flip($this->getDisplayGroupPosition());
        $groupPriority = $this->defaultColValues;
        $groupPriority['value'] = $groupCodeIndexes[$groupCode];

        return $groupPriority;
    }

    private function getStatusLabelByCode($groupCode)
    {
        $statusLabel = $this->getPriorityDefinition()[$groupCode]['statusLabel'];

        return [
            'statusCode' => $groupCode === self::STATUS_NO_ERROR ? 'success' : 'error',
            'cellClass' => $groupCode === self::STATUS_NO_ERROR ? 'sc_cell_success' : 'sc_cell_error',
            'value' => $statusLabel,
        ];
    }

    private function checkIsLocked($row, $columns, $key)
    {
        $columns[$key]['cellClass'] = 'sc_cell_success';
        if ($row['is_locked'])
        {
            $columns = $this->setStatusInformations($columns, self::STATUS_IS_LOCKED);
            $columns[$key]['statusCode'] = 'locked';
            $columns[$key]['cellClass'] = 'sc_cell_error';
        }

        return $columns[$key];
    }

    private function checkUserRef($row, $columns, $key)
    {
        $columns[$key]['cellClass'] = 'sc_cell_success';
        if (strlen($row[$key]) > $this->maxRefLength)
        {
            $columns = $this->setStatusInformations($columns, self::STATUS_SKU_TOO_LONG);
            $columns[$key]['statusCode'] = 'error';
            $columns[$key]['cellClass'] = 'sc_cell_error';
        }

        return $columns[$key];
    }

    private function checkName($row, $columns, $key)
    {
        $name = $row['name'];
        if (isset($row['combination_name']))
        {
            $name .= ' - '.$row['combination_name'];
        }
        $columns[$key]['value'] = $name;

        return $columns[$key];
    }

    private function checkReference($row, $columns, $key)
    {
        if (empty($row['reference']))
        {
            $columns = $this->setStatusInformations($columns, self::STATUS_MISSING_REFERENCE);
            $columns[$key]['statusCode'] = 'error';
            $columns[$key]['cellClass'] = 'sc_cell_error';
        }
        if ($row['duplicate_ref'])
        {
            $columns = $this->setStatusInformations($columns, self::STATUS_DUPLICATE_REFERENCE);
            $columns[$key]['statusCode'] = 'error';
            $columns[$key]['cellClass'] = 'sc_cell_error';
        }

        return $columns[$key];
    }

    private function checkSourceRef(array $row, $columns, $key)
    {
        if (isset($row['product_value']) && empty($row['product_value']))
        {
            $columns = $this->setStatusInformations($columns, self::STATUS_MISSING_COMPONENT);
            $columns[$key]['statusCode'] = 'error';
            $columns[$key]['cellClass'] = 'sc_cell_error';
        }

        return $columns[$key];
    }
}
