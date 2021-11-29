<?php
/*
 * League Administration
 *
 * @package           PluginPackage
 * @author            osw
 * @copyright         2020 osw
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       League Administration
 * Plugin URI:        https://osw.co.il
 * Description:       Flexible, convenient, easy-to-use online sports administration management plugin
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.4
 * Author:            OSW
 * Author URI:        https://osw.co.il
 * Text Domain:       plugin-slug
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

defined( 'ABSPATH' ) or die( 'Get out!' );

/*********************************** Plugin setting page ***********************************/
/**
 * Create sub menu page "new fixtures".
 */
function league_administration_add_settings_page() {
  add_submenu_page(
        'edit.php?post_type=cpt_1_fixtures',
        __( 'הגדרות Reference', 'textdomain' ),
        __( 'הגדרות', 'textdomain' ),
    'manage_options',
    'nelio-example-plugin',
    'nelio_render_settings_page'
  );
}
add_action( 'admin_menu', 'league_administration_add_settings_page' );


function nelio_render_settings_page() {
?>
  <h2>League Administration Plugin Settings</h2>
  <form action="options.php" method="post">
    <?php 
	settings_fields( 'nelio_example_plugin_settings' );
	do_settings_sections( 'nelio_example_plugin' );
	?>
    <input type="submit" name="submit" class="button button-primary" value="<?php esc_attr_e( 'Save' ); ?>"/>
  </form> 
<?php
}

function league_administration_settings() {
  register_setting(
    'nelio_example_plugin_settings',
    'nelio_example_plugin_settings',
    'nelio_validate_example_plugin_settings'
  );

  add_settings_section(
    'section_one',
    'Section One',
    'nelio_section_one_text',
    'nelio_example_plugin'
  );

  add_settings_field(
    'some_text_field',
    'Some Text Field',
    'nelio_render_some_text_field',
    'nelio_example_plugin',
    'section_one'
  );

  add_settings_field(
    'another_number_field',
    'Another Number Field',
    'nelio_render_another_number_field',
    'nelio_example_plugin',
    'section_one'
  );
}
add_action( 'admin_init', 'league_administration_settings' );


function nelio_validate_example_plugin_settings( $input ) {
    $output['some_text_field']      = sanitize_text_field( $input['some_text_field'] );
    $output['another_number_field'] = absint( $input['another_number_field'] );
    // ...
    return $output;
}

function nelio_section_one_text() {
  echo '<p>Settings Section</p>';
}

function nelio_render_some_text_field() {
  $options = get_option( 'nelio_example_plugin_settings' );
  printf(
    '<input type="text" name="%s" value="%s" />',
    esc_attr( 'nelio_example_plugin_settings[some_text_field]' ),
    esc_attr( $options['some_text_field'] )
  );
}

function nelio_render_another_number_field() {
  $options = get_option( 'nelio_example_plugin_settings' );
  printf(
    '<input type="number" name="%s" value="%s" />',
    esc_attr( 'nelio_example_plugin_settings[another_number_field]' ),
    esc_attr( $options['another_number_field'] )
  );
}

/*********************************** End plugin setting page ***********************************/
/*********************************** Plugin Post Type,Taxonomies ***********************************/

/**
 * Create  Post type "team".
 *
 * @see register_post_type() for registering custom post types.
 */
function o_team_custom_post_type() {
	$labels = array(
		'name'                => __( 'כל הקבוצות' ),
		'singular_name'       => __( 'cpt_1_team'),
		'menu_name'           => __( 'קבוצות'),
		'parent_item_colon'   => __( 'מונח אב'),
		'all_items'           => __( 'כל הקבוצות'),
		'view_item'           => __( 'הצג קבוצה'),
		'add_new_item'        => __( 'הוסף קבוצה'),
		'add_new'             => __( 'הוסף חדש'),
		'edit_item'           => __( 'ערוך קבוצה'),
		'update_item'         => __( 'עדכן קבוצה'),
		'search_items'        => __( 'חפש קבוצה'),
		'not_found'           => __( 'לא נמצא'),
		'not_found_in_trash'  => __( 'לא נמצא בפח האשפה')
	);
	$args = array(
		'label'               => __( 'קבוצות'),
		'description'         => __( 'קבוצות הליגה'),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields'),
		'public'              => true,
		'hierarchical'        => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'has_archive'         => true,
	    'can_export'          => true,
		'exclude_from_search' => false,
	    'yarpp_support'       => true,
		//'taxonomies' 	      => array('post_tag'),
		'publicly_queryable'  => true,
		'capability_type'     => 'page'
);
	register_post_type( 'cpt_1_team', $args );
}
add_action( 'init', 'o_team_custom_post_type', 0 );

/**
 * Create two taxonomies, competition and season for the post type "team".
 *
 * @see register_post_type() for registering custom post types.
 */
function o_create_competition_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x( 'תחרויות', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'competition', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'חפש תחרויות', 'textdomain' ),
        'all_items'         => __( 'כל התחרויות', 'textdomain' ),
        'parent_item'       => __( 'מונח האב', 'textdomain' ),
        'parent_item_colon' => __( 'מונח האב:', 'textdomain' ),
        'edit_item'         => __( 'ערוך תחרות', 'textdomain' ),
        'update_item'       => __( 'עדכן תחרות', 'textdomain' ),
        'add_new_item'      => __( 'הוסף תחרות', 'textdomain' ),
        'new_item_name'     => __( 'שם תחרות', 'textdomain' ),
        'menu_name'         => __( 'תחרויות', 'textdomain' ),
    );
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'competition' ),
    );
    register_taxonomy( 'competition', array( 'cpt_1_team' ), $args );
    unset( $args );
    unset( $labels );
    // Add new taxonomy
    $labels = array(
        'name'                       => _x( 'עונות', 'taxonomy general name', 'textdomain' ),
        'singular_name'              => _x( 'season', 'taxonomy singular name', 'textdomain' ),
        'search_items'               => __( 'חפש עונה', 'textdomain' ),
        'popular_items'              => __( 'עונות פופלריות', 'textdomain' ),
        'all_items'                  => __( 'כל העונות', 'textdomain' ),
        'parent_item'                => null,
        'parent_item_colon'          => null,
        'edit_item'                  => __( 'ערוך עונה', 'textdomain' ),
        'update_item'                => __( 'עדכן עונה', 'textdomain' ),
        'add_new_item'               => __( 'הוסף עונה', 'textdomain' ),
        'new_item_name'              => __( 'שם עונה', 'textdomain' ),
        'separate_items_with_commas' => __( 'Separate writers with commas', 'textdomain' ),
        'add_or_remove_items'        => __( 'Add or remove writers', 'textdomain' ),
        'choose_from_most_used'      => __( 'Choose from the most used writers', 'textdomain' ),
        'not_found'                  => __( 'No writers found.', 'textdomain' ),
        'menu_name'                  => __( 'עונות', 'textdomain' ),
    );
  
    $args = array(
        'hierarchical'          => false,
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'season' ),
    );
  
    register_taxonomy( 'season', 'cpt_1_team', $args );
}
add_action( 'init', 'o_create_competition_taxonomies', 0 );

/**
 * Create  Post type "fixtures".
 *
 * @see register_post_type() for registering custom post types.
 */
