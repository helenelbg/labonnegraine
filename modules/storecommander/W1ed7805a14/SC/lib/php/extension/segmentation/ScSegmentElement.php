<?php

/* ScSegmentation */

class ScSegmentElement extends ObjectModel
{
    public $id;
    public $id_segment;
    public $id_element;
    public $type_element;

    protected $tables = array('sc_segment_element');

    protected $table = 'sc_segment_element';
    protected $identifier = 'id_segment_element';

    public function getFields()
    {
        parent::validateFields();
        $fields['id_segment'] = (int) $this->id_segment;
        $fields['id_element'] = (int) $this->id_element;
        $fields['type_element'] = ($this->type_element);

        return $fields;
    }

    public static function checkInSegment($id_segment, $id_element, $type)
    {
        $return = false;

        $sql = 'SELECT id_segment_element 
                FROM '._DB_PREFIX_."sc_segment_element 
                WHERE id_segment='".(int)$id_segment."'
                    AND id_element='".(int)$id_element."'
                    AND type_element='".pSQL($type)."'
                LIMIT 1";
        $res = Db::getInstance()->executeS($sql);
        if (!empty($res[0]['id_segment_element']))
        {
            $return = true;
        }

        return $return;
    }
}
