<section id="<?php $tpl->ID; ?>" class="field <?php $tpl->CLASS; ?>" style="<?php $tpl->STYLE; ?>">
	<div class="message"></div>
	<section class="field-container">
		<?php if ($tpl->get('LABELPOSITION')=='left'): ?>
		<label for="<?php $tpl->ID; ?>-field"><?php $tpl->LABEL; ?>:</label>
		<?php endif; ?>
		<?php $tpl->EXTENDED; ?>
		<?php if ($tpl->get('LABELPOSITION')=='right'): ?>
		<label for="<?php $tpl->ID; ?>-field"><?php $tpl->LABEL; ?></label>
		<?php endif; ?>
		<div class="icon"></div>
	</section>
</section>