function o2_team_custom_post_type() {
	$labels = array(
		'name'                => __( 'כל המשחקים' ),
		'singular_name'       => __( 'cpt_1_fixtures'),
		'menu_name'           => __( 'משחקים'),
		'parent_item_colon'   => __( 'מונח אב'),
		'all_items'           => __( 'כל המשחקים'),
		'view_item'           => __( 'הצג משחק'),
		'add_new_item'        => __( 'הוסף משחק'),
		'add_new'             => __( 'הוסף חדש'),
		'edit_item'           => __( 'ערוך משחק'),
		'update_item'         => __( 'עדכן משחק'),
		'search_items'        => __( 'חפש משחק'),
		'not_found'           => __( 'לא נמצא'),
		'not_found_in_trash'  => __( 'לא נמצא בפח האשפה')
	);
	$args = array(
		'label'               => __( 'משחקים'),
		'description'         => __( 'משחקים בליגה'),
		'labels'              => $labels,
		//'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'custom-fields'),
		'supports'            => array(''),
		'public'              => true,
		'hierarchical'        => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'has_archive'         => true,
	    'can_export'          => true,
		'exclude_from_search' => false,
	    'yarpp_support'       => true,
		//'taxonomies' 	      => array('post_tag'),
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		//'register_meta_box_cb' => 'global_notice_meta_box'
);
	register_post_type( 'cpt_1_fixtures', $args );
}
add_action( 'init', 'o2_team_custom_post_type', 0 );

/**
 * Create two taxonomies, stage and division for the post type "fixtures".
 *
 * @see register_post_type() for registering custom post types.
 */
function o2_create_competition_taxonomies() {
    // Add new taxonomy, make it hierarchical (like categories)
    $labels = array(
        'name'              => _x( 'שלב', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'stage', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'חפש שלב', 'textdomain' ),
        'all_items'         => __( 'כל השלבים', 'textdomain' ),
        'parent_item'       => __( 'מונח האב', 'textdomain' ),
        'parent_item_colon' => __( 'מונח האב:', 'textdomain' ),
        'edit_item'         => __( 'ערוך שלב', 'textdomain' ),
        'update_item'       => __( 'עדכן שלב', 'textdomain' ),
        'add_new_item'      => __( 'הוסף שלב', 'textdomain' ),
        'new_item_name'     => __( 'שם השלב', 'textdomain' ),
        'menu_name'         => __( 'שלבים', 'textdomain' ),
    );
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'stage' ),
    );
    register_taxonomy( 'stage', array( 'cpt_1_fixtures' ), $args );
    unset( $args );
    unset( $labels );
    // Add new taxonomy, NOT hierarchical (like tags)
    $labels = array(
        'name'              => _x( 'בתים', 'taxonomy general name', 'textdomain' ),
        'singular_name'     => _x( 'division', 'taxonomy singular name', 'textdomain' ),
        'search_items'      => __( 'חפש בית', 'textdomain' ),
        'all_items'         => __( 'כל הבתים', 'textdomain' ),
        'parent_item'       => __( 'מונח האב', 'textdomain' ),
        'parent_item_colon' => __( 'מונח האב:', 'textdomain' ),
        'edit_item'         => __( 'ערוך בית', 'textdomain' ),
        'update_item'       => __( 'עדכן בית', 'textdomain' ),
        'add_new_item'      => __( 'הוסף בית', 'textdomain' ),
        'new_item_name'     => __( 'שם הבית', 'textdomain' ),
        'menu_name'         => __( 'בתים', 'textdomain' ),
    );
    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'division' ),
    );
  
    register_taxonomy( 'division', 'cpt_1_fixtures', $args );
}
add_action( 'init', 'o2_create_competition_taxonomies', 0 );

/*********************************** End Plugin Post Type,Taxonomies ***********************************/
/*********************************** Plugin Meta Box Fixtures ***********************************/

/**
 * Create a metabox with multiple fields.
 * Replace `_namespace` with some namespace for your project to avoid conflicts with other items
 */

/**
* Create the metabox
* @link https://developer.wordpress.org/reference/functions/add_meta_box/
*/
function _namespace_create_metabox() {
	// Can only be used on a single post type (ie. page or post or a custom post type).
	// Must be repeated for each post type you want the metabox to appear on.
	add_meta_box(
		'_namespace_metabox', // Metabox ID
		'תוצאות המשחק', // Title to display
		'_namespace_render_metabox', // Function to call that contains the metabox content
		'cpt_1_fixtures', // Post type to display metabox on
		'normal', // Where to put it (normal = main colum, side = sidebar, etc.)
		'default' // Priority relative to other metaboxes
	);
}
add_action( 'add_meta_boxes', '_namespace_create_metabox' );

/**
* Create the metabox default values
* This allows us to save multiple values in an array, reducing the size of our database.
* Setting defaults helps avoid "array key doesn't exit" issues.
* @todo
*/
function _namespace_metabox_defaults() {
	return array(
		'item_1'   => '-',  // Fixture Home team
		'item_2'   => '-',  // Fixture Away team
		'item_3'   => '',    // Set 1 home
		'item_4'   => '',    // Set 2 home
		'item_5'   => '',    // Set 3 home
		'item_6'   => '',    // Set 1 away
		'item_7'   => '',    // Set 2 away
		'item_8'   => '',    // Set 3 away
		'item_9'   => 0,    // Fixture Home result 
		'item_10'  => 0,    // Fixture Away result
		'item_11'  => 0,    // Fixture Date
		'item_12'  => '19:35', // Fixture Time
		'item_13'  => 0,    // Fixture result
		'item_14'  => 0, // Technical loss home
		'item_15'  => 0, // Technical loss away
		'item_16'  => 0, // Home Game counter
		'item_17'  => 0, // Away Game counter
		'item_18'  => 0, // Fixture Home win
		'item_19'  => 0, // Fixture Away win
		'item_20'  => 0, // Fixture Home lose
		'item_21'  => 0, // Fixture Away lose
	);
}


/**
 * 222222222Create Date Fixtures Meta Box
 *
 * @param int $post_id, int post
 */
// Register datepicker ui for properties   
function  register_datepick_fixtures2(){
    global $post;
    if($post->post_type == 'cpt_1_fixtures' && is_admin()) 
	{
		// Styles
	     wp_register_style( 'datetimepicker-style', WP_CONTENT_URL . '/plugins/league-administration/js/datetimepicker/jquery.datetimepicker.min.css', false, '1.0.0' );
         wp_enqueue_style( 'datetimepicker-style' );
	    
		// Scripts
	    wp_register_script('datetimepicker-js',WP_CONTENT_URL .'/plugins/league-administration/js/datetimepicker/jquery.datetimepicker.full.min.js', array('jquery'), '1.0.0', true);
        wp_enqueue_script('datetimepicker-js');
    }
}
add_action('admin_print_scripts', 'register_datepick_fixtures2');


