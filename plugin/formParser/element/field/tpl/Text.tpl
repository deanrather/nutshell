<?php if ($tpl->get('GROUPED')):?>
<label><?php $tpl->LABEL; ?>
<?php endif; ?>
<?php if (!$tpl->get('MULTILINE')): ?>
	<input id="<?php $tpl->ID; ?>-field" name="<?php $tpl->REF; ?>" type="text" placeholder="<?php $tpl->HELPER; ?>" value="<?php $tpl->VALUE; ?>" />
<?php else: ?>
	<textarea id="<?php $tpl->ID; ?>-field" name="<?php $tpl->REF; ?>" placeholder="<?php $tpl->HELPER; ?>"><?php $tpl->VALUE; ?></textarea>
<?php endif; ?>
<?php if ($tpl->get('GROUPED')):?>
</label>
<?php endif; ?>