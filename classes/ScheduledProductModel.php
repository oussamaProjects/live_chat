<?php
/**
 * Live Promo
 *
 * @author    L'nkboot 
 * @copyright L'nkboot 2020 
 * @license   http://www.lnkboot.fr/livepromo/license
 */

class ScheduledProductModel extends ObjectModel
{
    /** @var string name of action */
    
    
    public $id_product;
    public $minute;
    public $hour;
    public $day;
    public $weekday;
    public $month;
    public $common_option;
    public $note;
    public $notify;
    public $date_add;
    public $date_upd; 

    /**
     * @see \ObjectModel::$definition
     */
    public static $definition = array(
        'table'        => 'lnk_promo_scheduled_products',
        'primary'      => 'id_scheduled_products',
        'fields'       => array(
            'id_product'       => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),'minute'          => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),'hour'            => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),'day'             => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),'weekday'         => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),'month'           => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),'common_option'   => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),'note'            => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),'notify'          => array(
                'type'     => ObjectModel::TYPE_INT,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),'date_add'      => array(
                'type'      => ObjectModel::TYPE_DATE,
                'validate'  => 'isDate',
                'copy_post' => false,
            ),'date_upd'      => array(
                'type'      => ObjectModel::TYPE_DATE,
                'validate'  => 'isDate',
                'copy_post' => false,
            ),
        ),
    );
}