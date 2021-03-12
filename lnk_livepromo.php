<?php
/**
 * Live Promo
 *
 * @author    L'nkboot 
 * @copyright L'nkboot 2020 
 * @license   http://www.lnkboot.fr/livepromo/license
 */

if (!defined('_PS_VERSION_')) {
    exit;
}
include_once(_PS_MODULE_DIR_.'lnk_livepromo/config/config.php');
include_once(_PS_MODULE_DIR_.'lnk_livepromo/classes/LoggerHandler.php');
include_once(_PS_MODULE_DIR_.'lnk_livepromo/classes/LikedModel.php');
include_once(_PS_MODULE_DIR_.'lnk_livepromo/vendor/autoload.php');

class Lnk_livepromo extends Module
{

    public $actions = array(
        'actioncartsavenew' => array(
            1 => 'A été ajouté au panier',
            2 => 'Added to cart',
        ),
        'actionvalidateorder' => array(
            1 => 'a passé une commande',
            2 => 'places an order',
        ), 
        'actionafterdeleteproductincart' => array(
            1 => 'a été modifier dans le panier',
            2 => 'has been modified in the basket',
        ), 
        'actionauthentication' => array(
            1 => 'vient de se connecter',
            2 => 'just connected',
        ), 
        'actioncustomeraccountadd' => array(
            1 => 'a créé son compte',
            2 => 'created his account',
        ), 
        'actionlikedproduct' => array(
            1 => 'aime',
            2 => 'like',
        ), 
    );
    
    public $mytabs = array(
        array(
            'name' => 'Réglage',
            'visible' => true,
            'class_name' => 'AdminSetting',
            'parent_class_name' => 'AdminLivePromo',
        ),
        array(
            'name' => 'Promotions',
            'visible' => true,
            'class_name' => 'AdminPromo',
            'parent_class_name' => 'AdminLivePromo',
        ),
        array(
            'name' => 'Produit du jour',
            'visible' => true,
            'class_name' => 'AdminScheduledProduct',
            'parent_class_name' => 'AdminLivePromo',
        ),
        array(
            'name' => 'Logs',
            'visible' => true,
            'class_name' => 'AdminLog',
            'parent_class_name' => 'AdminLivePromo',
        ),
        array(
            'name' => 'Promo cron jobs',
            'visible' => false,
            'class_name' => 'AdminPromoCronJobs',
            'parent_class_name' => 'AdminLivePromo',
        )
    );

    private $_html;
    public $configs;
    public $defines;
    public $logger;
    public static $configTabs;
    public $webservice_url = 'http://webcron.prestashop.com/crons';
    public $cachefile = _PS_MODULE_DIR_ . "lnk_livepromo/cache/results.json"; 

    public function __construct(){
        $this->name = 'lnk_livepromo';
        $this->tab = 'others';
        $this->version = '1.0.0';
        $this->author = 'L\'nkboot';
        $this->need_instance = 1;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('L\'nk Live Promo');
        $this->description = $this->l('Live Promo in you front');

        $this->confirmUninstall = $this->l('Are you sure you want uninstall L\'nk Live Promo');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_); 

        $this->defines = new LP_defines();
        $this->configs = $this->defines->configs;

