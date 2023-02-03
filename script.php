<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Version;

/**
 * Script file of HelloWorld component.
 *
 * The name of this class is dependent on the component being installed.
 * The class name should have the component's name, directly followed by
 * the text InstallerScript (ex:. com_helloWorldInstallerScript).
 *
 * This class will be called by Joomla!'s installer, if specified in your component's
 * manifest file, and is used for custom automation actions in its installation process.
 *
 * In order to use this automation script, you should reference it in your component's
 * manifest file as follows:
 * <scriptfile>script.php</scriptfile>
 *
 * @package     Joomla.Administrator
 * @subpackage  com_helloworld
 *
 * @copyright   Copyright (C) 2005 - 2018 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
class plgTaskWt_jshopping_b24_pro_cronInstallerScript
{
    /**
     * This method is called after a component is installed.
     *
     * @param  \stdClass $installer - Parent object calling this method.
     *
     * @return void
     */
    public function install($installer)
    {
	    $version = new Version;

	    // only for Joomla 3.x

	    if (version_compare($version->getShortVersion(), '4.0', '<')) {

		    Factory::getApplication()->enqueueMessage('&#128546; <strong>WT JShopping Bitrix 24 PRO</strong> plugin doesn\'t support Joomla versions <span class="alert-link">lower 4</span>. Your Joomla version is <span class="badge badge-important">'.$version->getShortVersion().'</span>','error');
		    return false;

	    }
    }

    /**
     * This method is called after a component is uninstalled.
     *
     * @param  \stdClass $installer - Parent object calling this method.
     *
     * @return void
     */
    public function uninstall($installer) 
    {

		
    }

    /**
     * This method is called after a component is updated.
     *
     * @param  \stdClass $installer - Parent object calling object.
     *
     * @return void
     */
    public function update($installer) 
    {

    }

    /**
     * Runs just before any installation action is performed on the component.
     * Verifications and pre-requisites should run in this function.
     *
     * @param  string    $type   - Type of PreFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $installer - Parent object calling object.
     *
     * @return void
     */
    public function preflight($type, $installer) 
    {
	    $version = new Version;

	    // only for Joomla 3.x

	    if (version_compare($version->getShortVersion(), '4.0', '<')) {

		    Factory::getApplication()->enqueueMessage('&#128546; <strong>WT JShopping Bitrix 24 PRO</strong> plugin doesn\'t support Joomla versions <span class="alert-link">lower 4</span>. Your Joomla version is <span class="badge badge-important">'.$version->getShortVersion().'</span>','error');
		    return false;

	    }
    }
	


    /**
     * Runs right after any installation action is performed on the component.
     *
     * @param  string    $type   - Type of PostFlight action. Possible values are:
     *                           - * install
     *                           - * update
     *                           - * discover_install
     * @param  \stdClass $installer - Parent object calling object.
     *
     * @return void
     */
    function postflight($type, $installer)
    {
	    $smile = '';
	    if($type != 'uninstall')
	    {
		    $smiles    = ['&#9786;', '&#128512;', '&#128521;', '&#128525;', '&#128526;', '&#128522;', '&#128591;'];
		    $smile_key = array_rand($smiles, 1);
		    $smile     = $smiles[$smile_key];
	    }

	    $element = strtoupper($installer->getElement());
		echo "
		<style>	.thirdpartyintegration {
				display:flex;
				padding: 3px 5px;
				align-items:center;
			}
			.thirdpartyintegration-logo {
				height:32px;
				float:left; 
				margin-right: 5px;
			}
			
			.thirdpartyintegration.success {
				border: 1px solid #2F6F2F;
				background-color:#dfffdf;
			}
			.thirdpartyintegration.error {
				border: 1px solid #bd362f;
				background-color:#ffdddb;
			}
		</style>
		<div class='row bg-white m-3 p-3 shadow-sm border'>
		<div class='col-12 col-lg-8'>
		<h2>".$smile." ".Text::_("PLG_".$element."_AFTER_".strtoupper($type))." <br/>".Text::_("PLG_".$element)."</h2>
		".Text::_("PLG_".$element."_DESC");
		
		
			echo Text::_("PLG_".$element."_WHATS_NEW");


		    $thirdpartyextensions="";
		    if(file_exists(JPATH_SITE."/plugins/system/wt_jshopping_b24_pro/wt_jshopping_b24_pro.xml")){
			    $wt_jshopping_b24_pro = simplexml_load_file(JPATH_SITE."/plugins/system/wt_jshopping_b24_pro/wt_jshopping_b24_pro.xml");


				    $thirdpartyextensions .=  "<div class='thirdpartyintegration success'><img class='thirdpartyintegration-logo' src='https://web-tolk.ru/web_tolk_logo_wide.png'/>
								<div class='media-body'><strong>".$wt_jshopping_b24_pro->author."'s</strong> plugin <strong>".$wt_jshopping_b24_pro->name." v.".$wt_jshopping_b24_pro->version."</strong> detected. <a href='".$wt_jshopping_b24_pro->authorUrl."' target='_blank'>".$wt_jshopping_b24_pro->authorUrl."</a> <a href='mailto:".$wt_jshopping_b24_pro->authorEmail."' target='_blank'>".$wt_jshopping_b24_pro->authorEmail."</a></div>
							</div>";

		    }

		    if(file_exists(JPATH_SITE."/plugins/system/wt_jshopping_b24_pro/wt_jshopping_b24_pro.xml")){
			    echo "<h4>Supported third-party extensions was found</h4>".$thirdpartyextensions;

		    } else {
				echo '<h4 class="text-danger fw-bold">You need to install a <a href="https://web-tolk.ru/dev/joomla-plugins/wt-joomshopping-bitrix24-pro.html" target="_blank">WT JoomShopping Bitrix 24 PRO plugin</a> for work!</h4>';
		    }

		echo "</div>
		<div class='col-12 col-lg-4 d-flex flex-column justify-content-start'>
		<img width='200px' src='https://web-tolk.ru/web_tolk_logo_wide.png'>
		<p>Joomla Extensions</p>
		<p class='btn-group'>
			<a class='btn btn-sm btn-outline-primary' href='https://web-tolk.ru' target='_blank'>https://web-tolk.ru</a>
			<a class='btn btn-sm btn-outline-primary' href='mailto:info@web-tolk.ru'><i class='icon-envelope'></i> info@web-tolk.ru</a>
		</p>
		<p><a class='btn btn-info' href='https://t.me/joomlaru' target='_blank'>Joomla Russian Community in Telegram</a></p>
		
		".Text::_("PLG_".$element."_MAYBE_INTERESTING")."
		</div>


		";		
	
    }
}