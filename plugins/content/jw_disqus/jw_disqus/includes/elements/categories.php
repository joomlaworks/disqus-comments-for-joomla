<?php
/**
 * @version		3.4
 * @package		DISQUS Comments for Joomla! (package)
 * @author		JoomlaWorks - http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

// Create a category selector
if (version_compare(JVERSION, '1.6.0', 'ge'))
{
	class JFormFieldCategories extends JFormField
	{

		var $type = 'categories';

		function getInput()
		{
			$categories = array();
			$categories = JHtml::_('category.options', 'com_content');

			// Create the 'all categories' listing
			$option = new JObject;
			$option->value = '';
			$option->text = JText::_('JW_DISQUS_SELECT_ALL_CATEGORIES');
			array_unshift($categories, $option);

			// Output
			return JHTML::_('select.genericlist', $categories, $this->name.'[]', 'class="inputbox" style="width:220px;" multiple="multiple" size="12"', 'value', 'text', $this->value);
		}

	}

}
else
{

	class JElementCategories extends JElement
	{

		var $_name = 'categories';

		function fetchElement($name, $value, &$node, $control_name)
		{
			$db = JFactory::getDBO();
			$query = 'SELECT * FROM #__sections WHERE published=1';
			$db->setQuery($query);
			$sections = $db->loadObjectList();

			$categories = array();

			// Create the 'all categories' listing
			$categories[0]->id = '';
			$categories[0]->title = JText::_("JW_DISQUS_SELECT_ALL_CATEGORIES");

			// Create category listings, grouped by section
			foreach ($sections as $section)
			{
				$optgroup = JHTML::_('select.optgroup', $section->title, 'id', 'title');
				$query = 'SELECT id,title FROM #__categories WHERE published=1 AND section='.$section->id;
				$db->setQuery($query);
				$results = $db->loadObjectList();
				array_push($categories, $optgroup);
				foreach ($results as $result)
				{
					array_push($categories, $result);
				}
			}

			// Create the 'Uncategorised' listing
			$optgroup = JHTML::_('select.optgroup', JText::_("JW_DISQUS_UNCATEGORISED"), 'id', 'title');
			array_push($categories, $optgroup);
			$uncategorised = array();
			$uncategorised['id'] = '0';
			$uncategorised['title'] = JText::_("JW_DISQUS_UNCATEGORISED");
			array_push($categories, $uncategorised);

			// Output
			return JHTML::_('select.genericlist', $categories, ''.$control_name.'['.$name.'][]', 'class="inputbox" style="width:90%;" multiple="multiple" size="12"', 'id', 'title', $value);
		}

	}

}
