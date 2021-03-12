<?php
/**
 * Live Promo
 *
 * @author    L'nkboot 
 * @copyright L'nkboot 2020 
 * @license   http://www.lnkboot.fr/livepromo/license
 */
include_once(_PS_MODULE_DIR_.'lnk_livepromo/vendor/autoload.php');
class Lnk_livepromoPageModuleFrontController extends ModuleFrontController
{

    public $htmlresult = "";
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
        include_once($this->module->getLocalPath().'classes/LikedModel.php');
    }

    public function init()
	{
		parent::init();
    }
    
    public function initContent()
    {
        parent::initContent();
        $action = Tools::getValue('action');
        if(!Tools::getValue('ajax')){
            if ($this->context->customer->isLogged()) {
                $products = Liked::getProductByIdCustomer((int)$this->context->customer->id,$this->context->language->id);
                $nb_products = count($products);
                for ($i = 0; $i < $nb_products; ++$i) {
                    $obj = new Product((int)$products[$i]['id_product'], true, $this->context->language->id);
                    if (!Validate::isLoadedObject($obj)) {
                        continue;
                    } else {
                        $products[$i]['allow_oosp'] = $obj->isAvailableWhenOutOfStock((int)$obj->out_of_stock);
                        $images = $obj->getImages($this->context->language->id);
                        foreach ($images as $image) {
                            if ($image['cover']) {
                                $products[$i]['cover'] = $obj->id.'-'.$image['id_image'];
                                break;
                            }
                        }
                        if (!isset($products[$i]['cover'])) {
                            $products[$i]['cover'] = $this->context->language->iso_code.'-default';
                        }
                        $products[$i]['has_reduction'] = false;
                        $products[$i]['price'] = $obj->getPrice(true, null, 6, null, false, false);
                        $specific_price = SpecificPrice::getSpecificPrice( (int)$products[$i]['id_product'], 1, 0,0, $this->context->customer->id_default_group , 1 );
                        if($specific_price){
                            if($specific_price["reduction_type"] == "amount"){
                                $pricep = $products[$i]['price'] - $specific_price["reduction"];
                            }else{
                                $pricep = $products[$i]['price'] - ($products[$i]['price'] * (float)$specific_price["reduction"]);
                            }
                            $products[$i]['has_reduction'] = true;
                            $products[$i]['specific_price'] = Tools::displayPrice($pricep);
                        }
                        $products[$i]['price'] = Tools::displayPrice($products[$i]['price']);
                    }
                }
            }else{
                $products = array();
            }
            $this->context->smarty->assign(
                array(
                    'products' => $products,
                    'is_logged' => $this->context->customer->isLogged(),
                )
            );
            if (version_compare(_PS_VERSION_, '1.7', '>')) {
                $this->setTemplate('module:lnk_livepromo/views/templates/front/view.tpl');
            } else {
                $this->setTemplate('view.tpl');
            }
        }

    }


    public function displayAjaxGetStateLP()
    {
        // $this->module->logger->logDebug('order_number '. $this->module->getOrderNbr());
        // $this->module->logger->logDebug('cart_number '. $this->module->getCartNbr());
        // $this->module->logger->logDebug('like_number '. $this->module->getLikedNbr());
        // $this->module->logger->logDebug('customer_connected '. Configuration::get('LP_nbr_customer_connected'));
        
        die(Tools::jsonEncode(array(
            'order_number' => $this->module->getOrderNbr(),
            'cart_number'  => $this->module->getCartNbr(),
            'like_number'  => $this->module->getLikedNbr(),
            'customer_connected' => Configuration::get('LP_nbr_customer_connected'),
        )));
    }

    public function displayAjaxSetLiked()
    {
        if (!$this->context->customer->isLogged()) {
            die(Tools::jsonEncode(array(
                'success' => false,
                'error' => $this->module->l('You aren\'t logged in', 'page')))
            );
        }

        $id_product     = Tools::getValue('id_product');
        $actionProduct  = Tools::getValue('actionProduct');

        if(Liked::issetProduct((int)$this->context->customer->id, (int)$id_product)){
            Liked::removeProduct((int)$this->context->customer->id, (int)$id_product);
        }else{
            Liked::addProduct($this->context->customer->id, $id_product, $this->context->shop->id, $this->context->shop->id_shop_group);
            
            
            // $like = new Liked();
            // $like->id_product       = (int)$id_product;
            // $like->id_customer      = (int)$this->context->customer->id;
            // $like->id_shop          = $this->context->shop->id;
            // $like->id_shop_group    = $this->context->shop->id_shop_group;
            // $like->save();


            // $lp_server_node = Configuration::get('LP_socket_url');
            // $v2 = new \ElephantIO\Engine\SocketIO\Version2X($lp_server_node,['context' => ['ssl' => ['verify_peer_name' =>false, 'verify_peer' => false]]]);
            // $client = new \ElephantIO\Client($v2);
            // try
            // {
            //     $client->initialize();
                
            //     if ($this->context->customer->isLogged()) {
            //         $id = $this->context->customer->id;
            //         $p = array("customer" => $this->context->customer);
            //     } elseif (!empty($cookie->id_guest)) {
            //         $id = 'G'.$cookie->id_guest;
            //         $p = array("guest" => true);
            //     } else {
            //         $id = 'G'.$this->context->cookie->id_guest;
            //         $p = array("guest" => true);
            //     }

            //     $base = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://');
            //     $link = new Link();
            //     $name = array();
            //     $specificprice = array();
                 
            //     $groups = Group::getGroups($this->context->language->id);
            //     // $produit = new Product((int)$idProduct,false);
                
            //     foreach ($languages as $language) {
            //         $produit = new Product((int)$id_product,false,$language['id_lang']);
            //         $url = $link->getProductLink($produit);
            //         $image_type = ImageType::getFormatedName('home');
            //         $cover = $produit->getCover((int)$id_product);
            //         $image = $base.$link->getImageLink($produit->link_rewrite, $cover['id_image'], $image_type);
            //         $price = $produit->getPrice(true, null, 6, null, false, false);
            //         $prod_lang[$language['id_lang']] = array(
            //             'name' => $produit->name,
            //             'url' => $url,
            //             'image' => $image, 
            //             'price' => Tools::displayPrice($price),
            //         );
            //     }

            //     foreach ($groups as $group) {
            //         $pricep = 0;
            //         $specific_price = SpecificPrice::getSpecificPrice( (int)$id_product, 1, 0,0, $group['id_group'],1 );
            //         if($specific_price){
            //             if($specific_price["reduction_type"] == "amount"){
            //                 $pricep = $price - $specific_price["reduction"];
            //             }else{
            //                 $pricep = $price - ($price * (float)$specific_price["reduction"]);
            //             }
            //         }
            //         $specificprice[$group['id_group']] = Tools::displayPrice($pricep);
            //     }

            //     $productdetail= array( 
            //         'lang' => $prod_lang,
            //         'specific_price' => $specificprice,
            //     );
                
            //     $customer_name = $this->module->getNameCustomer($p); 

            //     if(!empty($productdetail)){
            //         $client->emit('actionlikedproduct', ['id' => $id, 'productdetail' => $productdetail, 'customer_name' => $customer_name ]);
            //     }

            //     $client->close();
            // }
            // catch (ServerConnectionFailureException $e)
            // {
            //     echo $e;
            // }



        }

        
        $this->module->emitAction([], 'actionlikedproduct', 'liked');

        die(Tools::jsonEncode(array('success' => true)));
    }
    
}
