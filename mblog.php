<?php
/*
Plugin Name: MBlog
Plugin URI: http://mblog-wp.blogspot.com/
Description: The microblog widget, that allows blog authors/contributors to cooperate. Can be made invisible to unregistered users.
Version:0.37
Author:Nordvind
Author URI: www.arttupeka.eu
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
 
//error_reporting(E_ALL ^ E_NOTICE);
add_action('wp_head','mblog_css');
add_action('init','init_scripts');
add_action('widgets_init', 'load_mblog');
add_action('admin_head','admin_css');
add_action('admin_menu','mblog_settings');

function mblog_settings(){
add_options_page('MBlog Options', 'MBlog', 'manage_options', 'mblog-settings', 'mblog_editoptions');
}

function mblog_editoptions(){
if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}
$fname = plugins_url().'/mblog/wall.txt';
$wall = file_get_contents($fname);
$records = explode("|",$wall);
?>
<div class="wrap">
<h2>MBlog Options</h2>
<h3>Conversation log</h3>
	<div id="mblog-log">
	<?php
	for ($i=0;$i<count($records);$i++){
		$info = explode(":::",$records[$i]);
		echo '<p><span class="mblog-a">'.$info[0].'</span>:'.$info[1].'</p>';
	}
	?>
	</div>
</div>
<?php
}

function init_scripts(){ 
if (!is_admin()){
	wp_enqueue_script('jquery');
	//wp_register_script('mblog',plugins_url().'/MBlog/mblog.js');
	wp_enqueue_script('mblog',plugins_url().'/mblog/mblog.js',false,null);
	}
}

function admin_css(){
	echo '<style type="text/css">
	#mblog-log{
	max-height:200px;
	overflow:scroll;
	border:1px solid #DEDEDE;
	}
	.mblog-a{
	font-weight:bold;
	}
	</style>';
}

function mblog_css(){
echo '<style type="text/css">
#mblog-window{ 
	height:400px;
	min-width:150px;
	padding:3px;
	margin:5px 3px;
	background:#FFF;
	border:1px solid #EFEFEF;
	overflow:hidden;
}
#mblog-input{
	min-width:150px;
	margin:3px;
}
.mblog-entry{
	height:70px;
}
.mblog-avt{
	float:left;
	margin:3px;
}
.mblog-uname{
	font-weight:bold;
}
</style>';
}

function load_mblog(){
	session_start();
	register_widget('mblog');
	
	global $current_user;
	get_currentuserinfo();
	if (is_user_logged_in()){
	$avatar = get_avatar($current_user->ID,$size='30');
	setcookie("user",$current_user->display_name, time()+60);
	$_SESSION['user'] = $current_user->display_name;
	$_SESSION['avatar'] = $avatar;
	}
}

class mblog extends WP_Widget{

	function mblog(){
		$this->is_user = is_user_logged_in();
			if ($this->is_user){

			}
			/* Widget settings. */
			$widget_ops = array( 'classname' => 'mblog-wdg', 'description' => 'Microblog/chat widget' );

			/* Widget control settings. */
			$control_ops = array( 'width' => 300, 'height' => 700, 'id_base' => 'mblog' );

			/* Create the widget. */
			$this->WP_Widget('mblog', 'MBlog', $widget_ops, $control_ops);
	}
	function widget($args,$instance){
		$show = isset($instance['hide_wdg']) ? false : true;
		if ($show || $this->is_user){
			//Microblog window
			
			//Post form
			if ($this->is_user){
				echo'<div id="mblog-window"></div>';

			echo '<form method="post" action="" onsubmit="processInp(); return false;">
			<input type="text" name="msg" maxlength="200" id="mblog-input" /><br />
			<input type="submit" value="Say" />
			</form>';
			}
		}
	}
	function update($new_instance,$old_instance){
		$instance = $old_instance;
		$instance['hide_wdg'] = $new_instance['hide_wdg'];
		return $instance;
	}
	function form($instance){
	$def = array('hide_wdg'=>'true');
	$instance = wp_parse_args((array)$instance,$def);
	?>
	<p>Visibility:</p>
	<input class="checkbox" type="checkbox" <?php checked($instance['hide_wdg'],1); ?> id="<?php echo $this->get_field_id('hide_wdg'); ?>" value="1" name="<?php echo $this->get_field_name('hide_wdg'); ?>"  />
	<label for="<?php echo $this->get_field_id( 'hide_wdg' ); ?>"> Hide microblog from unregistered users</label>
	<?php
		}
}
?>