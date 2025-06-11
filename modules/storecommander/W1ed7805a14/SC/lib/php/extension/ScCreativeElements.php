<?php

namespace Sc\Lib\Extension;

use CE\UId;
use SCI;

class ScCreativeElements
{
    /**
     * @param int   $idElement element id to edit
     * @param int   $type      use CE\Uid constants
     * @param int   $id_lang
     * @param mixed $sc_agent
     *
     * @return string
     */
    public static function EditLink($idElement, $type, $id_lang, $sc_agent)
    {
        $uid = new UId($idElement, $type, $id_lang, SCI::getSelectedShop());

        return SC_PS_PATH_ADMIN_REL.'index.php?controller=AdminCEEditor&token='.$sc_agent->getPSToken('AdminCEEditor').'&uid='.$uid;
    }
}
