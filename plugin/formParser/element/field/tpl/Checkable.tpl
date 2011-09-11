<?php if ($tpl->get('GROUPED')):?>
<label><?php $tpl->LABEL; ?>
<?php endif; ?>
<input id="<?php $tpl->ID; ?>-field" name="<?php $tpl->REF; ?>" type="<?php $tpl->TYPE; ?>" placeholder="<?php $tpl->HELPER; ?>" value="<?php $tpl->VALUE; ?>" <?php $tpl->CHECKED; ?> />
<?php if ($tpl->get('GROUPED')):?>
</label>
<?php endif; ?>