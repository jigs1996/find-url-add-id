<?php
// 

class Url_Name_List extends WP_List_Table {

	/** Class constructor */
	public function __construct() {

		parent::__construct( [
  			'singular' => __( 'ID Generator', 'sp' ), //singular name of the listed records
  			'plural'   => __( 'ID Generators', 'sp' ), //plural name of the listed records
  			'ajax'     => false //should this table support ajax?

  		]);

    }   

    function extra_tablenav( $which ) {
        if ( $which == "top" ){
        //The code that goes before the table is here
        // echo '<a class="button button-primary" style="float: left" href="'.admin_url('/').'admin.php?page=add-url-name">Add URL</a>';
        }
    }

    /**
     * Retrieve  data from  table
     *
     * @param int $per_page
     * @param int $page_number
     *
     * @return mixed
     */
    public static function get_name_url( $per_page = 15, $page_number = 1 ) {

        global $wpdb;
    
        $sql = "SELECT * FROM {$wpdb->prefix}sb_id_generator";
    
        if ( ! empty( $_REQUEST['orderby'] ) ) {
        $sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
        $sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
        }
    
        $sql .= " LIMIT $per_page";
    
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
    
    
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        
        return $result;
    }
    /**
     * Delete a url record.
     *
     * @param int $id customer ID
     */
    public static function delete_name_url( $id ) {
        global $wpdb;
    
        $wpdb->delete(
        "{$wpdb->prefix}sb_id_generator",
        [ 'ID' => $id ],
        [ '%d' ]
        );
    }

    /**
     * Returns the count of records in the database.
     *
     * @return null|string
     */
    public static function record_count() {
        global $wpdb;

        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}sb_id_generator";

        return $wpdb->get_var( $sql );
    }

    /** 
     * Text displayed when no customer data is available 
     */
    public function no_items() {
        _e( 'No data avaliable.', 'sp' );
    }

    /**
     * Method for name column
     *
     * @param array $item an array of DB data
     *
     * @return string
     */
    function column_url( $item ) {

        // create a nonce
        $edit_nonce = wp_create_nonce( 'sp_edit_customer' );
        $edit_url_name = wp_create_nonce( 'sp_edit_url_name' );

        $title = '<a href='.$item['url'].'>' . $item['url'] . '</a>';
    
        $actions = [
        'edit_price' => sprintf( '<a href="admin.php?page=edit-url-name&id='.$item['id'].'">Edit</a>', esc_attr( $_REQUEST['page'] ), 'edit_url_name', absint( $item['id'] ),  $edit_url_name),

        // 'find_replace' => sprintf( '<a href="'.admin_url('admin-ajax.php').'?action=find_replace&id='.$item['id'].'">Find Replace</a>', esc_attr( $_REQUEST['page'] ), 'edit_url_name', absint( $item['id'] ),  $edit_url_name),
    ];
    
        return $title . $this->row_actions( $actions );
    }

    /**
     * Render a column when no column specific method exists.
     *
     * @param array $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
        case 'id':
            return $item['id'];
        case 'name':
            return $item['name'];
        case 'url':
            return $this->column_url($item);
        default:
            return print_r( $item, true ); //Show the whole array for troubleshooting purposes
        }
    }

    /**
     * Render the bulk edit checkbox
     *
     * @param array $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
        '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id']
        );
    }

    /**
     *  Associative array of columns
     *
     * @return array
     */
    function get_columns() {
        $columns = [
        'cb'      => '<input type="checkbox" />',
        'id'    => __( 'ID', 'sp' ),
        'url'    => __( 'Url', 'sp' ),
        'name' => __( 'Name', 'sp' ),
        ];
    
        return $columns;
    }

    /**
     * Columns to make sortable.
     *
     * @return array
     */
    public function get_sortable_columns() {
        $sortable_columns = array(
            'id' => array('ID', true),
        'name' => array( 'Name', true ),
        'url' => array( 'Url', true )
        );
    
        return $sortable_columns;
    }

    /**
     * Returns an associative array containing the bulk action
     *
     * @return array
     */
    public function get_bulk_actions() {
        $actions = [
        'bulk-delete' => 'Delete'
        ];
    
        return $actions;
    }

    /**
     * Handles data query and filter, sorting, and pagination.
     */
        
    function prepare_items() {
        global $wpdb; //This is used only if making any database queries

        // print_r();die;
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = $this->get_items_per_page('url_per_page', 5);;
        
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        
        $this->process_bulk_action();
        
        $data = $this->get_name_url();
        
        function usort_reorder($a,$b){
            $orderby = (!empty($_REQUEST['orderby'])) ? strtolower($_REQUEST['orderby']) : 'id'; //If no sort, default to title
            $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
            $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
            return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
        }
        usort($data, 'usort_reorder');
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        // print_r($data);
        
        
        
        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        print_r($total_items);
        print_r($per_page);
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
        ) );


        $this->items = $data;
    }

    protected function bulk_actions( $which = '' ) {
		if ( is_null( $this->_actions ) ) {
			$this->_actions = $this->get_bulk_actions();
			/**
			 * Filters the list table Bulk Actions drop-down.
			 *
			 * The dynamic portion of the hook name, `$this->screen->id`, refers
			 * to the ID of the current screen, usually a string.
			 *
			 * This filter can currently only be used to remove bulk actions.
			 *
			 * @since 3.5.0
			 *
			 * @param string[] $actions An array of the available bulk actions.
			 */
			$this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
			$two            = '';
		} else {
			$two = '2';
		}
		if ( empty( $this->_actions ) ) {
			return;
		}
		echo '<label for="bulk-action-selector-' . esc_attr( $which ) . '" class="screen-reader-text">' . __( 'Select bulk action' ) . '</label>';
		echo '<select name="action' . $two . '" id="bulk-action-selector-' . esc_attr( $which ) . "\">\n";
		echo '<option value="-1">' . __( 'Bulk Actions' ) . "</option>\n";
		foreach ( $this->_actions as $name => $title ) {
			$class = 'edit' === $name ? ' class="hide-if-no-js"' : '';
			echo "\t" . '<option value="' . $name . '"' . $class . '>' . $title . "</option>\n";
		}
		echo "</select>\n";
		submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction$two" ) );
		echo "\n";
	}


    public function process_bulk_action() {

        //Detect when a bulk action is being triggered...
        if ( 'delete' === $this->current_action() ) {
      
          // In our file that handles the request, verify the nonce.
          $nonce = esc_attr( $_REQUEST['_wpnonce'] );
      
          if ( ! wp_verify_nonce( $nonce, 'sp_delete_name_url' ) ) {
            die( 'Go get a life script kiddies' );
          }
          else {
            self::delete_name_url( absint( $_GET['customer'] ) );
      
            wp_redirect( esc_url( add_query_arg() ) );
            exit;
          }
      
        }
      
        // If the delete bulk action is triggered
        if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
             || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
        ) {
      
          $delete_ids = esc_sql( $_POST['bulk-delete'] );
      
          // loop over the array of record IDs and delete them
          foreach ( $delete_ids as $id ) {
            self::delete_name_url( $id );
      
          }
      
          wp_redirect( esc_url( add_query_arg() ) );
          exit;
        }
    }
}




