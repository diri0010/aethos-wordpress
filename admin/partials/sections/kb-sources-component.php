<?php
/**
 * Knowledge Base Content Sources - Simplified Version
 */

// Get Pages settings
$kb_include_all_pages = (bool) get_option('aethos_kb_include_all_pages', true);
$kb_included_pages = get_option('aethos_kb_included_pages', array());
if (!is_array($kb_included_pages)) $kb_included_pages = array();
$kb_excluded_pages = get_option('aethos_kb_excluded_pages', array());
if (!is_array($kb_excluded_pages)) $kb_excluded_pages = array();
$kb_pages_auto_sync = (bool) get_option('aethos_kb_pages_auto_sync', true);

// Get Posts settings
$kb_include_all_posts = (bool) get_option('aethos_kb_include_all_posts', true);
$kb_included_posts = get_option('aethos_kb_included_posts', array());
if (!is_array($kb_included_posts)) $kb_included_posts = array();
$kb_excluded_posts = get_option('aethos_kb_excluded_posts', array());
if (!is_array($kb_excluded_posts)) $kb_excluded_posts = array();
$kb_posts_auto_sync = (bool) get_option('aethos_kb_posts_auto_sync', true);

// Fetch Pages
$all_pages = get_posts(array(
    'post_type' => 'page',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'title',
    'order' => 'ASC'
));
if (!is_array($all_pages)) $all_pages = array();

// Fetch Posts
$all_posts = get_posts(array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'title',
    'order' => 'ASC'
));
if (!is_array($all_posts)) $all_posts = array();

// Fetch Categories
$all_categories = get_terms(array(
    'taxonomy' => 'category',
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
));
if (is_wp_error($all_categories)) $all_categories = array();
if (!is_array($all_categories)) $all_categories = array();

// Category Settings
$kb_include_all_categories = (bool) get_option('aethos_kb_include_all_categories', true);
$kb_included_categories = get_option('aethos_kb_included_categories', array());
if (!is_array($kb_included_categories)) $kb_included_categories = array();
$kb_excluded_categories = get_option('aethos_kb_excluded_categories', array());
if (!is_array($kb_excluded_categories)) $kb_excluded_categories = array();

// Check if WooCommerce is active
$woo_active = class_exists('WooCommerce');

// WooCommerce Products
$kb_include_all_woo_products = false;
$kb_included_woo_products = array();
$kb_excluded_woo_products = array();
$kb_woo_products_auto_sync = false;
$all_woo_products = array();

if ($woo_active) {
    $kb_include_all_woo_products = (bool) get_option('aethos_kb_include_all_woo_products', true);
    $kb_included_woo_products = get_option('aethos_kb_included_woo_products', array());
    if (!is_array($kb_included_woo_products)) $kb_included_woo_products = array();
    $kb_excluded_woo_products = get_option('aethos_kb_excluded_woo_products', array());
    if (!is_array($kb_excluded_woo_products)) $kb_excluded_woo_products = array();
    $kb_woo_products_auto_sync = (bool) get_option('aethos_kb_woo_products_auto_sync', true);
    
    $all_woo_products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    if (!is_array($all_woo_products)) $all_woo_products = array();
}

// WooCommerce Categories
$kb_include_all_woo_categories = false;
$kb_included_woo_categories = array();
$kb_excluded_woo_categories = array();
$kb_woo_categories_auto_sync = false;
$all_woo_categories = array();

if ($woo_active) {
    $kb_include_all_woo_categories = (bool) get_option('aethos_kb_include_all_woo_categories', true);
    $kb_included_woo_categories = get_option('aethos_kb_included_woo_categories', array());
    if (!is_array($kb_included_woo_categories)) $kb_included_woo_categories = array();
    $kb_excluded_woo_categories = get_option('aethos_kb_excluded_woo_categories', array());
    if (!is_array($kb_excluded_woo_categories)) $kb_excluded_woo_categories = array();
    $kb_woo_categories_auto_sync = (bool) get_option('aethos_kb_woo_categories_auto_sync', true);
    
    $all_woo_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));
    if (is_wp_error($all_woo_categories)) $all_woo_categories = array();
    if (!is_array($all_woo_categories)) $all_woo_categories = array();
}

// Get custom post types (excluding built-ins and product)
$args = array(
    'public' => true,
    '_builtin' => false
);
$custom_post_types = get_post_types($args, 'objects');

// Prepare CPT data
$cpt_data = array();
foreach ($custom_post_types as $cpt) {
    if ($cpt->name === 'product') continue; // Skip WooCommerce
    
    $cpt_data[] = array(
        'name' => $cpt->name,
        'label' => $cpt->label,
        'include_all' => (bool) get_option("aethos_kb_include_all_{$cpt->name}", false),
        'included' => get_option("aethos_kb_included_{$cpt->name}", array()),
        'excluded' => get_option("aethos_kb_excluded_{$cpt->name}", array()),
        'auto_sync' => (bool) get_option("aethos_kb_{$cpt->name}_auto_sync", false),
        'items' => get_posts(array(
            'post_type' => $cpt->name,
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'title',
            'order' => 'ASC'
        ))
    );
}
?>

