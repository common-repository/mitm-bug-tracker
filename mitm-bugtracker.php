<?php
/*
  Plugin Name: Bug Tracker
  Plugin URI: 
  Description: In Bug Tracker system user can submit bugs.
  Author: Mahesh Patel
  Version: 1.0
 */

class mitm_bugtracker {

  var $pluginPath;
  var $pluginUrl;
  var $rootPath;
  var $wpdb;

  public function __construct() { 

    global $wpdb;
    $this->wpdb = $wpdb;
    $this->ds = DIRECTORY_SEPARATOR;
    $this->pluginPath = dirname(__FILE__) . $this->ds;
    $this->rootPath = dirname(dirname(dirname(dirname(__FILE__))));
    $this->pluginUrl = WP_PLUGIN_URL . '/mitm-bugtracker/';

	add_action('admin_menu', array($this, 'mitm_bugtracker_menu'));
    add_shortcode('mitmbugtracker', array($this, 'mitm_bugtracker_add'));
	add_action('wp_ajax_mitm_bugtracker_save',array($this, 'mitm_bugtracker_save'));
	add_action('wp_ajax_nopriv_mitm_bugtracker_save',array($this, 'mitm_bugtracker_save'));
  }
  
  public function mitm_bugtracker_save() {
  	 $_data = array();
	 $_data['bt_projectname']  = $_POST['project_name'];
	 $_data['bt_url']		   = $_POST['url'];
	 $_data['bt_date'] 	  	   = date('Y-m-d');
	 $_data['bt_desc']		   = trim($_POST['desc']);
	 $_data['bt_os'] 		   = $_POST['os'];
	 $_data['bt_browser'] 	   = $_POST['browser'];
	 $_data['bt_assingto']     = $_POST['assingto'];
	 $_data['bt_priority']     = $_POST['priority'];
	 $_data['bt_fixed'] 	   = $_POST['fixed'];
	 
	if (isset($_POST['id']) && $_POST['id']) {
       $this->wpdb->update("{$this->wpdb->prefix}mitm_bug_tracker", $_data, array("bt_id" => $_POST['id']));
    } else {
       $this->wpdb->insert("{$this->wpdb->prefix}mitm_bug_tracker",$_data);
    }
	
	if (mysql_error()) {
      $data['error'] = mysql_error();
    } else {
      if ($this->wpdb->insert_id) {
        $data['form_reset'] = true;
      }
      $data['msg'] = "The bug has been saved successfully.";
    }
	echo json_encode($data);
	die();
  }
  
  public function mitm_bugtracker_view() {
    require($this->pluginPath . "mitm_bugtracker_view.php");
  }
  
  public function mitm_bugtracker_os_view() {
  	 require($this->pluginPath . "mitm_bugtracker_os_view.php");
  }
  
  public function mitm_bugtracker_browser_view() {
  	 require($this->pluginPath . "mitm_bugtracker_browser_view.php");
  }
  
  public function mitm_bugtracker_member_view() {
  	 require($this->pluginPath . "mitm_bugtracker_member_view.php");
  }
  
  public function mitm_bugtracker_add() {
    require($this->pluginPath . "mitm_bugtracker_add.php");
  }
  
  public function mitm_bugtracker_os_add() {
  	 if(isset($_REQUEST['os_name'])) {
	    $_data = array();
		$_data['os_name'] = $_POST['os_name'];
	 	if (isset($_POST['os_id']) && $_POST['os_id']) {
		   $this->wpdb->update("{$this->wpdb->prefix}mitm_os", $_data, array("os_id" => $_POST['os_id']));
		} else {
		   $this->wpdb->insert("{$this->wpdb->prefix}mitm_os",$_data);
		}
	    wp_redirect('admin.php?page=mitm_bugtracker_os_view&sucess=true');
		exit;
	 }
  	 require($this->pluginPath . "mitm_bugtracker_os_add.php");
  }
  
  public function mitm_bugtracker_browser_add() {
  	 if(isset($_REQUEST['browser_name'])) {
	    $_data = array();
		$_data['browser_name'] = $_POST['browser_name'];
	 	if (isset($_POST['browser_id']) && $_POST['browser_id']) {
		   $this->wpdb->update("{$this->wpdb->prefix}mitm_browser", $_data, array("browser_id" => $_POST['browser_id']));
		} else {
		   $this->wpdb->insert("{$this->wpdb->prefix}mitm_browser",$_data);
		}
	    wp_redirect('admin.php?page=mitm_bugtracker_browser_view&sucess=true');
		exit;
	 }
  	 require($this->pluginPath . "mitm_bugtracker_browser_add.php");
  }
  
