<?php
    class Operateur
    {
        public $id;
        public $nom;
        public $prenom;
        public $id_role;

        public function __construct($id_operateur = 0)
        {
            if ( $id_operateur > 0 )
            {
                $req = new DbQuery();
                $req->select('lgo.id_operateur, lgo.nom_operateur, lgo.prenom_operateur, lgo.id_role');
                $req->from('LogiGraine_operateur', 'lgo');
                $req->where('lgo.id_operateur = "'.$id_operateur.'"');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_operateur']) && !empty($resu[0]['id_operateur']) )
                {
                    $this->id = $resu[0]['id_operateur'];
                    $this->nom = $resu[0]['nom_operateur'];
                    $this->prenom = $resu[0]['prenom_operateur'];
                    $this->id_role = $resu[0]['id_role'];
                }
            }
        }
    }
?>