<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
         
if ( !function_exists( 'child_theme_configurator_css' ) ):
    function child_theme_configurator_css() {
        wp_enqueue_style( 'chld_thm_cfg_child', trailingslashit( get_stylesheet_directory_uri() ) . 'style.css', array( 'bootstrap','flexslider','dokan-theme','dokan-theme','dokan-theme-skin' ) );
		wp_dequeue_script('dokan-spmv-search-js');
		if(is_page('dashboard')){
		wp_enqueue_script('wd-spmv-search-js', trailingslashit( get_stylesheet_directory_uri() ) . 'product-search.js', array('jquery'), null, true);
		}
    }
endif;
add_action( 'wp_enqueue_scripts', 'child_theme_configurator_css', 10 );

// END ENQUEUE PARENT ACTION

add_action('wp_head', function(){
	?>
<style>
.dokan-spmv-add-new-product-search-box-area.dokan-w13.section-closed {
    display: none !important;
}
</style>
<?php
});

//Filter the search for only posts and parts

add_filter( 'woocommerce_product_object_query_args', 'wd_woocommerce_product_object_query_args',10 );
function wd_woocommerce_product_object_query_args($args){
	
	if(is_page('dashboard')){
		$args['author__in'] = array(1);
		$args['s'] = 'sfdvg543gtghytrf';
    }
	
	return $args;
}


//Ajax search result

add_action('wp_ajax_wd_live_product_search', 'wd_live_product_search');
add_action('wp_ajax_nopriv_wd_live_product_search', 'wd_live_product_search');

function wd_live_product_search() {
    // Get the search term from the AJAX request.
    $search_term = sanitize_text_field($_POST['search_term']);
	
	$args = array(
        's' => $search_term,
        'status' => 'publish', 
		'author__in' => array(1),
        //'limit' => 10
    );
	
	$product_id = wc_get_product_id_by_sku( $search_term );

	if ( $product_id ) {
		$args['sku'] = $search_term;
		$args['s'] = '';
	}
	
    // Perform the product search.
    $products = wc_get_products($args);

    // Prepare and return the search results.
    ob_start();

    if (!empty($products)) {
        woocommerce_product_loop_start();
		foreach ( $products as $product ) {
			dokan_spmv_get_template(
				'search/result-row',
				[
					'product'     => $product,
					'search_word' => $search_word,
				]
			);
		}
		woocommerce_product_loop_end();
		wc_reset_loop();
    } else {
        echo '<tr><td colspan="4">' . __( 'No product found.', 'dokan' ) . '</td></tr>';
    }

    $response = ob_get_clean();

    // Send the HTML response back to the JavaScript.
    wp_send_json($response);
}


add_action('wp_footer', function(){
	if(is_page('dashboard')) :
	?>
	<script>
		var wdajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	jQuery(document).ready(function ($) {
		$('.listing-product-search-form input[name="search"]').on('input', function () {
			var searchTerm = $(this).val();

			// Perform the AJAX request only if the search term is not empty.
			if (searchTerm.length > 0) {
				var data = {
					action: 'wd_live_product_search', // This will be the name of your AJAX action hook.
					search_term: searchTerm
				};

				$.post(wdajaxurl, data, function (response) {
					// Handle the response here, such as displaying product suggestions.
					$('#dokan-spmv-product-list-table tbody').html(response);
				});
			} else {
				// Clear the results if the search term is empty.
				$('#dokan-spmv-product-list-table tbody').empty();
			}
		});
	});

	</script>
<?php
	endif;
});


add_action('dokan_product_content_inside_area_before', 'wd_dokan_before_new_product_inside_content_area', 1);
function wd_dokan_before_new_product_inside_content_area(){
	if(isset($_GET['product_id']) && $_GET['product_id'] == 0):
	?>
<div class="dokan-spmv-add-new-product-search-box-area dokan-w13 wd-section">
<a href="<?php echo get_permalink( get_page_by_path( 'dashboard' ) ).'products-search/'; ?>">
	<div class="info-section">
	<p class="main-header"><?php esc_html_e( 'Search similar products in this marketplace', 'dokan' ); ?></p>
        <p class="sub-header"><?php esc_html_e( 'to duplicate products image, content, attributes, tags etc', 'dokan' ); ?></p>
</div>
</a>
</div>
<?php
	endif;
}
