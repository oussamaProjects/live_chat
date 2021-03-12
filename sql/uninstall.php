<?php
/**
 * Live Promo
 *
 * @author    L'nkboot 
 * @copyright L'nkboot 2020 
 * @license   http://www.lnkboot.fr/livepromo/license
 */

/**
 * In some cases you should not drop the tables.
 * Maybe the merchant will just try to reset the module
 * but does not want to loose all of the data associated to the module.
 */
$sql = array( '
    DROP TABLE IF EXISTS `'._DB_PREFIX_.'lnk_promo_promos`;
    DROP TABLE IF EXISTS `'._DB_PREFIX_.'lnk_promo_scheduled_products`;
    DROP TABLE IF EXISTS `'._DB_PREFIX_.'lp_liked`;
    DROP TABLE IF EXISTS `'._DB_PREFIX_.'lp_processlogger` ;
    DROP TABLE IF EXISTS `'._DB_PREFIX_.'lp_scheduled_product` ', 
);

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
