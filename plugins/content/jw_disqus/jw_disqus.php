<?php
/**
 * @version		3.5
 * @package		DISQUS Comments for Joomla! (package)
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2014 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');
if (version_compare(JVERSION, '1.6.0', 'ge'))
{
	jimport('joomla.html.parameter');
}

class plgContentJw_disqus extends JPlugin
{

	// JoomlaWorks reference parameters
	var $plg_name = "jw_disqus";
	var $plg_copyrights_start = "\n\n<!-- JoomlaWorks \"DISQUS Comments for Joomla!\" (v3.5) starts here -->\n";
	var $plg_copyrights_end = "\n\n<!-- JoomlaWorks \"DISQUS Comments for Joomla!\" (v3.5) ends here -->\n";

	function plgContentJw_disqus(&$subject, $params)
	{
		parent::__construct($subject, $params);
		
		// Define the DS constant under Joomla! 3.0
		if (!defined('DS'))
		{
			define('DS', DIRECTORY_SEPARATOR);
		}
	}

	// Joomla! 1.5
	function onPrepareContent(&$row, &$params, $page = 0)
	{
		$this->renderDisqus($row, $params, $page = 0);
	}

	// Joomla! 1.6+
	function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		$this->renderDisqus($row, $params, $page = 0);
	}

	// The main function
	function renderDisqus(&$row, &$params, $page)
	{

		// API
		$mainframe = JFactory::getApplication();
		$document = JFactory::getDocument();
		$user = JFactory::getUser();

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
		if (JPluginHelper::isEnabled('content', $this->plg_name) == false)
			return;

		// Load the plugin language file the proper way
		JPlugin::loadLanguage('plg_content_'.$this->plg_name, JPATH_ADMINISTRATOR);

		// Simple checks before parsing the plugin
		$properties = get_object_vars($row);
		if (!array_key_exists('catid', $properties))
			return;
		if (version_compare(JVERSION, '1.6.0', 'lt'))
		{
			if (!array_key_exists('sectionid', $properties))
				return;
		}
		if (!$row->id || $option == 'com_rokdownloads')
			return;

		// ----------------------------------- Get plugin parameters -----------------------------------
		$plugin = JPluginHelper::getPlugin('content', $this->plg_name);
		$pluginParams = version_compare(JVERSION, '1.6.0', 'lt') ? new JParameter($plugin->params) : new JRegistry($plugin->params);

		$disqusSubDomain = trim($pluginParams->get('disqusSubDomain', ''));
		$disqusLanguage = $pluginParams->get('disqusLanguage');
		$selectedCategories = $pluginParams->get('selectedCategories', '');
		$selectedMenus = $pluginParams->get('selectedMenus', '');
		$disqusListingCounter = $pluginParams->get('disqusListingCounter', 1);
		$disqusArticleCounter = $pluginParams->get('disqusArticleCounter', 1);
		$disqusDevMode = $pluginParams->get('disqusDevMode', 0);

		// External parameter for controlling plugin layout within modules
		if (!$params)
			$params = version_compare(JVERSION, '1.6.0', 'lt') ? new JParameter(null) : new JRegistry(null);
		$parsedInModule = $params->get('parsedInModule');

		if (!$disqusSubDomain)
		{
			// Quick check before we proceed
			// Fix: Add notice only one time in page...
			if (!isset($this->noticeRaised))
			{
				$this->noticeRaised = true;
				JError::raiseNotice('', JText::_('JW_DISQUS_PLEASE_ENTER_YOUR_DISQUS_SUBDOMAIN'));
			}
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

		// ----------------------------------- Before plugin render -----------------------------------

		// Get the current category
		$currectCategory = $row->catid;

		// Define plugin category restrictions
		$selectedCategories = (array)$selectedCategories;
		if (sizeof($selectedCategories) == 1 && $selectedCategories[0] == '')
		{
			$categories[] = $currectCategory;
		}
		else
		{
			$categories = $selectedCategories;
		}

		// Define plugin menu restrictions
		$selectedMenus = (array)$selectedMenus;
		if (sizeof($selectedMenus) == 1 && $selectedMenus[0] == '')
		{
			$menus[] = $itemid;
		}
		else
		{
			$menus = $selectedMenus;
		}

		// ----------------------------------- Prepare elements -----------------------------------

		// Includes
		require_once (dirname(__FILE__).DS.$this->plg_name.DS.'includes'.DS.'helper.php');
		require_once (JPATH_SITE.DS.'components'.DS.'com_content'.DS.'helpers'.DS.'route.php');

		// Output object
		$output = new stdClass;

		// Article URLs
		$websiteURL = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off") ? "https://".$_SERVER['HTTP_HOST'] : "http://".$_SERVER['HTTP_HOST'];

		if (version_compare(JVERSION, '1.6.0', 'ge'))
		{
			$levels = $user->getAuthorisedViewLevels();
			if (in_array($row->access, $levels))
			{
				if ($view == 'article')
				{
					$itemURL = $row->readmore_link;
				}
				else
				{
					$itemURL = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid));
				}
			}
		}
		else
		{
			$itemURL = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug, $row->sectionid));
		}

		$itemURLbrowser = explode("#", $websiteURL.$_SERVER['REQUEST_URI']);
		$itemURLbrowser = $itemURLbrowser[0];

		// Article URL assignments
		$output->itemURL = $websiteURL.$itemURL;
		$output->itemURLrelative = $itemURL;
		$output->itemURLbrowser = $itemURLbrowser;
		$output->disqusIdentifier = substr(md5($disqusSubDomain), 0, 10).'_id'.$row->id;

		// Fetch elements specific to the "article" view only
		if (in_array($currectCategory, $categories) && in_array($itemid, $menus) && $option == 'com_content' && $view == 'article')
		{
			$output->comments = "
			<div id=\"disqus_thread\"></div>
			<script type=\"text/javascript\">
				//<![CDATA[
				var disqus_shortname = '".$disqusSubDomain."';
				var disqus_url = '".$output->itemURL."';
				var disqus_identifier = '".substr(md5($disqusSubDomain), 0, 10)."_id".$row->id."';
				var disqus_developer = '".$disqusDevMode."';
				var disqus_config = function(){
					this.language = '{$disqusLanguage}';
				};
				(function() {
					var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
					dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
					(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
				})();
				//]]>
			</script>
			<noscript>
				<a href=\"http://".$disqusSubDomain.".disqus.com/?url=ref\">".JText::_("JW_DISQUS_VIEW_THE_DISCUSSION_THREAD")."</a>
			</noscript>
			";
		}

		// ----------------------------------- Render the output -----------------------------------
		if (in_array($currectCategory, $categories) && in_array($itemid, $menus))
		{

			if (!defined('JW_DISQUS'))
				define('JW_DISQUS', true);

			// Append head includes only when the document is in HTML mode
			if (JRequest::getCmd('format') == 'html' || JRequest::getCmd('format') == '')
			{

				// CSS
				$plgCSS = DisqusHelper::getTemplatePath($this->plg_name, 'css/template.css');
				$plgCSS = $plgCSS->http;

				$document->addStyleSheet($plgCSS);

				// JS
				if (version_compare(JVERSION, '1.6.0', 'ge'))
				{
					JHtml::_('behavior.framework');
				}
				else
				{
					JHTML::_('behavior.mootools');
				}
			}

			if (($option == 'com_content' && $view == 'article') && $parsedInModule != 1)
			{

				// Fetch the template
				ob_start();
				$dsqArticlePath = DisqusHelper::getTemplatePath($this->plg_name, 'article.php');
				$dsqArticlePath = $dsqArticlePath->file;
				include ($dsqArticlePath);
				$getArticleTemplate = $this->plg_copyrights_start.ob_get_contents().$this->plg_copyrights_end;
				ob_end_clean();

				// Output
				$row->text = $getArticleTemplate;

			}
			else if ($disqusListingCounter && (($option == 'com_content' && ($view == 'frontpage' || $view == "featured" || $view == 'section' || $view == 'category')) || $parsedInModule == 1))
			{

				// Set '$row->text' to '$row->introtext' for J!1.6+
				if (version_compare(JVERSION, '1.6.0', 'ge'))
					$row->text = $row->introtext;

				// Fetch the template
				ob_start();
				$dsqListingPath = DisqusHelper::getTemplatePath($this->plg_name, 'listing.php');
				$dsqListingPath = $dsqListingPath->file;
				include ($dsqListingPath);
				$getListingTemplate = $this->plg_copyrights_start.ob_get_contents().$this->plg_copyrights_end;
				ob_end_clean();

				// Output
				$row->text = $getListingTemplate;

			}

		} // END IF

	} // END FUNCTION

} // END CLASS
