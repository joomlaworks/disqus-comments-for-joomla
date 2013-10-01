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

?>

<?php echo $row->text; ?>

<!-- DISQUS comments counter and anchor link -->
<a class="jwDisqusListingCounterLink" href="<?php echo $output->itemURL; ?>#disqus_thread" title="<?php echo JText::_("JW_DISQUS_ADD_A_COMMENT"); ?>" data-disqus-identifier="<?php echo $output->disqusIdentifier; ?>">
	<?php echo JText::_("JW_DISQUS_ADD_A_COMMENT"); ?>
</a>
