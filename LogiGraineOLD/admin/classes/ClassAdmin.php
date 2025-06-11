<?php
    class Admin
    {
        public $id;
        public $email;
        public $nom;
        public $prenom;

        public function __construct($id_admin = 0)
        {
            if ( $id_admin > 0 )
            {
                $req = new DbQuery();
                $req->select('lga.id_admin, lga.nom_admin, lga.prenom_admin, lga.email_admin');
                $req->from('LogiGraine_admin', 'lga');
                $req->where('lga.id_admin = "'.$id_admin.'"');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);

                if ( isset($resu[0]['id_admin']) && !empty($resu[0]['id_admin']) )
                {
                    $this->id = $resu[0]['id_admin'];
                    $this->nom = $resu[0]['nom_admin'];
                    $this->prenom = $resu[0]['prenom_admin'];
                    $this->email = $resu[0]['email_admin'];
                }
            }
        }
    }
?>