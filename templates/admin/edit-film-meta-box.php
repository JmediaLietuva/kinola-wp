<?php /* @var $film \Kinola\KinolaWp\Film */ ?>
<table>
    <tr>
        <td class="kinola-admin__table_field_name">
            <?php _ex( 'Title', 'Admin', 'kinola' ); ?>
        </td>
        <td class="kinola-admin__table_field_value">
            <?php echo $film->get_field( 'title' ); ?>
        </td>
    </tr>
    <tr>
        <td class="kinola-admin__table_field_name">
            <?php
            _ex( 'Original title', 'Admin', 'kinola' ); ?>
        </td>
        <td class="kinola-admin__table_field_value">
            <?php echo $film->get_field( 'title_original' ); ?>
        </td>
    </tr>
    <tr>
        <td class="kinola-admin__table_field_name">
            <?php _ex( 'Duration', 'Admin', 'kinola' ); ?>
        </td>
        <td class="kinola-admin__table_field_value">
            <?php echo $film->get_field( 'duration' ); ?><?php _ex( 'min', 'Admin', 'kinola' ); ?>
        </td>
    </tr>
    <tr>
        <td class="kinola-admin__table_field_name">
            <?php _ex( 'Year', 'Admin', 'kinola' ); ?>
        </td>
        <td class="kinola-admin__table_field_value">
            <?php echo $film->get_field( 'year' ); ?>
        </td>
    </tr>
    <tr>
        <td class="kinola-admin__table_field_name">
            <?php _ex( 'Rating', 'Admin', 'kinola' ); ?>
        </td>
        <td class="kinola-admin__table_field_value">
            <?php echo $film->get_field( 'rating' ); ?>
        </td>
    </tr>
    <tr>
        <td class="kinola-admin__table_field_name">
            <?php _ex( 'Distributor', 'Admin', 'kinola' ); ?>
        </td>
        <td class="kinola-admin__table_field_value">
            <?php echo $film->get_field( 'distributor' ); ?>
        </td>
    </tr>
    <tr>
        <td class="kinola-admin__table_field_name">
            <?php _ex( 'Languages', 'Admin', 'kinola' ); ?>
        </td>
        <td class="kinola-admin__table_field_value">
            <?php echo $film->get_field( 'languages' ); ?>
        </td>
    </tr>
    <tr>
        <td class="kinola-admin__table_field_name">
            <?php _ex( 'Subtitles', 'Admin', 'kinola' ); ?>
        </td>
        <td class="kinola-admin__table_field_value">
            <?php echo $film->get_field( 'subtitles' ); ?>
        </td>
    </tr>
</table>

<br>
<div>
    <em>
        <?php _ex(
            sprintf(
                'Film data is downloaded from Kinola web app. To change something, %s edit the film in Kinola %s and %s re-import %s.',
                "<a href='{$film->get_kinola_edit_link()}' target='_blank'>",
                '</a>',
                "<a href='{$film->get_import_link()}'>",
                '</a>'
            ),
            'Admin',
            'kinola'
        ); ?>
    </em>
</div>

<?php if ( WP_DEBUG ): ?>
    <br>
    <hr><br>
    <table>
        <tr>
            <td colspan="2"><strong>Debug</strong></td>
        </tr>
        <tr>
            <td class="kinola-admin__table_field_name">
                <?php _ex( 'ID', 'Admin', 'kinola' ); ?>
            </td>
            <td class="kinola-admin__table_field_value">
                <?php echo $film->get_remote_id(); ?>
            </td>
        </tr>
    </table>
<?php endif; ?>