/**
* Render the metabox markup
* This is the function called in `_namespace_create_metabox()`
*/
function _namespace_render_metabox() 
{
    // Variables
	global $post; // Get the current post data
	$saved = get_post_meta( $post->ID, '_namespace', true ); // Get the saved values
	$defaults = _namespace_metabox_defaults(); // Get the default values
	$details = wp_parse_args( $saved, $defaults ); // Merge the two in case any fields don't exist in the saved data
?>
<script>
jQuery(document).ready(function($){
	$('#_namespace_custom_metabox_item_11').datetimepicker({
		i18n:{
            de:{
                months:[
                'Januar','Februar','März','April',
                'Mai','Juni','Juli','August',
                'September','Oktober','November','Dezember',
                ],
                dayOfWeek:[
                "So.", "Mo", "Di", "Mi", 
                "Do", "Fr", "Sa.",
                ]
            }
        },
        timepicker:false,
        format:'Y/m/d'
	});
	$('#_namespace_custom_metabox_item_12').datetimepicker({
       datepicker:false,
	   step: 15,
	   minTime:'19:00',
	   maxTime:'22:00',
       format:'H:i'
    });
});
</script>
<table id="fix-table11" border="0" cellpadding="1%" style="width: 100%; padding: 1.4rem; background-color: #ffffff; border: 1px solid #c7c7c7;">
	<tr>
		<td>
		   	<label for="_namespace_custom_metabox_item_11"><?php _e( 'בחר תאריך', '_namespace' ); ?></label>
	    </td>
	    <td>
		   	<input type="text" name="_namespace_custom_metabox[item_11]" id="_namespace_custom_metabox_item_11" value="<?php echo esc_attr( $details['item_11'] ); ?>" style="width: 175; margin-right: 1rem; text-align: center;">
		</td>
		
		<td>
		   	<label for="_namespace_custom_metabox_item_12"><?php _e( 'בחר שעה', '_namespace' ); ?></label>
	    </td>
	    <td>
		   	<input type="text" name="_namespace_custom_metabox[item_12]" id="_namespace_custom_metabox_item_12" value="<?php echo esc_attr( $details['item_12'] ); ?>" style="width: 175; margin-right: 1rem; text-align: center;">
		</td>
	</tr>
</table>
<fieldset>	
    <table id="fix-table" border="0" cellpadding="1%" style="width: 100%; padding: 1.4rem; background-color: #ffffff; border: 1px solid #c7c7c7;">
    <thead>
            <tr>
                <th><h1><b>קבוצת בית</b></h1></th>
		        <th><h1><b>VS</b></h1></th>
                <th><h1><b>קבוצת חוץ</b></h1></th>
            </tr>
    </thead>
    <tbody>
    <tr>
	    <td>
		    <table id="fix-table1" border="0" cellpadding="1%" style="width: 100%; padding: 1.4rem; background-color: #ffffff; border: 1px solid #c7c7c7;">
                <tr>
	                <td>
		               <label for="_namespace_custom_metabox_item_1"><?php _e( 'בחר קבוצת בית', '_namespace' );?></label>
		            </td>
                    <td>
			            <select name="_namespace_custom_metabox[item_1]" id="_namespace_custom_metabox_item_1">
    		            <?php
    		            $args = array(  
        	            'post_type' => 'cpt_1_team',
        	            'post_status' => 'publish',
        	            'posts_per_page' => 100, 
        	            'orderby' => 'title', 
        	            'order' => 'ASC'
    		            );
			
    		            $loop = new WP_Query( $args ); 
			
			            while ( $loop->have_posts() ) : $loop->the_post(); 
	    
				        $team_page_title_temp  = $details['item_1'];

				        $team_page_title = get_the_title();
		
				        if($team_page_title_temp == $team_page_title)
				        {
		   			    echo '<option selected value="'.$team_page_title.'">'.$team_page_title.'</option>';   
	    		        }
				        else
				        {
					    echo '<option  value="'.$team_page_title.'">'.$team_page_title.'</option>';   
				        } 
			            endwhile;
			            wp_reset_postdata();
    		            ?>
   			            </select>  
		           </td>
	            </tr>
				<tr>
					<td>
		   				<label for="_namespace_custom_metabox_item_3"><?php _e( 'מערכה ראשונה', '_namespace' ); ?></label>
	    			</td>
	    			<td>
		   				<input type="text" name="_namespace_custom_metabox[item_3]" id="_namespace_custom_metabox_item_3" value="<?php echo esc_attr( $details['item_3'] ); ?>" style="width: 45px;text-align:center;padding:0;">
					</td>
				</tr>
				<tr>
					<td>
		   				<label for="_namespace_custom_metabox_item_4"><?php _e( 'מערכה שניה', '_namespace' ); ?></label>
	    			</td>
	    			<td>
		   				<input type="text" name="_namespace_custom_metabox[item_4]" id="_namespace_custom_metabox_item_4" value="<?php echo esc_attr( $details['item_4'] ); ?>" style="width: 45px;text-align:center;padding:0;">
					</td>
				</tr>
				<tr>
					<td>
		   				<label for="_namespace_custom_metabox_item_5"><?php _e( 'מערכה שלישית', '_namespace' ); ?></label>
	    			</td>
	    			<td>
		   				<input type="text" name="_namespace_custom_metabox[item_5]" id="_namespace_custom_metabox_item_5" value="<?php echo esc_attr( $details['item_5'] ); ?>" style="width: 45px;text-align:center;padding:0;">
					</td>
				</tr>
				<tr>
					<td>
		   				<label for="_namespace_custom_metabox_item_14"><?php _e( 'הפסד טכני', '_namespace' ); ?></label>
	    			</td>
	    			<td>		
<script>
  document.addEventListener("DOMContentLoaded", function() 
  {
	    var checkbox = document.getElementById("_namespace_custom_metabox_item_14");
	    var checkbox2 = document.getElementById("_namespace_custom_metabox_item_15");
	  
		checkbox.onclick = function tes() 
		{	
			if ( this.checked == true ) 
			{
				this.value = 1;
			} 
			else 
			{
				this.value = 0;
			}
		} 
		checkbox2.onclick = function tes() 
		{	
			if ( this.checked == true ) 
			{
				this.value = 1;
			} 
			else 
			{
				this.value = 0;
			}
		}
  });
</script>
                        <?php  $checked = ($details['item_14'] == 0) ? "" : "checked"; ?>
						<input style="margin:0;" type="checkbox" id="_namespace_custom_metabox_item_14" name="_namespace_custom_metabox[item_14]" value="<?php echo esc_attr( $details['item_14'] ); ?>" <?php echo $checked; ?> >
					</td>
				</tr>
            </table>
		</td>
		<td></td>
		<td>
		    <table id="fix-table2" border="0" cellpadding="1%" style="width: 100%; padding: 1.4rem; background-color: #f0f0f1; border: 1px solid #c7c7c7;">
                <tr>
	                <td>
		   				<label for="_namespace_custom_metabox_item_2"><?php _e( 'בחר קבוצת חוץ', '_namespace' );?></label>
					</td>
        			<td>
		   			<select name="_namespace_custom_metabox[item_2]" id="_namespace_custom_metabox_item_2">
    					<?php
    					$args2 = array(  
        				'post_type' => 'cpt_1_team',
        				'post_status' => 'publish',
        				'posts_per_page' => 100, 
        				'orderby' => 'title', 
        				'order' => 'ASC'
    					);
			
    					$loop2 = new WP_Query( $args2 ); 
			
						while ( $loop2->have_posts() ) : $loop2->the_post(); 
	    
						$team_page_title_temp2  = $details['item_2'];

						$team_page_title2 = get_the_title();
		
						if($team_page_title_temp2 == $team_page_title2)
						{
		   				echo '<option selected value="'.$team_page_title2.'">'.$team_page_title2.'</option>';   
	    				}
						else
						{
						echo '<option  value="'.$team_page_title2.'">'.$team_page_title2.'</option>';   
						} 
						endwhile;
						wp_reset_postdata();
    					?>
   						</select>
					</td>
	            </tr>
				<tr>
					<td>
		   				<label for="_namespace_custom_metabox_item_6"><?php _e( 'מערכה ראשונה', '_namespace' ); ?></label>
	    			</td>
	    			<td>
		   				<input type="text" name="_namespace_custom_metabox[item_6]" id="_namespace_custom_metabox_item_6" value="<?php echo esc_attr( $details['item_6'] ); ?>" style="width: 45px;text-align:center;padding:0;">
					</td>
				</tr>
				<tr>
					<td>
		   				<label for="_namespace_custom_metabox_item_7"><?php _e( 'מערכה שניה', '_namespace' ); ?></label>
	    			</td>
	    			<td>
		   				<input type="text" name="_namespace_custom_metabox[item_7]" id="_namespace_custom_metabox_item_7" value="<?php echo esc_attr( $details['item_7'] ); ?>" style="width: 45px;text-align:center;padding:0;">
					</td>
				</tr>
				<tr>
					<td>
		   				<label for="_namespace_custom_metabox_item_8"><?php _e( 'מערכה שלישית', '_namespace' ); ?></label>
	    			</td>
	    			<td>
		   				<input type="text" name="_namespace_custom_metabox[item_8]" id="_namespace_custom_metabox_item_8" value="<?php echo esc_attr( $details['item_8'] ); ?>" style="width: 45px;text-align:center;padding:0;">
					</td>
				</tr>
				<tr>
					<td>
		   				<label for="_namespace_custom_metabox_item_15"><?php _e( 'הפסד טכני', '_namespace' ); ?></label>
	    			</td>
	    			<td>
		   				<?php  $checked2 = ($details['item_15'] == 0) ? "" : "checked"; ?>
						<input style="margin:0;" type="checkbox" id="_namespace_custom_metabox_item_15" name="_namespace_custom_metabox[item_15]" value="<?php echo esc_attr( $details['item_15'] ); ?>" <?php echo $checked2; ?> >
					</td>
				</tr>
            </table>
		</td>
	</tr>
	</tbody>
	<tfoot>
        <tr>
            <td colspan="4" style=" text-align: center; ">
			    <table id="fix-table3" border="0" cellpadding="1%" style=" width: 100%; ">
				    <tr>
                        <td style="width: 46%;padding: 1.4rem;background-color: #ffffff;border: 1px solid #c7c7c7;">
							<table id="fix-table3" border="0" cellpadding="1%" style=" width: 100%; ">
							<tr>
							    <td><h1><?php echo esc_attr( $details['item_1'] ); ?></h1></td>
								<td><h1><?php echo esc_attr( $details['item_9'] ); ?></h1></td>
							</tr>
							</table>
				        </td>
						<td style=" width: 6%; ">
							<h1><b> : </b></h1>
				        </td>
						<td style="width: 46%;padding: 1.4rem;background-color: #f0f0f1;border: 1px solid #c7c7c7;">
							<h1>
							<b>
							<table id="fix-table3" border="0" cellpadding="1%" style=" width: 100%; ">
							<tr>
							    <td><h1><?php echo esc_attr( $details['item_10'] ); ?></h1></td>
								<td><h1><?php echo esc_attr( $details['item_2'] ); ?></h1></td>
							</tr>
							</table>
							</b>
							</h1>		
                            <input type="hidden" name="_namespace_custom_metabox[item_9]" id="_namespace_custom_metabox_item_9" value="<?php echo esc_attr( $details['item_9'] ); ?>" >
                            <input type="hidden" name="_namespace_custom_metabox[item_10]" id="_namespace_custom_metabox_item_10" value="<?php  echo esc_attr( $details['item_10'] ); ?>" >						
                            <input type="hidden" name="_namespace_custom_metabox[item_16]" id="_namespace_custom_metabox_item_16" value="<?php  echo esc_attr( $details['item_16'] ); ?>" >						
                            <input type="hidden" name="_namespace_custom_metabox[item_17]" id="_namespace_custom_metabox_item_17" value="<?php  echo esc_attr( $details['item_17'] ); ?>" >						
                            

<script>
document.getElementById('publish').onclick = function changeContent() 
{
	// Rplace date string "/" ( 1/1/2021 - 112021 )
	date = document.getElementById('_namespace_custom_metabox_item_11');
	x = date.value;
	d = x.replaceAll('/', '');
	date.value = d;
	
	// Rplace time string ":" ( 19:00 - 1900 )
	time = document.getElementById('_namespace_custom_metabox_item_12');
	y = time.value;
	t = y.replaceAll(':', '');
	time.value = t;
	
	//Get fields
	home_name = document.getElementById('_namespace_custom_metabox_item_1');
	away_name = document.getElementById('_namespace_custom_metabox_item_2');

	
	home_forfeit = document.getElementById('_namespace_custom_metabox_item_14');
	away_forfeit = document.getElementById('_namespace_custom_metabox_item_15');
	
	home_game_counter = document.getElementById('_namespace_custom_metabox_item_16');
	away_game_counter = document.getElementById('_namespace_custom_metabox_item_17');

	home_game_counter = parseInt(home_game_counter.value);
	away_game_counter = parseInt(away_game_counter.value);

	home_game_counter = 1;
	away_game_counter = 1;
	
	result_home_win = document.getElementById('_namespace_custom_metabox_item_18');
	result_away_win = document.getElementById('_namespace_custom_metabox_item_20');
	
	result_home_win = result_home_win.value;
	result_away_win = result_away_win.value;
	
	result_home_lost = document.getElementById('_namespace_custom_metabox_item_19');
	result_away_lost = document.getElementById('_namespace_custom_metabox_item_21');
	
	result_home_lost = parseInt(result_home_lost.value);
	result_away_lost = parseInt(result_away_lost.value);
	
	


	
	s1_home = document.getElementById('_namespace_custom_metabox_item_3');
	s1_away = document.getElementById('_namespace_custom_metabox_item_6');
	s2_home = document.getElementById('_namespace_custom_metabox_item_4');
	s2_away = document.getElementById('_namespace_custom_metabox_item_7');
	s3_home = document.getElementById('_namespace_custom_metabox_item_5');
	s3_away = document.getElementById('_namespace_custom_metabox_item_8');
	
	//Get fields value
	s1_home_v = parseInt(s1_home.value);
	s1_away_v = parseInt(s1_away.value);
	s2_home_v = parseInt(s2_home.value);
	s2_away_v = parseInt(s2_away.value);
	s3_home_v = parseInt(s3_home.value);
	s3_away_v = parseInt(s3_away.value);

	//IF ( Set 1 !=0 || "" , set 2 !=0 || "", set 3 !=0 || "" ) 
	set1_home_valid = ( s1_home_v != 0 && s1_home_v != "" && s1_home_v > 0 ) ? 1:0;
	set1_away_valid = ( s1_away_v != 0 && s1_away_v != "" && s1_away_v > 0 ) ? 1:0;
	set2_home_valid = ( s2_home_v != 0 && s2_home_v != "" && s2_home_v > 0 ) ? 1:0;
	set2_away_valid = ( s2_away_v != 0 && s2_away_v != "" && s2_away_v > 0 ) ? 1:0;
	set3_home_valid = ( s3_home_v != 0 && s3_home_v != "" && s3_home_v > 0 ) ? 1:0;
	set3_away_valid = ( s3_away_v != 0 && s3_away_v != "" && s3_away_v > 0 ) ? 1:0;
    
	//IF ( set 1,2,3 == set 1,2,3  )
	set_equal = ( s1_home_v == s1_away_v || s2_home_v == s2_away_v || s3_home_v == s3_away_v ) ? 1:0;

    var invalid = 0;
	var set_1_home = 0;
	var set_1_away = 0;
    var set_2_home = 0;
	var set_2_away = 0;
    var set_3_home = 0;
    var set_3_away = 0;
	
	var result_home_win = 0;
	var result_home_lost = 0;
	var result_away_win = 0;
	var result_away_lost = 0;
    
	if(set_equal != 1)
	{
		// ---------- Set 1 ------------
    	if( set1_home_valid == 1 && set1_away_valid == 1)
    	{
			if( s1_home_v > s1_away_v )
			{
			   set_1_away = 0;
			   set_1_home = set_1_home + 1;
			}
			else
			{
			   set_1_home = 0;
			   set_1_away = set_1_away + 1;
			}
    	}
    	else
		{
	    	invalid = 1
    	}	
		// ---------- Set 2 ------------
    	if( set2_home_valid == 1 && set2_away_valid == 1)
    	{
			if( s2_home_v > s2_away_v )
			{
			   set_2_away = 0;
			   set_2_home = set_2_home + 1;
			}
			else
			{
			   set_2_home = 0;
			   set_2_away = set_2_away + 1;
			}
    	}
    	else
		{
	    	invalid = 1
    	}

	}
	else
	{
		invalid = 1
	}

    // IF validate fields
	if( invalid != 1 )
	{
		if( set3_home_valid != "" && set3_away_valid != "")
    	{
			if( s3_home_v > s3_away_v )
			{
			   set_3_away = 0;
			   set_3_home = set_3_home + 1;
			}
			else
			{
			   set_3_home = 0;
			   set_3_away = set_3_away + 1;
			}
			
			result_away = set_1_away + set_2_away + set_3_away;
		    result_home = set_1_home + set_2_home + set_3_home;
		}
		else
		{
			result_away = set_1_away + set_2_away;
		    result_home = set_1_home + set_2_home;
		}
	}
	else
	{
	}
	
	
	
	
		if(home_forfeit.value == 1 && away_forfeit.value == 1)
		{
			result_home = 0;
			result_away = 0;
		}
		else if(home_forfeit.value == 1)
		{
			result_home = 0;
			result_away = 2;
		}
		else if(away_forfeit.value == 1)
		{
			result_home = 2;
			result_away = 0;
		}
		//result_home_win=NULL;
		if( result_home > result_away )
		{
			 result_home_win = 1;
			 //result_home_lost = 0;
			 //result_away_win = 0;
			 //result_away_lost = 1;
        }
		else
		{
			//result_home_win = 0;
			//result_home_lost = 1;
			result_away_win = 1;
			//result_away_lost = 0;
		}
		
		 //alert('HOME  ['+home_name.value+']'+' AWAY ['+away_name.value+']');
		 alert('HOME WIN ['+result_home_win+']'+
		 ' HOME LOST ['+result_home_lost+']' +
		 ' AWAY WIN ['+result_away_win+']' +
		 ' AWAY LOST ['+result_away_lost+']');

		document.querySelector('input[name="_namespace_custom_metabox[item_9]"]').value = result_home;
		document.querySelector('input[name="_namespace_custom_metabox[item_10]"]').value = result_away;
		document.querySelector('input[name="_namespace_custom_metabox[item_13]"]').value = result_home+' : '+result_away;
		document.querySelector('input[name="_namespace_custom_metabox[item_16]"]').value = home_game_counter;
		document.querySelector('input[name="_namespace_custom_metabox[item_17]"]').value = away_game_counter;
		document.querySelector('input[name="_namespace_custom_metabox[item_18]"]').value = result_home_win;
		document.querySelector('input[name="_namespace_custom_metabox[item_19]"]').value = result_home_lost;
		document.querySelector('input[name="_namespace_custom_metabox[item_20]"]').value = result_away_win;
		document.querySelector('input[name="_namespace_custom_metabox[item_21]"]').value = result_away_lost;
		
		//alert(home_game_counter);
}
</script>
							
                            <input type="hidden" name="_namespace_custom_metabox[item_18]" id="_namespace_custom_metabox_item_18" value="<?php  echo esc_attr( $details['item_18'] );  ?>" >
                            <input type="hidden" name="_namespace_custom_metabox[item_19]" id="_namespace_custom_metabox_item_19" value="<?php  echo esc_attr( $details['item_19'] );  ?>" >
                            <input type="hidden" name="_namespace_custom_metabox[item_20]" id="_namespace_custom_metabox_item_20" value="<?php  echo esc_attr( $details['item_20'] );  ?>" >
                            <input type="hidden" name="_namespace_custom_metabox[item_21]" id="_namespace_custom_metabox_item_21" value="<?php  echo esc_attr( $details['item_21'] );  ?>" >
                            <input type="hidden" name="_namespace_custom_metabox[item_13]" id="_namespace_custom_metabox_item_13" value="<?php  echo esc_attr( $details['item_13'] );  ?>" >
				        </td>
				    </tr>
                </table>
			</td>
        </tr>
    </tfoot>
    </table>
</fieldset>

<table id="fix-table4" border="0" cellpadding="1%" style=" width: 100%; ">
	<tr>
	    <td>
	        <h2>*
               <b>מערכות : </b>
               <span>2 מערכות עד 25 נקודות. </span>
               <span>מערכה שלישית עד 15 נקודות</span>
            </h2>
	    </td>
	    <td>
	        <h2>*
	            <b>ניקוד : </b>
	            <span>ניצחון = 2</span>,
	            <span>הפסד = 1</span>,
	            <span>תיקו = 0</span>
	        </h2>
	   </td>
    </tr>
</table>
<?php
    // Security field This validates that submission came from the actual dashboard and not the front end or a remote server.
	wp_nonce_field( '_namespace_form_metabox_nonce', '_namespace_form_metabox_process' );
}


