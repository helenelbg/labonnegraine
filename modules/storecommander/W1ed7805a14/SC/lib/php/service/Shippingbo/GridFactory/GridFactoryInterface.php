<?php

namespace Sc\service\Shippingbo\GridFactory;

interface GridFactoryInterface
{
    /**
     * @param array $row
     *
     * @return array
     */
    public function processColumns($row);
    public function setDisplayGroupPosition($displayGroupPosition);

    public function setColumnsToDisplay($columnsToDisplay);

}
