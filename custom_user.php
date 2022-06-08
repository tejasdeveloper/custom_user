<?PHP 
	add_action( 'admin_init', 'no_admin_access_to_custom_role', 100 );
	add_action('after_setup_theme', 'hide_admin_bar');
	add_action('init', 'add_custom_roles');
	
	add_shortcode("basic_user_form", 'form_general_user');
	add_action('admin_init', 'add_backend_user_edit');



function no_admin_access_to_custom_role()
{
    $redirect = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : home_url( '/' );
    if ( current_user_can( 'basic_need' )){
        exit( wp_redirect( $redirect ) );
	}
}

function hide_admin_bar() {
   if (current_user_can('administrator') || current_user_can('contributor') ) {
     // user can view admin bar
     show_admin_bar(true); // this line isn't essentially needed by default...
   } else {
     // hide admin bar
     show_admin_bar(false);
   }
}

function add_custom_roles(){

	add_role(
    'basic_need',
    __( 'General User' ),
    array(
        'read'         => false,  // true allows this capability
        'edit_posts'   => false,
        'delete_posts' => false, // Use false to explicitly deny
    )
	);
	
	add_role(
		'partner',
		__( 'Partner' ),
		array(
			'read'         => false,  // true allows this capability
			'edit_posts'   => false,
			'delete_posts' => false, // Use false to explicitly deny
		)
	);
}



function form_general_user(){
	 
	 if(isset($_POST["g_submit"])){
	 
	 	$g_first_name	= $_POST["g_first_name"];
		$g_last_name	= $_POST["g_last_name"];
		$g_email		= $_POST["g_email"];
		$g_phone		= $_POST["g_phone"];
		$g_address		= $_POST["g_address"];
		$g_username		= $_POST["g_username"];
		$g_password		= $_POST["g_password"];
		
		$_user_role		= "basic_need";
		
		$g_user_arr["first_name"] 	= $g_first_name;
		$g_user_arr["last_name"] 	= $g_last_name;
		
		$g_user_arr["user_email"] 	= $g_email;
		$g_user_arr["user_login"] 	= $g_username;
		$g_user_arr["user_pass"] 	= $g_password;
		$g_user_arr["role"]	 		= $_user_role;
		
		
		
		$user_id 	=	wp_insert_user($g_user_arr);
		add_user_meta( $user_id, "user_phone", $g_phone );
		add_user_meta( $user_id, "user_address", $g_address);
	 	
	 }
	 
	 ?>

		<div class="reg_form_main">
        	<form name="basic_form" action="" method="post" enctype="multipart/form-data">            
            <div class="reg_row">
            	<div class="formLable">First Name</div>
                <div class="formInput"><input type="text" name="g_first_name" id="g_first_name" /></div>
            </div>
            <div class="reg_row">
            	<div class="formLable">Last Name</div>
                <div class="formInput"><input type="text" name="g_last_name" id="g_last_name" /></div>
            </div>
             <div class="reg_row">
            	<div class="formLable">Email</div>
                <div class="formInput"><input type="text" name="g_email" id="g_email" /></div>
            </div>
             <div class="reg_row">
            	<div class="formLable">Phone</div>
                <div class="formInput"><input type="text" name="g_phone" id="g_phone" /></div>
            </div>
            <div class="reg_row">
            	<div class="formLable">Address</div>
                <div class="formInput"><input type="text" name="g_address" id="g_address" /></div>
            </div>
            <div class="reg_row">
            	<div class="formLable">Username</div>
                <div class="formInput"><input type="text" name="g_username" id="g_username" /></div>
            </div>
            <div class="reg_row">
            	<div class="formLable">Password</div>
                <div class="formInput"><input type="password" name="g_password" id="g_password" /></div>
            </div>
            <div class="reg_row">            
            	<input type="submit" name="g_submit" value="SUBMIT" id="g_submit" class="bluebutton" />
            </div>
            </form>
        
        </div>


<?PHP
}

function add_backend_user_edit(){
	// Hooks near the bottom of profile page (if current user) 
	add_action('show_user_profile', 'custom_user_profile_fields');
	
	// Hooks near the bottom of the profile page (if not current user) 
	add_action('edit_user_profile', 'custom_user_profile_fields');
	
	// Hook is used to save custom fields that have been added to the WordPress profile page (if current user) 
	add_action( 'personal_options_update', 'update_extra_profile_fields' );
	
	// Hook is used to save custom fields that have been added to the WordPress profile page (if not current user) 
	add_action( 'edit_user_profile_update', 'update_extra_profile_fields' );


}



// @param WP_User $user
function custom_user_profile_fields( $user ) {
?>
    <table class="form-table">
        <tr>
            <th>
                <label for="code"><?php _e( 'Phone' ); ?></label>
            </th>
            <td>
                <input type="text" name="user_phone" id="user_phone" value="<?php echo esc_attr( get_user_meta( $user->ID, 'user_phone', true ) ); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th>
                <label for="code"><?php _e( 'Address' ); ?></label>
            </th>
            <td>
                <input type="text" name="user_address" id="user_address" value="<?php echo esc_attr( get_user_meta(  $user->ID, 'user_address', true) ); ?>" class="regular-text" />
            </td>
        </tr>
    </table>
<?php
}



function update_extra_profile_fields( $user_id ) {
    if ( current_user_can( 'edit_user', $user_id ) ){
        update_user_meta( $user_id, 'user_phone', $_POST['user_phone'] );
		update_user_meta( $user_id, 'user_address', $_POST['user_address'] );
	}
}

?>