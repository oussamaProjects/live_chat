<?php
/**
 * Live Promo
 *
 * @author    L'nkboot 
 * @copyright L'nkboot 2020 
 * @license   http://www.lnkboot.fr/livepromo/license
 */

class LoggerHandlerModel extends ObjectModel
{
    /** @var string name of action */
    public $name;

    /** @var string Message to display */
    public $msg;

    /** @var string level (success|failed|info) */
    public $level;

    /** @var string Name of ObjectModel associated if needed */
    public $object_name;

    /** @var int|null Identifier of resource announced with ObjectModel if needed */
    public $object_id;

    /** @var string Date */
    public $date_add;

    /**
     * @see \ObjectModel::$definition
     */
    public static $definition = array(
        'table'        => 'lp_processlogger',
        'primary'      => 'id_lp_processlogger',
        'fields'       => array(
            'name'     => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),
            'msg'     => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size'     => 255,
            ),
            'level'     => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size'     => 10,
            ),
            'object_name'     => array(
                'type'     => ObjectModel::TYPE_STRING,
                'validate' => 'isGenericName',
                'size'     => 100,
            ),
            'object_id' => array(
                'type' => ObjectModel::TYPE_INT,
                'validate' => 'isUnsigned',
                'allow_null' => true,
            ),
            'date_add' => array(
                'type'      => ObjectModel::TYPE_DATE,
                'validate'  => 'isDate',
                'copy_post' => false,
            ),
        ),
    );
}
