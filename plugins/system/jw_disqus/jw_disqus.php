<?php
/**
 * @version		3.4
 * @package		DISQUS Comments for Joomla! (package)
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');
if (version_compare(JVERSION, '1.6.0', 'ge'))
{
	jimport('joomla.html.parameter');
}

class plgSystemJw_disqus extends JPlugin
{

	// JoomlaWorks reference parameters
	var $plg_name = "jw_disqus";
	var $plg_copyrights_start = "\n\n<!-- JoomlaWorks \"DISQUS Comments for Joomla!\" (v3.4) starts here -->\n";
	var $plg_copyrights_end = "\n\n<!-- JoomlaWorks \"DISQUS Comments for Joomla!\" (v3.4) ends here -->\n";

	function plgSystemJw_disqus(&$subject, $params)
	{
		parent::__construct($subject, $params);
		
		// Define the DS constant under Joomla! 3.0
		if (!defined('DS'))
		{
			define('DS', DIRECTORY_SEPARATOR);
		}
	}

	function onAfterRender()
	{

		// API
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();

		// Assign paths
		$sitePath = JPATH_SITE;
		$siteUrl = JURI::root(true);

		// Requests
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$layout = JRequest::getCmd('layout');
		$page = JRequest::getCmd('page');
		$secid = JRequest::getInt('secid');
		$catid = JRequest::getInt('catid');
		$itemid = JRequest::getInt('Itemid');
		if (!$itemid)
			$itemid = 999999;

		// Check if plugin is enabled
		if (JPluginHelper::isEnabled('system', $this->plg_name) == false)
			return;

		// Quick check to decide whether to render the plugin or not
		if (strpos(JResponse::getBody(), '#disqus_thread') === false)
			return;

		// Load the plugin language file the proper way
		JPlugin::loadLanguage('plg_system_'.$this->plg_name, JPATH_ADMINISTRATOR);

		// Admin check
		if ($mainframe->isAdmin())
			return;

		// ----------------------------------- Get plugin parameters -----------------------------------
		$plugin = JPluginHelper::getPlugin('content', $this->plg_name);
		$pluginParams = version_compare(JVERSION, '1.6.0', 'lt') ? new JParameter($plugin->params) : new JRegistry($plugin->params);

		$disqusSubDomain = trim($pluginParams->get('disqusSubDomain', ''));
		$disqusLanguage = $pluginParams->get('disqusLanguage');

		if (!$disqusSubDomain)
		{
			// Quick check before we proceed
			return;
		}
		else
		{
			// Perform some parameter cleanups
			$disqusSubDomain = str_replace(array(
				'http://',
				'.disqus.com/',
				'.disqus.com'
			), array(
				'',
				'',
				''
			), $disqusSubDomain);
		}

		// Append head includes only when the document is in HTML mode
		if (JRequest::getCmd('format') == 'html' || JRequest::getCmd('format') == '')
		{
			$elementToGrab = '</body>';
			$htmlToInsert = "
				<!-- JoomlaWorks \"DISQUS Comments for Joomla!\" (v3.4) -->
				<script type=\"text/javascript\">
					//<![CDATA[
					var disqus_shortname = '{$disqusSubDomain}';
					var disqus_config = function(){
						this.language = '{$disqusLanguage}';
					};
					(function () {
						var s = document.createElement('script'); s.async = true;
						s.type = 'text/javascript';
						s.src = 'http://' + disqus_shortname + '.disqus.com/count.js';
						(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
					}());
					//]]>
				</script>
			";

			// Output
			$buffer = JResponse::getBody();
			$buffer = str_replace($elementToGrab, $htmlToInsert."\n\n".$elementToGrab, $buffer);
			JResponse::setBody($buffer);
		}

	} // END FUNCTION

} // END CLASS
