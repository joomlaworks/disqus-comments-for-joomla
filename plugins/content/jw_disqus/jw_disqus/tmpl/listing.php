<?php
/**
 * @version    3.7.0
 * @package    Disqus Comments (for Joomla)
 * @author     JoomlaWorks - https://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2018 JoomlaWorks Ltd. All rights reserved.
 * @license    https://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die;

?>

<?php echo $row->text; ?>

<!-- Disqus comments counter and anchor link -->
<a class="jwDisqusListingCounterLink" href="<?php echo $output->itemURL; ?>#disqus_thread" title="<?php echo JText::_("JW_DISQUS_ADD_A_COMMENT"); ?>">
    <?php echo JText::_("JW_DISQUS_ADD_A_COMMENT"); ?>
</a>
