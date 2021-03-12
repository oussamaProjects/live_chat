<?php
/**
 * Live Promo
 *
 * @author    L'nkboot 
 * @copyright L'nkboot 2020 
 * @license   http://www.lnkboot.fr/livepromo/license
 */ 
include_once(_PS_MODULE_DIR_.'lnk_livepromo/classes/ScheduledProductModel.php');
include_once(_PS_MODULE_DIR_.'lnk_livepromo/utils/utils.php');

class AdminScheduledProductController extends ModuleAdminController
{
    /** @var bool $bootstrap Active bootstrap for Prestashop 1.6 */
    public $bootstrap = true;

    /** @var Module Instance of your module automatically set by ModuleAdminController */
    public $module;

    /** @var string Associated object class name */
    public $className = 'ScheduledProductModel';

    /** @var string Associated table name */
    public $table = 'lnk_promo_scheduled_products';

    /** @var string|false Object identifier inside the associated table */
    public $identifier = 'id_scheduled_products';

    /** @var string Default ORDER BY clause when $_orderBy is not defined */
    protected $_defaultOrderBy = 'id_scheduled_products';

    /** @var string Default ORDER WAY clause when $_orderWay is not defined */
    protected $_defaultOrderWay = 'DESC';

    /** @var bool List content lines are clickable if true */
    protected $list_no_link = true;

    public $multishop_context = 0;

    /**
     * @see AdminController::__construct()
     */
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->table = 'lnk_promo_scheduled_products';
        $this->className = 'ScheduledProductModel';

        $this->addRowAction('delete'); 
        $this->addRowAction('edit'); 

        $this->bulk_actions = array('delete' => array('text' => $this->module->l('Delete selected', 'AdminScheduledProductController'),'confirm' => $this->module->l('Would you like to delete the selected items?','AdminScheduledProductController'),));

        $this->fields_list = array(
            'id_product'                => array('title' => $this->module->l('ID',              'AdminScheduledProductController'), 'type' => 'int',        'align' => 'center', 'search'  => true,'class' => 'fixed-width-xs'),
            'name'                      => array('title' => $this->module->l('Name',            'AdminScheduledProductController'), 'type' => 'string',     'align' => 'center', 'orderby' => false),
            'minute'                    => array('title' => $this->module->l('Minute',          'AdminScheduledProductController'), 'type' => 'int',        'align' => 'center', 'orderby' => false),
            'hour'                      => array('title' => $this->module->l('Hour',            'AdminScheduledProductController'), 'type' => 'int',        'align' => 'center', 'orderby' => false),
            'day'                       => array('title' => $this->module->l('Day',             'AdminScheduledProductController'), 'type' => 'int',        'align' => 'center', 'orderby' => false),
            'weekday'                   => array('title' => $this->module->l('Weekday',         'AdminScheduledProductController'), 'type' => 'int',        'align' => 'center', 'orderby' => false),
            'month'                     => array('title' => $this->module->l('Month',           'AdminScheduledProductController'), 'type' => 'int',        'align' => 'center', 'orderby' => false),
            // 'common_option'             => array('title' => $this->module->l('Common option',   'AdminScheduledProductController'), 'type' => 'string', 'align' => 'center', 'orderby' => false),
            'notify'                    => array('title' => $this->module->l('Status',          'AdminScheduledProductController'), 'active' => 'status', 'type' => 'bool', 'align' => 'center', 'orderby' => false),
        );
        
