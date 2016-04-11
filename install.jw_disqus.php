<?php
/**
 * @version    3.6.x
 * @package    Disqus Comments (for Joomla)
 * @author     JoomlaWorks - http://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

if (version_compare(JVERSION, '1.6.0', 'lt'))
{
	jimport('joomla.installer.installer');
	$mainframe = JFactory::getApplication();
	$db = JFactory::getDBO();

	// Load language file
	$lang = JFactory::getLanguage();
	$lang->load('com_jw_disqus');

	// Set some variables
	$status = new stdClass;
	$src = $this->parent->getPath('source');

	// Install plugins
	$plugins = $this->manifest->getElementByPath('plugins');
	if (is_a($plugins, 'JSimpleXMLElement') && count($plugins->children()))
	{

		foreach ($plugins->children() as $plugin)
		{
			$pname = $plugin->attributes('plugin');
			$pgroup = $plugin->attributes('group');
			$path = $src.'/plugins/'.$pgroup.'/'.$pname;
			$installer = new JInstaller;
			$result = $installer->install($path);
			$status->plugins[] = array(
				'name' => $pname,
				'group' => $pgroup,
				'result' => $result
			);
			$query = "UPDATE #__plugins SET published=1 WHERE element=".$db->Quote($pname)." AND folder=".$db->Quote($pgroup);
			$db->setQuery($query);
			$db->query();
		}
	}

}
?>
<?php if (version_compare(JVERSION, '1.6.0', 'lt')): $rows = 0; ?>
<h2><?php echo JText::_('COM_JW_DISQUS_INSTALLATION_STATUS'); ?></h2>
<table class="adminlist">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('COM_JW_DISQUS_EXTENSION'); ?></th>
			<th width="30%"><?php echo JText::_('COM_JW_DISQUS_STATUS'); ?></th>
		</tr>
	</thead>
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
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
</table>
<?php endif; ?>