<?php
/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_VERSION_'))
    exit;
 
include_once(_PS_MODULE_DIR_.'lnk_livepromo/vendor/autoload.php');
class AdminPromoCronJobsController extends ModuleAdminController
{
    public function __construct()
    { 
        
        if (Tools::getValue('token') != Configuration::getGlobalValue('PROMO_CRONJOBS_EXECUTION_TOKEN')) {
            die('Invalid token');
        } 

        $this->postProcess();
        
        parent::__construct();
        
    }

    public function initContent()
    {
      parent::initContent();
    }

    public function display()
    {
        parent::display();
    }
    
    public function postProcess()
    {
        // $this->module->sendCallback();

        ob_start();
 
        if( Configuration::get('LP_active') && Configuration::get('LP_active_cron') ) 
            $this->runTasksCrons();

        ob_end_clean();
    }

    protected function runTasksCrons()
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'cart_rule cr 
        LEFT JOIN '. _DB_PREFIX_.'cart_rule_lang crl ON cr.id_cart_rule = crl.id_cart_rule 
        LEFT JOIN '. _DB_PREFIX_.'lnk_promo_promos pr ON pr.id_cart_rule = cr.id_cart_rule 
        WHERE `notify` = 1';

        $promotions = Db::getInstance()->executeS($query);

        if (is_array($promotions) && (count($promotions) > 0)) {
            foreach ($promotions as &$promo) {
               

                if ($this->shouldBeExecuted($promo) == true) {

                    // Tools::file_get_contents(urldecode($promo['task']), false);

                    $lp_server_node = Configuration::get('LP_socket_url');
                    $v2 = new \ElephantIO\Engine\SocketIO\Version2X($lp_server_node,['context' => ['ssl' => ['verify_peer_name' =>false, 'verify_peer' => false]]]);
                    $client = new \ElephantIO\Client($v2);
                    $client->initialize();
            
                    $client->emit('actiongetpromonotification', ['promo' => $promo ]
                    );
                   
                    $query = 'UPDATE ' . _DB_PREFIX_ . 'lnk_promo_promos SET `date_upd` = NOW() WHERE `id_promo` = '.(int)$promo['id_promo'];
                    Db::getInstance()->execute($query);
                    // `active` = IF (`one_shot` = TRUE, FALSE, `active`) 
                }
            }
        }
    }

    protected function shouldBeExecuted($cron)
    {
        $minute      = ($cron['minute'] == -1)      ? date('i') : $cron['minute'];
        $hour        = ($cron['hour'] == -1)        ? date('H') : $cron['hour'];
        $day         = ($cron['day'] == -1)         ? date('d') : $cron['day'];
        $month       = ($cron['month'] == -1)       ? date('m') : $cron['month'];
        $weekday     = ($cron['weekday'] == -1)     ? date('D') : date('D', strtotime('Sunday +' . $cron['weekday'] . ' days'));
        
        $day = date('Y').'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($day, 2, '0', STR_PAD_LEFT);
        $execution = $weekday.' '.$day.' '.str_pad($hour, 2, '0', STR_PAD_LEFT).' '.str_pad($minute, 2, '0', STR_PAD_LEFT);
        $now = date('D Y-m-d H i');

        return !(bool)strcmp($now, $execution);
    }
}