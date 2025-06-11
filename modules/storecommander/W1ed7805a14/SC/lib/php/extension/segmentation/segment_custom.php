<?php

class SegmentCustom
{
    public $name;
    public $liste_hooks = array();
    public $liste_actions_right_clic = array();
    public $manually_add_in = 'N';
    public $operator = 'AND';

    public function executeHook($name, $params = array())
    {
        $return = null;
        if (!empty($name) && in_array($name, $this->liste_hooks))
        {
            if (method_exists($this, '_executeHook_'.$name))
            {
                $returned = $this->{'_executeHook_'.$name}($name, $params);
                if (is_array($returned))
                {
                    if (empty($return))
                    {
                        $return = array();
                    }
                    $return = array_merge($return, $returned);
                }
                else
                {
                    $return .= $returned;
                }
            }
        }

        return $return;
    }

    public function getLevelFromDB($parent_id, $values, $niveau = 0)
    {
        $html = '';
        $sql = 'SELECT *
                    FROM '._DB_PREFIX_."sc_segment
                    WHERE id_parent = ".(int) $parent_id."
                    ORDER BY name";

        $res = Db::getInstance()->ExecuteS($sql);
        foreach ($res as $row)
        {
            if ($niveau > 0)
            {
                $name = '|- '.$row['name'];
            }
            else
            {
                $name = $row['name'];
            }
            for ($i = 1; $i <= $niveau; ++$i)
            {
                $name = '&nbsp;&nbsp;&nbsp;'.$name;
            }
            $html .= '<option value="'.$row['id_segment'].'" '.(isset($values['id_segment']) && $row['id_segment'] == $values['id_segment'] ? 'selected' : '').'>'.$name.'</option>';
            $html .= $this->getLevelFromDB($row['id_segment'], $values, ($niveau + 1));
        }

        return $html;
    }
}
