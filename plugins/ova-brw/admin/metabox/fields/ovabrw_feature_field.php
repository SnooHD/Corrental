<tr class="tr_rt_feature">
    <?php if ( apply_filters( 'ovabrw_show_icon_features', 'true' ) ): ?>
        <td width="30%">
            <input
                type="text"
                name="ovabrw_features_icons[]"
                placeholder="<?php esc_html_e( 'Class Font', 'ova-brw' ); ?>"
                value=""
            />
        </td>
    <?php endif; ?>
    <td width="20%">
        <input
            type="text"
            name="ovabrw_features_label[]"
            placeholder="<?php esc_html_e( 'Label', 'ova-brw' ); ?>"
            value=""
        />
    </td>
    <?php $cols = apply_filters( 'ovabrw_show_icon_features', 'true' ) ? '29%' : '59%'; ?>
    <td width="<?php echo $cols; ?>">
        <input
            type="text"
            name="ovabrw_features_desc[]"
            placeholder="<?php esc_html_e( 'Description', 'ova-brw' ); ?>"
            value=""
        />
    </td>
    <td width="10%">
      <select name="ovabrw_features_special[]" class="short_dura">
    		<option value="yes"><?php esc_html_e( 'Yes', 'ova-brw' ); ?></option>
    		<option value="no"><?php esc_html_e( 'No', 'ova-brw' ); ?></option>
    	</select>
    </td>
    <td width="10%">
      <select name="ovabrw_features_featured[]" class="short_dura">
            <option value="yes"><?php esc_html_e( 'Yes', 'ova-brw' ); ?></option>
            <option value="no" selected><?php esc_html_e( 'No', 'ova-brw' ); ?></option>
        </select>
    </td>
    <td width="1%"><a href="#" class="button delete_feature">x</a></td>
</tr>