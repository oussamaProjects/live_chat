<?php 
/**
 * Live Promo
 *
 * @author    L'nkboot 
 * @copyright L'nkboot 2020 
 * @license   http://www.lnkboot.fr/livepromo/license
 */
if (!defined('_PS_VERSION_'))
	exit;
class LP_defines extends Module
{
    public $subTabs=array();
    public $configs = array(); 
    public $is17 = false;

    public function __construct()
	{
        $this->name = 'lnk_livepromo';
        $this->context = Context::getContext();
        $id_shop = $this->context->shop->id;

        if (version_compare(_PS_VERSION_, '1.7', '>='))
            $this->is17 = true;
        
        $or = "d-none";
        if(Configuration::get('LP_range_order_number') == 'custom'){
            $or = " ";
        }
        $ca = "d-none";
        if(Configuration::get('LP_range_cart_number') == 'custom'){
            $ca = " ";
        }
        $this->configs = array(
            'tabs' => array(
                'general'   => $this->l('General configuration'),
                'front'     => $this->l('Front configuration'),
                'socket'    => $this->l('Events'),
                'display'   => $this->l('Display configuration'),
                // 'style'     => $this->l('Style configuration'),
            ),
            'fields' => array(
                array(
                    'type' => 'html',
                    'tab' => 'settings',
                    'name' => 'infos',
                    'html_content' => "<div class='alert alert-info'>".
                        $this->l('Define the settings and place the following string in the crontab:', 'payment_paypal')."<br />
                        <font color='#00B015'><strong>* * * * * wget -q -t 0 -w 5 --delete-after --no-check-certificate --max-redirect=1000 \"".$this->context->shop->getBaseURL()."modules/".$this->name."/cron.php?secure_key=".Configuration::getGlobalValue('PROMO_CRONJOBS_EXECUTION_TOKEN').(Shop::isFeatureActive() ? '&id_shop='.(int)$id_shop : '')."\"</strong></font>
                    </div>"
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Activate'),
                    'name' => 'LP_active',
                    'is_bool' => true,
                    'tab' => 'settings',
                    'hint' => $this->l('Activate the live promo module'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Activate the CRONJOB'),
                    'name' => 'LP_active_cron',
                    'is_bool' => true,
                    'tab' => 'settings',
                    'hint' => $this->l('Activate the CRONJOB'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
               
                array(
                    'type' => 'switch',
                    'label' => $this->l('Activate the Liked product systeme'),
                    'name' => 'LP_active_liked',
                    'is_bool' => true,
                    'tab' => 'settings',
                    'hint' => $this->l('Activate the Liked product systeme'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                
                array(
                    'type' => 'add_ip',
                    'label' => $this->l('Enable Live promo for IP'),
                    'name' => 'LP_IP', 
                    'tab' => 'settings', 
                ),
                
                /** Events configuration */
                array(
                    'type' => 'text',
                    'label' => $this->l('Url server socket'),
                    'name' => 'LP_socket_url',
                    'desc' => $this->l('Url server socket'),
                    'tab' => 'socket',
                    'class' => 'fixed-width-xl ',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show cart decrease'),
                    'name' => 'LP_cart_down',
                    'is_bool' => true,
                    'tab' => 'socket',
                    'hint' => $this->l('Decrease the quantity product in cart'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show cart increase'),
                    'name' => 'LP_cart_up',
                    'is_bool' => true,
                    'tab' => 'socket',
                    'hint' => $this->l('Increase the quantity product in cart'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show remove cart'),
                    'name' => 'LP_cart_remove',
                    'is_bool' => true,
                    'tab' => 'socket',
                    'hint' => $this->l('Remove product from cart'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show customer login'),
                    'name' => 'LP_customer_login',
                    'is_bool' => true,
                    'tab' => 'socket',
                    'hint' => $this->l('Showing customer when login'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Registered customers'),
                    'name' => 'LP_customer_registered',
                    'is_bool' => true,
                    'tab' => 'socket',
                    'hint' => $this->l('Shows new registered customers'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Validate order'),
                    'name' => 'LP_validate_order',
                    'is_bool' => true,
                    'tab' => 'socket',
                    'hint' => $this->l('Shows new order'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                /** Display configuration */
                array(
                    'type' => 'text',
                    'label' => $this->l('Number of messages'),
                    'name' => 'LP_messages_nbr', 
                    'tab' => 'display',
                    'class' => 'fixed-width-xl ',
                    'hint' => $this->l('Number of messages to display in chat module'), 
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Number of promotions'),
                    'name' => 'LP_promotions_nbr', 
                    'tab' => 'display',
                    'class' => 'fixed-width-xl ',
                    'hint' => $this->l('Number of promotions to display in chat module'), 
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show name customers'),
                    'name' => 'LP_customer_name',
                    'is_bool' => true,
                    'tab' => 'display',
                    'hint' => $this->l('Show full name customers'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show number customers'),
                    'name' => 'LP_customer_connected',
                    'is_bool' => true,
                    'tab' => 'display',
                    'hint' => $this->l('Show number customers connected'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show order number'),
                    'name' => 'LP_order_number',
                    'is_bool' => true,
                    'tab' => 'display',
                    'hint' => $this->l('Show order number'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'label' => $this->l('Range order number'),
                    'type' => 'radio',
                    'name' => 'LP_range_order_number',
                    'default' => 'day',
                    'tab' => 'display',
                    'values' => array(
                        array(
                            'id' => 'day',
                            'label' => $this->l('Order number by day'),
                            'value' => 'day',
                        ),
                        array(
                            'id' => 'month',
                            'label' => $this->l('Order number by month'),
                            'value' => 'month',
                        ),
                        array(
                            'id' => 'week',
                            'label' => $this->l('Order number by week'),
                            'value' => 'week',
                        ),
                        array(
                            'id' => 'custom',
                            'label' => $this->l('Custom order number custom'),
                            'value' => 'custom',
                        ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Before today'),
                    'name' => 'LP_order_range_date',
                    'class' => 'fixed-width-xl ',
                    'form_group_class' => $or,
                    'desc' => $this->l('day before today eg : 3 ( order between today - 3 )'),
                    'tab' => 'display',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show cart number'),
                    'name' => 'LP_cart_number',
                    'is_bool' => true,
                    'tab' => 'display',
                    'hint' => $this->l('Show cart number'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'label' => $this->l('Range cart number'),
                    'type' => 'radio',
                    'name' => 'LP_range_cart_number',
                    'default' => 'day',
                    'tab' => 'display',
                    'values' => array(
                        array(
                            'id' => 'day',
                            'label' => $this->l('Cart number by day'),
                            'value' => 'day',
                        ),
                        array(
                            'id' => 'month',
                            'label' => $this->l('Cart number by month'),
                            'value' => 'month',
                        ),
                        array(
                            'id' => 'week',
                            'label' => $this->l('Cart number by week'),
                            'value' => 'week',
                        ),
                        array(
                            'id' => 'custom',
                            'label' => $this->l('Custom cart number custom'),
                            'value' => 'custom',
                        ),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Before today'),
                    'name' => 'LP_cart_range_date',
                    'class' => 'fixed-width-xl ',
                    'form_group_class' => $ca,
                    'desc' => $this->l('day before today eg : 3 ( cart between today - 3 )'),
                    'tab' => 'display',
                ),
                /** Style configuration */ 

                array(
                    'type' => 'color',
                    'label' => $this->l('Main color'),
                    'name' => 'LP_style_main_color', 
                    'tab' => 'front',
                    'hint' => $this->l('ex: #e84667, #968c83, #ffd57e, #c060a1, #28abb9....'), 
                ), 

                array(
                    'type' => 'switch',
                    'label' => $this->l('Show feature product'),
                    'name' => 'LP_style_feature_product_display',
                    'is_bool' => true,
                    'tab' => 'front',
                    'hint' => $this->l('Show feature product'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show Dark/Light mode Button'),
                    'name' => 'LP_style_dark_light_mode_button',
                    'is_bool' => true,
                    'tab' => 'front',
                    'hint' => $this->l('Show Dark/Light mode Button'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show like Button'),
                    'name' => 'LP_style_like_button',
                    'is_bool' => true,
                    'tab' => 'front',
                    'hint' => $this->l('Show like Button'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show love Button'),
                    'name' => 'LP_style_love_button',
                    'is_bool' => true,
                    'tab' => 'front',
                    'hint' => $this->l('Show love Button'),
                    'values' => array(
                        array(
                            'id' => 'on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                ),

                array(
                    'type'  => 'select',
                    'label' => $this->l('Live label position'),
                    'name'  => 'LP_style_live_label_position',
                    'hint'  => $this->l('Live label position'),
                    'required' => false,
                    'lang'  => false,
                    'tab'   => 'front',
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 1, 
                                'name' => $this->l('Côté gauche') 
                            ),
                            array(
                                'id_option' => 2,
                                'name' => $this->l('Côté droit')
                            ),
                        ),
                        'id' => 'id_option', 
                        'name' => 'name'
                    )
                ),

                array(
                    'type' => 'select',
                    'label' => $this->l('Feature product position'),
                    'hint' => $this->l('Feature product position'),
                    'name' => 'LP_style_feature_product_position',
                    'required' => false,
                    'lang' => false,
                    'tab' => 'front',
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 1, 
                                'name' => $this->l('Côté gauche') 
                            ),
                            array(
                                'id_option' => 2,
                                'name' => $this->l('Côté droit')
                            ),
                        ),
                        'id' => 'id_option', 
                        'name' => 'name'
                    )
                ),

                
                array(
                    'type'  => 'text',
                    'label' => $this->l('Height of the side chat'),
                    'name'  => 'LP_style_height_side_chat', 
                    'tab'   => 'front',
                    'class' => 'fixed-width-xl ',
                    'hint'  => $this->l('ex: 10, 20, 30, ..., 100 Height of the side chat'), 
                ), 

            )
        );

    }

    public function getBaseLink()
    {
        return (Configuration::get('PS_SSL_ENABLED_EVERYWHERE')?'https://':'http://').$this->context->shop->domain.$this->context->shop->getBaseURI();
    }
}