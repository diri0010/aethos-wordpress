<?php
/**
 * Chatbot Visibility Content Section - Enhanced Version
 * 
 * Features:
 * - Multi-select dropdowns with auto-populated options
 * - Modern, clean styling
 * - Detailed summary with actual page names
 * - Consistent WooCommerce integration
 */

// Get current visibility settings
$global_visibility = (bool) get_option('aethos_global_visibility', false);
$included_pages = get_option('aethos_kb_included_pages', array());
if (!is_array($included_pages)) {
    $included_pages = array();
}
$excluded_pages = get_option('aethos_kb_excluded_pages', array());
if (!is_array($excluded_pages)) {
    $excluded_pages = array();
}
$included_categories = get_option('aethos_kb_included_categories', array());
if (!is_array($included_categories)) {
    $included_categories = array();
}
$excluded_categories = get_option('aethos_kb_excluded_categories', array());
if (!is_array($excluded_categories)) {
    $excluded_categories = array();
}
$include_all_pages = (bool) get_option('aethos_kb_include_all_pages', true);
$include_all_categories = (bool) get_option('aethos_kb_include_all_categories', true);

// WooCommerce settings
$woo_active = class_exists('WooCommerce');
if ($woo_active) {
    $included_woo_products = get_option('aethos_kb_included_woo_products', array());
    if (!is_array($included_woo_products)) {
        $included_woo_products = array();
    }
    $excluded_woo_products = get_option('aethos_kb_excluded_woo_products', array());
    if (!is_array($excluded_woo_products)) {
        $excluded_woo_products = array();
    }
    $included_woo_categories = get_option('aethos_kb_included_woo_categories', array());
    if (!is_array($included_woo_categories)) {
        $included_woo_categories = array();
    }
    $excluded_woo_categories = get_option('aethos_kb_excluded_woo_categories', array());
    if (!is_array($excluded_woo_categories)) {
        $excluded_woo_categories = array();
    }
    $include_all_woo_products = (bool) get_option('aethos_kb_include_all_woo_products', true);
    $include_all_woo_categories = (bool) get_option('aethos_kb_include_all_woo_categories', true);
}

// Fetch all pages and posts
$all_pages_posts = get_posts(array(
    'post_type' => array('page', 'post'),
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'orderby' => 'title',
    'order' => 'ASC'
));
if (!is_array($all_pages_posts)) {
    $all_pages_posts = array();
}

// Fetch all categories and tags
$all_categories = get_terms(array(
    'taxonomy' => array('category', 'post_tag'),
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
));
if (is_wp_error($all_categories) || !is_array($all_categories)) {
    $all_categories = array();
}

// Fetch WooCommerce products and categories if active
if ($woo_active) {
    $all_woo_products = get_posts(array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC'
    ));
    if (!is_array($all_woo_products)) {
        $all_woo_products = array();
    }
    
    $all_woo_categories = get_terms(array(
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));
    if (is_wp_error($all_woo_categories) || !is_array($all_woo_categories)) {
        $all_woo_categories = array();
    }
}
?>

<style>
/* Container Layout */
.aethos-visibility-container {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 24px;
    margin-bottom: 32px;
}

.aethos-visibility-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Card Styling */
.aethos-visibility-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.aethos-visibility-card h2 {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.aethos-visibility-card p {
    margin: 0 0 20px 0;
    color: #6b7280;
    font-size: 14px;
    line-height: 1.6;
}

/* Global Toggle */
.aethos-global-toggle {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    margin-bottom: 8px;
}

.aethos-toggle-switch {
    position: relative;
    display: inline-block;
    width: 52px;
    height: 28px;
}

.aethos-toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.aethos-toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: 0.3s;
    border-radius: 28px;
}

.aethos-toggle-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}

.aethos-toggle-switch input:checked + .aethos-toggle-slider {
    background-color: #4f46e5;
}

.aethos-toggle-switch input:checked + .aethos-toggle-slider:before {
    transform: translateX(24px);
}

