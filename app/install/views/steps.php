<?php if ($step != 0) { ?>
<div id="steps">
	<table>
		<tr>
			<td><img src="./medias/usvn/images/step_1<?php if ($step != 1) echo '_alt'; ?>.png"></td>
			<td><img src="./medias/usvn/images/step_2<?php if ($step != 2) echo '_alt'; ?>.png"></td>
			<td><img src="./medias/usvn/images/step_3<?php if ($step != 3) echo '_alt'; ?>.png"></td>
			<td><img src="./medias/usvn/images/step_4<?php if ($step != 4) echo '_alt'; ?>.png"></td>
			<td><img src="./medias/usvn/images/step_5<?php if ($step != 5) echo '_alt'; ?>.png"></td>
			<td><img src="./medias/usvn/images/step_6<?php if ($step != 6) echo '_alt'; ?>.png"></td>
			<td><img src="./medias/usvn/images/step_7<?php if ($step != 7) echo '_alt'; ?>.png"></td>
			<td><img src="./medias/usvn/images/step_8<?php if ($step != 8) echo '_alt'; ?>.png"></td>
		</tr>
		<tr>
			<td class="step<?php if ($step != 1) echo '_alt'; ?>"><?= T_("System check") ?></td>
			<td class="step<?php if ($step != 2) echo '_alt'; ?>"><?= T_("Language selection") ?></td>
			<td class="step<?php if ($step != 3) echo '_alt'; ?>"><?= T_("Agreement of the licence") ?></td>
			<td class="step<?php if ($step != 4) echo '_alt'; ?>"><?= T_("Configure USVN") ?></td>
			<td class="step<?php if ($step != 5) echo '_alt'; ?>"><?= T_("Database installation") ?></td>
			<td class="step<?php if ($step != 6) echo '_alt'; ?>"><?= T_("Create administrator user") ?></td>
			<td class="step<?php if ($step != 7) echo '_alt'; ?>"><?= T_("Check for new version") ?></td>
			<td class="step<?php if ($step != 8) echo '_alt'; ?>"><?= T_("Installation is over") ?></td>
		</tr>
	</table>
</div>
<?php }; ?>