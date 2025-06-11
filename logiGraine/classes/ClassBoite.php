<?php
    class Boite
    {
        public $id;
        public $code;
        public $id_product;

        public function __construct($code = 0)
        {
            if ( $code > 0 )
            {
                $req = new DbQuery();
                $req->select('lgb.id_boite, lgb.code_boite, lgb.id_product');
                $req->from('LogiGraine_boite', 'lgb');
                $req->where('lgb.code_boite = "'.$code.'"');
                $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
                
                if ( isset($resu[0]['id_boite']) && !empty($resu[0]['id_boite']) )
                {
                    $this->id = $resu[0]['id_boite'];
                    $this->code = $resu[0]['code_boite'];
                    $this->id_product = $resu[0]['id_product'];
                }
            }
        }

        public static function getNextBoite()
        {
            $req = new DbQuery();
            $req->select('lgb.code_boite');
            $req->from('LogiGraine_boite', 'lgb');
            $req->orderBy('lgb.code_boite DESC');
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);
            if ( isset($resu[0]['code_boite']) && !empty($resu[0]['code_boite']) )
            {
                return 'A'.str_pad(((int)substr($resu[0]['code_boite'],1,5)+1), 5, '0', STR_PAD_LEFT);
            }
            else 
            {
                return 'A00001';
            }
        }

        public function moveToFacing()
        {
            $req = 'UPDATE ps_LogiGraine_rangement_boite SET emplacement = "facing" WHERE id_boite = "'.$this->id.'";';
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req); 

            $req2 = 'UPDATE ps_LogiGraine_boite SET reserve = "0" WHERE id_boite = "'.$this->id.'";';
            $resu2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($req2);            
        }

        public function getProduct()
        {
            $req = 'SELECT name FROM ps_product_lang WHERE id_product = "'.$this->id_product.'" AND id_lang = 1;';
            $resu = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($req);     
            
            if ( isset($resu[0]['name']) && !empty($resu[0]['name']) )
            {
                return $resu[0]['name'];
            }     
            return false;  
        }
    }
?>