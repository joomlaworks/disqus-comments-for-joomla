<?php
/**
 * @version    3.7.0
 * @package    Disqus Comments (for Joomla)
 * @author     JoomlaWorks - https://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2018 JoomlaWorks Ltd. All rights reserved.
 * @license    https://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');
if (version_compare(JVERSION, '1.6.0', 'ge')) {
    jimport('joomla.html.parameter');
}

class plgSystemJw_disqus extends JPlugin
{
    // JoomlaWorks reference parameters
    public $plg_name = "jw_disqus";
    public $plg_copyrights_start = "\n\n<!-- JoomlaWorks \"Disqus Comments (for Joomla)\" (v3.7.0) starts here -->\n";
    public $plg_copyrights_end = "\n\n<!-- JoomlaWorks \"Disqus Comments (for Joomla)\" (v3.7.0) ends here -->\n";

    public function __construct(&$subject, $params)
    {
        parent::__construct($subject, $params);

        // Define the DS constant under Joomla! 3.0
        if (!defined('DS')) {
            define('DS', DIRECTORY_SEPARATOR);
        }
    }

    public function onAfterRender()
    {

        // API
        $app = JFactory::getApplication();
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
        if (!$itemid) {
            $itemid = 999999;
        }

        // Check if plugin is enabled
        if (JPluginHelper::isEnabled('system', $this->plg_name) == false) {
            return;
        }

        // Quick check to decide whether to render the plugin or not
        if (strpos(JResponse::getBody(), '#disqus_thread') === false) {
            return;
        }

        // Load the plugin language file the proper way
        JPlugin::loadLanguage('plg_system_'.$this->plg_name, JPATH_ADMINISTRATOR);

        // Admin check
        if ($app->isAdmin()) {
            return;
        }

        // ----------------------------------- Get plugin parameters -----------------------------------
        $plugin = JPluginHelper::getPlugin('content', $this->plg_name);
        $pluginParams = version_compare(JVERSION, '1.6.0', 'lt') ? new JParameter($plugin->params) : new JRegistry($plugin->params);

        $disqusSubDomain = trim($pluginParams->get('disqusSubDomain', ''));
        $disqusLanguage = $pluginParams->get('disqusLanguage');

        if (!$disqusSubDomain) {
            // Quick check before we proceed
            return;
        } else {
            // Perform some parameter cleanups
            $disqusSubDomain = str_replace(array(
                'http://',
                'https://',
                '.disqus.com/',
                '.disqus.com'
            ), array(
                '',
                '',
                '',
                ''
            ), $disqusSubDomain);
        }

        // Append to the <body> only when the document is in HTML mode
        if (JRequest::getCmd('format') == 'html' || JRequest::getCmd('format') == '') {
            $elementToGrab = '</body>';
            $htmlToInsert = "
                <!-- JoomlaWorks \"Disqus Comments (for Joomla)\" (v3.7.0) -->
                <script>var disqus_config = function(){this.language = '{$disqusLanguage}';};</script>
                <script id=\"dsq-count-scr\" src=\"//{$disqusSubDomain}.disqus.com/count.js\" async></script>
            ";

            // Output
            $buffer = JResponse::getBody();
            $buffer = str_replace($elementToGrab, $htmlToInsert."\n\n".$elementToGrab, $buffer);
            JResponse::setBody($buffer);
        }
    } // END FUNCTION
} // END CLASS
