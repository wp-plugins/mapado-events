<?php
/**
 * Admin
 * Settings page TPL
 */
?>

<div id="mapado-settings-page" class="mapado-settings-page">
	<h1>Mapado options</h1>

	<!-- Notifications -->
	<?php if ( !empty($vars['notification']) ) : ?>
		<div id="mapado-settings-updated" class="mapado-notifications <?php echo $vars['notification']['state'] ?> settings-error"> 
			<p><strong><?php echo $vars['notification']['text'] ?></strong></p>
		</div>
	<?php endif; ?>



	<!-- Mapado API settings form -->
	<form id="mapado_settings_form" action="" method="POST">
	<table id="mapado_settings_table" class="mapado_table widefat">
		<thead>
			<tr>
				<th colspan="2"><strong>Réglages API</strong></th>
				<th style="text-align:right"><a href="http://mag.mapado.com/api-keys-mapado/" title="Trouver ma clé API" target="_blank">Où trouver ces informations ?</a></th>
			</tr>
		</thead>

		<tfoot>
			<tr class="alternate">
				<td colspan="3" style="text-align:right">
					<input type="submit" name="mapado_settings_submit" id="mapado_settings_submit" class="button button-primary" value="Enregistrer les modifications">
				</td>
			</tr>
		</tfoot>

		<tbody>
			<tr>
				<td>
					<label for="mapado_api_id">ID</label>
					<input name="mapado_api_id" id="mapado_api_id" type="text" step="1" min="1" value="<?php if ( !empty($vars['api']['id']) ) echo $vars['api']['id'] ?>" style="width:100%">
				</td>

				<td>
					<label for="mapado_api_secret">Code secret</label>
					<input name="mapado_api_secret" id="mapado_api_secret" type="text" step="1" min="1" value="<?php if ( !empty($vars['api']['secret']) ) echo $vars['api']['secret'] ?>" style="width:100%">
				</td>

				<td>
					<label for="mapado_api_auth">Jeton d'autentification</label>
					<input name="mapado_api_auth" id="mapado_api_auth" type="text" step="1" min="1" value="<?php if ( $vars['auth'] ) echo $vars['auth'] ?>" style="width:100%">
				</td>
			</tr>
		</tbody>
	</table>
	</form>

	<!-- Settings activity single page -->
	<form id="mapado_add_settings_form" action="" method="POST">
	<table id="mapado_add_settings_table" class="mapado_table widefat">
		<thead>
			<tr>
				<th colspan="3"><strong>Paramètres supplémentaires</strong></th>
			</tr>
		</thead>

		<tfoot>
			<tr class="alternate">
				<td colspan="3" style="text-align:right">
					<input type="submit" name="mapado_add_settings_submit" id="mapado_add_settings_submit" class="button button-primary" value="Enregistrer les modifications">
				</td>
			</tr>
		</tfoot>

		<tbody>
			<tr>
				<td colspan="3">
					<input name="mapado_widget" id="mapado_widget" type="checkbox" value="1" <?php if ( !empty($vars['settings']['widget']) ) echo 'checked="checked"' ?>>
					<label for="mapado_widget">Afficher les informations principale dans un widget ?</label>
				</td>
			</tr>
		</tbody>
	</table>
	</form>

	<!-- AJAX user lists notifications -->
	<div id="mapado-userlists-notifications" class="mapado-notifications updated settings-error" style="display:none"></div>

	<!-- AJAX user lists -->
	<table id="mapado_user_lists" class="mapado_table widefat">
		<thead><tr>
			<th colspan="2">
				<strong>Mes listes</strong>
			</th>
			<th style="text-align:right">
				<?php if ( !empty($vars['api']) ) : ?>
					<a href="#" id="mapado_user_lists_refresh"><strong>Actualiser</strong></a>
				<?php endif; ?>
			</th>
		</tr></thead>

		<tbody></tbody>
	</table>



	<?php /* For the AJAX call; Inform that API settings are filled */ ?>
	<script>
		var ajaxUserLists	= {
			'load'	: false,
			'msg'	: 'Vous devez renseigner vos identifiants API'
		}

		<?php if ( !empty($vars['api']) && !empty($vars['api']['id']) && !empty($vars['api']['secret']) && !empty($vars['auth']) ) : ?>
		ajaxUserLists	= {
			'load'	: true,
			'msg'	: 'Chargement...'
		}
		<?php endif; ?>
	</script>
</div>