<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$post_id = get_the_ID();
$fields  = get_post_meta( $post_id, 'afeb_form_fields', true );
$fields  = is_array( $fields ) ? $fields : [];

wp_nonce_field( 'afeb_submissions_cpt', '_afeb_submissions_nonce' );
?>
<div id="afeb-submission-edit">
    <table class="widefat fixed striped">
        <thead>
        <tr>
            <th style="width: 25%;"><?php esc_html_e( 'Field Label', 'addons-for-elementor-builder' ); ?></th>
            <th><?php esc_html_e( 'Value', 'addons-for-elementor-builder' ); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ( $fields as $key => $field ) :
            $type  = $field['type'] ?? 'text';

            $label_candidates = [
                $field['label'] ?? '',
                $field['field_label'] ?? '',
                $field['name'] ?? '',
                $field['_id'] ?? '',
                is_string( $key ) ? $key : '',
            ];

            $label = '';
            foreach ( $label_candidates as $candidate ) {
                if ( is_string( $candidate ) && '' !== trim( $candidate ) ) {
                    $label = $candidate;
                    break;
                }
            }

            if ( '' === $label ) {
                $label = ucwords( trim( preg_replace( '/[\-_]+/', ' ', (string) $type ) ) );
            }

            if ( '' === $label ) {
                $label = sprintf( /* translators: %d: fallback field number */ __( 'Field %d', 'addons-for-elementor-builder' ), intval( $key ) + 1 );
            }

            $normalized_label = ucwords( trim( preg_replace( '/[\-_]+/', ' ', (string) $label ) ) );
            $label = $normalized_label ?: $label;
            $value = $field['value'] ?? '';

            $display_value = is_array( $value ) ? implode( ', ', array_filter($value) ) : $value;
            $field_meta = [
                '_id'     => $field['_id'] ?? '',
                'type'    => $type,
                'label'   => $label,
                'name'    => $field['name'] ?? '',
                'options' => $field['options'] ?? [],
                'index'   => $key,
            ];
            ?>
            <tr class="afeb-submission-row" data-field-meta='<?php echo esc_attr( wp_json_encode( $field_meta ) ); ?>'>
                <td><strong><?php echo esc_html( $label ); ?></strong></td>
                <td>
                    <!-- Read-only view -->
                    <div class="afeb-read-view">
                        <?php if ( $type === 'upload' && ! empty( $value ) ) :
                            // Decode any encoded delimiters (%7C or double-encoded %257C)
                            $raw_value = is_array( $value ) ? implode('|', $value) : $value;
                            $decoded   = urldecode( urldecode( $raw_value ) );

                            // Now split by pipe and clean up
                            $urls = explode( '|', rtrim( $decoded, '|' ) );
                            $urls = array_filter( array_map( 'trim', $urls ) );

                            foreach ( $urls as $url ) {
                                if ( empty( $url ) ) {
                                    continue;
                                }
                                $filename = basename( parse_url( $url, PHP_URL_PATH ) );
                                printf(
                                    '<div><a href="%s" target="_blank">%s</a></div>',
                                    esc_url( $url ),
                                    esc_html( $filename )
                                );
                            }

                        else :
                            echo nl2br( esc_html( $display_value ) );
                        endif; ?>

                    </div>

                    <!-- Edit view -->
                    <div class="afeb-edit-view" style="display:none;">
                        <input type="hidden" class="afeb-field-meta" value='<?php echo esc_attr( wp_json_encode( $field_meta ) ); ?>' />
                        <?php
                        $input_name = sprintf( 'afeb-form-fields[%s]', esc_attr($key) );
                        switch ( $type ) {
                            case 'textarea':
                                printf(
                                    '<textarea name="%1$s" class="widefat" rows="3">%2$s</textarea>',
                                    $input_name,
                                    esc_textarea( is_array($value) ? implode("\n", $value) : $value )
                                );
                                break;

                            case 'checkbox':
                                $vals = is_array($value) ? $value : [$value];
                                foreach ( $vals as $v ) {
                                    printf(
                                        '<input type="text" name="%1$s[]" class="widefat" value="%2$s" />',
                                        $input_name,
                                        esc_attr( $v )
                                    );
                                }
                                printf( '<input type="text" name="%1$s[]" class="widefat" value="" />', $input_name );
                                break;

                            case 'upload':
                                $vals = is_array($value) ? $value : [$value];
                                foreach ( $vals as $url ) {
                                    printf(
                                        '<input type="url" name="%1$s[]" class="widefat" value="%2$s" placeholder="%3$s" />',
                                        $input_name,
                                        esc_url( $url ),
                                        esc_attr__( 'File URL or upload a new one below', 'addons-for-elementor-builder' )
                                    );
                                }
                                printf( '<input type="file" name="%1$s[]" />', $input_name );
                                break;

                            default:
                                printf(
                                    '<input type="text" name="%1$s" class="widefat" value="%2$s" />',
                                    $input_name,
                                    esc_attr( is_array($value) ? implode(', ', $value) : $value )
                                );
                                break;
                        }
                        ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <p class="submit">
        <button type="button" id="afeb-edit-submission" class="button"><?php esc_html_e( 'Edit', 'addons-for-elementor-builder' ); ?></button>
        <button type="button" id="afeb-save-submission" class="button button-primary" style="display:none;"><?php esc_html_e( 'Save Submission', 'addons-for-elementor-builder' ); ?></button>
        <span id="afeb-save-status" style="margin-left: 10px;"></span>
    </p>
