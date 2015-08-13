<table id="mapado_option_list_table" class="mapado_table widefat striped">
    <thead>
    <tr>
        <th colspan="2"><strong>Options des listes d'événements</strong></th>
    </tr>
    </thead>

    <tbody>

    <!-- Column max -->
    <tr>
        <td>
            <span class="mapado_table__label">Nombre de colonnes maximum </span>
        </td>
        <td>
            <?php foreach ($vars['card_column_max']['options'] as $key => $val) : ?>
                <label class="mapado-input-radio">
                    <input type="radio" name="mapado_card_column_max" value="<?php echo $key ?>"
                        <?php if ($vars['card_column_max']['value'] == $key) echo 'checked="checked"'; ?>
                        >
                    <span>
                        <img src="<?php echo plugins_url('mapado-events/assets/images/'.$key.'-column.png'); ?>" alt="<?php echo $val ?>" title="<?php echo $val ?>" />
                        <img src="<?php echo plugins_url('mapado-events/assets/images/'.$key.'-column-active.png'); ?>" alt="<?php echo $val ?>" title="<?php echo $val ?>" />
                    </span>
                </label>
            <?php endforeach; ?>
        </td>
    </tr>

    <!-- Event par page -->
    <tr>
        <td>
            <span class="mapado_table__label">Nombre d'événements par page </span>
        </td>
        <td>
            <?php foreach ($vars['perpage']['options'] as $val) : ?>
                <label class="mapado-input-radio">
                    <input type="radio" name="mapado_perpage" value="<?php echo $val ?>"
                        <?php if ($vars['perpage']['value'] == $val) echo 'checked="checked"'; ?>
                        >
                    <span><?php echo $val ?></span>
                </label>
            <?php endforeach; ?>
        </td>
    </tr>

    <!-- Image position -->
    <tr>
        <td>
            <span class="mapado_table__label">Position des images </span>
        </td>
        <td>
            <?php foreach ($vars['card_thumb_position']['options'] as $key => $val) : ?>
                <label class="mapado-input-radio">
                    <input type="radio" name="mapado_card_thumb_position" value="<?php echo $key ?>"
                        <?php if ($vars['card_thumb_position']['value'] == $key) echo 'checked="checked"'; ?>
                        >
                    <span>
                        <img src="<?php echo plugins_url('mapado-events/assets/images/img-'.$key.'.png'); ?>" alt="<?php echo $val ?>" title="<?php echo $val ?>" />
                        <img src="<?php echo plugins_url('mapado-events/assets/images/img-'.$key.'-active.png'); ?>" alt="<?php echo $val ?>" title="<?php echo $val ?>" />
                    </span>
                </label>
            <?php endforeach; ?>
        </td>
    </tr>

    <!-- Image orientation -->
    <tr>
        <td>
            <span class="mapado_table__label">Orientation des images </span>
        </td>
        <td>
            <?php foreach ($vars['card_thumb_orientation']['options'] as $key => $val) : ?>
                <label class="mapado-input-radio">
                    <input type="radio" name="mapado_card_thumb_orientation" value="<?php echo $key ?>"
                        <?php if ($vars['card_thumb_orientation']['value'] == $key) echo 'checked="checked"'; ?>
                        >
                    <span><?php echo $val ?></span>
                </label>
            <?php endforeach; ?>
        </td>
    </tr>

    <!-- Image size -->
    <tr>
        <td>
            <span class="mapado_table__label">Taille des images</span>
        </td>
        <td>
            <?php foreach ($vars['card_thumb_size']['options'] as $key => $val) : ?>
                 <label class="mapado-input-radio">
                    <input type="radio" name="mapado_card_thumb_size" value="<?php echo $key ?>"
                        <?php if ($vars['card_thumb_size']['value'] == $key) echo 'checked="checked"'; ?>
                        >
                    <span><?php echo $val ?></span>
                </label>
            <?php endforeach; ?>
        </td>
    </tr>

    <!-- List sort setting -->
    <tr>
        <td>
            <span class="mapado_table__label">Tri des listes</span>
        </td>
        <td>
            <label class="mapado-input-radio">
                <input type="radio" name="mapado_list_sort" value="pertinence"
                    <?php if (empty($vars['settings']['list_sort']) || $vars['settings']['list_sort'] != 'future_date') echo 'checked="checked"'; ?>
                    >
                <span>Pertinence</span>
            </label>
            <label class="mapado-input-radio">
                <input type="radio" name="mapado_list_sort" value="future_date"
                    <?php if (!empty($vars['settings']['list_sort']) && $vars['settings']['list_sort'] == 'future_date') echo 'checked="checked"'; ?>
                    >
                <span>Date</span>
            </label>
        </td>
    </tr>

    <!-- Display past events setting -->
    <tr>
        <td>
            <label class="mapado_table__label" for="mapado_past_events">Afficher les événements passés ?</label>
        </td>
        <td>
            <input name="mapado_past_events" id="mapado_past_events" type="checkbox"
                   value="1" <?php if (!empty($vars['settings']['past_events'])) echo 'checked="checked"' ?>>
        </td>
    </tr>

    <!-- Advanced settings -->
    <tr>
        <td colspan="2"><a href="javascript:;" class="mpd-table-dropdown__trigger">Paramètres avancés</a></td>
    </tr>
    <tr class="mpd-table-dropdown__body">
        <td colspan="2">
            <table id="mapado_option_list_advanced_table" class="mapado_table widefat striped">

                <!-- Template -->
                <tr>
                    <td colspan="2">
                        <button type="button" class="button button-delete right js-mapado_template_reset">
                            Réinitialiser
                        </button>
                        <label class="mapado_table__label" for="mapado_card_template">Modèle d'affichage</label>
                        <textarea name="mapado_card_template" id="mapado_card_template" class="js-mapado_template_input"><?php
                            echo $vars['settings']->getValue('card_template')
                        ?></textarea>
                        <div style="display: none;" class="js-mapado_template_default"><?php
                            echo $vars['settings']->getDefaultValue('card_template')
                        ?></div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
</table>