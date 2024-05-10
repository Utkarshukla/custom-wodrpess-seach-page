<?php 
/**
 * Template Name: Custom Search Template
 */

get_header();
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

<style>
    .product-card {
        max-width: 300px;
        margin-bottom: 20px;
    }
    .product-card .card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
    }
</style>

<h1>Search</h1>
<form action="<?php echo esc_url( site_url('/search/') ); ?>" method="GET">
    <label for="varP">Product Name</label>
    <input type="text" id="varP" class="form-control" name="searchProduct"><br><br>
    
    <label for="cat">Category</label>
    <input type="text" id="cat"class="form-control" name="searchCat"><br><br>
    
    <label for="sku">SKU</label>
    <input type="text" id="sku"class="form-control" name="searchSku"><br><br>
    
    <input type="submit" class="btn btn-primary" value="Search">
</form>
<br>
<?php
if(isset($_GET['searchProduct']) && isset($_GET['searchCat']) && isset($_GET['searchSku'])) {
    $varP = $_GET['searchProduct'];
    $cat = $_GET['searchCat'];
    $sku = $_GET['searchSku'];
    $url = site_url("/search/?searchProduct=$varP&searchCat=$cat&searchSku=$sku");
}
?>
<?php
$searchProduct = isset($_GET['searchProduct']) ? sanitize_text_field($_GET['searchProduct']) : '';
$searchCat = isset($_GET['searchCat']) ? sanitize_text_field($_GET['searchCat']) : '';
$searchSku = isset($_GET['searchSku']) ? sanitize_text_field($_GET['searchSku']) : '';

$args = array(
    'post_type' => 'product',
    'posts_per_page' => 50,
    'meta_query' => array(
        'relation' => 'AND',
        array(
            'key' => '_stock_status',
            'value' => 'instock',
            'compare' => '='
        )
    )
);

if (!empty($searchProduct)) {
    $args['s'] = $searchProduct;
}

if (!empty($searchSku)) {
    $args['meta_query'][] = array(
        'key' => '_sku',
        'value' => $searchSku,
        'compare' => '='
    );
}

if (!empty($searchCat)) {
    $args['tax_query'] = array(
        array(
            'taxonomy' => 'product_cat',
            'field' => 'name',
            'terms' => $searchCat
        )
    );
}

$query = new WP_Query($args);

if ($query->have_posts()) {
    ?>
    <div class="row">
    <?php
    while ($query->have_posts()) {
        $query->the_post();
        $product = wc_get_product(get_the_ID());
        $is_variable = $product->is_type('variable');
        ?>
        <div class="col-sm-2 col-md-1">
            <div class="product-card">
                <div class="card">
                <a href="<?php echo get_permalink(); ?>"> <?php echo $product->get_image('thumbnail'); ?> </a>
                   <div class="card-body">
                   <a href="<?php echo get_permalink(); ?>"><h5 class="card-title"><?php echo get_the_title(); ?></h5></a>
                        <p class="card-text"><?php //echo $product->get_description(); ?></p>
                        <?php if ($is_variable) { ?>
                            <a href="<?php echo get_permalink(); ?>"><button class="btn btn-primary">Options</button></a>
                        <?php } else { ?>
                            <button class="btn btn-primary add-to-cart" data-product-id="<?php echo $product->get_id(); ?>">Add to Cart</button>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>
    </div>
    <?php
    wp_reset_postdata();
} else {
    echo '<p>No products found</p>';
}
?>

<script>
    jQuery(document).ready(function($) {
        $('.add-to-cart').click(function() {
            var productId = $(this).data('product-id');
            $.ajax({
                type: 'POST',
                url: '<?php echo admin_url('admin-ajax.php'); ?>',
                data: {
                    action: 'add_to_cart',
                    product_id: productId,
                },
                success: function(response) {
                    console.log(response);
                }
            });
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>