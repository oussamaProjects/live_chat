<?php
/**
 * Live Promo
 *
 * @author    L'nkboot 
 * @copyright L'nkboot 2020 
 * @license   http://www.lnkboot.fr/livepromo/license
 */

 
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');

/* Check to security token */
if (!Module::isInstalled('lnk_livepromo')) {
    die('Module not installed');
}

include_once(dirname(__FILE__).'/lnk_livepromo.php');

$lp = new Lnk_livepromo();
/* Check if the module is enabled */
if ($lp->active && Configuration::get('LP_active')) {
    if (Tools::getIsset('secure_key')) {
        if (Tools::getIsset('id_shop')) {
            Shop::setContext(Shop::CONTEXT_SHOP, (int)Tools::getValue('id_shop'));
        } else {
            Shop::setContext(Shop::CONTEXT_SHOP, (int)Configuration::get('PS_SHOP_DEFAULT'));
        }
        $secure_key = Configuration::getGlobalValue('PROMO_CRONJOBS_EXECUTION_TOKEN');
        if (!empty($secure_key) && $secure_key == Tools::getValue('secure_key')) {
            $lp->cronTask();
        } else {
            die('Wrong security key');
        }
    } else {
        die('Security key missing');
    }
} else {
    die('Module is not active');
}