.aethos-toggle-label {
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

/* Tab Buttons */
.aethos-tab-buttons {
    display: flex;
    gap: 0;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 20px;
}

.aethos-tab-button {
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

.aethos-tab-button:hover {
    color: #374151;
}

.aethos-tab-button.active {
    color: #4f46e5;
    border-bottom-color: #4f46e5;
}

/* Visibility Tab Content */
.aethos-visibility-tab {
    display: none !important;
}

.aethos-visibility-tab.active {
    display: block !important;
}

/* Include All Option */
.aethos-all-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 16px;
    background: #f0f9ff;
    border: 1px solid #bfdbfe;
    border-radius: 8px;
    margin-bottom: 16px;
}

.aethos-all-option input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.aethos-all-option label {
    font-size: 14px;
    font-weight: 500;
    color: #1e40af;
    cursor: pointer;
    margin: 0;
}

/* Modern Select Dropdown */
.aethos-select-wrapper {
    position: relative;
    margin-bottom: 16px;
}

.aethos-select-wrapper[data-disabled="true"] {
    opacity: 0.5;
    pointer-events: none;
}

.aethos-select-box {
    position: relative;
}

.aethos-select-dropdown {
    width: 100%;
    padding: 12px 40px 12px 16px;
    font-size: 14px;
    color: #374151;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
}

.aethos-select-dropdown:hover {
    border-color: #cbd5e1;
}

.aethos-select-dropdown:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.aethos-select-dropdown:disabled {
    background: #f9fafb;
    cursor: not-allowed;
}

.aethos-select-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6b7280;
    pointer-events: none;
    font-size: 18px;
}

/* Selected Items */
.aethos-selected-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 60px;
    padding: 12px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.aethos-selected-tag {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    background: #ffffff;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 13px;
    color: #374151;
    transition: all 0.2s;
}

.aethos-selected-tag:hover {
    border-color: #9ca3af;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.aethos-selected-tag .remove {
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
    transition: all 0.2s;
}

.aethos-selected-tag .remove:hover {
    background: #dc2626;
    transform: scale(1.1);
}

.aethos-empty-state {
    width: 100%;
    text-align: center;
    color: #9ca3af;
    font-size: 13px;
    padding: 20px;
}

