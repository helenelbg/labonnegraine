<?php

if (!function_exists('array_column'))
{
    /**
     * for php < 5.5.0.
     *
     * @param $columnKey
     * @param $indexKey
     *
     * @return array
     */
    function array_column(array $input, $columnKey, $indexKey = null)
    {
        $output = array();
        foreach ($input as $row)
        {
            $key = $value = null;
            $keySet = $valueSet = false;
            if ($indexKey !== null && array_key_exists($indexKey, $row))
            {
                $keySet = true;
                $key = (string) $row[$indexKey];
            }
            if ($columnKey === null)
            {
                $valueSet = true;
                $value = $row;
            }
            elseif (is_array($row) && array_key_exists($columnKey, $row))
            {
                $valueSet = true;
                $value = $row[$columnKey];
            }
            if ($valueSet)
            {
                if ($keySet)
                {
                    $output[$key] = $value;
                }
                else
                {
                    $output[] = $value;
                }
            }
        }

        return $output;
    }
}

if (!function_exists('is_iterable'))
{
    /**
     * for php < 7.1.0.
     *
     * @param $obj
     *
     * @return bool
     */
    function is_iterable($obj)
    {
        return is_array($obj) || (is_object($obj) && ($obj instanceof \Traversable));
    }
}
