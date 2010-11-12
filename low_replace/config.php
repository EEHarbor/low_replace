<?php

/**
* Low Replace config file
*
* @package			low-replace-ee2_addon
* @version			2.0.2
* @author			Lodewijk Schutte ~ Low <low@loweblog.com>
* @link				http://loweblog.com/freelance/article/pireplacephp/
* @license			http://creativecommons.org/licenses/by-sa/3.0/
*/

if ( ! defined('LOW_REPLACE_NAME'))
{
	define('LOW_REPLACE_NAME', 'Low Replace');
	define('LOW_REPLACE_VERSION', '2.0.2');
}
 
$config['name']    = LOW_REPLACE_NAME;
$config['version'] = LOW_REPLACE_VERSION;
 
$config['nsm_addon_updater']['versions_xml'] = 'http://loweblog.com/software/low-replace/feed/';