        $this->_join   .= ' LEFT JOIN '._DB_PREFIX_.'product_lang pl ON (a.id_product = pl.id_product)';
        $this->_select .= ' pl.name as name';
        
    }

    public function setMedia() {
        parent::setMedia();  
 
        $this->context->controller->addJqueryPlugin (array ('autocomplete', 'typewatch', 'jscroll')) ;
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . $this->module->name . '/views/css/back.css'); 
        $this->context->controller->addJS( _PS_MODULE_DIR_ . $this->module->name . '/views/js/scheduled_product.js'); 
        $this->context->controller->addJS( _PS_MODULE_DIR_ . $this->module->name . '/views/js/cron.js'); 
    }
      
    /**
     * Affichage du formulaire d'ajout / création de l'objet
     * @return string
     * @throws SmartyException
     */
    public function renderForm()
    {
        
        $current_object = $this->loadObject(true);

        $promo_scheduall  = array(
            "minute"        => "-1",
            "hour"          => "-1",
            "day"           => "-1",
            "weekday"       => "-1",
            "month"         => "-1",
            "common_option" => "* * * * * *",
            "note"          => "",
            "notify"        => "-1"
        );
        
        if (Validate::isUnsignedId($current_object->id) ) {
            $promo_scheduall = DB::getInstance()->getRow("Select * From "._DB_PREFIX_."lnk_promo_scheduled_products Where id_scheduled_products = ".$current_object->id);
        }
 

        $scheduled_product = "";
        if ($id_scheduled_products = Tools::getValue('id_scheduled_products')){  
            $scheduled_product = $this->scheduled_product($id_scheduled_products);
            if(!empty($scheduled_product) || !empty($scheduled_product[0]))
            $scheduled_product = $scheduled_product[0];
        }

        $assign = array(
            'currentIndex'              => self::$currentIndex,
            'currentToken'              => $this->token,
            'promo_scheduall'           => $promo_scheduall, 
            'id_scheduled_products'     => $id_scheduled_products, 
            'scheduled_product'         => $scheduled_product, 
            'common_options'            => utils::common_options(),
            'minute_options'            => utils::minute_options(),
            'hour_options'              => utils::hour_options(),
            'day_options'               => utils::day_options(),
            'month_options'             => utils::month_options(),
            'weekday_options'           => utils::weekday_options(), 
        );

        $this->context->smarty->assign($assign);
        
        $return = $this->context->smarty->fetch($this->getTemplatePath() . '/scheduled/scheduled_product.tpl'); 
        return $return;
        
    }

    // public function postProcess()
    // {
       
    //     if (Tools::isSubmit('submitAddlnk_promo_scheduled_products') || Tools::isSubmit('submitAddlnk_promo_scheduled_productsAndStay')) {

    //         $id_product                 = (int)Tools::getValue('id_product'); 
    //         $id_scheduled_products      = (int)Tools::getValue('id_scheduled_products'); 
    //         $common_option              = Tools::getValue('common_options') != ""   ? Tools::getValue('common_options')     : 0;
    //         $minute                     = Tools::getValue('minute') != ""           ? Tools::getValue('minute')             : 0;
    //         $hour                       = Tools::getValue('hour') != ""             ? Tools::getValue('hour')               : 0;
    //         $day                        = Tools::getValue('day') != ""              ? Tools::getValue('day')                : 0;
    //         $weekday                    = Tools::getValue('weekday') != ""          ? Tools::getValue('weekday')            : 0;
    //         $month                      = Tools::getValue('month') != ""            ? Tools::getValue('month')              : 0; 
    //         $note                       = Tools::getValue('note') != ""             ? Tools::getValue('note')               : ""; 
    //         $notify                     = (int)Tools::getValue('notify') ; 

    //         if (Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."lnk_promo_scheduled_products` WHERE `id_scheduled_products`='". $id_scheduled_products  ."'")) {
                
    //             $scheduled_products = "UPDATE `"._DB_PREFIX_."lnk_promo_scheduled_products` 
    //             SET 
    //                 `id_product` = '". $id_product ."', 
    //                 `common_option` = '". $common_option ."', 
    //                 `minute` = '". $minute ."', 
    //                 `hour` = '". $hour ."', 
    //                 `day` = '". $day ."', 
    //                 `weekday` = '". $weekday ."', 
    //                 `month` = '". $month ."',
    //                 `note` = '". $note ."',
    //                 `notify` = ". $notify ." ,
    //                 `date_upd` = NOW() 
    //             WHERE `id_scheduled_products`=". $id_scheduled_products ." LIMIT 1;";

    //             Db::getInstance()->execute($scheduled_products);
    //         } 
            
            
    //     }
 

    //     return parent::postProcess();
    // }

    // public function processAdd()
    // {
       
    //     if ($scheduled_products = parent::processAdd()) { 
             
    //         $id_product             = Tools::getValue('id_product') != ""               ? Tools::getValue('id_product') : 0;
    //         $common_option          = Tools::getValue('common_options') != ""           ? Tools::getValue('common_options')          : 0;
    //         $minute                 = Tools::getValue('minute') != ""                   ? Tools::getValue('minute')                  : 0;
    //         $hour                   = Tools::getValue('hour') != ""                     ? Tools::getValue('hour')                    : 0;
    //         $day                    = Tools::getValue('day') != ""                      ? Tools::getValue('day')                     : 0;
    //         $weekday                = Tools::getValue('weekday') != ""                  ? Tools::getValue('weekday')                 : 0;
    //         $month                  = Tools::getValue('month') != ""                    ? Tools::getValue('month')                   : 0; 
    //         $notify                 = (int)Tools::getValue('notify') ; 
    //         $id_scheduled_products  = $scheduled_products->id;

    //         $sql_promo = "INSERT INTO `"._DB_PREFIX_."lnk_promo_scheduled_products` (`id_scheduled_products`, `id_product`, `common_option`, `minute`, `hour`, `day`, `weekday`, `month`, `notify`, `date_add`) VALUES (". $id_scheduled_products .",". $id_product .", '". $common_option ."', '". $minute ."', '". $hour ."', '". $day ."', '". $weekday ."', '". $month ."', ". $notify .", NOW() )";
    //         // Db::getInstance()->execute($sql_promo);
            
    //     }
      

    //     return parent::processAdd();

    // }

    // public function processUpdate()
    // {
       
    //     if ($scheduled_products = parent::processUpdate()) { 
             
    //         $id_product             = Tools::getValue('id_product') != ""               ? Tools::getValue('id_product') : 0;
    //         $common_option          = Tools::getValue('common_options') != ""           ? Tools::getValue('common_options')          : 0;
    //         $minute                 = Tools::getValue('minute') != ""                   ? Tools::getValue('minute')                  : 0;
    //         $hour                   = Tools::getValue('hour') != ""                     ? Tools::getValue('hour')                    : 0;
    //         $day                    = Tools::getValue('day') != ""                      ? Tools::getValue('day')                     : 0;
    //         $weekday                = Tools::getValue('weekday') != ""                  ? Tools::getValue('weekday')                 : 0;
    //         $month                  = Tools::getValue('month') != ""                    ? Tools::getValue('month')                   : 0; 
    //         $note                   = Tools::getValue('note') != ""                    ? Tools::getValue('note')                   : 0; 
    //         $notify                 = (int)Tools::getValue('notify') ; 
    //         $id_scheduled_products  = $scheduled_products->id;

    //         $scheduled_products = "UPDATE `"._DB_PREFIX_."lnk_promo_scheduled_products` 
    //         SET 
    //             `id_product` = '". $id_product ."', 
    //             `common_option` = '". $common_option ."', 
    //             `minute` = '". $minute ."', 
    //             `hour` = '". $hour ."', 
    //             `day` = '". $day ."', 
    //             `weekday` = '". $weekday ."', 
    //             `month` = '". $month ."',
    //             `note` = '". $note ."',
    //             `notify` = ". $notify .",
    //             `date_upd` = NOW() 
    //         WHERE `id_scheduled_products`=". $id_scheduled_products ." LIMIT 1;";

    //         // Db::getInstance()->execute($scheduled_products);
            
    //     }
      

    //      return parent::processUpdate();

    // }
     
    /**
     * @see AdminScheduledProductController::scheduled_product()
     */
    public static function scheduled_product($id_scheduled_product,  Context $context = null){
        if (!$context)
            $context = Context::getContext();

        $sql = 'SELECT *
            FROM `'._DB_PREFIX_.'lnk_promo_scheduled_products` a
            LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product`= a.`id_product`) ' . Shop::addSqlAssociation('product', 'p'). '
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
                p.`id_product` = pl.`id_product`
                AND pl.`id_lang` = '.(int)$context->language->id. Shop::addSqlRestrictionOnLang('pl').'
            )
            WHERE `id_scheduled_products` = '.(int)$id_scheduled_product;

            return Db::getInstance()->executeS($sql);
    }
    
    /**
     * @see AdminScheduledProductController::initPageHeaderToolbar()
     */
    public function initPageHeaderToolbar(){

        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_scheduled_product'] = array(
                'href' => self::$currentIndex.'&addlp_scheduled_product&token='.$this->token,
                'desc' => $this->l('Add new scheduled product', null, null, false),
                'icon' => 'process-icon-new'
            );
        }
        
        parent::initPageHeaderToolbar();
        // Remove the help icon of the toolbar which no useful for us
        $this->context->smarty->clearAssign('help_link');
    }
 
}