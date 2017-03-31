<?php

namespace OutputDataConfigToolkitBundle\Tools;

use OutputDataConfigToolkitBundle\OutputDefinition\Dao;
use Pimcore\Config;
use Pimcore\Extension\Bundle\Installer\AbstractInstaller;
use Pimcore\Extension\Bundle\Installer\Exception\InstallationException;

class Installer extends AbstractInstaller {


    public function install()
    {

        if(!file_exists(PIMCORE_PRIVATE_VAR . "/config/outputdataconfig")) {
            \Pimcore\File::mkdir(PIMCORE_PRIVATE_VAR . "/config/outputdataconfig");
            copy(__DIR__ . "/Elements_OutputDataConfigToolkit/install/config.php", PIMCORE_PRIVATE_VAR . "/config/outputdataconfig/config.php");
        }

        $db = \Pimcore\Db::get();
        $db->query("CREATE TABLE `" . Dao::TABLE_NAME . "` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `o_id` int(11) NOT NULL,
              `o_classId` int(11) NOT NULL,
              `channel` varchar(255) COLLATE utf8_bin NOT NULL,
              `configuration` longtext CHARACTER SET latin1,
              PRIMARY KEY (`id`),
              UNIQUE KEY `Unique` (`o_id`,`o_classId`,`channel`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
        ");

        $db->query("INSERT INTO users_permission_definitions (`key`) VALUES ('bundle_outputDataConfigToolkit');");

        if($this->isInstalled()){
            return true;
        } else {
            return false;
        }

    }

    public function needsReloadAfterInstall()
    {
        return true;
    }

    public function isInstalled()
    {
        $result = null;
        try{
            if(Config::getSystemConfig()) {
                $result = \Pimcore\Db::get()->query("SHOW TABLES LIKE '" . Dao::TABLE_NAME . "';");
            }
        } catch(\Exception $e){}
        return !empty($result);

    }

    public function canBeInstalled()
    {
        return !$this->isInstalled();
    }

    public function canBeUninstalled()
    {
        return true;
    }

    public function uninstall()
    {
        $db = \Pimcore\Db::get();
        $db->query("DROP TABLE IF EXISTS `" . Dao::TABLE_NAME . "`;");

        $db->query("DELETE FROM users_permission_definitions WHERE `key` = 'bundle_outputDataConfigToolkit'");
        if(self::isInstalled()){
            throw new InstallationException("Could not be uninstalled.");
        }
    }

}