  public function mitm_bugtracker_member_add() {
  	 if(isset($_REQUEST['member_name'])) {
	    $_data = array();
		$_data['member_name'] = $_POST['member_name'];
	 	if (isset($_POST['member_id']) && $_POST['member_id']) {
		   $this->wpdb->update("{$this->wpdb->prefix}mitm_member", $_data, array("member_id" => $_POST['member_id']));
		} else {
		   $this->wpdb->insert("{$this->wpdb->prefix}mitm_member",$_data);
		}
	    wp_redirect('admin.php?page=mitm_bugtracker_member_view&sucess=true');
		exit;
	 }
  	 require($this->pluginPath . "mitm_bugtracker_member_add.php");
  }
  
  public function mitm_bugtracker_menu() {
    add_menu_page('Bugtracker','Bugtracker', 'administrator', "mitm_bugtracker_view", array($this, 'mitm_bugtracker_view'), $this->pluginUrl . "icon.png");
    add_submenu_page("mitm_bugtracker_view", "Add New Bug", "Add New Bug", 'administrator', "mitm_bugtracker_add", array($this, 'mitm_bugtracker_add'));
    add_submenu_page("mitm_bugtracker_view", "Operating System", "Operating System",'administrator', "mitm_bugtracker_os_view", array($this, 'mitm_bugtracker_os_view'));
    add_submenu_page("mitm_bugtracker_view", "Add New OS", "Add New OS",'administrator', "mitm_bugtracker_os_add", array($this, 'mitm_bugtracker_os_add'));
    add_submenu_page("mitm_bugtracker_view", "Browser", "Browser",'administrator', "mitm_bugtracker_browser_view", array($this, 'mitm_bugtracker_browser_view'));	
    add_submenu_page("mitm_bugtracker_view", "Add New Browser", "Add New Browser",'administrator', "mitm_bugtracker_browser_add", array($this, 'mitm_bugtracker_browser_add'));
    add_submenu_page("mitm_bugtracker_view", "Assign Members", "Members",'administrator', "mitm_bugtracker_member_view", array($this, 'mitm_bugtracker_member_view'));		
    add_submenu_page("mitm_bugtracker_view", "Add New Member", "Add New Member",'administrator', "mitm_bugtracker_member_add", array($this, 'mitm_bugtracker_member_add'));	
  }
}

function register_mitm_bugtracker_plugin() {
  global $mitm_bugtracker;
  $mitm_bugtracker = new mitm_bugtracker();
}

function install_mitm_bugtracker() {
	global $wpdb;
	$table_name = $wpdb->prefix . "mitm_bug_tracker";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	dbDelta("CREATE TABLE $table_name (
				 bt_id int(11) NOT NULL AUTO_INCREMENT,
				 bt_projectname varchar(255) DEFAULT NULL,
				 bt_date date DEFAULT NULL,
				 bt_desc text,
				 bt_os varchar(255) DEFAULT NULL,
				 bt_browser varchar(255) DEFAULT NULL,
				 bt_assingto varchar(255) DEFAULT NULL,
				 bt_priority varchar(255) DEFAULT NULL,
				 bt_screenshot varchar(255) DEFAULT NULL,
				 bt_fixed enum('Yes','No') DEFAULT NULL,
				 PRIMARY KEY (bt_id)
		)");

	$table_name = $wpdb->prefix . "mitm_os";
			
	dbDelta("CREATE TABLE $table_name (
				 os_id int(11) NOT NULL AUTO_INCREMENT,
				 os_name varchar(255) DEFAULT NULL,
				 PRIMARY KEY (os_id)
			)");	 
			
	$table_name = $wpdb->prefix . "mitm_browser";			
	
	dbDelta("CREATE TABLE $table_name (
			 browser_id int(11) NOT NULL AUTO_INCREMENT,
			 browser_name varchar(255) DEFAULT NULL,
			 PRIMARY KEY (browser_id)
		)");	
		
	$table_name = $wpdb->prefix . "mitm_member";	
	
	dbDelta("CREATE TABLE $table_name (
			 member_id int(11) NOT NULL AUTO_INCREMENT,
			 member_name varchar(255) DEFAULT NULL,
			 PRIMARY KEY (member_id)
		)");			
}
add_action("init", "register_mitm_bugtracker_plugin");
register_activation_hook(__FILE__,'install_mitm_bugtracker');
