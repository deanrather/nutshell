<?php if (!$tpl->get('MULTILINE')): ?>
	<input id="<?php $tpl->ID; ?>-field" name="<?php $tpl->REF; ?>" type="text" placeholder="<?php $tpl->HELPER; ?>" />
<?php else: ?>
	<textarea id="<?php $tpl->ID; ?>-field" name="<?php $tpl->REF; ?>" placeholder="<?php $tpl->HELPER; ?>"></textarea>
<?php endif; ?>
