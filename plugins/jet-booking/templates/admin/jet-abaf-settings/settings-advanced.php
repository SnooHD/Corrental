<div>
	<cx-vui-switcher
		label="<?php _e( 'Hide DB columns manager', 'jet-booking' ); ?>"
		description="<?php _e( 'Check this to hide the columns manager option to prevent accidental DB changes.', 'jet-booking' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="advancedSettings.hide_columns_manager"
		@input="updateSetting( $event, 'hide_columns_manager' )"
	></cx-vui-switcher>

	<cx-vui-switcher
		label="<?php _e( 'Enable iCal synchronization', 'jet-booking' ); ?>"
		description="<?php _e( 'Check this to allow export your bookings into iCal format and synchronize all your data with external calendars in iCal format.', 'jet-booking' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="advancedSettings.ical_synch"
		@input="updateSetting( $event, 'ical_synch' )"
	></cx-vui-switcher>

	<cx-vui-select
		label="<?php _e( 'Calendar sync interval', 'jet-booking' ); ?>"
		description="<?php _e( 'Select interval between synchronizing calendars.', 'jet-booking' ); ?>"
		:options-list="cronSchedules"
		:wrapper-css="[ 'equalwidth' ]"
		:size="'fullwidth'"
		:value="advancedSettings.synch_interval"
		@input="updateSetting( $event, 'synch_interval' )"
		v-if="advancedSettings.ical_synch"
	></cx-vui-select>

	<cx-vui-component-wrapper
		v-if="advancedSettings.ical_synch"
		label="<?php _e( 'Calendar sync start', 'jet-booking' ); ?>"
		description="<?php _e( 'Start calendar synchronization from this time.', 'jet-booking' ); ?>"
		:wrapper-css="[ 'equalwidth', 'schedule-time' ]"
	>
		<cx-vui-select
			:options-list="getInterval( 23 )"
			:value="advancedSettings.synch_interval_hours"
			@input="updateSetting( $event, 'synch_interval_hours' )"
			:prevent-wrap="true"
		></cx-vui-select>
		<span>:</span>
		<cx-vui-select
			:options-list="getInterval( 59 )"
			:value="advancedSettings.synch_interval_mins"
			@input="updateSetting( $event, 'synch_interval_mins' )"
			:prevent-wrap="true"
		></cx-vui-select>
		<span>HH:MM</span>
	</cx-vui-component-wrapper>

	<cx-vui-switcher
		label="<?php _e( 'Hide Setup Wizard', 'jet-booking' ); ?>"
		description="<?php _e( 'Enable the toggle to hide Set Up page and avoid unnecessary plugin resets.', 'jet-booking' ); ?>"
		:wrapper-css="[ 'equalwidth' ]"
		:value="advancedSettings.hide_setup"
		@input="updateSetting( $event, 'hide_setup' )"
	></cx-vui-switcher>
</div>