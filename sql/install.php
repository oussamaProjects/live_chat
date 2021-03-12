<?php
/**
 * Live Promo
 *
 * @author    L'nkboot 
 * @copyright L'nkboot 2020 
 * @license   http://www.lnkboot.fr/livepromo/license
 */

$sql = array();

// $sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lnk_ordersmanagement` (
//     `id_ordersmanagement` int(11) NOT NULL AUTO_INCREMENT,
//     `id_order` int(11) NOT NULL,
//     `id_cart_rule` int(11) NOT NULL,
//     `id_cart_new` int(11) NOT NULL,
//     `id_order_new` int(11) NOT NULL,
//     `id_shop` int(11) NOT NULL,
//     `options` varchar(254),
//     `options_selected` tinyint(1) NOT NULL,
//     `state` tinyint(1) NOT null DEFAULT 1,
//     `state_owner` tinyint(1) NOT null DEFAULT 1,
//     `token` varchar(64) character set utf8 NOT NULL,
//     `code` varchar(104) character set utf8,
//     `message` text character set utf8,
//     `reply` text character set utf8,
//     `total` FLOAT UNSIGNED,
//     `note` text character set utf8,
//     `date_add` datetime NOT NULL,
//     `date_upd` datetime NOT NULL,
//     PRIMARY KEY  (`id_ordersmanagement`)
// ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lnk_promo_promos` (
    `id_promo` int(11) NOT NULL AUTO_INCREMENT,
    `id_cart_rule` int(11) NOT NULL,
    `minute` varchar(254) NOT NULL,
    `hour` varchar(254) NOT NULL,
    `day` varchar(254) NOT NULL,
    `weekday` varchar(254) NOT NULL,
    `month` varchar(254) NOT NULL,
    `common_option` varchar(254), 
    `note` text character set utf8,
    `notify` tinyint,
    `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_upd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (`id_promo`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lnk_promo_scheduled_products` (
    `id_scheduled_products` int(11) NOT NULL AUTO_INCREMENT,
    `id_product` int(11) NOT NULL,
    `minute` varchar(254) NOT NULL,
    `hour` varchar(254) NOT NULL,
    `day` varchar(254) NOT NULL,
    `weekday` varchar(254) NOT NULL,
    `month` varchar(254) NOT NULL,
    `common_option` varchar(254), 
    `note` text character set utf8,
    `notify` tinyint,
    `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_upd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (`id_scheduled_products`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'lp_processlogger` (
    `id_lp_processlogger` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `msg` varchar(255) NOT NULL,
    `level` varchar(10) NOT NULL,
    `object_name` varchar(100) NOT NULL,
    `object_id` tinyint,
    `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (`id_lp_processlogger`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';


$sql[] = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'lp_liked` (
    `id_liked` int(10) unsigned NOT NULL auto_increment,
    `id_customer` int(10) unsigned NOT NULL,
    `id_product` int(10) unsigned NOT NULL,
    `id_shop` int(10) unsigned default 1,
    `id_shop_group` int(10) unsigned default 1,
    `date_add` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_upd` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (`id_liked`)
  ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;';



foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) == false) {
        return false;
    }
}
