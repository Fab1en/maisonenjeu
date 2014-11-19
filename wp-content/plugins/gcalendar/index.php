<?php
/*
Plugin Name: Google calendar
Plugin URI: http://cabyres.com
Description: Display events information from a google calendar.
Version: 0.1
Author: Fabien Quatravaux
Author URI: http://cabyres.com
Text Domain: gCalendar
*/

// gCalendar options
if ( is_admin() ){
    // plugin options
    add_option('gCalendar',array('account'=>'', 'key' => ''));

    // admin panel
    add_action('admin_menu', 'gCalendar_menu');
    add_action('admin_init', 'register_gCalendarsettings');
}

function gCalendar_menu() {
	add_options_page(__('Options pour Google Calendar','gCalendar'), _x('Google Calendar','gCalendar'), 'manage_options', 'gCalendar', 'gCalendar_option_page');
	function gCalendar_option_page() {
	    if (!current_user_can('manage_options'))  {
		    wp_die( __('Vous n&rsquo;avez pas les droits suffisants pour accéder à cette page.') );
	    }
	    ?>
	    <div class="wrap">
	        <div class="icon32" id="icon-options-general"><br></div>
	        <h2><?php echo __('Google Calendar','gCalendar') ?></h2>
	        <form method="post" action="options.php"> 
	            <?php 
	            settings_fields('gCalendar');
	            do_settings_sections('gCalendar');
	            ?>
	            <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Enregistrer les modifications','gCalendar') ?>" />
                </p>
	        </form>
	    </div>
	    <?php
    }
}

function register_gCalendarsettings() {
    register_setting( 'gCalendar', 'gCalendar'/* TODO, 'check_callback'*/);
    
    add_settings_section('gCalendar_main', _x('Compte Google', 'gCalendar','gCalendar'), 'gCalendar_settings_section', 'gCalendar');
    
    function gCalendar_settings_section(){}
    
    add_settings_field('gCalendar_account', _x('Entrer l\'identifiant du compte google', 'gCalendar','gCalendar'), 'gCalendar_settings_field1', 'gCalendar', 'gCalendar_main');
    add_settings_field('gCalendar_key', _x('Entrer la clé API', 'gCalendar','gCalendar'), 'gCalendar_settings_field2', 'gCalendar', 'gCalendar_main');
    function gCalendar_settings_field1(){
        $options = get_option('gCalendar');
        echo "<input id='gCalendar_account' name='gCalendar[account]' size='90' type='text' value='{$options['account']}' />";
    }
    function gCalendar_settings_field2(){
        $options = get_option('gCalendar');
        echo "<input id='gCalendar_key' name='gCalendar[key]' size='90' type='text' value='{$options['key']}' />";
    }
}

/**
 *  Read and decode a calendar feed from the given Google account.
 */
function gCalendar_getFeed(){
  $options = get_option('gCalendar');

  $calendarURL = 'https://www.googleapis.com/calendar/v3/calendars/';
  $calendarURL .= $options['account'] . '/events';
  $calendarURL .= '?key=' . $options['key'];
  $calendarURL .= '&timeMin=' . substr(date('c'), 0, -6) . '.' . date('Z') . 'Z';
  $calendarURL .= '&orderBy=startTime';
  $calendarURL .= '&singleEvents=true';

  /* Check if json functionality is present (require PHP >= 5.2.0) */
  if (! function_exists("json_decode")) return 0;
  
  $json=json_decode(file_get_contents($calendarURL),true);
  if(trim($json=="")) {
	return 0;
  }else{
	return $json;
  }
}

/**
 * Returns formatted HTML containing the event data.
 * HTML template is taken from configuration file.
 */
function gCalendar_formatEventHTML($event=array()){
  $o = '<div class="vevent">
    <div class="summary">
      <span class="title">'.$event['title'].'</span>
      <abbr class="dtstart" title="'.$event['time_iso'].'">
        <span class="weekday">'.$event['weekday'].'</span> 
        <span class="daynum">'.$event['daynum'].'</span> 
        <span class="month">'.$event['month'].'</span>
      </abbr>
      <a class="location" href="http://maps.google.fr/maps?hl=fr&q='.utf8_encode($event['location']).'+Le+Mans">'.utf8_encode($event['location']).'</a>
    </div>
    <div class="content">'.$event['content'].'</div>
  </div>';
  return $o;
}

/**
 * Displays the more immediat event in the future.
 */
function gCalendar_nextEvent()
{
  global $plugin_cf, $plugin_tx;
  $o="";
  setlocale (LC_TIME, 'fr_FR.utf8');
  
  $feed=gCalendar_getFeed();
  if ($feed==0) return "";
  $event=$feed['items'][0];
  $data=array();
  
  $startTime=date_format(date_create($event['start']['dateTime']),'U');
  $data['time_iso']=$event['start']['dateTime'];
  $data['weekday']=utf8_decode(strftime('%A',$startTime));
  $data['daynum']=strftime('%e',$startTime);
  $data['month']=strftime('%B',$startTime);
  $data['time']=strftime('%Hh%M',$startTime);
  $data['location']=utf8_decode($event['location']);
  $data['title']=$event['summary'];
  $data['content']= isset($event['description']) ? $event['description'] : '';

  $o.="<div id=\"cal\" class=\"vcalendar\">";
  $o.=gCalendar_formatEventHTML($data);
  $o.="</div>";
  
  return $o;
}

/**
 * Displays a list of all events registered in the future.
 */
function gCalendar_eventList(){
  $o="";
  setlocale (LC_TIME, 'fr_FR.utf8');
  
  $feed=gCalendar_getFeed();
  $o.="<ol class=\"vcalendar\">";
  foreach ($feed['items'] as $event){
	$data=array();
	$startTime=date_format(date_create($event['start']['dateTime']),'U');
	$data['time_iso']=$event['start']['dateTime'];
	$data['weekday']=utf8_decode(strftime('%A',$startTime));
	$data['daynum']=strftime('%e',$startTime);
	$data['month']=strftime('%B',$startTime);
	$data['time']=strftime('%Hh%M',$startTime);
	$data['location']=utf8_decode($event['location']);
	$data['title']=$event['summary'];
	$data['content']= isset($event['description']) ? $event['description'] : '';
	
	$o.="<li>";
	$o.=gCalendar_formatEventHTML($data);
	$o.="</li>";
  }
  $o.="</ol>";
  return $o;
}

add_shortcode('gcalendar', 'gCalendar_eventList');

?>