/**
* Save the metabox
* @param  Number $post_id The post ID
* @param  Array  $post    The post data
*/
function _namespace_save_metabox( $post_id, $post ) {

	// Verify that our security field exists. If not, bail.
	if ( !isset( $_POST['_namespace_form_metabox_process'] ) ) return;

	// Verify data came from edit/dashboard screen
	if ( !wp_verify_nonce( $_POST['_namespace_form_metabox_process'], '_namespace_form_metabox_nonce' ) ) {
		return $post->ID;
	}

	// Verify user has permission to edit post
	if ( !current_user_can( 'edit_post', $post->ID )) {
		return $post->ID;
	}
	
	// Check that our custom fields are being passed along
	// This is the `name` value array. We can grab all
	// of the fields and their values at once.
	if ( !isset( $_POST['_namespace_custom_metabox'] ) ) {
		return $post->ID;
	}

	/**
	* Sanitize all data
	* This keeps malicious code out of our database.
	*/

	// Set up an empty array
	$sanitized = array();

	// Loop through each of our fields
	foreach ( $_POST['_namespace_custom_metabox'] as $key => $detail ) 
	{
		// Sanitize the data and push it to our new array
		// `wp_filter_post_kses` strips our dangerous server values
		// and allows through anything you can include a post.
		$sanitized[$key] = wp_filter_post_kses( $detail );
	}

	// Save our submissions to the database
	update_post_meta( $post->ID, '_namespace', $sanitized );
}
add_action( 'save_post', '_namespace_save_metabox', 1, 2 );


