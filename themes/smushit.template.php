<?php

/**
 * Displays the list of attachments and current smushed status
 * Shows results of a smushit run on the selected files
 */
function template_attachment_smushit()
{
	global $context, $txt, $settings;

	if ($context['completed'])
	{
		echo '
	<div id="manage_attachments">
		<h2 class="category_header">', $txt['smushit_attachments_complete'], '</h2>
		<div class="content">
			<p>', $txt['smushit_attachments_complete_desc'], '</p>
			<table class="table_grid">
				<thead>
					<tr class="table_header">
						<th scope="col"></th>
						<th scope="col">#</th>
						<th scope="col">', $txt['attachment_name'], '</th>
						<th scope="col">', $txt['smushit_attachments'], '</th>
					</tr>
				</thead>
				<tbody>';

		// Loop through each result reporting the status
		$i = 1;
		$savings = 0;

		if (isset($context['smushit_results']))
		{
			foreach ($context['smushit_results'] as $attach_id => $result)
			{
				$attach_id = str_replace('+', '', $attach_id, $count);
				list($filename, $result) = explode('|', $result, 2);
				echo '
					<tr>
						<td class="standard_row">
							<img src="' . $settings['images_url'] . '/icons/' . (($count != 0) ? 'field_valid' : 'field_invalid') . '.png' . '"/>
						</td>
						<td class="standard_row">' . $i . '</td>
						<td class="standard_row">[' . $attach_id . '] ' . $filename . '</td>
						<td class="standard_row">' . $result . '</td>
					</tr>';
				$i++;

				// Keep track of how great we are
				if ($count != 0 && preg_match('~.*\((\d*)\).*~', $result, $thissavings))
					$savings += $thissavings[1];
			}
		}

		// Show the total savings in some form the user will understand
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$savings = max($savings, 0);
		$pow = floor(($savings ? log($savings) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$savings /= pow(1024, $pow);

		echo '
				</tbody>
			</table>
			<br />
			<p>
				<strong>' . $txt['smushit_attachments_savings'] . ' ' . round($savings, 2) . ' ' . $units[$pow] . '</strong>
			</p>
		</div>
	</div>';
	}
}

/**
 * Maintenance section, injected in the layer via ->add
 */
function template_smushit_maintain_below()
{
	global $txt, $scripturl, $context;

	echo '
	<h2 class="category_header">', $txt['smushit_attachment_check'], '</h2>
	<div id="manage_boards" class="content">
		<form action="', $scripturl, '?action=admin;area=manageattachments;sa=smushit;', $context['session_var'], '=', $context['session_id'], '" method="post" accept-charset="UTF-8">
			<p>', $txt['smushit_attachment_check_desc'], '</p>
			<br />
			', $txt['smushit_attachments_age'], ' <input type="text" name="smushitage" value="25" size="4" class="input_text" /> ', $txt['days_word'], '<br />
			<input type="submit" name="submit" value="', $txt['smushit_attachment_now'], '" class="right_submit" />
		</form>
	</div>';
}