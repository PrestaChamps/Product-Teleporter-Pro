<?php
/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <zoli@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */

/**
 * Class ProductTeleporterProConfig for easy-peasy module configuration
 */
class ProductTeleporterProConfig
{
    /** Required for PHP < 5.6 compatibility */
    public static $className = 'ProductTeleporterProConfig';

    static $multiLang = [
    ];

    /**
     * Save a config value
     *
     * @param $key
     * @param $value
     *
     * @return bool
     */
    public static function saveValue($key, $value)
    {
        return Configuration::updateValue($key, $value);
    }

    /**
     * Get configuration keys and values
     *
     * @return array
     */
    public static function getConfigurationValues()
    {
        try {
            $class = new ReflectionClass(static::$className);
            $values = [];
            foreach ($class->getConstants() as $constant) {
                if (is_string($constant)) {
                    if (in_array($constant, static::$multiLang)) {
                        static::getMultilangConfigValues($constant, $values);
                    } else {
                        $values[$constant] = Configuration::get($constant);
                    }
                }
            }
            return $values;
        } catch (Exception $exception) {
            return [];
        }
    }

    /**
     * Get a multilang config key (mainly used with the HelperForm class)
     *
     * @param $key
     * @param $values
     */
    private static function getMultilangConfigValues($key, &$values)
    {
        $languages = Language::getLanguages(false, false, false);
        $values[$key] = [];
        foreach ($languages as $language) {
            $values[$key][$language['id_lang']] = Configuration::get($key, $language['id_lang']);
        }
    }

    /**
     * Decide if a config key exists in the DB or not, doesn't really care about multilang
     *
     * @param null $configKey
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    public static function configExists($configKey = null)
    {
        $query = new \DbQuery();
        $query->select('count(*)');
        $query->from('configuration');
        $query->where("name = '" . pSQL($configKey) . "'");

        return (int)Db::getInstance()->executeS($query) > 0;
    }

}