//
// Save a copy to our revision history
// This is optional, and potentially undesireable for certain data types.
// Restoring a a post to an old version will also update the metabox.

/**
* Save events data to revisions
* @param  Number $post_id The post ID
*/
function _namespace_save_revisions( $post_id ) 
{
    // Check if it's a revision
	$parent_id = wp_is_post_revision( $post_id );

	// If is revision
	if ( $parent_id ) 
	{
		// Get the saved data
		$parent = get_post( $parent_id );
		$details = get_post_meta( $parent->ID, '_namespace', true );

		// If data exists and is an array, add to revision
		if ( !empty( $details ) && is_array( $details ) ) 
		{
			// Get the defaults
			$defaults = _namespace_metabox_defaults();

			// For each default item
			foreach ( $defaults as $key => $value ) 
			{
				// If there's a saved value for the field, save it to the version history
				if ( array_key_exists( $key, $details ) ) 
				{
					add_metadata( 'post', $post_id, '_namespace_' . $key, $details[$key] );
				}
			}
		}
	}
}
add_action( 'save_post', '_namespace_save_revisions' );


/**
* Restore events data with post revisions
* @param  Number $post_id     The post ID
* @param  Number $revision_id The revision ID
*/
function _namespace_restore_revisions( $post_id, $revision_id ) 
{
    // Variables
	$post = get_post( $post_id ); // The post
	$revision = get_post( $revision_id ); // The revision
	$defaults = _namespace_metabox_defaults(); // The default values
	$details = array(); // An empty array for our new metadata values

	// Update content
	// For each field
	foreach ( $defaults as $key => $value ) 
	{
        // Get the revision history version
		$detail_revision = get_metadata( 'post', $revision->ID, '_namespace_' . $key, true );

		// If a historic version exists, add it to our new data
		if ( isset( $detail_revision ) ) 
		{
			$details[$key] = $detail_revision;
		}
	}

	// Replace our saved data with the old version
	update_post_meta( $post_id, '_namespace', $details );
}
add_action( 'wp_restore_post_revision', '_namespace_restore_revisions', 10, 2 );


