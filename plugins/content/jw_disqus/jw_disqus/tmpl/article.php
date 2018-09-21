<?php
/**
 * @version    3.7.0
 * @package    Disqus Comments (for Joomla)
 * @author     JoomlaWorks - https://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2018 JoomlaWorks Ltd. All rights reserved.
 * @license    GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die;

?>

<a id="startOfPage"></a>

<?php if($disqusArticleCounter): ?>
<!-- DISQUS comments counter and anchor link -->
<div class="jwDisqusArticleCounter">
    <span>
        <a class="jwDisqusArticleCounterLink" href="<?php echo $output->itemURL; ?>#disqus_thread" title="<?php echo JText::_("JW_DISQUS_ADD_A_COMMENT"); ?>" data-disqus-identifier="<?php echo $output->disqusIdentifier; ?>">
            <?php echo JText::_("JW_DISQUS_VIEW_COMMENTS"); ?>
        </a>
    </span>
    <div class="clr"></div>
</div>
<?php endif; ?>

<?php echo $row->text; ?>

<!-- DISQUS comments block -->
<div class="jwDisqusForm">
    <?php echo $output->comments; ?>
    <div id="jwDisqusFormFooter">
        <a id="jwDisqusBackToTop" href="#startOfPage">
            <?php echo JText::_("JW_DISQUS_BACK_TO_TOP"); ?>
        </a>
        <div class="clr"></div>
    </div>
</div>

<div class="clr"></div>
