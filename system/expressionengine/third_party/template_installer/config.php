<?php

if ( ! defined('TEMPLATE_INSTALLER_ADDON_NAME'))
{
	define('TEMPLATE_INSTALLER_ADDON_NAME',         'Template Installer');
	define('TEMPLATE_INSTALLER_ADDON_VERSION',      '0.1');
}

$config['name']=TEMPLATE_INSTALLER_ADDON_NAME;
$config['version']=TEMPLATE_INSTALLER_ADDON_VERSION;

$config['nsm_addon_updater']['versions_xml']='http://www.intoeetive.com/index.php/update.rss/307';