<?php
class ovr_calendar_widget extends WP_Widget {
  function __construct() {
    parent::__construct(
    // Base ID of your widget
    'ovr_calendar',

    // Widget name will appear in UI
    'OvR Calendar',

    // Widget description
    array( 'description' => 'Calendar for small tile display or full page' )
    );
  }
  public function widget( $args, $instance ) {
    wp_enqueue_style('ovr_calendar_style', plugin_dir_url( dirname(__FILE__) ) . 'css/ovr-calendar-widget.css');
    echo <<<FRONTEND
    <div class="ovr_calendar_widget">
      <div class="ovr_calendar_widget_inner">
        <div class="ovr_calendar_widget_content">
          <div class="ovr_calendar">
            <div class="month">
              <ul>
                <li class="prev"><i class="fa fa-arrow-left fa-lg" aria-hidden="true"></i></li>
                <li class="next"><i class="fa fa-arrow-right fa-lg" aria-hidden="true"></i></li>
                <li>
                  <h4>December 2016</h4>
                </li>
              </ul>
            </div>
            <ul class="weekdays">
              <li>Su</li>
              <li>Mo</li>
              <li>Tu</li>
              <li>We</li>
              <li>Th</li>
              <li>Fr</li>
              <li>Sa</li>
            </ul>

            <ul class="days">
            <li>&nbsp;</li>
              <li>&nbsp;</li>
              <li>&nbsp;</li>
              <li>&nbsp;</li>
              <li>1</li>
              <li>2</li>
              <li>3 <i class="fa fa-snowflake-o icon winter" aria-hidden="true"></i></li>
              <li>4 <i class="fa fa-snowflake-o icon winter" aria-hidden="true"></i></li></li>
              <li class="active">5</li>
              <li>6</li>
              <li>7</li>
              <li>8</li>
              <li>9</li>
              <li>10 <i class="fa fa-snowflake-o icon winter" aria-hidden="true"></i></li></li>
              <li>11 <i class="fa fa-snowflake-o icon winter" aria-hidden="true"></i></li></li>
              <li>12</li>
              <li>13</li>
              <li>14</li>
              <li>15</li>
              <li>16</li>
              <li>17 <i class="fa fa-snowflake-o icon winter" aria-hidden="true"></i></li></li>
              <li>18 <i class="fa fa-snowflake-o icon winter" aria-hidden="true"></i></li></li>
              <li>19</li>
              <li>20</li>
              <li>21</li>
              <li>22</li>
              <li>23</li>
              <li>24 <i class="fa fa-snowflake-o icon winter" aria-hidden="true"></i></li></li>
              <li>25 <i class="fa fa-snowflake-o icon winter" aria-hidden="true"></i></li></li>
              <li>26</li>
              <li>27</li>
              <li>28</li>
              <li>29</li>
              <li>30</li>
              <li>31 <i class="fa fa-snowflake-o icon summer" aria-hidden="true"></i></li></li>
              <li>&nbsp;</li>
              <li>&nbsp;</li>
              <li>&nbsp;</li>
              <li>&nbsp;</li>
              <li>&nbsp;</li>
              <li>&nbsp;</li>
              <li>&nbsp;</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
FRONTEND;
  }
}