<style>
/* 2-Column Layout */
.aethos-kb-wrapper {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 24px;
    margin-bottom: 24px;
}

.aethos-kb-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.aethos-kb-summary {
    position: sticky;
    top: 32px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}

.aethos-kb-summary h3 {
    margin: 0 0 16px 0;
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}

.aethos-kb-summary-item {
    display: flex;
    gap: 12px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    margin-bottom: 12px;
}

.aethos-kb-summary-icon {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}

.aethos-kb-summary-icon.success {
    background: #d1fae5;
    color: #065f46;
}

.aethos-kb-summary-icon.info {
    background: #dbeafe;
    color: #1e40af;
}

.aethos-kb-summary-icon.warning {
    background: #fef3c7;
    color: #92400e;
}

.aethos-kb-summary-text {
    flex: 1;
    font-size: 13px;
    line-height: 1.6;
    color: #374151;
}

@media (max-width: 1200px) {
    .aethos-kb-wrapper {
        grid-template-columns: 1fr;
    }
    
    .aethos-kb-summary {
        position: static;
        max-height: none;
    }
}
.aethos-kb-tab-buttons {
    display: flex;
    gap: 0;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 20px;
}

.aethos-kb-tab-button {
    padding: 12px 24px;
    border: none;
    background: none;
    color: #6b7280;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    border-bottom: 2px solid transparent;
    margin-bottom: -2px;
    transition: all 0.2s;
}

.aethos-kb-tab-button:hover {
    color: #374151;
}

.aethos-kb-tab-button.active {
    color: #4f46e5;
    border-bottom-color: #4f46e5;
}

.aethos-kb-tab {
    display: none;
}

.aethos-kb-tab.active {
    display: block;
}

.aethos-kb-all-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: #f0f9ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    margin-bottom: 16px;
}

.aethos-kb-all-option input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.aethos-kb-all-option label {
    font-size: 14px;
    font-weight: 500;
    color: #1e40af;
    cursor: pointer;
    margin: 0;
}

.aethos-kb-auto-sync-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    margin-top: 16px;
}

.aethos-kb-auto-sync-option input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.aethos-kb-auto-sync-option label {
    font-size: 14px;
    font-weight: 500;
    color: #166534;
    cursor: pointer;
    margin: 0;
}

.aethos-kb-select-wrapper {
    position: relative;
    margin-bottom: 16px;
}

.aethos-kb-select-wrapper[data-disabled="true"] {
    opacity: 0.5;
    pointer-events: none;
}

.aethos-kb-select-box {
    position: relative;
}

.aethos-kb-select-dropdown {
    width: 100%;
    padding: 12px 40px 12px 16px;
    font-size: 14px;
    color: #374151;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}

.aethos-kb-select-dropdown:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.aethos-kb-select-dropdown:disabled {
    background: #f9fafb;
    cursor: not-allowed;
}

.aethos-kb-select-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    pointer-events: none;
    font-size: 18px;
}

.aethos-kb-selected-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 60px;
    padding: 12px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.aethos-kb-selected-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    color: #374151;
}

.aethos-kb-selected-tag .remove {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    background: #ef4444;
    color: white;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
    line-height: 1;
}

.aethos-kb-empty-state {
    width: 100%;
    text-align: center;
    color: #9ca3af;
    font-size: 13px;
    padding: 20px;
}
</style>

<div class="aethos-kb-wrapper">
    <div class="aethos-kb-main">

