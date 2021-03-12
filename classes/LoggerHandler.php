<?php
/**
 * Live Promo
 *
 * @author    L'nkboot 
 * @copyright L'nkboot 2020 
 * @license   http://www.lnkboot.fr/livepromo/license
 */

class LoggerHandler
{

    private static $process;

    /**
     * @var array logs
     */
    private static $logs = array();


    public static function openLogger($process = null)
    {
        self::$process = $process;
        self::autoErasingLogs();
    }

    /**
     * @param string|null $msg
     */
    public static function closeLogger($msg = null)
    {
        if (self::$process != null && false === empty($msg)) {
            self::logInfo($msg, self::$process['name'], self::$process['id']); // summary
        }
        self::saveLogsInDb();
    }

    /**
     * @param string $msg
     * @param string|null $objectModel
     * @param int|null $objectId
     * @param string|null $name
     * @param string $level
     */
    public static function addLog($msg, $objectModel = null, $objectId = null, $name = null, $level = 'info')
    {
        self::$logs[] = array(
            'name' => pSQL($name),
            'msg' => pSQL($msg),
            'level' => pSQL($level),
            'object_name' => pSQL($objectModel),
            'object_id' => (int)$objectId,
            'date_add' => date("Y-m-d H:i:s"),
        );

        self::saveLogsInDb();
    }

    /**
     * @param string $msg
     * @param string|null $objectModel
     * @param int|null $objectId
     * @param string $name
     */
    public static function logSuccess($msg, $objectModel = null, $objectId = null, $name = 'default')
    {
        if (self::$process != null) {
            $name = self::$process['name'];
        }
        self::addLog($msg, $objectModel, $objectId, $name, 'success');
    }

    /**
     * @param string $msg
     * @param string|null $objectModel
     * @param int|null $objectId
     * @param string $name
     */
    public static function logError($msg, $objectModel = null, $objectId = null, $name = 'default')
    {
        if (self::$process != null) {
            $name = self::$process['name'];
        }
        self::addLog($msg, $objectModel, $objectId, $name, 'error');
    }

    /**
     * @param string $msg
     * @param string|null $objectModel
     * @param int|null $objectId
     * @param string $name
     */
    public static function logInfo($msg, $objectModel = null, $objectId = null, $name = 'default')
    {
        if (self::$process != null) {
            $name = self::$process['name'];
        }
        self::addLog($msg, $objectModel, $objectId, $name, 'info');
    }

    /**
     * @return bool
     */
    public static function saveLogsInDb()
    {
        $result = true;
        if (false === empty(self::$logs)) {
            
            $result = Db::getInstance()->insert(
                'lp_processlogger',
                self::$logs
            );

            if ($result) {
                self::$logs = array();
            }
        }

        return $result;
    }

    /**
     * @return bool
     */
    public static function autoErasingLogs()
    {
        if (self::isAutoErasingEnabled()) {
            return Db::getInstance()->delete(
                'lp_processlogger',
                sprintf(
                    'date_add <= NOW() - INTERVAL %d DAY',
                    self::getAutoErasingDelayInDays()
                )
            );
        }

        return true;
    }

    /**
     * @return bool
     */
    public static function isAutoErasingEnabled()
    {
        return false === (bool)Configuration::get('LP_EXTLOGS_ERASING_DISABLED');
    }

    /**
     * @return int
     */
    public static function getAutoErasingDelayInDays()
    {
        $numberOfDays = Configuration::get('LP_EXTLOGS_ERASING_DAYSMAX');

        if (empty($numberOfDays) || false === is_numeric($numberOfDays)) {
            return 5;
        }

        return (int)$numberOfDays;
    }
}