/**
* Get the data to display on the revisions page
* @param  Array $fields The fields
* @return Array The fields
*/
function _namespace_get_revisions_fields( $fields ) 
{
    // Get our default values
	$defaults = _namespace_metabox_defaults();

	// For each field, use the key as the title
	foreach ( $defaults as $key => $value ) 
	{
		$fields['_namespace_' . $key] = ucfirst( $key );
	}
	return $fields;
}
add_filter( '_wp_post_revision_fields', '_namespace_get_revisions_fields' );


/**
* Display the data on the revisions page
* @param  String|Array $value The field value
* @param  Array        $field The field
*/
function _namespace_display_revisions_fields( $value, $field ) 
{
	global $revision;
	return get_metadata( 'post', $revision->ID, $field, true );
}
add_filter( '_wp_post_revision_field_my_meta', '_namespace_display_revisions_fields', 10, 2 );


/*********************************** End Plugin Meta Box Fixtures ***********************************/
/*********************************** Plugin Methods ***********************************/

/**
 * Create Autogenerate Page Title From Meta Box
 *
 * @param int $post_id, int post
 */
function fixtures_autogenerate_title( $post_id, $post )
{
  // Variables
  global $post; // Get the current post data

  if ( 'cpt_1_fixtures' == $post->post_type ) 
  {
	$saved = get_post_meta( $post->ID, '_namespace', true ); // Get the saved values
	$defaults = _namespace_metabox_defaults(); // Get the default values
	$details = wp_parse_args( $saved, $defaults ); // Merge the two in case any fields don't exist in the saved data
	$new_title = $details['item_1']." VS ".$details['item_2'];  

    $args2 = array(
      'ID'          =>   $post_id,
      'post_title'  =>   $new_title,
      'post_name'   =>   $new_title
    );

    wp_reset_postdata();

    // unhook this function so it doesn't loop infinitely
    remove_action('save_post', 'fixtures_autogenerate_title',30,2);
 
    // update the post, which calls save_post again
    wp_update_post( $args2 );
 
    // re-hook this function
    add_action('save_post', 'fixtures_autogenerate_title',30,2);  
  }  

}
add_action( 'save_post', 'fixtures_autogenerate_title', 30, 2 );



/**
 * Create Custom Shortcode For Fixtures Display View
 *
 * @param int $saved, int defaults, int details
 */
function custom_shortcode_fixtures_view()
{
    // Variables
	global $post; // Get the current post data
	$saved = get_post_meta( $post->ID, '_namespace', true ); // Get the saved values
	$defaults = _namespace_metabox_defaults(); // Get the default values
	$details = wp_parse_args( $saved, $defaults ); // Merge the two in case any fields don't exist in the saved data

    // Get Post_type cpt_1_fixtures AND Taxonomy division AND Key Field(DATE) _namespace[item_11]
    $args = array(
        'posts_per_page' => 100,
	    'post_type'      => array( 'cpt_1_fixtures' ),
	    'tax_query'      => array(
		    'relation'   => 'AND',
		    array(
			    'taxonomy'  => 'stage',
			    //'terms'   =>  'שלב ב' ,
			    'field'     => 'slug',
			    'operator'  => 'AND',
		    ),
	    ),
	    'meta_query' => array(
		    'relation' => 'AND',
		    array(
			    'key'     => '_namespace',
			    'value'     => '"'. 'item_11' .'"',
			    'compare' => 'LIKE',
		    ),
	    ),
	    'orderby'  => array(
		    '_namespace' => 'ASC',
	    ),
    );
	
    $loop = new WP_Query( $args ); 
	
    $counterRow = 0;		
    $counterRow2 = 0;		
    $xx = 0;		
	while ( $loop->have_posts() ) : $loop->the_post(); 
	
	$id = $post->ID;
	$test = get_post_meta( $id, '_namespace', true );
	$test_defaults = _namespace_metabox_defaults();
	$test_details = wp_parse_args( $test, $test_defaults );
	
	$fixtures_date = $test_details['item_11'];
	
	$arr1 = str_split($fixtures_date);
	$y = $arr1[0].$arr1[1].$arr1[2].$arr1[3];
	$m = $arr1[4].$arr1[5];
	$d = $arr1[6].$arr1[7];
	$fixtures_date = $d."/".$m."/".$y;
	
	$fixtures_time = $test_details['item_12'];
	
	$arr2 = str_split($fixtures_time);
	$hour = $arr2[0].$arr2[1];
	$seconds = $arr2[2].$arr2[3];
	$fixtures_time = $hour.":".$seconds;
	
    $home_fimage = $test_details['item_1'];
    $home_page = get_page_by_title($home_fimage, OBJECT, 'cpt_1_team');
    $home_pageID = $home_page->ID;
    $home_featured_img_url = get_the_post_thumbnail_url($home_pageID,'full'); 

    $away_fimage = $test_details['item_2'];
    $away_page = get_page_by_title($away_fimage, OBJECT, 'cpt_1_team');
    $away_pageID = $away_page->ID;
    $away_featured_img_url = get_the_post_thumbnail_url($away_pageID,'full'); 
	
	//Returns Term Name For Taxonomy "division"
    $term_list = wp_get_post_terms( $id, 'division', array( 'fields' => 'names' ) );
    $fixtures_division_t_name =  $term_list[0];
	
	//Returns Term Name For Taxonomy "stage"
	$term_list2 = wp_get_post_terms( $id, 'stage', array( 'fields' => 'names' ) );
	$fixtures_stage_t_name =  $term_list2[0];
	
	if($fixtures_stage_t_name == "שלב א")
	{
		$counterRow = $counterRow + 1 ;
		
		$stage_1_table_body .= '<tr>';
		$stage_1_table_body .= '<td>'.$counterRow.'</td>';
		$stage_1_table_body .= '<td class="fix-devi">'.$fixtures_division_t_name.'</td>';
		$stage_1_table_body .= '<td>'.$fixtures_date.'</td>';
		$stage_1_table_body .= '<td>'.$fixtures_time.'</td>';
		$stage_1_table_body .= '<td><img src="'.$home_featured_img_url.'" style="width: 30px; height: 30px; border-radius: 50%;" alt="">';
        $stage_1_table_body .= '<td>'.$test_details['item_1'].'</td>';
        $stage_1_table_body .= '<td class="fix-res">'.$test_details['item_13'].'</td>';
		$stage_1_table_body .= '<td>'.$test_details['item_2'].'</td>';
		$stage_1_table_body .= '<td><img src="'.$away_featured_img_url.'" style="width: 30px; height: 30px; border-radius: 50%;" alt="">';
        $stage_1_table_body .= '</tr>';
	}
	else
	{
		$counterRow2 = $counterRow2 + 1 ;
		
		$stage_2_table_body .= '<tr>';
		$stage_2_table_body .= '<td>'.$counterRow2.'</td>';
		$stage_2_table_body .= '<td class="fix-devi">'.$fixtures_division_t_name.'</td>';
		$stage_2_table_body .= '<td>'.$fixtures_date.'</td>';
		$stage_2_table_body .= '<td>'.$fixtures_time.'</td>';
		$stage_2_table_body .= '<td><img src="'.$home_featured_img_url.'" style="width: 30px; height: 30px; border-radius: 50%;" alt="">';
        $stage_2_table_body .= '<td>'.$test_details['item_1'].'</td>';
        $stage_2_table_body .= '<td class="fix-res">'.$test_details['item_13'].'</td>';
		$stage_2_table_body .= '<td>'.$test_details['item_2'].'</td>';
		$stage_2_table_body .= '<td><img src="'.$away_featured_img_url.'" style="width: 30px; height: 30px; border-radius: 50%;" alt="">';
        $stage_2_table_body .= '</tr>';
	}
	endwhile;
	wp_reset_postdata();
	
	$stage_1_table_header .= '<h4 class="fix-group-title">שלב א</h4>';
    $stage_1_table_header .= '<table id="fix-table4">';
    $stage_1_table_header .= '<thead>';
    $stage_1_table_header .= '<tr>';
    $stage_1_table_header .= '<th>מספר</th>';
    $stage_1_table_header .= '<th>בית</th>';
    $stage_1_table_header .= '<th>תאריך</th>';
    $stage_1_table_header .= '<th>שעה</th>';
    $stage_1_table_header .= '<th colspan="2">קבוצה</th>';
    $stage_1_table_header .= '<th>תוצאה</th';
    $stage_1_table_header .= '<th colspan="2">קבוצה</th>';
    $stage_1_table_header .= '</tr>';
    $stage_1_table_header .= '</thead>';
	$stage_1_table_header .= '<tbody>';
	$stage_1_table_footer .= '</tbody>';
	$stage_1_table_footer .= '</table>';
	
	$stage_2_table_header .= '<h4 class="fix-group-title" style="margin-top: 1rem;">שלב ב</h4>';
    $stage_2_table_header .= '<table id="fix-table4">';
    $stage_2_table_header .= '<thead>';
    $stage_2_table_header .= '<tr>';
    $stage_2_table_header .= '<th>מספר</th>';
    $stage_2_table_header .= '<th>בית</th>';
    $stage_2_table_header .= '<th>תאריך</th>';
    $stage_2_table_header .= '<th>שעה</th>';
    $stage_2_table_header .= '<th colspan="2">קבוצה</th>';
    $stage_2_table_header .= '<th>תוצאה</th';
    $stage_2_table_header .= '<th colspan="2">קבוצה</th>';
    $stage_2_table_header .= '</tr>';
    $stage_2_table_header .= '</thead>';
	$stage_2_table_header .= '<tbody>';
	$stage_2_table_footer .= '</tbody>';
	$stage_2_table_footer .= '</table>';
	
    echo $stage_1_table_header.$stage_1_table_body.$stage_1_table_footer; 
    echo $stage_2_table_header.$stage_2_table_body.$stage_2_table_footer; 
}
add_shortcode( 'fixtures_view', 'custom_shortcode_fixtures_view' );


