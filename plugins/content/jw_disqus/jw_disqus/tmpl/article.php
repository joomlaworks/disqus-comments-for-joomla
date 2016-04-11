<?php
/**
 * @version    3.6.x
 * @package    Disqus Comments (for Joomla)
 * @author     JoomlaWorks - http://www.joomlaworks.net
 * @copyright  Copyright (c) 2006 - 2016 JoomlaWorks Ltd. All rights reserved.
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
		<a class="jwDisqusArticleCounterLink" href="#disqus_thread" data-disqus-identifier="<?php echo $output->disqusIdentifier; ?>"><?php echo JText::_("JW_DISQUS_VIEW_COMMENTS"); ?></a>
	</span>
	<div class="clr"></div>
</div>
<?php endif; ?>

<?php echo $row->text; ?>

<!-- DISQUS comments block -->
<div class="jwDisqusForm">
	<?php echo $output->comments; ?>
	<div id="jwDisqusFormFooter">
		<a target="_blank" href="http://disqus.com" class="dsq-brlink">
			<?php echo JText::_("JW_DISQUS_BLOG_COMMENTS_POWERED_BY"); ?> <span class="logo-disqus">DISQUS</span>
		</a>
		<a id="jwDisqusBackToTop" href="#startOfPage">
			<?php echo JText::_("JW_DISQUS_BACK_TO_TOP"); ?>
		</a>
		<div class="clr"></div>
	</div>
</div>

<div class="clr"></div>