class SP_Plugin {

	// class instance
	static $instance;

	// customer WP_List_Table object
	public $customers_obj;

	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
    }

    public static function set_screen( $status, $option, $value ) {
        return $value;
    }
    
    public function plugin_menu() {
    
        $hook = add_menu_page(
            'ID Generator',
            'ID Generator',
            'manage_options',
            'sb-id-generator',
            [ $this, 'plugin_settings_page' ]
        );
    
        add_action( "load-$hook", [ $this, 'screen_option' ] );
    
    }
    /**
    * Screen options
    */
    public function screen_option() {

        $option = 'per_page';
        $args   = [
            'label'   => 'URL',
            'default' => 15,
            'option'  => 'url_per_page'
        ];

        add_screen_option( $option, $args );

        $this->customers_obj = new Url_Name_List();
    }
    /**
    * Plugin settings page
    */
    public function plugin_settings_page() {
        ?>
        <div class="wrap">
        <div class="wrap">
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder">
                    <div class="meta-box-sortables">
                        <div class="postbox-container" style="float: left">
                            <div class="postbox" >
                                <h2 class="hndle"><span>Add Url</span></h2>
                                <div class="inside">
                                    <div class="main">
                                         <?php
                                            /**
                                             * Individual product price editing table
                                             */
                                        ?>
                                        <div class="edit-product-vip-cust">
                                        <form action="<?php echo admin_url('admin-ajax.php'); ?>" method="post">
                                            <table>
                                                <thead>
                                                    <td>Url</td>
                                                    <td>Name</td>
                                                    <td></td>
                                                </thead>
                                                <tbody  class="close-row append-row">
                                                    <tr>
                                                        <td><input type="text" name="url" id ="url"></td>
                                                        <td><input type="text" name="name" id ="name"></td>
                                                        <td><button class="button button-primary" id="save-edited-rows">Add Url</button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                                <input type="hidden" name="action" value="add_url_name">
                                            </form>
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder">
                    <div id="post-body-content">
                        <div class="meta-box-sortables ui-sortable">
                            <form method="post">
                                <?php
                                $this->customers_obj->prepare_items();
                                $this->customers_obj->search_box('Search', 'search');
                                $this->customers_obj->display(); ?>
                            </form>
                        </div>
                    </div>
                </div>
                <br class="clear">
            </div>
        </div>
    <?php
    }
        /** Singleton instance */
    public static function get_instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new self();
        }

        return self::$instance;
    }

}