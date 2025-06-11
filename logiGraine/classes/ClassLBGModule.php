<?php
    class LBGModule
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
                $req->from('LogiGraine_module', 'lgm');
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

        public static function getModulesByRole($id_role = 0)
        {
            if ( $id_role > 0 )
            {
                $req = new DbQuery();
                $req->select('lgm.id_module');
                $req->from('LogiGraine_module', 'lgm');
                $req->leftJoin('LogiGraine_role_module', 'lgrm', 'lgm.`id_module` = lgrm.`id_module`');
                $req->where('lgrm.id_role = "'.$id_role.'"');
                $req->orderBy('lgm.ordre_module', 'ASC');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                $resultat = array();
                foreach($resu as $rangee)
                {
                    $moduleTmp = new LBGModule($rangee['id_module']);
                    $resultat[] = $moduleTmp;
                }
                return $resultat;
            }
            return false;
        }

        public static function testModuleByOperateur($id_module = 0, $id_operateur = 0)
        {
            if ( $id_module > 0 && $id_operateur > 0 )
            {
                $req = new DbQuery();
                $req->select('lgm.id_module');
                $req->from('LogiGraine_module', 'lgm');
                $req->leftJoin('LogiGraine_role_module', 'lgrm', 'lgm.`id_module` = lgrm.`id_module`');
                $req->leftJoin('LogiGraine_role', 'lgr', 'lgrm.`id_role` = lgr.`id_role`');
                $req->leftJoin('LogiGraine_operateur', 'lgo', 'lgr.`id_role` = lgo.`id_role`');
                $req->where('lgm.id_module = "'.$id_module.'" AND lgo.id_operateur = "'.$id_operateur.'"');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                $resultat = array();
                foreach($resu as $rangee)
                {
                    $moduleTmp = new LBGModule($rangee['id_module']);
                    $resultat[] = $moduleTmp;
                }
                return $resultat;
            }
            return false;
        }
    }
?>