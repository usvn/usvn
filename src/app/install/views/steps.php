<?php if ($step != 0) { ?>
<div id="steps">
	<table>
		<tr>
	<?php foreach ($installSteps as $i => $t) { ?>
			<td><img src="./medias/usvn/images/step_<?php echo $i; ?><?php if ($step != $i) echo '_alt'; ?>.png"></td>
	<?php } ?>
		</tr>
		<tr>
	<?php foreach ($installSteps as $i => $t) { ?>
			<td class="step<?php if ($step != $i) echo '_alt'; ?>">
			<?php echo T_($t); ?>
			</td>
	<?php } ?>
		</tr>
	</table>
</div>
<?php }; ?>