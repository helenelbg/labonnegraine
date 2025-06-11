<?php

require_once(dirname(__FILE__) . "/../../../config/config.inc.php");
require_once(dirname(__FILE__) . "/../../../init.php");

$result["etat"] = "KO";

if(isset($_POST["utm_source"])) $_POST["utm_source"] = pSQL($_POST["utm_source"]);
if(isset($_POST["utmFrom"])) $_POST["utmFrom"] = pSQL($_POST["utmFrom"]);
if(isset($_POST["utmTo"])) $_POST["utmTo"] = pSQL($_POST["utmTo"]);

if (isset($_POST["utm_source"])) {

    $where = "";
    $where2 = "";

    if(!empty($_POST["utmFrom"]) && $_POST["utmFrom"] != ""){
        $where .= " AND `o`.`date_add` >= '".$_POST["utmFrom"]." 00:00:00'";
        $where2 .= " AND `so`.`date_add` >= '".$_POST["utmFrom"]." 00:00:00'";
    }

    if(!empty($_POST["utmTo"]) && $_POST["utmTo"] != ""){
        $where .= " AND `o`.`date_add` <= '".$_POST["utmTo"]." 23:59:59'";
        $where2 .= " AND `so`.`date_add` <= '".$_POST["utmTo"]." 23:59:59'";
    }

    if ($_POST["utm_source"] == "Global") {
        $CA = Db::getInstance()->executeS('SELECT (
            SELECT SUM(`total_paid_tax_incl`) FROM `' . _DB_PREFIX_ . 'orders` `o` WHERE (utm_source <> "" OR utm_medium <> "" OR utm_campaign <> "") AND `valid` = 1 '.$where.'
            ) AS "avec", (
            SELECT SUM(`total_paid_tax_incl`) FROM `' . _DB_PREFIX_ . 'orders` `o` WHERE (utm_source = "" AND utm_medium = "" AND utm_campaign = "") AND `valid` = 1 '.$where.'
            ) AS "sans" 
            FROM `' . _DB_PREFIX_ . 'orders` LIMIT 1
        ');

        $CASource = Db::getInstance()->executeS('SELECT SUM(`total_paid_tax_incl`) as "montant", `utm_source` FROM `' . _DB_PREFIX_ . 'orders` `o` WHERE `utm_source` <> "" AND `valid` = 1 '.$where.' GROUP BY `utm_source`');
        $dataSource[] = ["test", "test"];
        foreach ($CASource as $val) {
            $dataSource[] = [$val["utm_source"], round($val["montant"], 2)];
        }

        $utmAge = Db::getInstance()->executeS('SELECT DATEDIFF( NOW(), `c`.`birthday`) / 365.25 AS "age" 
            FROM `' . _DB_PREFIX_ . 'customer` `c`
            INNER JOIN `' . _DB_PREFIX_ . 'orders` `o` ON `o`.`id_customer` = `c`.`id_customer`
            WHERE `o`.`utm_source` <> "" AND `valid` = 1 '.$where.'
        ');
        $dataAge = [
            ["test", "test"], ["Non renseigné", 0], ["0-10 ans", 0], ["10-20 ans", 0], ["20-30 ans", 0], ["30-40 ans", 0], ["40-50 ans", 0], ["50-60 ans", 0], ["60-70 ans", 0], ["70-80 ans", 0], ["80-90 ans", 0], ["90-100 ans", 0]
        ];

        foreach ($utmAge as $age) {

            if ($age["age"] == 0) {
                $dataAge[1][1] += 1;
            }else if ($age["age"] < 10) {
                $dataAge[2][1] += 1;
            } else if ($age["age"] < 20) {
                $dataAge[3][1] += 1;
            } else if ($age["age"] < 30) {
                $dataAge[4][1] += 1;
            } else if ($age["age"] < 40) {
                $dataAge[5][1] += 1;
            } else if ($age["age"] < 50) {
                $dataAge[6][1] += 1;
            } else if ($age["age"] < 60) {
                $dataAge[7][1] += 1;
            } else if ($age["age"] < 70) {
                $dataAge[8][1] += 1;
            } else if ($age["age"] < 80) {
                $dataAge[9][1] += 1;
            } else if ($age["age"] < 90) {
                $dataAge[10][1] += 1;
            } else if ($age["age"] < 100) {
                $dataAge[11][1] += 1;
            }
        }

        $utmSexe = Db::getInstance()->executeS('SELECT (CASE WHEN `c`.`id_gender` = 1 THEN "Homme" WHEN `c`.`id_gender` = 2 THEN "Femme" ELSE "Autre" END) AS "sexe", COUNT(CASE WHEN `c`.`id_gender` = 1 THEN "Homme" WHEN `c`.`id_gender` = 2 THEN "Femme" ELSE "Autre" END) AS "val"
                FROM `' . _DB_PREFIX_ . 'customer` `c` 
                INNER JOIN `' . _DB_PREFIX_ . 'orders` `o` ON `o`.`id_customer` = `c`.`id_customer` 
                WHERE `o`.`utm_source` <> "" AND `valid` = 1 '.$where.'
                GROUP BY `c`.`id_gender`
            ');
        $dataSexe[] = ["test", "test"];

        foreach ($utmSexe as $genre) {
            $dataSexe[] = [$genre["sexe"], (int)$genre["val"]];
        }

        $utmPays = Db::getInstance()->executeS('SELECT `c`.`name` AS `pays`,  COUNT(`c`.`name`) AS `val`
            FROM `' . _DB_PREFIX_ . 'orders` `o` 
            INNER JOIN `' . _DB_PREFIX_ . 'address` `a` ON `a`.`id_address` = `o`.`id_address_delivery` 
            INNER JOIN `' . _DB_PREFIX_ . 'country_lang` `c` ON `c`.`id_country` = `a`.`id_country` 
            WHERE `c`.`id_lang` = 1 AND `o`.`utm_source` <> "" AND `valid` = 1 '.$where.'
            GROUP BY `c`.`name`
        ');
        $dataPays[] = ["test", "test"];

        foreach ($utmPays as $pays) {
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
                ) > 0, "Anciens", "Nouveaux") as `new`, 
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

        $result["data"] = [
            [
                "options" => '{"title": "Chiffre d\'affaires global (en €)", "pieHole": 0.4}',
                "col" => 2,
                "data" => [["test", "test"], ["Avec UTM", round($CA[0]["avec"], 2)], ["Sans UTM", round($CA[0]["sans"], 2)]],
                "type" => "pie"
            ],
            [
                "options" => '{"title": "Chiffre d\'affaires par UTM Source (en €)", "pieHole": 0.4}',
                "col" => 2,
                "data" => $dataSource,
                "type" => "pie"
            ],
            [
                "options" => '{"title": "Âge", "pieHole": 0.4}',
                "col" => 2,
                "data" => $dataAge,
                "type" => "pie"
            ],
            [
                "options" => '{"title": "Sexe", "pieHole": 0.4}',
                "col" => 2,
                "data" => $dataSexe,
                "type" => "pie"
            ],
            [
                "options" => '{"title": "Pays", "pieHole": 0.4}',
                "col" => 2,
                "data" => $dataPays,
                "type" => "pie"
            ],
            [
                "options" => '{"title": "Clients", "pieHole": 0.4}',
                "col" => 2,
                "data" => $dataNew,
                "type" => "pie"
            ]
        ];
    }else{
        $utm_medium = Db::getInstance()->executeS('SELECT `utm_medium`, SUM(`total_paid_tax_incl`) as "nb" 
            FROM `' . _DB_PREFIX_ . 'orders` `o`
            WHERE `valid` = 1 AND `utm_source` = "'.$_POST["utm_source"].'" '.$where.'
            GROUP BY `utm_medium`
        ');
        $dataUtmMedium = [["test", "test"]];

        foreach($utm_medium as $utm){
            $dataUtmMedium[] = [$utm["utm_medium"], (int)$utm["nb"]];
        }

        $utmNew = Db::getInstance()->executeS('SELECT   
            IF(( SELECT `so`.`id_order` 
                FROM `' . _DB_PREFIX_ . 'orders` `so` 
                WHERE `so`.`id_customer` = `o`.`id_customer` AND `so`.`id_order` < `o`.`id_order` '.$where2.' LIMIT 1
                ) > 0, "Anciens", "Nouveaux") as `new`, 
            COUNT(IF(( SELECT `so`.`id_order` 
                FROM `' . _DB_PREFIX_ . 'orders` `so` 
                WHERE `so`.`id_customer` = `o`.`id_customer` AND `so`.`id_order` < `o`.`id_order` '.$where2.' LIMIT 1
                ) > 0, 0, 1)) AS `nb`
            FROM `' . _DB_PREFIX_ . 'orders` `o` 
            WHERE `o`.`utm_source` = "'.$_POST["utm_source"].'" AND `valid` = 1 '.$where.'
            GROUP BY new
        ');

        $dataNew[] = ["test", "test"];

        foreach($utmNew as $new){
            $dataNew[] = [$new["new"], (int)$new["nb"]];
        }

        $result["data"] = [
            [
                "options" => json_encode(["title" => "Chiffre d'affaires par utm_medium pour l'urm_source ".$_POST["utm_source"]." (en €)", "pieHole" => 0.4]),
                "col" => 2,
                "data" => $dataUtmMedium,
                "type" => "pie"
            ],
            [
                "options" => json_encode(["title" => "Clients", "pieHole" => 0.4]),
                "col" => 2,
                "data" => $dataNew,
                "type" => "pie"
            ],
        ];

        foreach($utm_medium as $utm){
            $utm_campaign = Db::getInstance()->executeS('SELECT `utm_campaign`, SUM(`total_paid_tax_incl`) as "nb" 
                FROM `' . _DB_PREFIX_ . 'orders`  `o` 
                WHERE `valid` = 1 AND `utm_source` = "'.$_POST["utm_source"].'" AND `utm_medium` = "'.$utm["utm_medium"].'" '.$where.'
                GROUP BY `utm_campaign`
            ');

            $dataUtmCampaign = [["test", "test"]];

            foreach($utm_campaign as $camp){
                $dataUtmCampaign[] = [$camp["utm_campaign"], (int)$camp["nb"]];
            }

            $result["data"][] = [
                "options" => json_encode(["title" => "Chiffre d'affaires par utm_campaign pour l'utm_medium ".$utm["utm_medium"]." (en €)", "pieHole" => 0.4]),
                "col" => count($utm_medium),
                "data" => $dataUtmCampaign,
                "type" => "pie"
            ];
        }
    }

    $result["etat"] = "OK";
}

echo json_encode($result);
