<?php
if (isset($_GET['browser_id']) && $_GET['browser_id'] && isset($_GET['action']) && $_GET['action'] == 'delete') {
  $table_name = $this->wpdb->prefix . "mitm_browser"; 
  $this->wpdb->delete("$table_name", array('browser_id' => $_GET['browser_id']));
  if ($this->wpdb->rows_affected) {
	 wp_redirect('admin.php?page=mitm_bugtracker_browser_view&delsucess=true');
	 exit;
  }
}

if( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Bub_Tracker_Browser_List_Table extends WP_List_Table { 
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'browser',    //singular name of the listed records
            'plural'    => 'browseres',   //plural name of the listed records
            'ajax'      => false     //does this table support ajax?
        ) );
    }
    
    function column_default($item, $column_name){
        switch($column_name){
            case 'browser_name':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_browser_name($item){
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&browser_id=%s">Edit</a>','mitm_bugtracker_browser_add','edit',$item['browser_id'])
            //'delete'    => sprintf('<a href="?page=%s&action=%s&browser_id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['browser_id']),
        );
        
        //Return the title contents
        return sprintf('%1$s %3$s',
            /*$1%s*/ $item['browser_name'],
            /*$2%s*/ $item['browser_id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['browser_id']                //The value of the checkbox should be the record's id
        );
    }
    
    function get_columns(){
        $columns = array(
            'cb'      => '<input type="checkbox" />', //Render a checkbox instead of text
            'browser_name' => 'Browser Name'
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'browser_name' => array('browser_name',false)    //true means it's already sorted
        );
        return $sortable_columns;
    }
    
    function get_bulk_actions() {
        $actions = array(
            //'delete'    => 'Delete'
        );
        return $actions;
    }
    
    function process_bulk_action() {
      
    }
    
    function prepare_items() { 
        global $wpdb;
        $table_name = $wpdb->prefix . "mitm_browser";  // do not forget about tables prefix
	
        $per_page = 5; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
       // $this->process_bulk_action();

        // will be used in pagination settings
	
        $total_items = $wpdb->get_var("SELECT COUNT(browser_id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'browser_id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
   	    $current_page = $this->get_pagenum();
		$data = $wpdb->get_results("SELECT * FROM $table_name ORDER BY $orderby $order", ARRAY_A);

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
$bugTrackerBrowserListTable = new Bub_Tracker_Browser_List_Table();
//Fetch, prepare, sort, and filter our data...
$bugTrackerBrowserListTable->prepare_items();
?>

<div class="wrap">
  <div id="icon-users" class="icon32"><br/>
  </div>
  <h2>Bug Tracker Browser List&nbsp;&nbsp;<a class="add-new-h2" href="admin.php?page=mitm_bugtracker_browser_add">Add New</a></h2>
  <?php if(isset($_REQUEST['sucess']) && $_REQUEST['sucess'] == true) { ?>
  <div class="updated below-h2" id="message">
    <p>Browser has been added successfully.</p>
  </div>
  <?php } ?>
    <?php if(isset($_REQUEST['delsucess']) && $_REQUEST['delsucess'] == true) { ?>
 	 <div class="updated below-h2" id="message">
   	   <p>Record has been deleted successfully.</p>
  	 </div>
  <?php } ?>
  <form id="bugs-filter" method="get">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <?php $bugTrackerBrowserListTable->display(); ?>
  </form>
</div>