</div>

<script type="text/javascript">
    jQuery(document).ready(function($){
        $('#afeb-edit-submission').on('click', function(e){
            e.preventDefault();
            $('.afeb-read-view').hide();
            $('.afeb-edit-view').show();
            $('#afeb-edit-submission').hide();
            $('#afeb-save-submission').show();
        });

        $('#afeb-save-submission').on('click', function(e){
            e.preventDefault();
            var $status = $('#afeb-save-status');
            $status.text('<?php echo esc_js( __( 'Saving...', 'addons-for-elementor-builder' ) ); ?>');

            var formData = new FormData($('#afeb-submission-edit form')[0] || document.createElement('form'));
            // Append AJAX action manually
            formData.append('action', 'afeb_update_submission');
            formData.append('post_id', '<?php echo esc_js($post_id); ?>');
            formData.append('nonce', '<?php echo esc_js( wp_create_nonce( 'afeb_admin_submission' ) ); ?>');

            // Append our fields manually from DOM
            $('#afeb-submission-edit').find('input[name],textarea[name],select[name]').each(function(){
                var elName = $(this).attr('name');
                if($(this).attr('type') === 'file'){
                    if (this.files.length) {
                        for (var i=0; i<this.files.length; i++) {
                            formData.append(elName, this.files[i]);
                        }
                    }
                } else {
                    formData.append(elName, $(this).val());
                }
            });

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(resp){
                    if (resp.success) {
                        $status.css('color','green').text(resp.data && resp.data.message ? resp.data.message : 'Saved.');
                        $('.afeb-edit-view').hide();
                        $('.afeb-read-view').each(function(){
                            var $cell = $(this);
                            var values = $cell.siblings('.afeb-edit-view')
                            .find('input:not([type="file"]):not(.afeb-field-meta):not([type="hidden"]), textarea')
                            .map(function(){
                                return $(this).val();
                            }).get()
                            .filter(function(val){
                                return val && val.toString().trim() !== '';
                            });

                            var display = values.join(', ').replace(/\n/g, '<br>');
                            $cell.html(display).show();
                        });
                        $('#afeb-edit-submission').show();
                        $('#afeb-save-submission').hide();
                    } else {
                        $status.css('color','red').text(resp.data && resp.data.message ? resp.data.message : 'Error');
                    }
                },
                error: function(){
                    $status.css('color','red').text('<?php echo esc_js( __( 'AJAX error.', 'addons-for-elementor-builder' ) ); ?>');
                }
            });
        });
    });
</script>
