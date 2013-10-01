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

class DisqusHelper
{

	// Path overrides for MVC templating
	public static function getTemplatePath($pluginName, $file)
	{

		$mainframe = JFactory::getApplication();
		$p = new stdClass;
		$pluginGroup = 'content';

		if (file_exists(JPATH_SITE.'/templates/'.$mainframe->getTemplate().'/html/'.$pluginName.'/'.$file))
		{
			$p->file = JPATH_SITE.'/templates/'.$mainframe->getTemplate().'/html/'.$pluginName.'/'.$file;
			$p->http = JURI::root(true).'/templates/'.$mainframe->getTemplate().'/html/'.$pluginName.'/'.$file;
		}
		else
		{
			if (version_compare(JVERSION, '1.6.0', 'ge'))
			{
				// Joomla! 1.6+
				$p->file = JPATH_SITE.'/plugins/'.$pluginGroup.'/'.$pluginName.'/'.$pluginName.'/tmpl/'.$file;
				$p->http = JURI::root(true).'/plugins/'.$pluginGroup.'/'.$pluginName.'/'.$pluginName.'/tmpl/'.$file;
			}
			else
			{
				// Joomla! 1.5
				$p->file = JPATH_SITE.'/plugins/'.$pluginGroup.'/'.$pluginName.'/tmpl/'.$file;
				$p->http = JURI::root(true).'/plugins/'.$pluginGroup.'/'.$pluginName.'/tmpl/'.$file;
			}
		}
		return $p;
	}

} // end class
