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

class Liked extends ObjectModel
{
    /** @var integer Wishlist ID */
    public $id;

    /** @var integer Customer ID */
    public $id_customer;

    /** @var integer id_product */
    public $id_product;


    /** @var date Object creation date */
    public $date_add;

    /** @var date Object last modification date */
    public $date_upd;

    /** @var integer id_shop */
    public $id_shop;

    /** @var integer id_shop_group */
    public $id_shop_group;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'lp_liked',
        'primary' => 'id_liked',
        'fields' => array(
            'id_customer' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_shop' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop_group' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'date_add' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>        array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
        )
    );


    public static function issetProduct($id_customer, $id_product)
    {
        return (Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT SUM(lk.`id_product`) AS nbProducts
              FROM `'._DB_PREFIX_.'lp_liked` lk
            WHERE  lk.`id_customer` = '.(int)($id_customer).' AND  lk.`id_product` = '.(int)($id_product)
            )
        );
    }

    /**
     * Remove product
     *
     * @return boolean succeed
     */
    public static function removeProduct($id_customer, $id_product)
    {
        return Db::getInstance()->execute(
            'DELETE FROM `'._DB_PREFIX_.'lp_liked`
        WHERE `id_customer` = '.(int)($id_customer).'
        AND `id_product` = '.(int)($id_product)
        );
    }

      /**
     * Add product
     *
     * @return boolean succeed
     */
    public static function addProduct($id_customer, $id_product, $id_shop, $id_shop_group)
    {
        return Db::getInstance()->execute(
            'INSERT INTO `' . _DB_PREFIX_ . 'lp_liked` ( `id_customer`, `id_product`, `id_shop`, `id_shop_group`) 
            VALUES (' . (int)$id_customer . ', ' . (int)$id_product . ', ' . (int)$id_shop . ', ' . (int)$id_shop_group . ')'
        );
    }

    /**
     * Get Wishlist products by Customer ID
     *
     * @return array Results
     */
    public static function getProductByIdCustomer($id_customer, $id_lang)
    {
        $t_products = Db::getInstance()->executeS(
            'SELECT wp.`id_product`,
                p.*,
                p.`quantity` AS product_quantity, 
                pl.`name`, pl.link_rewrite,
                cl.link_rewrite AS category_rewrite
            FROM `'._DB_PREFIX_.'lp_liked` wp
            LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = wp.`id_product`
            '.Shop::addSqlAssociation('product', 'p').'
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON pl.`id_product` = wp.`id_product`'.Shop::addSqlRestrictionOnLang('pl').'
            LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON cl.`id_category` = product_shop.`id_category_default` AND cl.id_lang='.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'
            WHERE wp.`id_customer` = '.(int)($id_customer).'
            AND pl.`id_lang` = '.(int)($id_lang).' GROUP BY p.id_product'
        );
        if (empty($t_products) === true or !sizeof($t_products)) {
            return array();
        }
        
        //$currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        $currency = (int)Context::getContext()->currency->id;
        if (!$currency) {
            $currency = (int)Configuration::get('PS_CURRENCY_DEFAULT');
        }
        $products = Product::getProductsProperties($id_lang, $t_products);

        for ($i = 0; $i < sizeof($products); ++$i) {
            // if (Configuration::get('Is_TAX')) {
            //     $products[$i]['price'] = Product::convertPriceWithCurrency(array('price' => $products[$i]['price'], 'currency' => $currency), Context::getContext()->smarty);
            // } else {
                $products[$i]['price'] = Product::convertPriceWithCurrency(array('price' => $products[$i]['price_tax_exc'], 'currency' => $currency), Context::getContext()->smarty);
            //}
        }

        return $products;
    }

}
