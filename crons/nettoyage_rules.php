<?php
require('../config/config.inc.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRule.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleFamily.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountDatabase.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleCondition.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleGroup.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleAction.php');
include_once(_PS_MODULE_DIR_.'quantitydiscountpro/classes/QuantityDiscountRuleMessage.php');
error_log('NETTOYAGE RULES - DEBUT');
for ($i = 1; $i <= 3; $i++) {
    QuantityDiscountRule::removeUnusedRules(null, 1);
}
error_log('NETTOYAGE RULES - FIN');
?>