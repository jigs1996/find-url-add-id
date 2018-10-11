<?php
    //Add Admin Menu
    function edit_name_url(){
        add_submenu_page( null, 'Edit url', 'Edit url', 'manage_options', 'edit-url-name', 'edit_url_name' );
    }
    //Menu Page Design
    function edit_url_name(){

        $current_url=$_SERVER['REQUEST_URI'];
        /**
         * If customer query string not set then 
         * it'll redirect to list of vip customer page
         */
        if(!isset($_GET['id'])) {
            ?>
            <div>
                <h3 style="color: red">Please selct atleast one url</h3>
            </div>
            <?php
                $table = SP_Plugin::get_instance();
                $table->screen_option();
                $table->plugin_settings_page();
                return;
        }


        /**
         * IF SET QUERY STRING OF  "id"
         */
        global $wpdb;

        $sql = "SELECT * FROM {$wpdb->prefix}sb_id_generator WHERE id=".$_GET['id'];
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
?>        
        <!-- <button class="button button-primary" id="save-edited-rows">Save</button>
        <a href="admin.php?page=vip-customers-list" class="button button-primary">Tillbaka till alla VIP-kunder</a> -->
        <div class="wrap">
            <div id="dashboard-widgets-wrap">
                <div id="dashboard-widgets" class="metabox-holder">
                    <div class="meta-box-sortables">
                        <div class="postbox-container" style="float: left">
                            <div class="postbox" style="padding-left: 40px;padding-right: 40px;">
                                <h2 class="hndle"><span>Edit</span></h2>
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
                                                    </thead>
                                                        <tbody  class="close-row append-row">
                                                        
                                                        <?php
                                                            if(!empty($result)){
                                                                foreach($result as $key=>$value){
                                                        ?>
                                                                <tr>
                                                                    <th>ID</th>
                                                                    <td><?php echo $value['id']  ?></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>URL</th>
                                                                    <td><input type="text" name="url" id ="url" value="<?php echo $value['url'] ?>" style="width: 350px;"></td>
                                                                </tr>
                                                                <tr>
                                                                    <th>Name</th>
                                                                    <td><input type="text" name="name" id ="name" value="<?php echo $value['name'] ?>" style="width: 350px;"></td>
                                                                </tr>
                                                        <?php
                                                                }
                                                            } 
                                                        ?>
                                                                <!-- <tr>
                                                                    <td><input type="text" name="data" id ="sku"></td>
                                                                    <td><input type="number" name="price" id ="price"></td>
                                                                    <td><a href="javascript:void(0);" class="remove_row" style="text-decoration:none">X</a></td>
                                                                </tr> -->
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>
                                                                <td colspan="3" style="text-align: center;">
                                                                <input type="hidden" name="id" id ="id" value="<?php echo $value['id'] ?>">
                                                                <input type="hidden" name="action" value="save_edited_url_name">
                                                                <button class="button button-primary" id="save-edited-rows" style="padding: 0 20px;">Save</button></td>
                                                            </tr>
                                                        </tfoot>
                                                </table>
                                            </form>
                                            <!-- <a class="btn" id="add-row" style="cursor: pointer">Add Product</a> -->
                                        </div> 
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
<?php
       
    }
add_action( 'admin_menu', 'edit_name_url');

