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

class Com_jw_disqusInstallerScript
{
	public function postflight($type, $parent)
	{
		$db = JFactory::getDBO();
		$db->setQuery("UPDATE #__extensions SET enabled = 0 WHERE client_id = 1 AND element = ".$db->Quote($parent->get('element')));
		$db->query();
       	$status = new stdClass;
        $status->plugins = array();
        $src = $parent->getParent()->getPath('source');
        $manifest = $parent->getParent()->manifest;
        $plugins = $manifest->xpath('plugins/plugin');
        foreach ($plugins as $plugin)
        {
            $name = (string)$plugin->attributes()->plugin;
            $group = (string)$plugin->attributes()->group;
            $path = $src.'/plugins/'.$group.'/'.$name;
            $installer = new JInstaller;
            $result = $installer->install($path);
            if ($result)
            {
                if (JFile::exists(JPATH_SITE.'/plugins/'.$group.'/'.$name.'/'.$name.'.xml'))
                {
                    JFile::delete(JPATH_SITE.'/plugins/'.$group.'/'.$name.'/'.$name.'.xml');
                }
                JFile::move(JPATH_SITE.'/plugins/'.$group.'/'.$name.'/'.$name.'.j25.xml', JPATH_SITE.'/plugins/'.$group.'/'.$name.'/'.$name.'.xml');
            }
            $query = "UPDATE #__extensions SET enabled=1, ordering=99 WHERE type='plugin' AND element=".$db->Quote($name)." AND folder=".$db->Quote($group);
            $db->setQuery($query);
            $db->query();
            $status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
        }
        $this->installationResults($status);
       
	}
    public function uninstall($parent)
    {
        $db = JFactory::getDBO();
        $status = new stdClass;
        $status->plugins = array();
        $manifest = $parent->getParent()->manifest;
        $plugins = $manifest->xpath('plugins/plugin');
        foreach ($plugins as $plugin)
        {
            $name = (string)$plugin->attributes()->plugin;
            $group = (string)$plugin->attributes()->group;
            $query = "SELECT `extension_id` FROM #__extensions WHERE `type`='plugin' AND element = ".$db->Quote($name)." AND folder = ".$db->Quote($group);
            $db->setQuery($query);
            $extensions = $db->loadColumn();
            if (count($extensions))
            {
                foreach ($extensions as $id)
                {
                    $installer = new JInstaller;
                    $result = $installer->uninstall('plugin', $id);
                }
                $status->plugins[] = array('name' => $name, 'group' => $group, 'result' => $result);
            }
            
        }
        $this->uninstallationResults($status);
    }

    private function installationResults($status)
    {
        $language = JFactory::getLanguage();
        $language->load('com_jw_disqus');
        $rows = 0;
		?>
		<h2><?php echo JText::_('COM_JW_DISQUS_INSTALLATION_STATUS'); ?></h2>
		<table class="adminlist">
			<thead>
				<tr>
					<th class="title" colspan="2"><?php echo JText::_('COM_JW_DISQUS_EXTENSION'); ?></th>
					<th width="30%"><?php echo JText::_('COM_JW_DISQUS_STATUS'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row0">
					<td class="key" colspan="2"><?php echo JText::_('COM_JW_DISQUS_COMPONENT'); ?></td>
					<td><strong><?php echo JText::_('COM_JW_DISQUS_INSTALLED'); ?></strong></td>
				</tr>
				<?php if (count($status->plugins)): ?>
				<tr>
					<th><?php echo JText::_('COM_JW_DISQUS_PLUGIN'); ?></th>
					<th><?php echo JText::_('COM_JW_DISQUS_GROUP'); ?></th>
					<th></th>
				</tr>
				<?php foreach ($status->plugins as $plugin): ?>
				<tr class="row<?php echo (++ $rows % 2); ?>">
					<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
					<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
					<td><strong><?php echo ($plugin['result']) ? JText::_('COM_JW_DISQUS_INSTALLED') : JText::_('COM_JW_DISQUS_NOT_INSTALLED'); ?></strong></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}

	private function uninstallationResults($status)
	{
		$language = JFactory::getLanguage();
		$language->load('com_jw_disqus');
		$rows = 0;
		?>
		<h2><?php echo JText::_('COM_JW_DISQUS_REMOVAL_STATUS'); ?></h2>
		<table class="adminlist">
			<thead>
				<tr>
					<th class="title" colspan="2"><?php echo JText::_('COM_JW_DISQUS_EXTENSION'); ?></th>
					<th width="30%"><?php echo JText::_('COM_JW_DISQUS_STATUS'); ?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="3"></td>
				</tr>
			</tfoot>
			<tbody>
				<tr class="row0">
					<td class="key" colspan="2"><?php echo JText::_('COM_JW_DISQUS_COMPONENT'); ?></td>
					<td><strong><?php echo JText::_('COM_JW_DISQUS_REMOVED'); ?></strong></td>
				</tr>
				<?php if (count($status->plugins)): ?>
				<tr>
					<th><?php echo JText::_('COM_JW_DISQUS_PLUGIN'); ?></th>
					<th><?php echo JText::_('COM_JW_DISQUS_GROUP'); ?></th>
					<th></th>
				</tr>
				<?php foreach ($status->plugins as $plugin): ?>
				<tr class="row<?php echo (++ $rows % 2); ?>">
					<td class="key"><?php echo ucfirst($plugin['name']); ?></td>
					<td class="key"><?php echo ucfirst($plugin['group']); ?></td>
					<td><strong><?php echo ($plugin['result']) ? JText::_('COM_JW_DISQUS_REMOVED') : JText::_('COM_JW_DISQUS_NOT_REMOVED'); ?></strong></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
		<?php
	}

}
