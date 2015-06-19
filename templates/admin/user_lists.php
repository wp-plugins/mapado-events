<?php
/**
 * Mapado Plugin
 * Admin AJAX user list TPL
 */
?>

<?php foreach ( $vars['user_lists'] as $k => $list ) :
	$exist	= false;

	if ( !empty($vars['imported_lists']) )
		$exist	= array_key_exists( $list->getUuid(), $vars['imported_lists'] );

	/* To alternate background color line */
	$class	= '';
	if ( ($k % 2) == 0 )
		$class	= ' alternate';
?>
	<tr id="list-<?php echo $list->getUuid() ?>" data-uuid="<?php echo $list->getUuid() ?>" class="list<?php echo $class ?>">
		<td>
			<?php if ( $exist ) : ?>
				<a href="<?php echo MapadoUtils::getUserListUrl( $vars['imported_lists'][$list->getUuid()] ) ?>" title="" target="_blank"><?php echo $list->getTitle() ?></a>
			<?php else :
				echo $list->getTitle(); 
			endif; ?>
		</td>
		<td>
			<label for="list-slug-<?php echo $list->getUuid() ?>"></label>
			<span><strong>Choisir le slug :</strong> <?php bloginfo( 'url' ) ?>/</span>
			<input type="text" id="list-slug-<?php echo $list->getUuid() ?>" name="list-slug-<?php echo $list->getUuid() ?>" placeholder="slug-de-ma-liste" class="list-slug" value="<?php if ( $exist ) echo $vars['imported_lists'][$list->getUuid()] ?>">
			<input type="hidden" id="list-title-<?php echo $list->getUuid() ?>" name="list-title-<?php echo $list->getUuid() ?>" class="list-title" value="<?php echo $list->getTitle() ?>">
		</td>
		<td style="text-align:right">
			<?php if ( $exist ) : ?>
				<a href="" class="button button-delete">Supprimer la liste</a>
			<?php else : ?>
				<a href="" class="button button-primary">Importer la liste</a>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>