        self::$configTabs = array(
            'settings' => $this->l('Settings'),
            'front'     => $this->l('Front configuration'),
            'socket' => $this->l('Events'),
            'display' => $this->l('Display configuration'),
            'style'     => $this->l('Style configuration'),
            'helps' => $this->l('Help'),
        );
        $this->updateWebservice(true);
        $this->logger = new \FileLogger(0);
        $this->logger->setFilename(_PS_MODULE_DIR_.'lnk_livepromo/actions.log'); 
    }

    public function install(){
        Configuration::updateValue('LNK_LIVEPROMO_LIVE_MODE', false);
        Configuration::updateValue('LP_range_order_number', 'day');
        Configuration::updateValue('LP_range_cart_number', 'day');
        Configuration::updateValue('LP_nbr_customer_connected', 0);

        
        $token = Tools::encrypt(Tools::getShopDomainSsl().time());
        Configuration::updateGlobalValue('PROMO_CRONJOBS_EXECUTION_TOKEN', $token);
        Configuration::updateValue('PROMO_CRONJOBS_WEBSERVICE_ID', 0);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('backOfficeFooter') &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayFooterLivePromo') &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('actionCartSave') &&
            $this->registerHook('actionAfterDeleteProductInCart') &&
            $this->registerHook('actionAuthentication') &&
            $this->registerHook('actionCustomerAccountAdd') &&
            $this->registerHook('customerAccount') &&
            $this->registerHook('productActions') &&
            $this->registerHook('displayProductListReviews') &&
            $this->registerHook('customerAccount') &&
            $this->registerHook('displayMyAccountBlock') &&
            $this->registerHook('displayLikeBtn') &&
            $this->installTab();
    }

    protected function isLocalEnvironment(){
        if (isset($_SERVER['REMOTE_ADDR']) === false) {
            return true;
        }

        return in_array(Tools::getRemoteAddr(), array('127.0.0.1', '::1')) || preg_match('/^172\.16\.|^192\.168\.|^10\.|^127\.|^localhost|\.local$/', Configuration::get('PS_SHOP_DOMAIN'));
    }

    protected function updateWebservice($use_webservice){
        if ($this->isLocalEnvironment() == true) {
            return true;
        }

        $link = new Link();
        $admin_folder = $this->getAdminDir();
        if (version_compare(_PS_VERSION_, '1.7', '<') == true) {
            $path = Tools::getShopDomainSsl(true, true).__PS_BASE_URI__.$admin_folder;
            $cron_url = $path.'/'.$link->getAdminLink('AdminPromoCronJobs', false);
        } else {
            $cron_url = $link->getAdminLink('AdminPromoCronJobs', false);
        }

        $webservice_id = Configuration::get('PROMO_CRONJOBS_WEBSERVICE_ID') ? '/'.Configuration::get('PROMO_CRONJOBS_WEBSERVICE_ID') : null;

        $data = array(
            'callback' => $link->getModuleLink($this->name, 'callback'),
            'domain' => Tools::getShopDomainSsl(true, true).__PS_BASE_URI__,
            'data' => $cron_url.'&token='.Configuration::getGlobalValue('PROMO_CRONJOBS_EXECUTION_TOKEN'),
            'cron_token' => Configuration::getGlobalValue('PROMO_CRONJOBS_EXECUTION_TOKEN'),
            'active' => (bool)$use_webservice
        ); 

        $context_options = array(
            'http' => array(
                'method' => (is_null($webservice_id) == true) ? 'POST' : 'PUT',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($data)
            )
        );
    
        // echo $data['data'];
        // var_dump($this->webservice_url.$webservice_id);
        // var_dump(stream_context_create($context_options));
        $result = Tools::file_get_contents($this->webservice_url.$webservice_id, false, stream_context_create($context_options));
        
        // var_dump($result);
        // die;
        
        if ($result != false) {
            Configuration::updateValue('PROMO_CRONJOBS_WEBSERVICE_ID', (int)$result);
        }

        if (((Tools::isSubmit('install') == true) || (Tools::isSubmit('reset') == true)) && ((bool)$result == false)) {
            return true;
        } elseif (((Tools::isSubmit('install') == false) || (Tools::isSubmit('reset') == false)) && ((bool)$result == false)) {
            // return $this->setErrorMessage('An error occurred while trying to contact PrestaShop\'s cron tasks webservice.');
            return false;
        }

        // if ((bool)$use_webservice == true) {
        //     return $this->setSuccessMessage('Your cron tasks have been successfully added to PrestaShop\'s cron tasks webservice.');
        // }
        // return $this->setSuccessMessage('Your cron tasks have been successfully registered using the Advanced mode.');
    }

    protected function getAdminDir(){
        if (defined('_PS_ADMIN_DIR_')){
            return basename(_PS_ADMIN_DIR_);
        }
        return false;
            
    }

    public function sendCallback(){
        ignore_user_abort(true);
        set_time_limit(0);

        ob_start();
        echo $this->name.'_prestashop';
        header('Connection: close');
        header('Content-Length: '.ob_get_length());
        ob_end_flush();
        ob_flush();
        flush();

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function installTab(){
        $languages = Language::getLanguages(false);
        $tab = new Tab();
        $tab->class_name = 'AdminLivePromo';
        $tab->module = 'lnk_livepromo';
        $tab->id_parent = 0;   
        $tab->icon='icon-AdminPriceRule';         
        foreach($languages as $lang){
            $tab->name[$lang['id_lang']] = $this->l('Live Promo');
        }
        $tab->save();
        $TabId = Tab::getIdFromClassName('AdminLivePromo');
        if($TabId){
            foreach($this->mytabs as $tabArg)
            {
                if(!Tab::getIdFromClassName($tabArg['class_name']))
                {
                    $tab = new Tab();
                    $tab->class_name = $tabArg['class_name'];
                    $tab->module = 'lnk_livepromo';
                    $tab->id_parent = $TabId; 
                    foreach($languages as $lang){
                        $tab->name[$lang['id_lang']] = $tabArg['name'];
                    }
                    $tab->save();
                }
            }                
        }   
        return true;
        
    }

    public function uninstall(){
        Configuration::deleteByName('LNK_LIVEPROMO_LIVE_MODE');

        include(dirname(__FILE__).'/sql/uninstall.php');
        $this->uninstallTab();
        return parent::uninstall();
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function uninstallTab(){

        foreach($this->mytabs as $tab)
        {
            if($tabId = Tab::getIdFromClassName($tab['class_name']))
            {                
                $tab = new Tab($tabId);
                if($tab)
                    $tab->delete();
            }                
        }

        $idTab = Tab::getIdFromClassName('AdminLivePromo');
        if ($idTab !== false) {
            $catalogTab = new Tab($idTab);
            $catalogTab->delete();
        }

        return true;
    }

    /**
     * Load the configuration form
     */
    public function getContent(){
        $this->_postConfig();
        $form = $this->renderConfig();


        $this->context->smarty->assign(
            array(
                'module_dir' => $this->_path, 
                'form' => $form,
            )
        );
        $output = $this->context->smarty->fetch($this->local_path . 'views/templates/admin/index.tpl');
        
        return $output;
    }

    public function renderConfig($title= '',$icon= 'icon-AdminAdmin'){
        $id_shop = (int)$this->context->shop->id;
        $fields_form = array(
            'form' => array(
                //'tabs' => $this->configs['tabs'],
                'input' => $this->configs['fields'],
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper = new HelperForm();
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $this->fields_form = array();
        $helper->module = $this;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveConfig';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name.'&control='.Tools::getValue('control');
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $language = new Language((int)Configuration::get('PS_LANG_DEFAULT'));        
        $languages = Language::getLanguages(false);
        $helper->override_folder = '/';  
        $fields = $this->getValueFields();   
                
        $helper->tpl_vars = array(
            'base_url' => $this->context->shop->getBaseURL(),
            'language' => array(
                'id_lang' => $language->id,
                'iso_code' => $language->iso_code
            ),
            'fields_value' => $fields,
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'isConfigForm' => true,
            'old_version' => version_compare(_PS_VERSION_, '1.6.0.7', '<'),
            'link_module_blog' => $this->_path,
        );
        return $helper->generateForm(array($fields_form));	
        //$this->_html .= $helper->generateForm(array($fields_form));	

    }

    public function getValueFields(){
        $fields = array(); 
        foreach ($this->configs['fields'] as $key => $value) {
            $fields[$value['name']] = Tools::getValue($value['name'], Configuration::get($value['name']));
        }
        return $fields;
    }

    private function _postConfig(){
        if(Tools::isSubmit('saveConfig'))
        {
            foreach ($this->configs['fields'] as $key => $value) {
                Configuration::updateValue($value['name'],Tools::getValue($value['name']));
            }  
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookBackOfficeHeader(){
        if (
            Module::isEnabled($this->name) && ( Tools::getValue('controller', false) == 'AdminModules' || Tools::getValue('controller', false) == 'AdminPromo')
            // && Tools::getValue('configure', false) == $this->name
        ) {

            $this->smarty->assign(array(
                'lp_form_tabs' => array(self::$configTabs),
            ));
            $this->context->controller->addJquery(); 
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
            $this->context->controller->addJs($this->_path.'/views/js/cron.js');
            return $this->display(__FILE__, 'head.tpl');
        }

        
        $this->context->controller->addCSS($this->_path . 'views/css/global.css');
    }

    public function hookBackOfficeFooter($param){
        if (Tools::getValue('module_name') == $this->name || Tools::getValue('configure') == $this->name) {
            $html = '';
            $html .= '<script type="text/javascript" src="' . $this->_path . 'views/js/back.js" ></script>';
            return $html;
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader($params){

        if(Configuration::get('LP_active')){
            if (Configuration::get('LP_IP') == "" || in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('LP_IP')))) {

                if((bool)$this->context->customer->isLogged()){
                    $usersocket = $this->context->customer->id;
                    $group = Customer::getDefaultGroupId((int)$this->context->customer->id);
                    $p = array("customer" => $this->context->customer);
                    $customer_name = $this->getNameCustomer($p);
                }else{
                    $usersocket = 'G'.$this->context->cookie->id_guest;
                    $group = 1;
                    $p = array("guest" => true);
                    $customer_name = $this->getNameCustomer($p);
                }

                $lp_server_node = Configuration::get('LP_socket_url');
                $this->context->controller->addJs($this->_path.'/node_modules/socket.io-client/dist/socket.io.js');
                Media::addJsDef(array('lp_urlmedia' => $this->context->link->getMediaLink(_MODULE_DIR_.'lnk_livepromo/views/img/')));
                Media::addJsDef(array('lp_url_svg ' => $this->context->link->getMediaLink(_MODULE_DIR_.'lnk_livepromo/views/img/svg/')));
                Media::addJsDef(array('usersocket' => $usersocket));
                Media::addJsDef(array('usersocketname' => $customer_name));
                Media::addJsDef(array('lp_server_node' => $lp_server_node));
                Media::addJsDef(array('lp_lang_id' => $this->context->language->id));
                Media::addJsDef(array('lp_group_id' => $group));

                Media::addJsDef(array(
                    'lp_token' => Tools::getToken(false),
                    'lp_ajaxurl' => $this->context->link->getModuleLink('lnk_livepromo', 'page', array(), true),
                    'LP_CONFIGURATION' => array(
                        'lp_messages_nbr'   => Configuration::get('LP_messages_nbr'),
                        'lp_promotions_nbr' => Configuration::get('LP_promotions_nbr'),
                    ),
                    'LP_STYLE' => array(
                        'lp_style_height_side_chat'         => Configuration::get('LP_style_height_side_chat'),
                        'lp_style_feature_product_display'  => Configuration::get('LP_style_feature_product_display'),
                        'lp_style_live_label_position'      => Configuration::get('LP_style_live_label_position'),
                        'lp_style_feature_product_position' => Configuration::get('LP_style_feature_product_position'),
                        'lp_style_main_color'               => Configuration::get('LP_style_main_color'),
                        'lp_style_dark_light_mode_button'   => Configuration::get('LP_style_dark_light_mode_button'),
                        'lp_style_like_button'              => Configuration::get('LP_style_like_button'),
                        'lp_style_love_button'              => Configuration::get('LP_style_love_button'),
                    )
                ));
                
                
                $this->context->controller->addJs($this->_path.'/views/js/libs/smooth-scrollbar.js');
                $this->context->controller->addJs($this->_path.'/views/js/libs/clipboard.min.js'); 
                $this->context->controller->addJs($this->_path.'/views/js/client.js');
                $this->context->controller->addJs($this->_path.'/views/js/front.js');
    
                $this->context->controller->addCSS($this->_path.'/views/css/style.css');
                // $this->context->controller->addCSS($this->_path.'/views/scss/front.css');
                return $this->display(__FILE__, '/front-head.tpl');
            }else{
                return;
            }
        }
    }
    
    public function hookDisplayFooter($params)	{

        if(Configuration::get('LP_active')){

            if (Configuration::get('LP_IP') == "" || in_array(Tools::getRemoteAddr(), explode(',', Configuration::get('LP_IP')))) {

                if((bool)$this->context->customer->isLogged()){ 
                    $group = Customer::getDefaultGroupId((int)$this->context->customer->id);
                }else{ 
                    $group = 1;
                }
                
                $messages = $this->getCachedMessages($group);
             
                $this->smarty->assign(array(
                    'customer'              => $params,
                    'lp_group_id'           => $group, 
                    'messages'              => $messages, 
                    'lp_urlmedia'           => $this->context->link->getMediaLink(_MODULE_DIR_.'lnk_livepromo/views/img/'),
                    'lp_url_svg'            => $this->context->link->getMediaLink(_MODULE_DIR_.'lnk_livepromo/views/img/svg/'),
                    'order_number'          => $this->getOrderNbr(),
                    'cart_number'           => $this->getCartNbr(),
                    'pl_template_dir'       => _PS_MODULE_DIR_ . 'lnk_livepromo',
                    'customer_connected'    => Configuration::get('LP_nbr_customer_connected'),
                ));
                return $this->display(__FILE__, 'footer.tpl');
            }else{
                return;
            }
        }else{
            return;
        }
    }

    /**
     * Tracking Events 
     */
    public function hookActionValidateOrder($params){
        if(Configuration::get('LP_active') && Configuration::get('LP_validate_order')){
            $this->emitAction($params, 'actionvalidateorder', 'order', 'none');
        }
        return true;
    }
    
    /**
     * Tracking Events
     * Add product in cart.
     * actionCartSave
     * hookActionCartSave.
     */
    public function hookActionCartSave($params)	{
        if(Configuration::get('LP_active') && !empty($params["cart"]) && Tools::getValue('id_product')){
            $qty_to_check = (Tools::getValue('op', 'up') == 'down') ?  'down' : 'up';  
                     
            if ($qty_to_check == 'down' && Configuration::get('LP_cart_down')) {
                $this->emitAction($params, 'actioncartsavenew', 'product', $qty_to_check); 
            }elseif($qty_to_check == 'up' && Configuration::get('LP_cart_up')) {
                $this->emitAction($params, 'actioncartsavenew', 'product', $qty_to_check); 
            }

            return true;
        }else{
            return true;
        }
    }
   
    /**
     * Tracking Events
     * Add product in cart.
     * actionAfterDeleteProductInCart
     * hookActionAfterDeleteProductInCart.
     */
    public function hookActionAfterDeleteProductInCart($params)	{
        if(Configuration::get('LP_active') && Configuration::get('LP_cart_remove')){
            $this->emitAction($params, 'actionafterdeleteproductincart'); 
            return true;
        }else{
            return true;
        }
    }
    
    /**
     * Tracking Events
     * login customers.
     * actionAuthentication
     * hookActionAuthentication.
     */
    public function hookActionAuthentication($params){
        if(Configuration::get('LP_active') && Configuration::get('LP_customer_login')){
            $this->emitAction($params, 'actionauthentication', 'customer'); 
            return true;
        }else{
            return true;
        }
    }
    
    /**
     * Tracking Events
     * login customers.
     * actionCustomerAccountAdd
     * hookActionCustomerAccountAdd.
     */
    public function hookActionCustomerAccountAdd($params){
        if(Configuration::get('LP_active') && Configuration::get('LP_customer_registered')){
            $this->emitAction($params, 'actioncustomeraccountadd', 'customer');
            return true;
        }else{
            return true;
        }
    }

    /**
     * Liked Hook
     */
    public function hookProductActions($params){
        if(!Configuration::get('LP_active_liked')){
            return;
        }
        $data_template = $this->likedButton($params);
        $data_template["detailproduct"] = true;
        $this->context->smarty->assign($data_template);
        return $this->display(__FILE__, 'liked-btn.tpl');
    }

    public function hookDisplayProductListReviews($params){
        if(!Configuration::get('LP_active_liked')){
            return;
        }
        $data_template = $this->likedButton($params);
        $data_template["detailproduct"] = false;
        $this->context->smarty->assign($data_template);
        return $this->display(__FILE__, 'liked-btn.tpl');
    }

    public function hookDisplayLikeBtn($params){
        if(!Configuration::get('LP_active_liked')){
            return ;
        }
        $data_template = $this->likedButton($params);
        $data_template["detailproduct"] = false;
        $this->context->smarty->assign($data_template);
        return $this->display(__FILE__, 'liked-btn.tpl');
    }

    public function hookCustomerAccount($params){
        if(!Configuration::get('LP_active_liked')){
            return;
        }
        return $this->display(__FILE__, 'my-account.tpl');
    }
    
    public function hookDisplayMyAccountBlock($params){
        if(!Configuration::get('LP_active_liked')){
            return;
        }
        return $this->display(__FILE__, 'my-account-block.tpl');
    }

    public function emitAction($params, $action, $type = 'product', $qty_to_check = 'none')	{ 

        $lp_server_node = Configuration::get('LP_socket_url');
        $v2 = new \ElephantIO\Engine\SocketIO\Version2X($lp_server_node,['context' => ['ssl' => ['verify_peer_name' =>false, 'verify_peer' => false]]]);

        try
        {
            $client = new \ElephantIO\Client($v2);
            $client->initialize();
            $message = $this->getMessageDetails($action, $params, $type);
            array_push($message, ['qty_to_check' => $qty_to_check]);
            if(!empty($message)){

                // ob_start();
                // var_dump(Tools::getValue('id_product'));
                // var_dump($params);
                // var_dump($message); 
                // $result = ob_get_clean();
                // $this->logger->logDebug("productdetail ".$result); 

                $this->makeCacheFile($message, $type);
                $client->emit($action, $message);   
                $client->close();
            } 
            return true;
        }
        catch (\ElephantIO\Exception\ServerConnectionFailureException $e)
        {
            if($_SERVER['REMOTE_ADDR'] == '196.70.254.137'){
                var_dump('Server Connection Failure!!'); 
                // var_dump($e); 
            }else{
                return true;
            }
        }
        
        
    }
    
    public function getMessageDetails($action, $params, $type){

        $cart   = !empty($params['cart']) ? $params['cart'] : '';
        $cookie = !empty($params['cookie']) ? $params['cookie'] : '';
        $mode   = (Tools::getIsset('update')) ? 'update' : 'add';

        $languages = Language::getLanguages(true);

        if($type == 'order' || $type == 'customer'){
            $customer_name = $this->getNameCustomer(array("customer" => $params['customer']));
            $id = $params['customer']->id . '-' . $type;

            foreach ($languages as $language) {
                 
                if(isset($this->actions[$action]))
                    $prod_lang[$language['id_lang']]['action'] = $this->actions[$action][$language['id_lang']]; 

                if(in_array($action, array('actionvalidateorder')))
                    $prod_lang[$language['id_lang']]['extra_action'] = 'celebrate'; 

                if(in_array($action, array('actionlikedproduct')))
                    $prod_lang[$language['id_lang']]['extra_action'] = 'heart_icon'; 

            } 
            
            return ['iddiv' => time(false).$id, 'id' => $id, 'mode' => $mode, 'productdetail' => array('lang' => $prod_lang), 'customer_name' => $customer_name ];
        }

        

        ob_start();
        var_dump($cookie);
        $result = ob_get_clean();
        $this->logger->logDebug("1 : id_guest  ".$result);
        if ($this->context->customer->isLogged()) {
            $id = $this->context->customer->id . '-' . $type;
            $p = array("customer" => $this->context->customer);
        } elseif (!empty($cookie->id_guest)) {
            $id = 'G' . $cookie->id_guest . '-' . $type;
            $p = array("guest" => true); 
        } else {
            // $id = 'G' . $this->context->cookie->id_guest . '-' . $type;
            $id = 'GC' . $this->context->cookie->id_cart . '-' . $type;
            $id = 'GC' . $this->context->cookie->checksum . '-' . $type;
            $p = array("guest" => true); 
            ob_start();
                var_dump($this->context->cookie);
                $result = ob_get_clean();
                $this->logger->logDebug("2 : id_guest  ".$result);
        }

        $customer_name = $this->getNameCustomer($p);
        $productdetail = array();

        if(Tools::getValue('id_product')){

            $idProduct = (int)Tools::getValue('id_product');
            $base = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://');
            $link = new Link(); 
            $specificprice = array();
            
            $groups = Group::getGroups($this->context->language->id);
            

            foreach ($languages as $language) {
                $produit = new Product((int)$idProduct,false,$language['id_lang']);
                 
                $url = $link->getProductLink($produit);
                $image_type = ImageType::getFormatedName('home');
                $cover = $produit->getCover((int)$idProduct);
                $image = $base.$link->getImageLink($produit->link_rewrite, $cover['id_image'], $image_type);
                $price = $produit->getPrice(true, null, 6, null, false, false);
                $prod_lang[$language['id_lang']] = array(
                    'id'    => $produit->id,
                    'name'  => $produit->name,
                    'url'   => $url,
                    'image' => $image, 
                    'price' => Tools::displayPrice($price),
                );

                if(isset($this->actions[$action]))
                    $prod_lang[$language['id_lang']]['action'] = $this->actions[$action][$language['id_lang']]; 

                if(in_array($action, array('actionvalidateorder')))
                    $prod_lang[$language['id_lang']]['extra_action'] = 'celebrate'; 
                if(in_array($action, array('actionlikedproduct')))
                    $prod_lang[$language['id_lang']]['extra_action'] = 'heart_icon'; 

            }
            

            foreach ($groups as $group) {
                $pricep = 0;
                $specific_price = SpecificPrice::getSpecificPrice( (int)$idProduct, 1, 0,0, $group['id_group'],1 );
                if($specific_price){
                    if($specific_price["reduction_type"] == "amount"){
                        $pricep = $price - $specific_price["reduction"];
                    }else{
                        $pricep = $price - ($price * (float)$specific_price["reduction"]);
                    }
                }
                $specificprice[$group['id_group']] = Tools::displayPrice($pricep);
                
                // ob_start();
                // var_dump($group);
                // $result = ob_get_clean();
                // $this->logger->logDebug("group ".$result);
            }
         
            $productdetail= array( 
                'lang' => $prod_lang,
                'specific_price' => $specificprice,
            ); 
            // ob_start();
            // var_dump($productdetail);
            // $result = ob_get_clean();
            // $this->logger->logDebug("productdetail ".$result);
        }

        return ['iddiv' => time(false).$id, 'id' => $id, 'mode' => $mode, 'productdetail' => $productdetail, 'customer_name' => $customer_name ];
        
    }

    public function getNameCustomer($params){

        if(isset($params['customer'])){
            if(Configuration::get('LP_customer_name')){
                $name = ucfirst($params['customer']->lastname).' '.ucfirst($params['customer']->firstname);
            }else{
                $lastname = substr($params['customer']->lastname, 0, 1);
                $name = ucfirst($lastname).'**** '.ucfirst($params['customer']->firstname);
            }
        }elseif(isset($params['newCustomer'])){
            if(Configuration::get('LP_customer_name')){
                $name = ucfirst($params['newCustomer']->lastname).' '.ucfirst($params['newCustomer']->firstname);
            }else{
                $lastname = substr($params['newCustomer']->lastname, 0, 1);
                $name = ucfirst($lastname).'**** '.ucfirst($params['newCustomer']->firstname);
            }
        }else{
            $name = $this->l('Guest Customer');
        }
        
        return $name;
    }

    public function likedButton($params){
        if(Validate::isLoadedObject($params['product'])){
            $idproduct = (int)($params['product']->id);
        }else{
            $idproduct = (int)($params['product']['id_product']);
        }
        if ($this->context->customer->isLogged()){
            $this->smarty->assign(array(
                'issetProduct' => Liked::issetProduct((int)$this->context->customer->id, (int)$idproduct)
            ));
        }

        $data_template = array(
            'logged' => $this->context->customer->isLogged(),
            'liked_product' => $params['product'],
            'lp_id_product' => $idproduct,
            'login_link' => $this->context->link->getPageLink('my-account', true),
        );
        return $data_template;
    }

    /**
     * Get infos ----------------------------------
     */
    public function getOrderNbr(){
        if(Configuration::get('LP_active') && Configuration::get('LP_order_number')){
            $type = Configuration::get('LP_range_order_number');
            switch ($type) {
                case 'day':
                    $nbr = $this->getOrder(date("Y-m-d"));
                    break;
                case 'week':
                    $nbr = $this->getOrder(date("Y-m-d"),7);
                    break;
                case 'month':
                    $nbr = $this->getOrder(date("Y-m-d"),30);
                    break;
                case 'custom':
                    $nbr = $this->getOrder(date("Y-m-d") , Configuration::get('LP_order_range_date', false));
                    break;
                default:
                    $nbr = 0;
                    break;
            }
            return $nbr;

        }else{
            return 0;
        }
    }

    public function getOrder($date1,  $date2=''){
        if(!empty($date2)){
            $date = date('Y-m-d', strtotime($date1. ' - '.$date2.' days'));
            return Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."orders` WHERE (DATE(`date_add`) BETWEEN '".$date."' AND '".$date1."')");
        }else{
            return Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."orders` WHERE `date_add` like '%".$date1."%'");
        }
    }
    
    public function getLikedNbr(){
        return Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."lp_liked`");
    }

    public function getCartNbr(){
        if(Configuration::get('LP_active') && Configuration::get('LP_cart_number')){
            $type = Configuration::get('LP_range_cart_number');
            switch ($type) {
                case 'day':
                    $nbr = $this->getCart(date("Y-m-d"));
                    break;
                case 'week':
                    $nbr = $this->getCart(date("Y-m-d"),7);
                    break;
                case 'month':
                    $nbr = $this->getCart(date("Y-m-d"),30);
                    break;
                case 'custom':
                    $nbr = $this->getCart(date("Y-m-d") , Configuration::get('LP_order_range_date', false));
                    break;
                default:
                    $nbr = 0;
                    break;
            }
            return $nbr;

        }else{
            return 0;
        }
    }

    public function getCart($date1, $date2=''){
        if(!empty($date2)){
            $date = date('Y-m-d', strtotime($date1. ' - '.$date2.' days'));
            return Db::getInstance()->getValue("SELECT COUNT(*)  FROM `"._DB_PREFIX_."cart` WHERE (DATE(`date_upd`) BETWEEN '".$date."' AND '".$date1."')");
        }else{
            return Db::getInstance()->getValue("SELECT COUNT(*) FROM `"._DB_PREFIX_."cart` WHERE `date_upd` like '%".$date1."%'");
        }
    }

    /**
     * 
     * Cron Section
     */
    public function cronTask()
    {
        if( Configuration::get('LP_active') && Configuration::get('LP_active_cron') ) 
            return $this->runTasksCrons();
    }

    protected function runTasksCrons(){

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
                    $client->emit('actiongetpromonotification', ['promo' => $promo ]);
                    $client->close();

                    // $query = 'UPDATE ' . _DB_PREFIX_ . 'lnk_promo_promos SET `date_upd` = NOW() WHERE `id_promo` = '.(int)$promo['id_promo'];
                    Db::getInstance()->execute($query);
                    // `active` = IF (`one_shot` = TRUE, FALSE, `active`) 
                }
            }
        }



        $base = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://');
        $link = new Link();  
        $image_type = ImageType::getFormatedName('home');

        $query = 'SELECT *, i.* FROM ' . _DB_PREFIX_ . 'product p 
        LEFT JOIN '. _DB_PREFIX_.'product_lang pl ON p.id_product = pl.id_product 
        LEFT JOIN '. _DB_PREFIX_.'lnk_promo_scheduled_products psp ON psp.id_product = p.id_product 
        LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop ON (image_shop.`id_product` = p.`id_product` AND image_shop.`cover` = 1 )
        LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_image` = image_shop.`id_image`)
        WHERE `notify` = 1';

        $scheduled_products = Db::getInstance()->executeS($query);

        if (is_array($scheduled_products) && (count($scheduled_products) > 0)) {
            foreach ($scheduled_products as &$scheduled_product) {
                if ($this->shouldBeExecuted($scheduled_product) == true) {
                    // Tools::file_get_contents(urldecode($promo['task']), false);
                    $lp_server_node = Configuration::get('LP_socket_url');
                    $v2 = new \ElephantIO\Engine\SocketIO\Version2X($lp_server_node,['context' => ['ssl' => ['verify_peer_name' =>false, 'verify_peer' => false]]]);
                    $client = new \ElephantIO\Client($v2);
                    $client->initialize();
 
                    $produit = new Product((int)$scheduled_product['id_product'], false, $this->context->language->id);
                    $image_url = $base . $link->getImageLink($produit->link_rewrite, $scheduled_product['id_image'], $image_type);
                    $scheduled_product['image_url'] = $image_url;

                    $client->emit('actiongetscheduled_product', ['scheduled_product' => $scheduled_product ]);
                    $client->close();
                    
                    // $query = 'UPDATE ' . _DB_PREFIX_ . 'lnk_promo_scheduled_products SET `date_upd` = NOW() WHERE `id_scheduled_products` = '.(int)$scheduled_product['id_scheduled_products'];
                    Db::getInstance()->execute($query);
                }
            }
        }


        $this->emptyCachedMessages();

        return true;
    }

    protected function shouldBeExecuted($cron){
        $minute      = ($cron['minute'] == -1)      ? date('i') : $cron['minute'];
        $hour        = ($cron['hour'] == -1)        ? date('H') : $cron['hour'];
        $day         = ($cron['day'] == -1)         ? date('d') : $cron['day'];
        $month       = ($cron['month'] == -1)       ? date('m') : $cron['month'];
        $weekday     = ($cron['weekday'] == -1)     ? date('D') : date('D', strtotime('Sunday +' . $cron['weekday'] . ' days'));
        $day = date('Y').'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($day, 2, '0', STR_PAD_LEFT);
        $execution = $weekday . ' ' . $day . ' ' . str_pad($hour, 2, '0', STR_PAD_LEFT) . ' ' . str_pad($minute, 2, '0', STR_PAD_LEFT);
        $now = date('D Y-m-d H i');
       
        return !(bool)strcmp($now, $execution);

    }

    public function makeCacheFile($data , $name){
        
        //return;
        $name = isset($name) ? $name : "";

        $data['type'] = $name;
        $data['time'] = date('H:i');
        $inp = [];
        
        $inp = file_get_contents($this->cachefile);
        $tempArray  = json_decode($inp, true);

        if($tempArray == "" && $tempArray == null){
            $tempArray = [];
        }
        
        array_push($tempArray, $data);
        $jsonData = json_encode($tempArray);
        file_put_contents($this->cachefile, $jsonData);

    }

    public function getCachedMessages($group){

        $messages = [];

        $inp        = file_get_contents($this->cachefile);
        $messages   = json_decode($inp, true);



        $messages   = array_slice($messages, -Configuration::get('LP_messages_nbr'));
        return $messages;

    }

    public function emptyCachedMessages(){

        if ($this->shouldBeExecuted(array(
            'minute'    => 24,
            'hour'      => 8,
            'day'       => -1,
            'month'     => -1,
            'weekday'   => -1, 
        )) == true) { 
            file_put_contents($this->cachefile, "");
        }

    }

}