/**
 * Create Custom Shortcode For  League tables Display View
 *
 * @param int $saved, int defaults, int details
 */
function custom_shortcode_league_tables_view()
{
	global $post; // Get the current post data

    // Get Post_type cpt_1_fixtures AND Taxonomy division AND Key Field(DATE) _namespace[item_11]
    $args = array(
        'posts_per_page' => 100,
	    'post_type'      => array( 'cpt_1_fixtures' ),
	    'tax_query'      => array(
		    'relation'   => 'AND',
		    array(
			    'taxonomy'  => 'stage',
			    //'terms'   =>  'שלב א' ,
			    'field'     => 'slug',
			    'operator'  => 'AND',
		    ),
	    ),
	    'meta_query' => array(
		    'relation' => 'AND',
		    array(
			    'key'     => '_namespace',
			    'value'     => '"'. 'item_11' .'"',
			    'compare' => 'LIKE',
		    ),
	    ),
	    'orderby'  => array(
		    '_namespace' => 'ASC',
	    ),
    );
	
    $loop = new WP_Query( $args ); 
	
    $fixArr = array(
	    'id' => NULL,
		'name' => NULL,
	    'game' => NULL
	);
	$fixArr2 = array(
	    'id' => NULL,
		'name' => NULL,
	    'game' => NULL
	);
	
	$fixArr_table[] = array(
		'id' => NULL,
		'name' => NULL,
		'game' => NULL,
		'win' => NULL,
		'lose' => NULL
	 );

    $counterRow = 0;

	while ( $loop->have_posts() ) : $loop->the_post(); 
	
	    // vars
		$id = $post->ID;
		$postMeta = get_post_meta( $id, '_namespace', true );
		$defaults = _namespace_metabox_defaults();
		$details = wp_parse_args( $postMeta, $defaults );
		
		//Returns Term Name For Taxonomy "division"
        $term_list = wp_get_post_terms( $id, 'division', array( 'fields' => 'names' ) );
        $fixtures_division_t_name =  $term_list[0];
		
		//Returns Term Name For Taxonomy "stage"
	    $term_list2 = wp_get_post_terms( $id, 'stage', array( 'fields' => 'names' ) );
	    $fixtures_stage_t_name =  $term_list2[0];
	
	    if($fixtures_stage_t_name == "שלב א")
	    {
			if($fixtures_division_t_name == "בית א")
		    {
				$fixArr1[] = array(
		            'id' => $id,
					//'name' => $details['item_1'],
					'home_name' =>$details['item_1'],
					'away_name' =>$details['item_2'],
					'game' => 0,
					'home_win' => $details['item_18'],
					'home_lose' => $details['item_19'],
					'away_win' => $details['item_20'],
					'away_lose' => $details['item_21']
	            );
				$fixArr2[] = array(
		            'id' => $id,
					//'name' => $details['item_2'],
					'home_name' =>$details['item_1'],
					'away_name' =>$details['item_2'],
					'game' => 0,
					'home_win' => $details['item_18'],
					'home_lose' => $details['item_19'],
					'away_win' => $details['item_20'],
					'away_lose' => $details['item_21']
	            );		
			}
		}
		
	endwhile;
	
	wp_reset_postdata();
	
    $fixArr1 = array(
        array('id' => 9939,'home_name' =>"תעש מערכות",'away_name' =>"סולאראדג'",
		'game' => 0,'home_win' => 1,'home_lose' => 0,'away_win' => 0,'away_lose' => 0),
		array('id' => 9917,'home_name' =>"דניה סיבוס",'away_name' =>"סולאראדג'",
		'game' => 0,'home_win' => 1,'home_lose' => 0,'away_win' => 0,'away_lose' => 0),
		array('id' => 9926,'home_name' =>"סולאראדג'",'away_name' =>"פרטנר",
		'game' => 0,'home_win' => 0,'home_lose' => 1,'away_win' => 0,'away_lose' => 0),
		array('id' => 9933,'home_name' =>"סולאראדג'",'away_name' =>"בית חולים לוינשטיין",
		'game' => 0,'home_win' => 0,'home_lose' => 1,'away_win' => 0,'away_lose' => 0),
		array('id' => 9925,'home_name' =>"סולאראדג'",'away_name' =>"בית חולים מאיר",
		'game' => 0,'home_win' => 0,'home_lose' => 1,'away_win' => 0,'away_lose' => 0),
		array('id' => 9937,'home_name' =>"בית חולים מאיר",'away_name' =>"פרטנר",
		'game' => 0,'home_win' => 0,'home_lose' => 1,'away_win' => 0,'away_lose' => 0),
		array('id' => 9936,'home_name' =>"תעש מערכות",'away_name' =>"בית חולים לוינשטיין",
		'game' => 0,'home_win' => 1,'home_lose' => 0,'away_win' => 0,'away_lose' => 0)
		);




    $count = count($fixArr1);
	
    $resArr1 = array('id' => null,'name' => null,'win'  => null,'lose'  => null);
    $resArr2 = array('id' => null,'name' => null,'win'  => null,'lose'  => null);
   

	
	$i=0;
	$win = 1;
    foreach($fixArr1 as $key => $val)
	{ 
	
	    if($val['home_win'] == 1)
	    {
		    
		  
	      
			$resArr1[$i]['id']  = $val['id'];
	        $resArr1[$i]['name']  = $val['home_name'];
			$resArr1[$i]['win']  = $val['home_win'];
			$resArr1[$i]['lose']  = 0;
			
			
	    }
	   
	   $i++;
	}
	
//echo '<pre>';
//print_r($resArr1);
//echo '</pre>';
//echo "<p>00000000</p>";	




/*
  if (!in_array($val['home_name'], array_column($resArr1, 'name'))) 
		    {
			 $win = 1; 
		    }
		    else
		    {
             $win = $win + 1;
             //array_replace($a1,$a2)
		    }
*/



$arr = array(
    array('id' => 2123,'name' => 'תעש מערכות','win'  => 1,'lose'  => 0),
    array('id' => 3123,'name' => 'דניה סיבוס','win'  => 1,'lose'  => 0),
    array('id' => 4123,'name' => 'תעש מערכות','win'  => 1,'lose'  => 0)
);


$res = [];
// loop through each inner array `[0 => 0.5]`, `[1 => 0.9]` etc...
foreach($arr as $inner) { 
  // for each key (0, 1, etc) and value (0.5, 0.9, etc) populate your $res
  foreach($inner as $key=>$val) 
  { 
	 $res[$key][] = $val; // append $val to the array at index $key, if the array doesn't exist, create it and add $val	
  }
}

/*
echo '<pre>';
print_r($res);
echo '</pre>';
*/


$array2 = array(
    array('id' => 2123,'name' => 'תעש מערכות','win'  => 1,'lose'  => 0),
    array('id' => 3123,'name' => 'דניה סיבוס','win'  => 1,'lose'  => 0),
    array('id' => 3333,'name' => 'דניה סיבוס','win'  => 1,'lose'  => 0),
    array('id' => 4123,'name' => 'תעש מערכות','win'  => 1,'lose'  => 0)
);

foreach($array2 as $key2 => $item)
{
	if(in_array("דניה סיבוס",  array_column($array2, 'name')))
    {
        //$key = array_search("תעש מערכות", array_column($array2, 'name'),false);
        //echo $key;
    }
	
}










$gfg_array = array(
    'id' => array(
		    '0' => 2121,
            '1' => 2122,
            '2' => 2123,
            '3' => 2124
    ),
      
    'name' => array(
            '0' => 'תעש מערכות',
            '1' => 'aaa',
            '2' => 'bbbb',
            '3' => 'דניה סיבוס'
    ),
      
    'win' => array(
            '0' => 1,
            '1' => 1,
            '2' => 1,
            '3' => 1 
    )
);

// Multidimensional array search
// Function to recursively search for a given value
function array_search_id($search_value, $array, $id_path) 
{
    if(is_array($array) && count($array) > 0) 
	{
        foreach($array as $key => $value) 
		{
            $temp_path = $id_path;
              
            // Adding current key to search path
            array_push($temp_path, $key);
  
            // Check if this value is an array
            // with atleast one element
            if(is_array($value) && count($value) > 0) 
			{
                $res_path = array_search_id( $search_value, $value, $temp_path );
  
                if ($res_path != null) 
				{
                    return $res_path;
                }
            }
            else if($value == $search_value) 
			{
                return join("|", $temp_path);
            }
        }
    }  
    return null;
}




/**
   * PHP Search an Array for multiple key / value pairs
   */

  function multi_array_search($array, $search) {
    // Create the result array
    $result = array();

    // Iterate over each array element
    foreach ($array as $key => $value){

      // Iterate over each search condition
      foreach ($search as $k => $v){

        // If the array element does not meet the search condition then continue to the next element
        if (!isset($value[$k]) || $value[$k] != $v){
          continue 2;
        }
      }
      // Add the array element's key to the result array
      $result[] = $key;
    }

    // Return the result array
    return $result;
  }


  
  
  
  
  
  

$j=0;
foreach($gfg_array as $keyyy => $itemmm)
{
	
	if($keyyy == "name")
	{
		
		foreach($itemmm as $nkey => $nval)
		{


           $index = multi_array_search($gfg_array, array($nkey => $itemmm[$j]));
		   
		   if($index[0] != NULL)
		   {
			  //Item found 
			  echo '<p>'.$nval.'</p>';
		   }
		   else
		   {
			   //Item not found
			  
		   }
 var_dump($index);

			//Check for duplictae name on difrent arr index
			if($nval == $tempNval && $nkey != $tempNkey )
			{
				//echo '<p> Key - '.$nkey.' | Val - '.$nval.'</p>';
				//$gfg_array['win'][$pieces['2']] += $gfg_array['win'][$pieces['2']];
			}
			else
			{
				/*
				$search_path = array_search_id($nval, $gfg_array, array('$'));
				$pieces = explode('|', $search_path);
				
				if($tempWin != 1)
				{
					echo $tempWin;
				}
				else
				{
					echo $gfg_array['win'][$pieces['2']];
					$gfg_array['win'][$pieces['2']] += $gfg_array['win'][$pieces['2']];
				}
				*/
				
			   
			}
			
			$search_path = array_search_id($nval, $gfg_array, array('$'));
			$pieces = explode('|', $search_path);
			$tempWin  = $gfg_array['win'][$pieces['2']];
			$tempNval = $nval;
			$tempNkey = $nkey;
			
		}
		
		
		//var_dump($itemmm);
		// Search duplicate name
		$search_path = array_search_id($itemmm[$j], $gfg_array, array('$'));

		// Not set or not define
		if (!isset($search_path) || empty($search_path)) 
		{
		  	echo "Not found";	
		}
		else
		{
			//$pieces = explode('|', $search_path);
			//$gfg_array['win'][$pieces['2']] += $gfg_array['win'][$pieces['2']] + 1;
			//echo $gfg_array['win']['1'];
			//echo $gfg_array['name'][$j];
		}
		
		
		
	}
	
    $j++;
	
}



// Search duplicate name
//$search_path = array_search_id('תעש מערכות', $gfg_array, array('$'));

// Not set or not define
/*if (!isset($search_path) || empty($search_path)) 
{
  	echo "Not found";	
}
else
{
	//$pieces = explode('|', $search_path);
	//$gfg_array['win']['1'] = $gfg_array['win']['1'] + 1;
	//echo $gfg_array['win']['1'];
}

*/


echo '<pre>';
print_r($gfg_array);
echo '</pre>';


}
add_shortcode( 'league_tables_view', 'custom_shortcode_league_tables_view' );