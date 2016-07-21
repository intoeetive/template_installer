<?=form_open('C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=template_installer'.AMP.'method=install');?>

<div id="ti_warning">
	<h4><?=lang('warning')?></h4>
    <?=lang('warning_text')?>
</div>
<div class="clear_left shun"></div>

<p><?=lang('path_to_src')?>
<br />
<?=form_input('dir_path', $dir_path)?></p>


<p><?=form_submit('submit', lang('install_templates'), 'class="submit"')?></p>

<div class="clear_left shun"></div>
<div>
	<h4><?=lang('instructions')?></h4>
    <?=lang('instructions_text')?>
</div>