/* Summary Panel */
.aethos-visibility-summary {
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

.aethos-visibility-summary h3 {
    margin: 0 0 16px 0;
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
}

.aethos-summary-item {
    display: flex;
    gap: 12px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    margin-bottom: 12px;
}

.aethos-summary-icon {
    flex-shrink: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
}

.aethos-summary-icon.success {
    background: #d1fae5;
    color: #065f46;
}

.aethos-summary-icon.info {
    background: #dbeafe;
    color: #1e40af;
}

.aethos-summary-icon.warning {
    background: #fef3c7;
    color: #92400e;
}

.aethos-summary-icon.neutral {
    background: #f3f4f6;
    color: #6b7280;
}

.aethos-summary-text {
    flex: 1;
    font-size: 13px;
    line-height: 1.6;
    color: #374151;
}

.aethos-summary-list {
    margin: 8px 0 0 0;
    padding-left: 16px;
    font-size: 12px;
    color: #6b7280;
}

.aethos-summary-list li {
    margin-bottom: 4px;
}

/* Responsive */
@media (max-width: 1200px) {
    .aethos-visibility-container {
        grid-template-columns: 1fr;
    }
    
    .aethos-visibility-summary {
        position: static;
        max-height: none;
    }
}
</style>

<div class="aethos-visibility-container">
    <div class="aethos-visibility-main">
        <!-- Global Visibility -->
        <div class="aethos-visibility-card">
            <h2>Global Chatbot Visibility</h2>
            <p>Use this master switch to show or hide the chatbot across your entire website.</p>
            
            <div class="aethos-global-toggle">
                <label class="aethos-toggle-switch">
                    <input type="checkbox" id="aethos_global_visibility" name="aethos_global_visibility" value="1" <?php checked($global_visibility, true); ?>>
                    <span class="aethos-toggle-slider"></span>
                </label>
                <span class="aethos-toggle-label">Chatbot is Active</span>
            </div>
        </div>
        
        <!-- Page & Post Rules -->
        <div class="aethos-visibility-card" data-rule-type="pages">
            <h2>Page & Post Rules</h2>
            <p>Control which specific pages, posts, or custom post types display the chatbot.</p>
            
            <div class="aethos-tab-buttons">
                <button type="button" class="aethos-tab-button active" data-tab="page-include">Include on</button>
                <button type="button" class="aethos-tab-button" data-tab="page-exclude">Exclude from</button>
            </div>
            
            <!-- Include Tab -->
            <div id="page-include" class="aethos-visibility-tab active" data-tab-type="include" data-content-type="pages">
                <div class="aethos-all-option">
                    <input type="checkbox" id="include_all_pages" name="aethos_kb_include_all_pages" value="1" <?php checked($include_all_pages, true); ?>>
                    <label for="include_all_pages">Include on all pages and posts</label>
                </div>
                
                <div class="aethos-select-wrapper" data-disabled="<?php echo $include_all_pages ? 'true' : 'false'; ?>">
                    <div class="aethos-select-box">
                        <select class="aethos-select-dropdown" data-target="included_pages" <?php echo $include_all_pages ? 'disabled' : ''; ?>>
                            <option value="">Select pages or posts to include...</option>
                            <?php foreach ($all_pages_posts as $item): ?>
                                <option value="<?php echo $item->ID; ?>" data-type="<?php echo ucfirst($item->post_type); ?>">
                                    <?php echo esc_html($item->post_title) . ' (' . ucfirst($item->post_type) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="dashicons dashicons-arrow-down-alt2 aethos-select-icon"></span>
                    </div>
                    
                    <div class="aethos-selected-items" data-for="included_pages">
                        <?php if (!empty($included_pages)): ?>
                            <?php foreach ($included_pages as $page_id): 
                                $page = get_post($page_id);
                                if ($page):
                            ?>
                            <span class="aethos-selected-tag" data-id="<?php echo $page_id; ?>">
                                <?php echo esc_html($page->post_title) . ' (' . ucfirst($page->post_type) . ')'; ?>
                                <span class="remove" title="Remove">×</span>
                            </span>
                            <?php endif; endforeach; ?>
                        <?php else: ?>
                            <div class="aethos-empty-state">No pages or posts selected</div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="aethos_kb_included_pages" class="aethos-hidden-input" value="<?php echo esc_attr(implode(',', $included_pages)); ?>">
                </div>
            </div>
            
            <!-- Exclude Tab -->
            <div id="page-exclude" class="aethos-visibility-tab" data-tab-type="exclude" data-content-type="pages">
                <div class="aethos-select-wrapper">
                    <div class="aethos-select-box">
                        <select class="aethos-select-dropdown" data-target="excluded_pages">
                            <option value="">Select pages or posts to exclude...</option>
                            <?php foreach ($all_pages_posts as $item): ?>
                                <option value="<?php echo $item->ID; ?>" data-type="<?php echo ucfirst($item->post_type); ?>">
                                    <?php echo esc_html($item->post_title) . ' (' . ucfirst($item->post_type) . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="dashicons dashicons-arrow-down-alt2 aethos-select-icon"></span>
                    </div>
                    
                    <div class="aethos-selected-items" data-for="excluded_pages">
                        <?php if (!empty($excluded_pages)): ?>
                            <?php foreach ($excluded_pages as $page_id): 
                                $page = get_post($page_id);
                                if ($page):
                            ?>
                            <span class="aethos-selected-tag" data-id="<?php echo $page_id; ?>">
                                <?php echo esc_html($page->post_title) . ' (' . ucfirst($page->post_type) . ')'; ?>
                                <span class="remove" title="Remove">×</span>
                            </span>
                            <?php endif; endforeach; ?>
                        <?php else: ?>
                            <div class="aethos-empty-state">No pages or posts excluded</div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="aethos_kb_excluded_pages" class="aethos-hidden-input" value="<?php echo esc_attr(implode(',', $excluded_pages)); ?>">
                </div>
            </div>
        </div>
        
        <!-- Category & Tag Rules -->
        <div class="aethos-visibility-card" data-rule-type="categories">
            <h2>Category & Tag Rules</h2>
            <p>Show or hide the chatbot on posts within specific categories or tags.</p>
            
            <div class="aethos-tab-buttons">
                <button type="button" class="aethos-tab-button active" data-tab="category-include">Include on</button>
                <button type="button" class="aethos-tab-button" data-tab="category-exclude">Exclude from</button>
            </div>
            
            <!-- Include Tab -->
            <div id="category-include" class="aethos-visibility-tab active" data-tab-type="include" data-content-type="categories">
                <div class="aethos-all-option">
                    <input type="checkbox" id="include_all_categories" name="aethos_kb_include_all_categories" value="1" <?php checked($include_all_categories, true); ?>>
                    <label for="include_all_categories">Include on all categories and tags</label>
                </div>
                
                <div class="aethos-select-wrapper" data-disabled="<?php echo $include_all_categories ? 'true' : 'false'; ?>">
                    <div class="aethos-select-box">
                        <select class="aethos-select-dropdown" data-target="included_categories" <?php echo $include_all_categories ? 'disabled' : ''; ?>>
                            <option value="">Select categories or tags to include...</option>
                            <?php foreach ($all_categories as $term): ?>
                                <option value="<?php echo $term->term_id; ?>" data-type="<?php echo $term->taxonomy === 'category' ? 'Category' : 'Tag'; ?>">
                                    <?php echo esc_html($term->name) . ' (' . ($term->taxonomy === 'category' ? 'Category' : 'Tag') . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="dashicons dashicons-arrow-down-alt2 aethos-select-icon"></span>
                    </div>
                    
                    <div class="aethos-selected-items" data-for="included_categories">
                        <?php if (!empty($included_categories)): ?>
                            <?php foreach ($included_categories as $term_id): 
                                $term = get_term($term_id);
                                if ($term && !is_wp_error($term)):
                            ?>
                            <span class="aethos-selected-tag" data-id="<?php echo $term_id; ?>">
                                <?php echo esc_html($term->name) . ' (' . ($term->taxonomy === 'category' ? 'Category' : 'Tag') . ')'; ?>
                                <span class="remove" title="Remove">×</span>
                            </span>
                            <?php endif; endforeach; ?>
                        <?php else: ?>
                            <div class="aethos-empty-state">No categories or tags selected</div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="aethos_kb_included_categories" class="aethos-hidden-input" value="<?php echo esc_attr(implode(',', $included_categories)); ?>">
                </div>
            </div>
            
            <!-- Exclude Tab -->
            <div id="category-exclude" class="aethos-visibility-tab" data-tab-type="exclude" data-content-type="categories">
                <div class="aethos-select-wrapper">
                    <div class="aethos-select-box">
                        <select class="aethos-select-dropdown" data-target="excluded_categories">
                            <option value="">Select categories or tags to exclude...</option>
                            <?php foreach ($all_categories as $term): ?>
                                <option value="<?php echo $term->term_id; ?>" data-type="<?php echo $term->taxonomy === 'category' ? 'Category' : 'Tag'; ?>">
                                    <?php echo esc_html($term->name) . ' (' . ($term->taxonomy === 'category' ? 'Category' : 'Tag') . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="dashicons dashicons-arrow-down-alt2 aethos-select-icon"></span>
                    </div>
                    
                    <div class="aethos-selected-items" data-for="excluded_categories">
                        <?php if (!empty($excluded_categories)): ?>
                            <?php foreach ($excluded_categories as $term_id): 
                                $term = get_term($term_id);
                                if ($term && !is_wp_error($term)):
                            ?>
                            <span class="aethos-selected-tag" data-id="<?php echo $term_id; ?>">
                                <?php echo esc_html($term->name) . ' (' . ($term->taxonomy === 'category' ? 'Category' : 'Tag') . ')'; ?>
                                <span class="remove" title="Remove">×</span>
                            </span>
                            <?php endif; endforeach; ?>
                        <?php else: ?>
                            <div class="aethos-empty-state">No categories or tags excluded</div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="aethos_kb_excluded_categories" class="aethos-hidden-input" value="<?php echo esc_attr(implode(',', $excluded_categories)); ?>">
                </div>
            </div>
        </div>
        
        <?php if ($woo_active): ?>
        <!-- WooCommerce Product Rules -->
        <div class="aethos-visibility-card" data-rule-type="woo-products">
            <h2>WooCommerce Product Rules</h2>
            <p>Control chatbot visibility on specific WooCommerce product pages.</p>
            
            <div class="aethos-tab-buttons">
                <button type="button" class="aethos-tab-button active" data-tab="woo-product-include">Include on</button>
                <button type="button" class="aethos-tab-button" data-tab="woo-product-exclude">Exclude from</button>
            </div>
            
            <!-- Include Tab -->
            <div id="woo-product-include" class="aethos-visibility-tab active" data-tab-type="include" data-content-type="woo-products">
                <div class="aethos-all-option">
                    <input type="checkbox" id="include_all_woo_products" name="aethos_kb_include_all_woo_products" value="1" <?php checked($include_all_woo_products, true); ?>>
                    <label for="include_all_woo_products">Include on all products</label>
                </div>
                
                <div class="aethos-select-wrapper" data-disabled="<?php echo $include_all_woo_products ? 'true' : 'false'; ?>">
                    <div class="aethos-select-box">
                        <select class="aethos-select-dropdown" data-target="included_woo_products" <?php echo $include_all_woo_products ? 'disabled' : ''; ?>>
                            <option value="">Select products to include...</option>
                            <?php foreach ($all_woo_products as $product): ?>
                                <option value="<?php echo $product->ID; ?>" data-type="Product">
                                    <?php echo esc_html($product->post_title) . ' (Product)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="dashicons dashicons-arrow-down-alt2 aethos-select-icon"></span>
                    </div>
                    
                    <div class="aethos-selected-items" data-for="included_woo_products">
                        <?php if (!empty($included_woo_products)): ?>
                            <?php foreach ($included_woo_products as $product_id): 
                                $product = get_post($product_id);
                                if ($product):
                            ?>
                            <span class="aethos-selected-tag" data-id="<?php echo $product_id; ?>">
                                <?php echo esc_html($product->post_title) . ' (Product)'; ?>
                                <span class="remove" title="Remove">×</span>
                            </span>
                            <?php endif; endforeach; ?>
                        <?php else: ?>
                            <div class="aethos-empty-state">No products selected</div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="aethos_kb_included_woo_products" class="aethos-hidden-input" value="<?php echo esc_attr(implode(',', $included_woo_products)); ?>">
                </div>
            </div>
            
            <!-- Exclude Tab -->
            <div id="woo-product-exclude" class="aethos-visibility-tab" data-tab-type="exclude" data-content-type="woo-products">
                <div class="aethos-select-wrapper">
                    <div class="aethos-select-box">
                        <select class="aethos-select-dropdown" data-target="excluded_woo_products">
                            <option value="">Select products to exclude...</option>
                            <?php foreach ($all_woo_products as $product): ?>
                                <option value="<?php echo $product->ID; ?>" data-type="Product">
                                    <?php echo esc_html($product->post_title) . ' (Product)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="dashicons dashicons-arrow-down-alt2 aethos-select-icon"></span>
                    </div>
                    
                    <div class="aethos-selected-items" data-for="excluded_woo_products">
                        <?php if (!empty($excluded_woo_products)): ?>
                            <?php foreach ($excluded_woo_products as $product_id): 
                                $product = get_post($product_id);
                                if ($product):
                            ?>
                            <span class="aethos-selected-tag" data-id="<?php echo $product_id; ?>">
                                <?php echo esc_html($product->post_title) . ' (Product)'; ?>
                                <span class="remove" title="Remove">×</span>
                            </span>
                            <?php endif; endforeach; ?>
                        <?php else: ?>
                            <div class="aethos-empty-state">No products excluded</div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="aethos_kb_excluded_woo_products" class="aethos-hidden-input" value="<?php echo esc_attr(implode(',', $excluded_woo_products)); ?>">
                </div>
            </div>
        </div>
        
        <!-- WooCommerce Category Rules -->
        <div class="aethos-visibility-card" data-rule-type="woo-categories">
            <h2>WooCommerce Category Rules</h2>
            <p>Show or hide the chatbot on products within specific WooCommerce categories.</p>
            
            <div class="aethos-tab-buttons">
                <button type="button" class="aethos-tab-button active" data-tab="woo-category-include">Include on</button>
                <button type="button" class="aethos-tab-button" data-tab="woo-category-exclude">Exclude from</button>
            </div>
            
            <!-- Include Tab -->
            <div id="woo-category-include" class="aethos-visibility-tab active" data-tab-type="include" data-content-type="woo-categories">
                <div class="aethos-all-option">
                    <input type="checkbox" id="include_all_woo_categories" name="aethos_kb_include_all_woo_categories" value="1" <?php checked($include_all_woo_categories, true); ?>>
                    <label for="include_all_woo_categories">Include on all product categories</label>
                </div>
                
                <div class="aethos-select-wrapper" data-disabled="<?php echo $include_all_woo_categories ? 'true' : 'false'; ?>">
                    <div class="aethos-select-box">
                        <select class="aethos-select-dropdown" data-target="included_woo_categories" <?php echo $include_all_woo_categories ? 'disabled' : ''; ?>>
                            <option value="">Select product categories to include...</option>
                            <?php foreach ($all_woo_categories as $category): ?>
                                <option value="<?php echo $category->term_id; ?>" data-type="Category">
                                    <?php echo esc_html($category->name) . ' (Category)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="dashicons dashicons-arrow-down-alt2 aethos-select-icon"></span>
                    </div>
                    
                    <div class="aethos-selected-items" data-for="included_woo_categories">
                        <?php if (!empty($included_woo_categories)): ?>
                            <?php foreach ($included_woo_categories as $cat_id): 
                                $category = get_term($cat_id, 'product_cat');
                                if ($category && !is_wp_error($category)):
                            ?>
                            <span class="aethos-selected-tag" data-id="<?php echo $cat_id; ?>">
                                <?php echo esc_html($category->name) . ' (Category)'; ?>
                                <span class="remove" title="Remove">×</span>
                            </span>
                            <?php endif; endforeach; ?>
                        <?php else: ?>
                            <div class="aethos-empty-state">No product categories selected</div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="aethos_kb_included_woo_categories" class="aethos-hidden-input" value="<?php echo esc_attr(implode(',', $included_woo_categories)); ?>">
                </div>
            </div>
            
            <!-- Exclude Tab -->
            <div id="woo-category-exclude" class="aethos-visibility-tab" data-tab-type="exclude" data-content-type="woo-categories">
                <div class="aethos-select-wrapper">
                    <div class="aethos-select-box">
                        <select class="aethos-select-dropdown" data-target="excluded_woo_categories">
                            <option value="">Select product categories to exclude...</option>
                            <?php foreach ($all_woo_categories as $category): ?>
                                <option value="<?php echo $category->term_id; ?>" data-type="Category">
                                    <?php echo esc_html($category->name) . ' (Category)'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <span class="dashicons dashicons-arrow-down-alt2 aethos-select-icon"></span>
                    </div>
                    
                    <div class="aethos-selected-items" data-for="excluded_woo_categories">
                        <?php if (!empty($excluded_woo_categories)): ?>
                            <?php foreach ($excluded_woo_categories as $cat_id): 
                                $category = get_term($cat_id, 'product_cat');
                                if ($category && !is_wp_error($category)):
                            ?>
                            <span class="aethos-selected-tag" data-id="<?php echo $cat_id; ?>">
                                <?php echo esc_html($category->name) . ' (Category)'; ?>
                                <span class="remove" title="Remove">×</span>
                            </span>
                            <?php endif; endforeach; ?>
                        <?php else: ?>
                            <div class="aethos-empty-state">No product categories excluded</div>
                        <?php endif; ?>
                    </div>
                    
                    <input type="hidden" name="aethos_kb_excluded_woo_categories" class="aethos-hidden-input" value="<?php echo esc_attr(implode(',', $excluded_woo_categories)); ?>">
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Summary Panel -->
    <div class="aethos-visibility-summary">
        <h3>
            <span class="dashicons dashicons-visibility" style="font-size: 18px;"></span>
            Visibility Summary
        </h3>
        <div id="visibility-summary"></div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    'use strict';
    
    /**
     * Initialize all visibility functionality
     */
    function initializeVisibility() {
        setupTabSwitching();
        setupGlobalToggle();
        setupIncludeAllToggles();
        setupSelectDropdowns();
        setupTagRemoval();
        updateSummary();
        
        console.log('Visibility functionality initialized');
    }
    
    /**
     * Setup tab switching functionality
     */
    function setupTabSwitching() {
        $('.aethos-tab-button').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var tabId = $button.data('tab');
            var $card = $button.closest('.aethos-visibility-card');
            
            // Update button states
            $card.find('.aethos-tab-button').removeClass('active');
            $button.addClass('active');
            
            // Update tab content visibility
            $card.find('.aethos-visibility-tab').removeClass('active');
            $card.find('#' + tabId).addClass('active');
            
            console.log('Tab switched to:', tabId);
        });
    }
    
    /**
     * Setup global visibility toggle
     */
    function setupGlobalToggle() {
        $('#aethos_global_visibility').on('change', function() {
            updateSummary();
        });
    }
    
    /**
     * Setup include all toggles
     */
    function setupIncludeAllToggles() {
        $('input[id^="include_all_"]').on('change', function() {
            var $checkbox = $(this);
            var isChecked = $checkbox.is(':checked');
            var $wrapper = $checkbox.closest('.aethos-visibility-tab').find('.aethos-select-wrapper');
            
            $wrapper.attr('data-disabled', isChecked ? 'true' : 'false');
            $wrapper.find('.aethos-select-dropdown').prop('disabled', isChecked);
            
            updateSummary();
        });
    }
    
    /**
     * Setup select dropdowns
     */
    function setupSelectDropdowns() {
        // Update dropdown options on page load
        updateAllDropdownOptions();
        
        $('.aethos-select-dropdown').on('change', function() {
            var $select = $(this);
            var $option = $select.find('option:selected');
            var value = $option.val();
            
            if (!value) return;
            
            var text = $option.text();
            var target = $select.data('target');
            var $container = $select.closest('.aethos-select-wrapper').find('.aethos-selected-items[data-for="' + target + '"]');
            var $hiddenInput = $select.closest('.aethos-select-wrapper').find('input[name="aethos_' + target + '"]');
            
            // Check if already selected
            if ($container.find('.aethos-selected-tag[data-id="' + value + '"]').length > 0) {
                $select.val('');
                return;
            }
            
            // Remove empty state if exists
            $container.find('.aethos-empty-state').remove();
            
            // Add new tag
            var $tag = $('<span class="aethos-selected-tag" data-id="' + value + '">' + 
                        text + 
                        '<span class="remove" title="Remove">×</span></span>');
            $container.append($tag);
            
            // Update hidden input
            updateHiddenInput($container, $hiddenInput);
            
            // Reset select
            $select.val('');
            
            // Update dropdown options to hide selected item
            updateDropdownOptions($select);
            
            // Update summary
            updateSummary();
        });
    }
    
    /**
     * Setup tag removal
     */
    function setupTagRemoval() {
        $(document).on('click', '.aethos-selected-tag .remove', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            var $tag = $(this).closest('.aethos-selected-tag');
            var $container = $tag.closest('.aethos-selected-items');
            var $hiddenInput = $container.siblings('.aethos-hidden-input');
            
            $tag.remove();
            
            // Show empty state if no tags left
            if ($container.find('.aethos-selected-tag').length === 0) {
                var dataFor = $container.data('for');
                var emptyMessage = getEmptyStateMessage(dataFor);
                $container.html('<div class="aethos-empty-state">' + emptyMessage + '</div>');
            }
            
            // Update hidden input
            updateHiddenInput($container, $hiddenInput);
            
            // Update dropdown options to show removed item
            var $select = $container.closest('.aethos-select-wrapper').find('.aethos-select-dropdown');
            updateDropdownOptions($select);
            
            // Update summary
            updateSummary();
        });
    }
    
    /**
     * Update hidden input with selected IDs
     */
    function updateHiddenInput($container, $hiddenInput) {
        var ids = [];
        $container.find('.aethos-selected-tag').each(function() {
            ids.push($(this).data('id'));
        });
        $hiddenInput.val(ids.join(','));
    }
    
    /**
     * Get empty state message based on content type
     */
    function getEmptyStateMessage(dataFor) {
        var messages = {
            'included_pages': 'No pages or posts selected',
            'excluded_pages': 'No pages or posts excluded',
            'included_categories': 'No categories or tags selected',
            'excluded_categories': 'No categories or tags excluded',
            'included_woo_products': 'No products selected',
            'excluded_woo_products': 'No products excluded',
            'included_woo_categories': 'No product categories selected',
            'excluded_woo_categories': 'No product categories excluded'
        };
        return messages[dataFor] || 'No items selected';
    }
    
    /**
     * Update visibility summary
     */
    function updateSummary() {
        var $summary = $('#visibility-summary');
        var html = '';
        
        // Global status
        var isGlobalActive = $('#aethos_global_visibility').is(':checked');
        html += '<div class="aethos-summary-item">';
        html += '<div class="aethos-summary-icon ' + (isGlobalActive ? 'success' : 'neutral') + '">';
        html += '<span class="dashicons dashicons-' + (isGlobalActive ? 'yes' : 'minus') + '"></span>';
        html += '</div>';
        html += '<div class="aethos-summary-text">';
        html += 'Chatbot is <strong>' + (isGlobalActive ? 'globally active' : 'globally inactive') + '</strong>.';
        html += '</div>';
        html += '</div>';
        
        if (!isGlobalActive) {
            $summary.html(html);
            return;
        }
        
        // Page & Post Rules
        var includeAllPages = $('#include_all_pages').is(':checked');
        var includedPages = getSelectedItems('included_pages');
        var excludedPages = getSelectedItems('excluded_pages');
        
        html += '<div class="aethos-summary-item">';
        html += '<div class="aethos-summary-icon info">';
        html += '<span class="dashicons dashicons-admin-page"></span>';
        html += '</div>';
        html += '<div class="aethos-summary-text">';
        if (includeAllPages) {
            html += '<strong>Included on all pages</strong> and posts.';
        } else if (includedPages.length > 0) {
            html += '<strong>Included on ' + includedPages.length + ' page(s):</strong>';
            html += '<ul class="aethos-summary-list">';
            includedPages.forEach(function(item) {
                html += '<li>' + item + '</li>';
            });
            html += '</ul>';
        } else {
            html += 'Not included on any specific pages.';
        }
        
        if (excludedPages.length > 0) {
            html += '<br><strong>Excluded from ' + excludedPages.length + ' page(s):</strong>';
            html += '<ul class="aethos-summary-list">';
            excludedPages.forEach(function(item) {
                html += '<li>' + item + '</li>';
            });
            html += '</ul>';
        }
        html += '</div>';
        html += '</div>';
        
        // Category & Tag Rules
        var includeAllCategories = $('#include_all_categories').is(':checked');
        var includedCategories = getSelectedItems('included_categories');
        var excludedCategories = getSelectedItems('excluded_categories');
        
        html += '<div class="aethos-summary-item">';
        html += '<div class="aethos-summary-icon info">';
        html += '<span class="dashicons dashicons-category"></span>';
        html += '</div>';
        html += '<div class="aethos-summary-text">';
        if (includeAllCategories) {
            html += '<strong>Included on all categories</strong> and tags.';
        } else if (includedCategories.length > 0) {
            html += '<strong>Included on ' + includedCategories.length + ' category/tag(s):</strong>';
            html += '<ul class="aethos-summary-list">';
            includedCategories.forEach(function(item) {
                html += '<li>' + item + '</li>';
            });
            html += '</ul>';
        } else {
            html += 'Not included on any specific categories or tags.';
        }
        
        if (excludedCategories.length > 0) {
            html += '<br><strong>Excluded from ' + excludedCategories.length + ' category/tag(s):</strong>';
            html += '<ul class="aethos-summary-list">';
            excludedCategories.forEach(function(item) {
                html += '<li>' + item + '</li>';
            });
            html += '</ul>';
        }
        html += '</div>';
        html += '</div>';
        
        <?php if ($woo_active): ?>
        // WooCommerce Product Rules
        var includeAllWooProducts = $('#include_all_woo_products').is(':checked');
        var includedWooProducts = getSelectedItems('included_woo_products');
        var excludedWooProducts = getSelectedItems('excluded_woo_products');
        
        html += '<div class="aethos-summary-item">';
        html += '<div class="aethos-summary-icon info">';
        html += '<span class="dashicons dashicons-products"></span>';
        html += '</div>';
        html += '<div class="aethos-summary-text">';
        if (includeAllWooProducts) {
            html += '<strong>Included on all products</strong>.';
        } else if (includedWooProducts.length > 0) {
            html += '<strong>Included on ' + includedWooProducts.length + ' product(s):</strong>';
            html += '<ul class="aethos-summary-list">';
            includedWooProducts.forEach(function(item) {
                html += '<li>' + item + '</li>';
            });
            html += '</ul>';
        } else {
            html += 'Not included on any specific products.';
        }
        
        if (excludedWooProducts.length > 0) {
            html += '<br><strong>Excluded from ' + excludedWooProducts.length + ' product(s):</strong>';
            html += '<ul class="aethos-summary-list">';
            excludedWooProducts.forEach(function(item) {
                html += '<li>' + item + '</li>';
            });
            html += '</ul>';
        }
        html += '</div>';
        html += '</div>';
        
        // WooCommerce Category Rules
        var includeAllWooCategories = $('#include_all_woo_categories').is(':checked');
        var includedWooCategories = getSelectedItems('included_woo_categories');
        var excludedWooCategories = getSelectedItems('excluded_woo_categories');
        
        html += '<div class="aethos-summary-item">';
        html += '<div class="aethos-summary-icon info">';
        html += '<span class="dashicons dashicons-tag"></span>';
        html += '</div>';
        html += '<div class="aethos-summary-text">';
        if (includeAllWooCategories) {
            html += '<strong>Included on all product categories</strong>.';
        } else if (includedWooCategories.length > 0) {
            html += '<strong>Included on ' + includedWooCategories.length + ' product category/categories:</strong>';
            html += '<ul class="aethos-summary-list">';
            includedWooCategories.forEach(function(item) {
                html += '<li>' + item + '</li>';
            });
            html += '</ul>';
        } else {
            html += 'Not included on any specific product categories.';
        }
        
        if (excludedWooCategories.length > 0) {
            html += '<br><strong>Excluded from ' + excludedWooCategories.length + ' product category/categories:</strong>';
            html += '<ul class="aethos-summary-list">';
            excludedWooCategories.forEach(function(item) {
                html += '<li>' + item + '</li>';
            });
            html += '</ul>';
        }
        html += '</div>';
        html += '</div>';
        <?php endif; ?>
        
        $summary.html(html);
    }
    
    /**
     * Update dropdown options to hide selected items
     */
    function updateDropdownOptions($select) {
        var target = $select.data('target');
        var $container = $select.closest('.aethos-select-wrapper').find('.aethos-selected-items[data-for="' + target + '"]');
        
        // Get all selected IDs
        var selectedIds = [];
        $container.find('.aethos-selected-tag').each(function() {
            selectedIds.push($(this).data('id').toString());
        });
        
        // Show/hide options based on selection
        $select.find('option').each(function() {
            var $option = $(this);
            var value = $option.val();
            
            if (value && selectedIds.indexOf(value) !== -1) {
                $option.hide();
            } else {
                $option.show();
            }
        });
    }
    
    /**
     * Update all dropdown options on page load
     */
    function updateAllDropdownOptions() {
        $('.aethos-select-dropdown').each(function() {
            updateDropdownOptions($(this));
        });
    }
    
    /**
     * Get selected items text
     */
    function getSelectedItems(dataFor) {
        var items = [];
        $('.aethos-selected-items[data-for="' + dataFor + '"] .aethos-selected-tag').each(function() {
            var text = $(this).text().replace('×', '').trim();
            items.push(text);
        });
        return items;
    }
    
    // Initialize on page load
    initializeVisibility();
});
</script>
