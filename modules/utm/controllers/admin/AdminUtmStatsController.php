<?php

class AdminUtmStatsController extends ModuleAdminController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $this->bootstrap = true;
    }

    public function initContent()
    {
        parent::initContent();
        $this->getDatas($this->dispatch());
        $this->setTemplate('stats.tpl');
    }

    private function dispatch()
    {
        $url = "./_partials/";
        $active = (!empty(Tools::getValue("page_fragment"))) ? Tools::getValue("page_fragment") : "Commandes";

        $utmFrom = (!empty(Tools::getValue("utmFrom")) && Tools::getValue("utmFrom") != "") ? Tools::getValue("utmFrom") : (new DateTime(date("Y-m-d")))->sub(new DateInterval('P1M'))->format("Y-m-d");
        $utmTo = (!empty(Tools::getValue("utmTo")) && Tools::getValue("utmTo") != "") ? Tools::getValue("utmTo") : date("Y-m-d");

        switch (Tools::getValue("page_fragment")) {
            case "Commandes":
                $url .= "commandes.tpl";
                break;
            case "Clients":
                $url .= "clients.tpl";
                break;
            case "Statistiques":
                $url .= "statistiques.tpl";
                break;
            default:
                $url .= "commandes.tpl";
                break;
        }

        $this->context->smarty->assign([
            "dataPost" => Tools::getValue("page_fragment"),
            "fragments" => $url,
            "controllerUrl" => "index.php?controller=".$_GET["controller"]."&token=".$_GET["token"],
            "active" => $active,
            "utmFrom" => $utmFrom,
            "utmTo" => $utmTo
        ]);

        return $active;
    }

    private function getDatas($page)
    {
        if ($page === "Commandes") {
            $data = Db::getInstance()->executeS('SELECT `o`.`id_order`, `cu`.`email`, `cu`.`lastname`, `cu`.`firstname`, IF(`cu`.`id_gender` = 1, "H", "F") AS "sexe", `c`.`name` AS `pays`, DATEDIFF( NOW(), `cu`.`birthday`) / 365.25 AS "age", `gl`.`name` AS "group", `o`.`utm_source`, `o`.`utm_medium`, `o`.`utm_campaign`, `o`.`total_paid_tax_incl`, `o`.`payment`, `os`.`name` AS `etat`, `o`.`date_add` 
                FROM `' . _DB_PREFIX_ . 'orders` `o` 
                INNER JOIN `' . _DB_PREFIX_ . 'address` `a` ON `a`.`id_address` = `o`.`id_address_delivery` 
                INNER JOIN `' . _DB_PREFIX_ . 'country_lang` `c` ON `c`.`id_country` = `a`.`id_country` 
                INNER JOIN `' . _DB_PREFIX_ . 'order_state_lang` `os` ON `os`.`id_order_state` = `o`.`current_state` 
                INNER JOIN `' . _DB_PREFIX_ . 'customer` `cu` ON `cu`.`id_customer` = `o`.`id_customer` 
                INNER JOIN `' . _DB_PREFIX_ . 'group_lang` `gl` ON `gl`.`id_group` = `cu`.`id_default_group`
                WHERE `o`.`utm_source` <> "" OR `o`.`utm_medium` <> "" OR `o`.`utm_campaign` <> "" 
                GROUP BY `o`.`id_order` 
                ORDER BY `o`.`id_order` DESC

            ');

            $this->context->smarty->assign([
                "data" => $data
            ]);
        }else if ($page === "Clients") {
            $data = Db::getInstance()->executeS('SELECT `c`.`id_customer`, `c`.`lastname`, `c`.`firstname`, `c`.`email`, `gl`.`name` AS "group", `c`.`utm_source`, `c`.`utm_medium`, `c`.`utm_campaign`, `c`.`utm_expire`, `c`.`date_add`, (SELECT `c`.`date_add` FROM `' . _DB_PREFIX_ . 'guest` `g` LEFT JOIN `' . _DB_PREFIX_ . 'connections` `con` ON `con`.`id_guest` = `g`.`id_guest` WHERE `g`.`id_customer` = `c`.`id_customer` ORDER BY `c`.`date_add` DESC LIMIT 1) AS "lastVisite" 
                FROM `' . _DB_PREFIX_ . 'customer` `c` 
                INNER JOIN `' . _DB_PREFIX_ . 'group_lang` `gl` ON `gl`.`id_group` = `c`.`id_default_group`
                WHERE `c`.`utm_source` <> "" OR `c`.`utm_medium` <> "" OR `c`.`utm_campaign` <> "" OR `c`.`utm_expire` iS NOT NULL 
                GROUP BY `c`.`id_customer`
            ');

            $this->context->smarty->assign([
                "data" => $data
            ]);
        }else if($page === "Statistiques"){

            $where = "";
            $where2 = "";

            $utmFrom = (!empty(Tools::getValue("utmFrom")) && Tools::getValue("utmFrom") != "") ? Tools::getValue("utmFrom") : (new DateTime(date("Y-m-d")))->sub(new DateInterval('P1M'))->format("Y-m-d");
            $utmTo = (!empty(Tools::getValue("utmTo")) && Tools::getValue("utmTo") != "") ? Tools::getValue("utmTo") : date("Y-m-d");

            if(!empty($utmFrom) && $utmFrom != ""){
                $where .= " AND `o`.`date_add` >= '".$utmFrom." 00:00:00'";
                $where2 .= " AND `so`.`date_add` >= '".$utmFrom." 00:00:00'";
            }

            if(!empty($utmTo) && $utmTo != ""){
                $where .= " AND `o`.`date_add` <= '".$utmTo." 23:59:59'";
                $where2 .= " AND `so`.`date_add` <= '".$utmTo." 23:59:59'";
            }

            $CA = Db::getInstance()->executeS('SELECT (
                SELECT SUM(`total_paid_tax_incl`) FROM `' . _DB_PREFIX_ . 'orders` `o` WHERE (utm_source <> "" OR utm_medium <> "" OR utm_campaign <> "") AND `valid` = 1 '.$where.'
            ) AS "avec", (
                SELECT SUM(`total_paid_tax_incl`) FROM `' . _DB_PREFIX_ . 'orders` `o` WHERE (utm_source = "" AND utm_medium = "" AND utm_campaign = "") AND `valid` = 1 '.$where.'
            ) AS "sans" FROM `' . _DB_PREFIX_ . 'orders` `o` LIMIT 1');

            $CASource = Db::getInstance()->executeS('SELECT SUM(`total_paid_tax_incl`) as "montant", `utm_source` FROM `' . _DB_PREFIX_ . 'orders` `o` WHERE `utm_source` <> "" AND `valid` = 1 '.$where.' GROUP BY `utm_source`');
            $dataSource[] = ["test", "test"];
            foreach($CASource as $val){
                $dataSource[] = [$val["utm_source"], round($val["montant"], 2)];
            }

            $utmAge = Db::getInstance()->executeS('SELECT DATEDIFF( NOW(), `c`.`birthday`) / 365.25 AS "age" 
                FROM `' . _DB_PREFIX_ . 'customer` `c`
                INNER JOIN `' . _DB_PREFIX_ . 'orders` `o` ON `o`.`id_customer` = `c`.`id_customer`
                WHERE `o`.`utm_source` <> "" AND `valid` = 1 '.$where.' 
            ');
            $dataAge = [
                ["test", "test"], ["0-10 ans", 0], ["10-20 ans", 0], ["20-30 ans", 0], ["30-40 ans", 0], ["40-50 ans", 0], ["50-60 ans", 0], ["60-70 ans", 0], ["70-80 ans", 0], ["80-90 ans", 0], ["90-100 ans", 0]
            ];

            foreach($utmAge as $age){

                if($age["age"] < 10){
                    $dataAge[1][1]+=1;
                }else if($age["age"] < 20){
                    $dataAge[2][1]+=1;
                }else if($age["age"] < 30){
                    $dataAge[3][1]+=1;
                }else if($age["age"] < 40){
                    $dataAge[4][1]+=1;
                }else if($age["age"] < 50){
                    $dataAge[5][1]+=1;
                }else if($age["age"] < 60){
                    $dataAge[6][1]+=1;
                }else if($age["age"] < 70){
                    $dataAge[7][1]+=1;
                }else if($age["age"] < 80){
                    $dataAge[8][1]+=1;
                }else if($age["age"] < 90){
                    $dataAge[9][1]+=1;
                }else if($age["age"] < 100){
                    $dataAge[10][1]+=1;
                }
            }

            $utmSexe = Db::getInstance()->executeS('SELECT IF(`c`.`id_gender` = 1, "Homme", "Femme") AS "sexe", COUNT(IF(`c`.`id_gender` = 1, "H", "F")) AS "val" 
                FROM `' . _DB_PREFIX_ . 'customer` `c` 
                INNER JOIN `' . _DB_PREFIX_ . 'orders` `o` ON `o`.`id_customer` = `c`.`id_customer` 
                WHERE `o`.`utm_source` <> "" AND `valid` = 1  AND `c`.`id_gender` IN (1, 2) '.$where.'
                GROUP BY `c`.`id_gender`
 
            ');
            $dataSexe[] = ["test", "test"];

            foreach($utmSexe as $genre){
                $dataSexe[] = [$genre["sexe"], (int)$genre["val"]];
            }
            
            $utmPays = Db::getInstance()->executeS('SELECT `c`.`name` AS `pays`,  COUNT(`c`.`name`) AS `val`
                FROM `' . _DB_PREFIX_ . 'orders` `o` 
                INNER JOIN `' . _DB_PREFIX_ . 'address` `a` ON `a`.`id_address` = `o`.`id_address_delivery` 
                INNER JOIN `' . _DB_PREFIX_ . 'country_lang` `c` ON `c`.`id_country` = `a`.`id_country` 
                WHERE `o`.`utm_source` <> "" AND `valid` = 1 '.$where.' 
                GROUP BY `c`.`name`
            ');
            $dataPays[] = ["test", "test"];

            foreach($utmPays as $pays){
                $dataPays[] = [$pays["pays"], (int)$pays["val"]];
            }

            $listSource = Db::getInstance()->executeS('SELECT DISTINCT(`utm_source`)
                FROM `' . _DB_PREFIX_ . 'orders` `o`
                WHERE `utm_source` <> "" AND `valid` = 1 '.$where.'
                ORDER BY `utm_source`
            ');

            $utmNew = Db::getInstance()->executeS('SELECT   
                IF(( SELECT `so`.`id_order` 
                    FROM `' . _DB_PREFIX_ . 'orders` `so` 
                    WHERE `so`.`id_customer` = `o`.`id_customer` AND `so`.`id_order` < `o`.`id_order` '.$where2.' LIMIT 1
                    ) > 0, "Non", "Oui") as `new`, 
                COUNT(IF(( SELECT `so`.`id_order` 
                    FROM `' . _DB_PREFIX_ . 'orders` `so` 
                    WHERE `so`.`id_customer` = `o`.`id_customer` AND `so`.`id_order` < `o`.`id_order` '.$where2.' LIMIT 1
                    ) > 0, 0, 1)) AS `nb`
                FROM `' . _DB_PREFIX_ . 'orders` `o` 
                WHERE `o`.`utm_source` <> "" AND `valid` = 1 '.$where.'
                GROUP BY new
            ');
            $dataNew[] = ["test", "test"];

            foreach($utmNew as $new){
                $dataNew[] = [$new["new"], (int)$new["nb"]];
            }

            $this->context->smarty->assign([
                "dataChart" => json_encode([
                    [
                        "options" => '{"title": "Chiffre d\'affaire global", "pieHole": 0.4}',
                        "col" => 2,
                        "data" => [["test", "test"], ["Avec UTM", round($CA[0]["avec"], 2)], ["Sans UTM", round($CA[0]["sans"], 2)]],
                        "type" => "pie"
                    ],
                    [
                        "options" => '{"title": "Chiffre d\'affaire (UTM Source)", "pieHole": 0.4}',
                        "col" => 2,
                        "data" => $dataSource,
                        "type" => "pie"
                    ],
                    [
                        "options" => '{"title": "UTM age", "pieHole": 0.4}',
                        "col" => 4,
                        "data" => $dataAge,
                        "type" => "pie"
                    ],
                    [
                        "options" => '{"title": "UTM sexe", "pieHole": 0.4}',
                        "col" => 4,
                        "data" => $dataSexe,
                        "type" => "pie"
                    ],
                    [
                        "options" => '{"title": "UTM pays", "pieHole": 0.4}',
                        "col" => 4,
                        "data" => $dataPays,
                        "type" => "pie"
                    ],
                    [
                        "options" => '{"title": "Nouveaux clients", "pieHole": 0.4}',
                        "col" => 4,
                        "data" => $dataNew,
                        "type" => "pie"
                    ]           
                ]),
                "listSource" => $listSource
            ]);
        }
    }
}
