<?php
/**
 * L'nk Orders Management Module
 *
 * @author    Abdelakbir el Ghazouani - By L'nkboot.fr
 * @copyright 2016-2020 Abdelakbir el Ghazouani - By L'nkboot.fr (http://www.lnkboot.fr)
 * @license   Commercial license see license.txt
 * @category  Module
 * Support by mail  : contact@lnkboot.fr
 */

if (!defined('_PS_VERSION_'))
    exit;
class AdminSettingController extends ModuleAdminController
{
    public function __construct()
    {

        $context = Context::getContext();
        $ordersmanagementLink = $context->link->getAdminLink('AdminModules').'&configure=lnk_livepromo&module_name=lnk_livepromo&control=config';
        Tools::redirectAdmin($ordersmanagementLink);
        exit();
    }

 


}
