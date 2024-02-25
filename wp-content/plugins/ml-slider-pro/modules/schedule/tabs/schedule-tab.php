<?php

global $wp_locale;
$current_time   = current_time( 'timestamp' );
$one_week_later = strtotime( '+1 week', $current_time );
?>

<div class="row schedule">
    <div class="ms-switch-button">
        <label>
            <input
                class="schedule-slide mr-0"
                type="checkbox"
                id="schedule-select-<?php
                echo esc_attr($post->ID) ?>"
                name="attachment[<?php
                echo esc_attr($post->ID) ?>][schedule]"
                value="on"
                <?php
                echo $is_scheduled ? 'checked="checked"' : ''; ?>
            >
            <span></span>
        </label>
    </div><label class="schedule-slide" for="schedule-select-<?php
    echo esc_attr($post->ID) ?>">
        <?php esc_html_e( 'Schedule this slide', 'ml-slider-pro' ) ?>
    </label>
    <span class="text-gray ml-4 float-right">
        <?php
        // Get the timezone setting from global settings
        $timezone_string = get_option('timezone_string');

        // Display timezone saved in settings; based on wp_timezone_string() function
        $timezone_string    = get_option( 'timezone_string' );
        $offset             = (float) get_option( 'gmt_offset' );
        if ( $timezone_string ) {
            $parts  = explode( '/', $timezone_string );
            $city   = end( $parts );
            $city   = str_replace( '_', ' ', $city );

            echo sprintf( esc_html__( '%s time', 'ml-slider-pro' ), esc_html( $city ) );
        } elseif( $offset ) {
            $hours   = (int) $offset;
            $minutes = ( $offset - $hours );

            $sign      = ( $offset < 0 ) ? '-' : '+';
            $abs_hour  = abs( $hours );
            $abs_mins  = abs( $minutes * 60 );
            $tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

            echo sprintf( esc_html__( 'UTC%s timezone', 'ml-slider-pro' ), esc_html( $tz_offset ) );
        } else {
            echo sprintf( 
                esc_html__( '%sUses your settings timezone%s', 'ml-slider-pro' ), 
                '<a href="' . esc_url( admin_url( 'options-general.php#timezone_string' ) ) . '" target="_blank" class="button-link">',
                '</a>'
            );
        }
        ?>
        <span class="tipsy-tooltip-top ms-time-helper text-gray float-right ml-1 tipsy-tooltip-top"
            data-time="<?php 
            esc_attr_e(gmdate('Y-m-d H:i:s', $current_time)); ?>" data-now-text="<?php
            esc_attr_e('Current server time', 'ml-slider-pro') ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2" stroke-linecap="round"
                stroke-linejoin="round" class="feather feather-clock">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </span>
    </span>
    <div class="schedule_wrapper_settings relative bg-gray-lightest border border-gray-light border-l-8 rtl:border-r-8 rtl:border-l-0 rtl:pl-0 rtl:pr-4 hide-if-notchecked mt-2 p-5"<?php 
        echo ! $is_scheduled ? ' style="display: none;"' : '' ?>>
        <div>
            <table class="schedule-main-settings schedule_settings_date_range mb-1">
                <tbody>
                <tr>
                    <td colspan="2">
                        <span class="block font-semibold pb-3 border-solid border-b border-gray-light">
                            <?php esc_html_e( 'Between selected dates', 'ml-slider-pro' ) ?>
                        </span>
                    </td>
                </tr>
                <tr>
                        <td>
                            <label
                                for="date-range-<?php
                                echo esc_attr($post->ID); ?>"
                                class="m-0">
                                <?php esc_html_e( 'Date range', 'ml-slider-pro' ) ?>
                            </label>
                        </td>
                        <td>
                            <div class="ms-switch-button">
                                <label>
                                    <input
                                        type="checkbox"
                                        class="relative m-0"
                                        id="date-range-<?php
                                        echo esc_attr($post->ID); ?>"
                                        value="on"
                                        name="attachment[<?php
                                        echo esc_attr($post->ID); ?>][date_range]"
                                        <?php
                                        echo $date_range ? 'checked="checked"' : ''; ?>
                                    > 
                                    <span></span>
                                </label>
                            </div>
                        </td>
                    </tr>
                <?php
                // Essentially if $schedule_start isn't set, we can set defaults here
                $texts = array(
                    'from' => _x('From', 'As in "From January 7th to April 2nd..."', 'ml-slider-pro'),
                    'to' => _x('To', 'As in "From January 7th to April 2nd..."', 'ml-slider-pro')
                );
                foreach (array('from' => $schedule_start, 'to' => $schedule_end) as $field_id => $date) :

                    // Different default dates for "From" and "To" 
                    $time = $field_id === 'to' ? $one_week_later : $current_time;
                    
                    // By default use the current time
                    $start_real = $date ? mysql2date( 'Y-m-d', $date, false ) : gmdate( 'Y-m-d', $time );
                    $hh = $date ? mysql2date( 'H', $date, false ) : gmdate( 'H', $time );
                    $mn = $date ? mysql2date( 'i', $date, false ) : gmdate( 'i', $time );

                    // By default select every day ($days_scheduled could be false if coming from an older version)
                    $days_scheduled = ($date && $days_scheduled) ? $days_scheduled : array(0, 1, 2, 3, 4, 5, 6);
                    ?>
                    <tr class="schedule_settings_from_to"<?php 
                        echo ! $date_range ? ' style="display: none;"' : '' ?>>
                        <td>
                            <?php esc_html_e($texts[$field_id]); ?>
                        </td>
                        <td>
                            <label class="mr-1 mb-0">
                                <span class="screen-reader-text"><?php
                                    echo esc_html__('Date'); ?></span>
                                <input
                                        data-lpignore="true"
                                        type="text"
                                        class="datepicker w-32 md:w-24 m-0"
                                        name="attachment[<?php
                                        echo esc_attr($post->ID); ?>][<?php
                                        echo esc_attr($field_id); ?>][date]"
                                        value="<?php
                                        echo esc_attr($start_real); ?>"
                                        placeholder="YYYY-MM-DD"
                                >
                            </label>
                            <span class="mx-1">
                                <?php
                                echo esc_html_x('at', 'As in "your slide will display Tuesday at 5pm"', 'ml-slider-pro'); ?>
                            </span>
                            <label class="m-0 ml-1 ms-schedule-time">
                                <input
                                    data-lpignore="true"
                                    type="text"
                                    class="w-auto text-center pr-0 pl-1 border-0 m-0"
                                    name="attachment[<?php
                                    echo esc_attr($post->ID); ?>][<?php
                                    echo esc_attr($field_id); ?>][hh]"
                                    value="<?php
                                    echo esc_attr($hh); ?>"
                                    size="2"
                                    maxlength="2"
                                    autocomplete="off"
                                    placeholder="00"
                                >:<input
                                    data-lpignore="true"
                                    type="text"
                                    class="w-auto text-center pr-1 pl-0 border-0 m-0"
                                    name="attachment[<?php
                                    echo esc_attr($post->ID); ?>][<?php
                                    echo esc_attr($field_id); ?>][mn]"
                                    value="<?php
                                    echo esc_attr($mn); ?>"
                                    size="2"
                                    maxlength="2"
                                    autocomplete="off"
                                    placeholder="00"
                                >
                            </label>
                        </td>
                    </tr>
                <?php
                endforeach; ?>
                </tbody>
            </table>
            <div class="schedule_datetime_invalid_notice mb-2 mt-0 mx-0 notice notice-warning notice-alt" style="display: none;">
                <p class="py-1 m-0 not-italic text-black">
                    <?php esc_html_e( 'Choose valid dates and time', 'ml-sider-pro' ) ?>
                </p>
            </div>
            <div class="schedule_datetime_order_notice mb-2 mt-0 mx-0 notice notice-warning notice-alt" style="display: none;">
                <p class="py-1 m-0 not-italic text-black">
                    <?php esc_html_e( '"To" date should be after "From" date!', 'ml-sider-pro' ) ?>
                </p>
            </div>
            <div class="schedule_settings_weekdays mb-1">
                <table class="schedule-main-settings">
                    <tbody>
                    <tr>
                        <td colspan="3">
                            <span class="block font-semibold py-3 border-solid border-b border-gray-light">
                                <?php esc_html_e( 'Days of the week', 'ml-slider-pro' ) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label
                                for="all-weekdays-<?php
                                echo esc_attr($post->ID); ?>"
                                class="m-0">
                                <?php esc_html_e( 'All days', 'ml-slider-pro' ) ?>
                            </label>
                        </td>
                        <td>
                            <div class="ms-switch-button">
                                <label>
                                    <input
                                        type="checkbox"
                                        class="relative m-0"
                                        id="all-weekdays-<?php
                                        echo esc_attr($post->ID); ?>"
                                        value="on"
                                        name="attachment[<?php
                                        echo esc_attr($post->ID); ?>][all_weekdays]"
                                        <?php
                                        echo $all_weekdays ? 'checked="checked"' : ''; ?>
                                    > 
                                    <span></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <span class="schedule_all_weekdays_description text-gray-dark"<?php 
                                echo ! $all_weekdays ? ' style="display: none;"' : '' ?>>
                                <?php _e( 'If unchecked, you can choose specific days of the week.', 'ml-slider-pro' ) ?>
                            </span>
                        </td>
                    </tr>
                    <tr class="schedule_settings_specific_weekdays"<?php 
                                    echo $all_weekdays ? ' style="display: none;"' : '' ?>>
                        <td>
                            <?php esc_html_e( 'On these days', 'ml-slider-pro' ) ?>
                        </td>
                        <td colspan="2">
                            <table class="days-schedules">
                                <tbody>
                                <tr class="flex">
                                    <?php
                                    // Sunday = 0, etc
                                    foreach (array_values($wp_locale->weekday_abbrev) as $day_id => $day_abbr) :
                                        $days_scheduled = is_array($days_scheduled) ? $days_scheduled : array(); ?>
                                        <td class="mr-1 flex flex-col items-center justify-center w-7">
                                            <input
                                                    type="checkbox"
                                                    class="mx-auto"
                                                    name="attachment[<?php
                                                    echo esc_attr($post->ID); ?>][days][<?php
                                                    echo esc_attr($day_id); ?>]"
                                                <?php
                                                echo in_array($day_id, $days_scheduled) ? 'checked="checked"' : ''; ?>
                                            ><label><?php
                                                echo esc_html($day_abbr) ?></label>
                                        </td>
                                    <?php
                                    endforeach; ?>
                                </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="schedule_datetime_days_notice mb-2 mt-0 mx-0 notice notice-warning notice-alt" style="display: none;">
                <p class="py-1 m-0 not-italic text-black">
                    <?php esc_html_e( 'Check at least one day', 'ml-sider-pro' ) ?>
                </p>
            </div>
            <div class="schedule_settings_daily_constraint mb-1">
                <table class="schedule-main-settings">
                    <tbody>
                    <tr>
                        <td colspan="3">
                            <span class="block font-semibold py-3 border-solid border-b border-gray-light">
                                <?php esc_html_e( 'Daily constraint', 'ml-slider-pro' ) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label
                                for="all-day-<?php
                                echo esc_attr($post->ID); ?>"
                                class="m-0">
                                <?php esc_html_e( 'All day', 'ml-slider-pro' ) ?>
                            </label>
                        </td>
                        <td>
                            <div class="ms-switch-button">
                                <label>
                                    <input
                                        type="checkbox"
                                        class="relative m-0"
                                        id="all-day-<?php
                                        echo esc_attr($post->ID); ?>"
                                        value="on"
                                        name="attachment[<?php
                                        echo esc_attr($post->ID); ?>][all_day]"
                                        <?php
                                        echo $all_day ? 'checked="checked"' : ''; ?>
                                    > 
                                    <span></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <span class="schedule_all_day_description text-gray-dark"<?php 
                                echo ! $all_day ? ' style="display: none;"' : '' ?>>
                                <?php _e( 'If unchecked, you can choose specific times during each day.', 'ml-slider-pro' ) ?>
                            </span>
                        </td>
                    </tr>
                    <tr class="schedule_settings_between_constraint"<?php 
                        echo $all_day ? ' style="display: none;"' : '' ?>>
                        <td>
                            <?php esc_html_e( 'Between', 'ml-slider-pro' ) ?> 
                        </td>
                        <td colspan="2">
                            <?php
                            // use 00:00 if nothing is set
                            //echo '</a>';
                            $hh_from = $constraint_time_start ? gmdate('H', strtotime($constraint_time_start)) : '00';
                            $mn_from = $constraint_time_start ? gmdate('i', strtotime($constraint_time_start)) : '00';
                            $hh_until = $constraint_time_end ? gmdate('H', strtotime($constraint_time_end)) : '23';
                            $mn_until = $constraint_time_end ? gmdate('i', strtotime($constraint_time_end)) : '00';
                            ?>
                            <span class="m-0 inline-block ms-schedule-time">
                                <input
                                    data-lpignore="true"
                                    type="text"
                                    class="w-auto text-center pr-0 pl-1 border-0 m-0"
                                    name="attachment[<?php
                                    echo esc_attr($post->ID); ?>][constraint_from][hh]"
                                    value="<?php
                                    echo esc_attr($hh_from); ?>"
                                    size="2"
                                    maxlength="2"
                                    autocomplete="off"
                                    placeholder="00"
                                >:<input
                                    data-lpignore="true"
                                    type="text"
                                    class="w-auto text-center pl-0 pr-1 border-0 m-0"
                                    name="attachment[<?php
                                    echo esc_attr($post->ID); ?>][constraint_from][mn]"
                                    value="<?php
                                    echo esc_attr($mn_from); ?>"
                                    size="2"
                                    maxlength="2"
                                    autocomplete="off"
                                    placeholder="00"
                                >
                            </span>
                            <span class="mx-1 px-1">
                                <?php echo esc_html_x( 'and', 'As in "Between 1:00 and 2:30..."', 'ml-slider-pro' ) ?>
                            </span>
                            <span class="m-0 inline-block ms-schedule-time">
                                <input
                                    data-lpignore="true"
                                    type="text"
                                    class="w-auto text-center pr-0 pl-1 border-0 m-0"
                                    name="attachment[<?php
                                    echo esc_attr($post->ID); ?>][constraint_to][hh]"
                                    value="<?php
                                    echo esc_attr($hh_until); ?>"
                                    size="2"
                                    maxlength="2"
                                    autocomplete="off"
                                    placeholder="00"
                                >:<input
                                    data-lpignore="true"
                                    type="text"
                                    class="w-auto text-center pl-0 pr-1 border-0 m-0"
                                    name="attachment[<?php
                                    echo esc_attr($post->ID); ?>][constraint_to][mn]"
                                    value="<?php
                                    echo esc_attr($mn_until); ?>"
                                    size="2"
                                    maxlength="2"
                                    autocomplete="off"
                                    placeholder="00"
                                >
                            </span>
                            <a href="#" class="schedule_reset_time_constraint button button-secondary mx-2">
                                <?php esc_html_e( 'Reset', 'ml-slider-pro' ) ?>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="schedule_constraint_time_invalid_notice mb-2 mt-0 mx-0 notice notice-warning notice-alt" style="display: none;">
                    <p class="py-1 m-0 not-italic text-black">
                        <?php esc_html_e( 'Choose valid times', 'ml-sider-pro' ) ?>
                    </p>
                </div>
                <div class="schedule_constraint_time_order_notice mb-2 mt-0 mx-0 notice notice-warning notice-alt" style="display: none;">
                    <p class="py-1 m-0 not-italic text-black">
                        <?php esc_html_e( 'The second time should be later than the first time.', 'ml-sider-pro' ) ?>
                    </p>
                </div>
            </div>
            <div class="schedule_settings_daily_constraint mb-1">
                <table class="schedule-main-settings">
                    <tbody>
                    <tr>
                        <td colspan="3">
                            <span class="block font-semibold py-3 border-solid border-b border-gray-light">
                                <?php esc_html_e( 'Show or hide?', 'ml-slider-pro' ) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label
                                for="show-hide-<?php
                                echo esc_attr($post->ID); ?>"
                                class="relative m-0">
                                <?php esc_html_e('Show', 'ml-slider-pro') ?>
                            </label>
                        </td>
                        <td>
                            <div class="ms-switch-button">
                                <label>
                                <input
                                    type="checkbox"
                                    id="show-hide-<?php
                                    echo esc_attr($post->ID); ?>"
                                    value="on"
                                    name="attachment[<?php
                                    echo esc_attr($post->ID); ?>][show_during_constraint]"
                                    <?php
                                    echo $constraint_time_show ? 'checked="checked"' : ''; ?>
                                    class="relative m-0 rtl:ml-0 rtl:mr-2">
                                    <span></span>
                                </label>
                            </div>
                        </td>
                        <td>
                            <span class="schedule_show_description text-gray-dark"<?php 
                                echo ! $constraint_time_show ? ' style="display: none;"' : '' ?>>
                                <?php _e( 'If unchecked, the slide will be hidden during the schedule constraints.', 'ml-slider-pro' ) ?>
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
