<?php
if( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Bub_Tracker_OS_List_Table extends WP_List_Table { 
    
    function __construct(){
        global $status, $page;
                
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'os',    //singular name of the listed records
            'plural'    => 'oss',   //plural name of the listed records
            'ajax'      => false     //does this table support ajax?
        ) );
    }
    
    function column_default($item, $column_name){
        switch($column_name){
            case 'os_name':
                return $item[$column_name];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }
    
    function column_os_name($item){
        //Build row actions
        $actions = array(
            'edit'      => sprintf('<a href="?page=%s&action=%s&os_id=%s">Edit</a>','mitm_bugtracker_os_add','edit',$item['os_id'])
            //'delete'    => sprintf('<a href="?page=%s&action=%s&os_id=%s">Delete</a>',$_REQUEST['page'],'delete',$item['os_id']),
        );
        
        //Return the title contents
        return sprintf('%1$s %3$s',
            /*$1%s*/ $item['os_name'],
            /*$2%s*/ $item['os_id'],
            /*$3%s*/ $this->row_actions($actions)
        );
    }
    
    function column_cb($item){
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
            /*$2%s*/ $item['os_id']                //The value of the checkbox should be the record's id
        );
    }
    
    function get_columns(){
        $columns = array(
            'cb'      => '<input type="checkbox" />', //Render a checkbox instead of text
            'os_name' => 'OS Name'
        );
        return $columns;
    }

    function get_sortable_columns() {
        $sortable_columns = array(
            'os_name' => array('os_name',false)    //true means it's already sorted
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
        //Detect when a bulk action is being triggered...
	
        if( 'delete'===$this->current_action() ) {
            wp_die('Items deleted (or they would be if we had items to delete)!');
        }
    }
    
    function prepare_items() { 
        global $wpdb;
        $table_name = $wpdb->prefix . "mitm_os";  // do not forget about tables prefix
	
        $per_page = 5; // constant, how much records will be shown per page

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        // here we configure table headers, defined in our methods
        $this->_column_headers = array($columns, $hidden, $sortable);

        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // will be used in pagination settings
		
        $total_items = $wpdb->get_var("SELECT COUNT(os_id) FROM $table_name");

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'os_id';
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
$bugTrackerOSListTable = new Bub_Tracker_OS_List_Table();
//Fetch, prepare, sort, and filter our data...
$bugTrackerOSListTable->prepare_items();
?>

<div class="wrap">
  <div id="icon-users" class="icon32"><br/>
  </div>
  <h2>Bug Tracker OS List&nbsp;&nbsp;<a class="add-new-h2" href="admin.php?page=mitm_bugtracker_os_add">Add New</a></h2>
  <?php if(isset($_REQUEST['sucess']) && $_REQUEST['sucess'] == true) { ?>
  <div class="updated below-h2" id="message">
    <p>Operating System has been added successfully.</p>
  </div>
  <?php } ?>
  <form id="bugs-filter" method="get">
    <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
    <?php $bugTrackerOSListTable->display(); ?>
  </form>
</div>
