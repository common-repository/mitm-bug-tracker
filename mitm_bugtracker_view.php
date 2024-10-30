<?php
if (isset($_GET['id']) && $_GET['id'] && isset($_GET['action']) && $_GET['action'] == 'delete') {
  $table_name = $this->wpdb->prefix . "mitm_bug_tracker"; 
  $this->wpdb->delete("$table_name", array('bt_id' => $_GET['id']));
  if ($this->wpdb->rows_affected) {
	 wp_redirect('admin.php?page=mitm_bugtracker_view&sucess=true');
	 exit;
  }
}

if( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Bub_Tracker_List_Table extends WP_List_Table { 
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'bug',    //singular name of the listed records
            'plural'    => 'bugs',   //plural name of the listed records
            'ajax'      => false     //does this table support ajax?
        ) );
    }
    
    function column_default($item, $column_name){
        switch($column_name){
            case 'bt_projectname':
			case 'bt_date':
			case 'os_name':
			case 'browser_name':
			case 'member_name':
			case 'bt_priority':
			case 'bt_fixed':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_bt_projectname($item){
        
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>','mitm_bugtracker_add','edit',$item['bt_id']),
            'delete'    => sprintf('<a href="?page=%s&action=%s&id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['bt_id']),
        );
        
        //Return the title contents
        return sprintf('%1$s %3$s',
            /*$1%s*/ $item['bt_projectname'],
            /*$2%s*/ $item['bt_id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['bt_id']                //The value of the checkbox should be the record's id
        );
    }
    
    function get_columns(){
        $columns = array(
            'cb'        	 => '<input type="checkbox" />', //Render a checkbox instead of text
            'bt_projectname' => 'Project Name',
            'bt_date'		 => 'Date',
			'os_name'		 => 'OS',
			'browser_name'	 => 'Browser',
			'member_name'	 => 'Assing To',
			'bt_priority'	 => 'Priority',
			'bt_fixed'	 	 => 'Fixed?'			
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'bt_projectname' => array('bt_projectname',false),     //true means it's already sorted
            'bt_date'  	     => array('bt_date',false),
			'bt_url'         => array('bt_url',false),
			'os_name'        => array('os_name',false),
			'browser_name'   => array('bt_browser',false),
			'member_name'    => array('bt_assingto',false),
			'bt_priority'    => array('bt_priority',false),
			'bt_fixed'       => array('bt_fixed',false)																	
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            'delete'    => 'Delete'
        );
        return $actions;
    }
    
    function process_bulk_action() {
        //Detect when a bulk action is being triggered...
 	    $bug_id = ( is_array( $_REQUEST['bug'] ) ) ? $_REQUEST['bug'] : array( $_REQUEST['bug'] );
        if( 'delete'===$this->current_action()) {
			if(count($bug_id)>0)	{
				foreach ($bug_id as $id ) {
					global $wpdb;
            		$id = absint( $id );
					$tbl_bug = $wpdb->prefix . "mitm_bug_tracker"; 
		            $wpdb->query( "DELETE FROM $tbl_bug WHERE bt_id = $id" );
				}
				wp_redirect('admin.php?page=mitm_bugtracker_view&sucess=true');
				exit;
        	}
      	}
    }
    
    function prepare_items() { 
        global $wpdb;
        $tbl_bug 	 = $wpdb->prefix . "mitm_bug_tracker";  // do not forget about tables prefix
		$tbl_os 	 = $wpdb->prefix . "mitm_os";
		$tbl_browser = $wpdb->prefix . "mitm_browser";
		$tbl_member  = $wpdb->prefix . "mitm_member";
	
        $per_page = 5; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
		
        $total_items = $wpdb->get_var("SELECT COUNT(bt_id) FROM $tbl_bug");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'bt_id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';
		
	    $current_page = $this->get_pagenum();
        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
		$qry = "SELECT 
					$tbl_bug.*,
					$tbl_os.os_name,
					$tbl_browser.browser_name,
					$tbl_member.member_name					
				FROM 
					$tbl_bug,
					$tbl_os,
					$tbl_browser,
					$tbl_member
				WHERE 
					$tbl_bug.bt_os     = $tbl_os.os_id  AND
					$tbl_bug.bt_browser  = $tbl_browser.browser_id  AND
					$tbl_bug.bt_assingto = $tbl_member.member_id										
				ORDER BY 
					$orderby $order";
				
        $data  = $wpdb->get_results($qry, ARRAY_A);
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
	    $this->items = $data;
        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }
}
//Create an instance of our package class...
$bugTrackerListTable = new Bub_Tracker_List_Table();
//Fetch, prepare, sort, and filter our data...
$bugTrackerListTable->prepare_items();
?>
<div class="wrap">
  <div id="icon-users" class="icon32"><br/>
  </div>
  <h2>Bug Tracker List&nbsp;&nbsp;<a class="add-new-h2" href="admin.php?page=mitm_bugtracker_add">Add New</a></h2>
   <?php if(isset($_REQUEST['sucess']) && $_REQUEST['sucess'] == true) { ?>
	  <div class="updated below-h2" id="message">
		<p>Record has been deleted successfully.</p>
	  </div>
  <?php } ?>
  <form id="bugs-filter" method="get">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <?php $bugTrackerListTable->display(); ?>
  </form>
</div>