<!-- Pages Card -->
<div class="aethos-content-card">
    <h3>Pages</h3>
    <p class="description">Control which pages are included in your chatbot's knowledge base.</p>
    
    <div class="aethos-kb-tab-buttons">
        <button type="button" class="aethos-kb-tab-button active" data-tab="pages-include">Include</button>
        <button type="button" class="aethos-kb-tab-button" data-tab="pages-exclude">Exclude</button>
    </div>
    
    <div id="pages-include" class="aethos-kb-tab active">
        <div class="aethos-kb-all-option">
            <input type="checkbox" id="kb_include_all_pages" name="aethos_kb_include_all_pages" value="1" <?php checked($kb_include_all_pages, true); ?>>
            <label for="kb_include_all_pages">Include all pages in knowledge base</label>
        </div>
        
        <div class="aethos-kb-select-wrapper" data-disabled="<?php echo $kb_include_all_pages ? 'true' : 'false'; ?>">
            <div class="aethos-kb-select-box">
                <select class="aethos-kb-select-dropdown" data-target="kb_included_pages" <?php echo $kb_include_all_pages ? 'disabled' : ''; ?>>
                    <option value="">Select pages to include...</option>
                    <?php foreach ($all_pages as $page): ?>
                        <option value="<?php echo $page->ID; ?>"><?php echo esc_html($page->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="dashicons dashicons-arrow-down-alt2 aethos-kb-select-icon"></span>
            </div>
            
            <div class="aethos-kb-selected-items" data-for="kb_included_pages">
                <?php if (!empty($kb_included_pages)): ?>
                    <?php foreach ($kb_included_pages as $page_id): 
                        $page = get_post($page_id);
                        if ($page):
                    ?>
                    <span class="aethos-kb-selected-tag" data-id="<?php echo $page_id; ?>">
                        <?php echo esc_html($page->post_title); ?>
                        <span class="remove" title="Remove">×</span>
                    </span>
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="aethos-kb-empty-state">No pages selected</div>
                <?php endif; ?>
            </div>
            
            <?php foreach ($kb_included_pages as $page_id): ?>
                <input type="hidden" name="aethos_kb_included_pages[]" value="<?php echo esc_attr($page_id); ?>">
            <?php endforeach; ?>
        </div>
        
        <div class="aethos-kb-auto-sync-option">
            <input type="checkbox" id="kb_pages_auto_sync" name="aethos_kb_pages_auto_sync" value="1" <?php checked($kb_pages_auto_sync, true); ?>>
            <label for="kb_pages_auto_sync">Automatically add new pages to knowledge base</label>
        </div>
    </div>
    
    <div id="pages-exclude" class="aethos-kb-tab">
        <div class="aethos-kb-select-wrapper">
            <div class="aethos-kb-select-box">
                <select class="aethos-kb-select-dropdown" data-target="kb_excluded_pages">
                    <option value="">Select pages to exclude...</option>
                    <?php foreach ($all_pages as $page): ?>
                        <option value="<?php echo $page->ID; ?>"><?php echo esc_html($page->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="dashicons dashicons-arrow-down-alt2 aethos-kb-select-icon"></span>
            </div>
            
            <div class="aethos-kb-selected-items" data-for="kb_excluded_pages">
                <?php if (!empty($kb_excluded_pages)): ?>
                    <?php foreach ($kb_excluded_pages as $page_id): 
                        $page = get_post($page_id);
                        if ($page):
                    ?>
                    <span class="aethos-kb-selected-tag" data-id="<?php echo $page_id; ?>">
                        <?php echo esc_html($page->post_title); ?>
                        <span class="remove" title="Remove">×</span>
                    </span>
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="aethos-kb-empty-state">No pages excluded</div>
                <?php endif; ?>
            </div>
            
            <?php foreach ($kb_excluded_pages as $page_id): ?>
                <input type="hidden" name="aethos_kb_excluded_pages[]" value="<?php echo esc_attr($page_id); ?>">
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Posts Card -->
<div class="aethos-content-card">
    <h3>Posts</h3>
    <p class="description">Control which blog posts are included in your chatbot's knowledge base.</p>
    
    <div class="aethos-kb-tab-buttons">
        <button type="button" class="aethos-kb-tab-button active" data-tab="posts-include">Include</button>
        <button type="button" class="aethos-kb-tab-button" data-tab="posts-exclude">Exclude</button>
    </div>
    
    <div id="posts-include" class="aethos-kb-tab active">
        <div class="aethos-kb-all-option">
            <input type="checkbox" id="kb_include_all_posts" name="aethos_kb_include_all_posts" value="1" <?php checked($kb_include_all_posts, true); ?>>
            <label for="kb_include_all_posts">Include all posts in knowledge base</label>
        </div>
        
        <div class="aethos-kb-select-wrapper" data-disabled="<?php echo $kb_include_all_posts ? 'true' : 'false'; ?>">
            <div class="aethos-kb-select-box">
                <select class="aethos-kb-select-dropdown" data-target="kb_included_posts" <?php echo $kb_include_all_posts ? 'disabled' : ''; ?>>
                    <option value="">Select posts to include...</option>
                    <?php foreach ($all_posts as $post): ?>
                        <option value="<?php echo $post->ID; ?>"><?php echo esc_html($post->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="dashicons dashicons-arrow-down-alt2 aethos-kb-select-icon"></span>
            </div>
            
            <div class="aethos-kb-selected-items" data-for="kb_included_posts">
                <?php if (!empty($kb_included_posts)): ?>
                    <?php foreach ($kb_included_posts as $post_id): 
                        $post_item = get_post($post_id);
                        if ($post_item):
                    ?>
                    <span class="aethos-kb-selected-tag" data-id="<?php echo $post_id; ?>">
                        <?php echo esc_html($post_item->post_title); ?>
                        <span class="remove" title="Remove">×</span>
                    </span>
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="aethos-kb-empty-state">No posts selected</div>
                <?php endif; ?>
            </div>
            
            <?php foreach ($kb_included_posts as $post_id): ?>
                <input type="hidden" name="aethos_kb_included_posts[]" value="<?php echo esc_attr($post_id); ?>">
            <?php endforeach; ?>
        </div>
        
        <div class="aethos-kb-auto-sync-option">
            <input type="checkbox" id="kb_posts_auto_sync" name="aethos_kb_posts_auto_sync" value="1" <?php checked($kb_posts_auto_sync, true); ?>>
            <label for="kb_posts_auto_sync">Automatically add new posts to knowledge base</label>
        </div>
    </div>
    
    <div id="posts-exclude" class="aethos-kb-tab">
        <!-- Exclude Categories -->
        <div class="aethos-kb-select-wrapper">
            <label style="display:block; margin-bottom:8px; font-weight:500; color:#374151;">Exclude Categories</label>
            <div class="aethos-kb-select-box">
                <select class="aethos-kb-select-dropdown" data-target="kb_excluded_categories">
                    <option value="">Select categories to exclude...</option>
                    <?php foreach ($all_categories as $cat): ?>
                        <option value="<?php echo $cat->term_id; ?>"><?php echo esc_html($cat->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="dashicons dashicons-arrow-down-alt2 aethos-kb-select-icon"></span>
            </div>
            
            <div class="aethos-kb-selected-items" data-for="kb_excluded_categories">
                <?php if (!empty($kb_excluded_categories)): ?>
                    <?php foreach ($kb_excluded_categories as $cat_id): 
                        $cat = get_term($cat_id, 'category');
                        if ($cat && !is_wp_error($cat)):
                    ?>
                    <span class="aethos-kb-selected-tag" data-id="<?php echo $cat_id; ?>">
                        <?php echo esc_html($cat->name); ?>
                        <span class="remove" title="Remove">×</span>
                    </span>
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="aethos-kb-empty-state">No categories excluded</div>
                <?php endif; ?>
            </div>
            
            <?php foreach ($kb_excluded_categories as $cat_id): ?>
                <input type="hidden" name="aethos_kb_excluded_categories[]" value="<?php echo esc_attr($cat_id); ?>">
            <?php endforeach; ?>
        </div>

        <hr style="margin: 24px 0; border: 0; border-top: 1px solid #e5e7eb;">

        <!-- Exclude Specific Posts -->
        <div class="aethos-kb-select-wrapper">
            <label style="display:block; margin-bottom:8px; font-weight:500; color:#374151;">Exclude Specific Posts</label>
            <div class="aethos-kb-select-box">
                <select class="aethos-kb-select-dropdown" data-target="kb_excluded_posts">
                    <option value="">Select posts to exclude...</option>
                    <?php foreach ($all_posts as $post): ?>
                        <option value="<?php echo $post->ID; ?>"><?php echo esc_html($post->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="dashicons dashicons-arrow-down-alt2 aethos-kb-select-icon"></span>
            </div>
            
            <div class="aethos-kb-selected-items" data-for="kb_excluded_posts">
                <?php if (!empty($kb_excluded_posts)): ?>
                    <?php foreach ($kb_excluded_posts as $post_id): 
                        $post_item = get_post($post_id);
                        if ($post_item):
                    ?>
                    <span class="aethos-kb-selected-tag" data-id="<?php echo $post_id; ?>">
                        <?php echo esc_html($post_item->post_title); ?>
                        <span class="remove" title="Remove">×</span>
                    </span>
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="aethos-kb-empty-state">No posts excluded</div>
                <?php endif; ?>
            </div>
            
            <?php foreach ($kb_excluded_posts as $post_id): ?>
                <input type="hidden" name="aethos_kb_excluded_posts[]" value="<?php echo esc_attr($post_id); ?>">
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php foreach ($cpt_data as $cpt): ?>
<!-- <?php echo esc_html($cpt['label']); ?> Card -->
<div class="aethos-content-card">
    <h3><?php echo esc_html($cpt['label']); ?></h3>
    <p class="description">Control which <?php echo strtolower($cpt['label']); ?> are included in your chatbot's knowledge base.</p>
    
    <div class="aethos-kb-tab-buttons">
        <button type="button" class="aethos-kb-tab-button active" data-tab="<?php echo esc_attr($cpt['name']); ?>-include">Include</button>
        <button type="button" class="aethos-kb-tab-button" data-tab="<?php echo esc_attr($cpt['name']); ?>-exclude">Exclude</button>
    </div>
    
    <div id="<?php echo esc_attr($cpt['name']); ?>-include" class="aethos-kb-tab active">
        <div class="aethos-kb-all-option">
            <input type="checkbox" id="kb_include_all_<?php echo esc_attr($cpt['name']); ?>" name="aethos_kb_include_all_<?php echo esc_attr($cpt['name']); ?>" value="1" <?php checked($cpt['include_all'], true); ?>>
            <label for="kb_include_all_<?php echo esc_attr($cpt['name']); ?>">Include all <?php echo strtolower($cpt['label']); ?> in knowledge base</label>
        </div>
        
        <div class="aethos-kb-select-wrapper" data-disabled="<?php echo $cpt['include_all'] ? 'true' : 'false'; ?>">
            <div class="aethos-kb-select-box">
                <select class="aethos-kb-select-dropdown" data-target="kb_included_<?php echo esc_attr($cpt['name']); ?>" <?php echo $cpt['include_all'] ? 'disabled' : ''; ?>>
                    <option value="">Select <?php echo strtolower($cpt['label']); ?> to include...</option>
                    <?php foreach ($cpt['items'] as $item): ?>
                        <option value="<?php echo $item->ID; ?>"><?php echo esc_html($item->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="dashicons dashicons-arrow-down-alt2 aethos-kb-select-icon"></span>
            </div>
            
            <div class="aethos-kb-selected-items" data-for="kb_included_<?php echo esc_attr($cpt['name']); ?>">
                <?php if (!empty($cpt['included'])): ?>
                    <?php foreach ($cpt['included'] as $item_id): 
                        $item = get_post($item_id);
                        if ($item):
                    ?>
                    <span class="aethos-kb-selected-tag" data-id="<?php echo $item_id; ?>">
                        <?php echo esc_html($item->post_title); ?>
                        <span class="remove" title="Remove">×</span>
                    </span>
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="aethos-kb-empty-state">No items selected</div>
                <?php endif; ?>
            </div>
            
            <?php foreach ($cpt['included'] as $item_id): ?>
                <input type="hidden" name="aethos_kb_included_<?php echo esc_attr($cpt['name']); ?>[]" value="<?php echo esc_attr($item_id); ?>">
            <?php endforeach; ?>
        </div>
        
        <div class="aethos-kb-auto-sync-option">
            <input type="checkbox" id="kb_<?php echo esc_attr($cpt['name']); ?>_auto_sync" name="aethos_kb_<?php echo esc_attr($cpt['name']); ?>_auto_sync" value="1" <?php checked($cpt['auto_sync'], true); ?>>
            <label for="kb_<?php echo esc_attr($cpt['name']); ?>_auto_sync">Automatically add new <?php echo strtolower($cpt['label']); ?> to knowledge base</label>
        </div>
    </div>
    
    <div id="<?php echo esc_attr($cpt['name']); ?>-exclude" class="aethos-kb-tab">
        <div class="aethos-kb-select-wrapper">
            <div class="aethos-kb-select-box">
                <select class="aethos-kb-select-dropdown" data-target="kb_excluded_<?php echo esc_attr($cpt['name']); ?>">
                    <option value="">Select <?php echo strtolower($cpt['label']); ?> to exclude...</option>
                    <?php foreach ($cpt['items'] as $item): ?>
                        <option value="<?php echo $item->ID; ?>"><?php echo esc_html($item->post_title); ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="dashicons dashicons-arrow-down-alt2 aethos-kb-select-icon"></span>
            </div>
            
            <div class="aethos-kb-selected-items" data-for="kb_excluded_<?php echo esc_attr($cpt['name']); ?>">
                <?php if (!empty($cpt['excluded'])): ?>
                    <?php foreach ($cpt['excluded'] as $item_id): 
                        $item = get_post($item_id);
                        if ($item):
                    ?>
                    <span class="aethos-kb-selected-tag" data-id="<?php echo $item_id; ?>">
                        <?php echo esc_html($item->post_title); ?>
                        <span class="remove" title="Remove">×</span>
                    </span>
                    <?php endif; endforeach; ?>
                <?php else: ?>
                    <div class="aethos-kb-empty-state">No items excluded</div>
                <?php endif; ?>
            </div>
            
            <?php foreach ($cpt['excluded'] as $item_id): ?>
                <input type="hidden" name="aethos_kb_excluded_<?php echo esc_attr($cpt['name']); ?>[]" value="<?php echo esc_attr($item_id); ?>">
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endforeach; ?>

    </div>
    
    <!-- Summary Panel -->
    <div class="aethos-kb-summary">
        <h3>
            <span class="dashicons dashicons-book-alt"></span>
            Knowledge Base Summary
        </h3>
        
        <div class="aethos-kb-summary-item">
            <div class="aethos-kb-summary-icon success">
                <span class="dashicons dashicons-admin-page"></span>
            </div>
            <div class="aethos-kb-summary-text">
                <strong>Pages</strong><br>
                <?php if ($kb_include_all_pages): ?>
                    <span style="color: #059669;">✓ All pages included</span>
                <?php elseif (!empty($kb_included_pages)): ?>
                    <span style="color: #059669;">✓ Included:</span>
                    <ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 12px;">
                        <?php foreach (array_slice($kb_included_pages, 0, 5) as $page_id): 
                            $page = get_post($page_id);
                            if ($page):
                        ?>
                        <li><?php echo esc_html($page->post_title); ?></li>
                        <?php endif; endforeach; ?>
                        <?php if (count($kb_included_pages) > 5): ?>
                        <li><em>+<?php echo count($kb_included_pages) - 5; ?> more</em></li>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <span style="color: #9ca3af;">No pages included</span>
                <?php endif; ?>
                <?php if (!empty($kb_excluded_pages)): ?>
                    <br><span style="color: #dc2626;">✗ Excluded:</span>
                    <ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 12px;">
                        <?php foreach (array_slice($kb_excluded_pages, 0, 3) as $page_id): 
                            $page = get_post($page_id);
                            if ($page):
                        ?>
                        <li><?php echo esc_html($page->post_title); ?></li>
                        <?php endif; endforeach; ?>
                        <?php if (count($kb_excluded_pages) > 3): ?>
                        <li><em>+<?php echo count($kb_excluded_pages) - 3; ?> more</em></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="aethos-kb-summary-item">
            <div class="aethos-kb-summary-icon info">
                <span class="dashicons dashicons-admin-post"></span>
            </div>
            <div class="aethos-kb-summary-text">
                <strong>Posts</strong><br>
                <?php if ($kb_include_all_posts): ?>
                    <span style="color: #059669;">✓ All posts included</span>
                <?php elseif (!empty($kb_included_posts)): ?>
                    <span style="color: #059669;">✓ Included:</span>
                    <ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 12px;">
                        <?php foreach (array_slice($kb_included_posts, 0, 5) as $post_id): 
                            $post_item = get_post($post_id);
                            if ($post_item):
                        ?>
                        <li><?php echo esc_html($post_item->post_title); ?></li>
                        <?php endif; endforeach; ?>
                        <?php if (count($kb_included_posts) > 5): ?>
                        <li><em>+<?php echo count($kb_included_posts) - 5; ?> more</em></li>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <span style="color: #9ca3af;">No posts included</span>
                <?php endif; ?>
                <?php if (!empty($kb_excluded_categories)): ?>
                    <br><span style="color: #dc2626;">✗ Excluded Categories:</span>
                    <ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 12px;">
                        <?php foreach (array_slice($kb_excluded_categories, 0, 3) as $cat_id): 
                            $cat = get_term($cat_id, 'category');
                            if ($cat && !is_wp_error($cat)):
                        ?>
                        <li><?php echo esc_html($cat->name); ?></li>
                        <?php endif; endforeach; ?>
                        <?php if (count($kb_excluded_categories) > 3): ?>
                        <li><em>+<?php echo count($kb_excluded_categories) - 3; ?> more</em></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
                <?php if (!empty($kb_excluded_posts)): ?>
                    <br><span style="color: #dc2626;">✗ Excluded Posts:</span>
                    <ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 12px;">
                        <?php foreach (array_slice($kb_excluded_posts, 0, 3) as $post_id): 
                            $post_item = get_post($post_id);
                            if ($post_item):
                        ?>
                        <li><?php echo esc_html($post_item->post_title); ?></li>
                        <?php endif; endforeach; ?>
                        <?php if (count($kb_excluded_posts) > 3): ?>
                        <li><em>+<?php echo count($kb_excluded_posts) - 3; ?> more</em></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        
        <?php foreach ($cpt_data as $cpt): ?>
        <div class="aethos-kb-summary-item">
            <div class="aethos-kb-summary-icon warning">
                <span class="dashicons dashicons-admin-settings"></span>
            </div>
            <div class="aethos-kb-summary-text">
                <strong><?php echo esc_html($cpt['label']); ?></strong><br>
                <?php if ($cpt['include_all']): ?>
                    <span style="color: #059669;">✓ All included</span>
                <?php elseif (!empty($cpt['included'])): ?>
                    <span style="color: #059669;">✓ Included:</span>
                    <ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 12px;">
                        <?php foreach (array_slice($cpt['included'], 0, 5) as $item_id): 
                            $item = get_post($item_id);
                            if ($item):
                        ?>
                        <li><?php echo esc_html($item->post_title); ?></li>
                        <?php endif; endforeach; ?>
                        <?php if (count($cpt['included']) > 5): ?>
                        <li><em>+<?php echo count($cpt['included']) - 5; ?> more</em></li>
                        <?php endif; ?>
                    </ul>
                <?php else: ?>
                    <span style="color: #9ca3af;">None included</span>
                <?php endif; ?>
                <?php if (!empty($cpt['excluded'])): ?>
                    <br><span style="color: #dc2626;">✗ Excluded:</span>
                    <ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 12px;">
                        <?php foreach (array_slice($cpt['excluded'], 0, 3) as $item_id): 
                            $item = get_post($item_id);
                            if ($item):
                        ?>
                        <li><?php echo esc_html($item->post_title); ?></li>
                        <?php endif; endforeach; ?>
                        <?php if (count($cpt['excluded']) > 3): ?>
                        <li><em>+<?php echo count($cpt['excluded']) - 3; ?> more</em></li>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Tab switching
    $('.aethos-kb-tab-button').on('click', function() {
        const tabId = $(this).data('tab');
        const $card = $(this).closest('.aethos-content-card');
        
        $card.find('.aethos-kb-tab-button').removeClass('active');
        $(this).addClass('active');
        
        $card.find('.aethos-kb-tab').removeClass('active');
        $card.find('#' + tabId).addClass('active');
    });
    
    // Include All checkbox toggle
    $('input[name^="aethos_kb_include_all_"]').on('change', function() {
        const $card = $(this).closest('.aethos-content-card');
        const isChecked = $(this).is(':checked');
        const $wrapper = $card.find('.aethos-kb-select-wrapper').first();
        
        if (isChecked) {
            $wrapper.attr('data-disabled', 'true');
            $wrapper.find('select').prop('disabled', true);
        } else {
            $wrapper.attr('data-disabled', 'false');
            $wrapper.find('select').prop('disabled', false);
        }
        
        // Update summary when Include All changes
        const name = $(this).attr('name');
        const contentType = name.replace('aethos_kb_include_all_', '');
        updateSummary('kb_included_' + contentType);
    });
    
    // Multi-select dropdown
    $('.aethos-kb-select-dropdown').on('change', function() {
        const selectedId = $(this).val();
        if (!selectedId) return;
        
        const target = $(this).data('target');
        const $selectedContainer = $(`.aethos-kb-selected-items[data-for="${target}"]`);
        const $hiddenInput = $(`input[name="aethos_${target}"]`);
        const selectedText = $(this).find('option:selected').text();
        
        if ($selectedContainer.find(`[data-id="${selectedId}"]`).length > 0) {
            $(this).val('');
            return;
        }
        
        $selectedContainer.find('.aethos-kb-empty-state').remove();
        
        const $tag = $(`
            <span class="aethos-kb-selected-tag" data-id="${selectedId}">
                ${selectedText}
                <span class="remove" title="Remove">×</span>
            </span>
        `);
        $selectedContainer.append($tag);
        
        updateHiddenInput(target);
        $(this).val('');
    });
    
    // Remove tag
    $(document).on('click', '.aethos-kb-selected-tag .remove', function() {
        const $tag = $(this).closest('.aethos-kb-selected-tag');
        const $container = $tag.closest('.aethos-kb-selected-items');
        const target = $container.data('for');
        
        $tag.remove();
        
        if ($container.find('.aethos-kb-selected-tag').length === 0) {
            $container.append('<div class="aethos-kb-empty-state">No items selected</div>');
        }
        
        updateHiddenInput(target);
    });
    
    function updateHiddenInput(target) {
        const $container = $(`.aethos-kb-selected-items[data-for="${target}"]`);
        const $wrapper = $container.closest('.aethos-kb-select-wrapper');
        
        // Remove all existing hidden inputs for this target
        $wrapper.find(`input[name="aethos_${target}[]"]`).remove();
        
        // Create new hidden input for each selected item
        $container.find('.aethos-kb-selected-tag').each(function() {
            const id = $(this).data('id');
            const $hiddenInput = $(`<input type="hidden" name="aethos_${target}[]" value="${id}">`);
            $wrapper.append($hiddenInput);
        });
        
        // Update opposite dropdown to exclude selected items
        filterDropdownOptions(target);
        
        // Update summary panel
        updateSummary(target);
    }
    
    function filterDropdownOptions(target) {
        // Determine the opposite target
        let oppositeTarget = '';
        if (target.includes('included')) {
            oppositeTarget = target.replace('included', 'excluded');
        } else if (target.includes('excluded')) {
            oppositeTarget = target.replace('excluded', 'included');
        }
        
        if (!oppositeTarget) return;
        
        // Get selected IDs from current target
        const selectedIds = [];
        $(`.aethos-kb-selected-items[data-for="${target}"] .aethos-kb-selected-tag`).each(function() {
            selectedIds.push($(this).data('id').toString());
        });
        
        // Filter opposite dropdown
        const $oppositeDropdown = $(`.aethos-kb-select-dropdown[data-target="${oppositeTarget}"]`);
        $oppositeDropdown.find('option').each(function() {
            const optionValue = $(this).val();
            if (optionValue && selectedIds.includes(optionValue)) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
        
        // Also filter current dropdown based on opposite selections
        const oppositeSelectedIds = [];
        $(`.aethos-kb-selected-items[data-for="${oppositeTarget}"] .aethos-kb-selected-tag`).each(function() {
            oppositeSelectedIds.push($(this).data('id').toString());
        });
        
        const $currentDropdown = $(`.aethos-kb-select-dropdown[data-target="${target}"]`);
        $currentDropdown.find('option').each(function() {
            const optionValue = $(this).val();
            if (optionValue && oppositeSelectedIds.includes(optionValue)) {
                $(this).hide();
            } else {
                $(this).show();
            }
        });
    }
    
    // Initialize dropdown filtering on page load
    $('.aethos-kb-selected-items').each(function() {
        const target = $(this).data('for');
        if (target) {
            filterDropdownOptions(target);
        }
    });
    
    function updateSummary(target) {
        console.log('updateSummary called with target:', target);
        
        // Extract the content type from target
        let contentType = target.replace('kb_included_', '').replace('kb_excluded_', '');
        
        // Special handling: Map categories to posts
        if (contentType === 'categories') {
            contentType = 'posts';
        }
        
        console.log('Extracted contentType:', contentType);
        
        // Get counts for included and excluded (standard items)
        const includedCount = $(`.aethos-kb-selected-items[data-for="kb_included_${contentType}"] .aethos-kb-selected-tag`).length;
        const excludedCount = $(`.aethos-kb-selected-items[data-for="kb_excluded_${contentType}"] .aethos-kb-selected-tag`).length;
        
        // Find the summary item for this content type
        const $summaryItems = $('.aethos-kb-summary-item');
        let $targetSummary = null;
        
        // Try to match by content type name
        $summaryItems.each(function(index) {
            const $strong = $(this).find('strong').first();
            const summaryText = $strong.text().toLowerCase().trim();
            const normalizedContentType = contentType.toLowerCase().replace(/_/g, ' ');
            
            const matches = 
                summaryText === normalizedContentType || 
                summaryText === contentType.toLowerCase() ||
                summaryText === normalizedContentType + 's' ||
                summaryText === normalizedContentType.replace(/s$/, '') ||
                summaryText.replace(/s$/, '') === normalizedContentType.replace(/s$/, '') ||
                summaryText.replace(/ies$/, 'y') === normalizedContentType.replace(/ies$/, 'y') ||
                normalizedContentType.includes(summaryText) ||
                summaryText.includes(normalizedContentType);
            
            if (matches) {
                $targetSummary = $(this);
                return false;
            }
        });
        
        if (!$targetSummary) {
            console.warn('Summary item not found for:', contentType);
            return;
        }
        
        // Get the display name from the summary
        const displayName = $targetSummary.find('strong').first().text();
        
        // Build the summary HTML
        let summaryHtml = `<strong>${displayName}</strong><br>`;
        
        // Check if "Include All" is checked
        const $includeAllCheckbox = $(`#kb_include_all_${contentType}`);
        const isIncludeAll = $includeAllCheckbox.length > 0 && $includeAllCheckbox.is(':checked');
        
        if (isIncludeAll) {
            summaryHtml += '<span style="color: #059669;">✓ All included</span>';
        } else if (includedCount > 0) {
            summaryHtml += '<span style="color: #059669;">✓ Included:</span>';
            summaryHtml += '<ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 12px;">';
            
            $(`.aethos-kb-selected-items[data-for="kb_included_${contentType}"] .aethos-kb-selected-tag`).slice(0, 5).each(function() {
                const itemText = $(this).clone().children().remove().end().text().trim();
                summaryHtml += `<li>${itemText}</li>`;
            });
            
            if (includedCount > 5) {
                summaryHtml += `<li><em>+${includedCount - 5} more</em></li>`;
            }
            summaryHtml += '</ul>';
        } else {
            summaryHtml += '<span style="color: #9ca3af;">None included</span>';
        }
        
        // Special Handling: Excluded Categories (only for Posts)
        if (contentType === 'posts') {
            const excludedCatCount = $(`.aethos-kb-selected-items[data-for="kb_excluded_categories"] .aethos-kb-selected-tag`).length;
            
            if (excludedCatCount > 0) {
                summaryHtml += '<br><span style="color: #dc2626;">✗ Excluded Categories:</span>';
                summaryHtml += '<ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 12px;">';
                
                $(`.aethos-kb-selected-items[data-for="kb_excluded_categories"] .aethos-kb-selected-tag`).slice(0, 3).each(function() {
                    const itemText = $(this).clone().children().remove().end().text().trim();
                    summaryHtml += `<li>${itemText}</li>`;
                });
                
                if (excludedCatCount > 3) {
                    summaryHtml += `<li><em>+${excludedCatCount - 3} more</em></li>`;
                }
                summaryHtml += '</ul>';
            }
        }
        
        // Standard Excluded Items
        if (excludedCount > 0) {
            let label = 'Excluded:';
            if (contentType === 'posts') label = 'Excluded Posts:';
            
            summaryHtml += `<br><span style="color: #dc2626;">✗ ${label}</span>`;
            summaryHtml += '<ul style="margin: 4px 0 0 0; padding-left: 16px; font-size: 12px;">';
            
            $(`.aethos-kb-selected-items[data-for="kb_excluded_${contentType}"] .aethos-kb-selected-tag`).slice(0, 3).each(function() {
                const itemText = $(this).clone().children().remove().end().text().trim();
                summaryHtml += `<li>${itemText}</li>`;
            });
            
            if (excludedCount > 3) {
                summaryHtml += `<li><em>+${excludedCount - 3} more</em></li>`;
            }
            summaryHtml += '</ul>';
        }
        
        $targetSummary.find('.aethos-kb-summary-text').html(summaryHtml);
    }
});
</script>
