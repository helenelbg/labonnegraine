<?php
    class LBGModuleAdmin
    {
        public $id;
        public $nom;
        public $script;
        public $picto;

        public function __construct($id_module = 0)
        {
            if ( $id_module > 0 )
            {
                $req = new DbQuery();
                $req->select('lgm.id_module, lgm.nom_module, lgm.script_module, lgm.picto_module');
                $req->from('LogiGraine_admin_module', 'lgm');
                $req->where('lgm.id_module = "'.$id_module.'"');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_module']) && !empty($resu[0]['id_module']) )
                {
                    $this->id = $resu[0]['id_module'];
                    $this->nom = $resu[0]['nom_module'];
                    $this->script = $resu[0]['script_module'];
                    $this->picto = $resu[0]['picto_module'];
                }
            }
        }

        public static function getModules()
        {
            $req = new DbQuery();
            $req->select('lgm.id_module');
            $req->from('LogiGraine_admin_module', 'lgm');
            $req->orderBy('lgm.ordre_module', 'ASC');
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

            $resultat = array();
            foreach($resu as $rangee)
            {
                $moduleTmp = new LBGModuleAdmin($rangee['id_module']);
                $resultat[] = $moduleTmp;
            }
            return $resultat;
        }
    }
?>