<?php
/**
 * Knowledge Base Sources Component
 * Granular control over KB content inclusion
 */

// Get all content types
$kb_content_types = array();

// Pages
$pages_count = wp_count_posts('page')->publish;
$kb_content_types[] = array(
    'key' => 'pages',
    'name' => 'Pages',
    'icon' => 'dashicons-admin-page',
    'count' => $pages_count,
    'mode' => get_option('aethos_kb_pages_mode', 'include_all'),
    'included' => get_option('aethos_kb_pages_included', array()),
    'excluded' => get_option('aethos_kb_pages_excluded', array()),
    'auto_sync' => get_option('aethos_kb_pages_auto_sync', true),
    'post_type' => 'page'
);

// Posts
$posts_count = wp_count_posts('post')->publish;
$kb_content_types[] = array(
    'key' => 'posts',
    'name' => 'Posts',
    'icon' => 'dashicons-admin-post',
    'count' => $posts_count,
    'mode' => get_option('aethos_kb_posts_mode', 'include_all'),
    'included' => get_option('aethos_kb_posts_included', array()),
    'excluded' => get_option('aethos_kb_posts_excluded', array()),
    'auto_sync' => get_option('aethos_kb_posts_auto_sync', true),
    'post_type' => 'post'
);

// Custom Post Types
$args = array(
    'public' => true,
    '_builtin' => false
);
$custom_post_types = get_post_types($args, 'objects');

foreach ($custom_post_types as $cpt) {
    $cpt_count = wp_count_posts($cpt->name)->publish;
    $kb_content_types[] = array(
        'key' => 'cpt_' . $cpt->name,
        'name' => $cpt->label,
        'icon' => 'dashicons-admin-settings',
        'count' => $cpt_count,
        'mode' => get_option("aethos_kb_cpt_{$cpt->name}_mode", 'none'),
        'included' => get_option("aethos_kb_cpt_{$cpt->name}_included", array()),
        'excluded' => get_option("aethos_kb_cpt_{$cpt->name}_excluded", array()),
        'auto_sync' => get_option("aethos_kb_cpt_{$cpt->name}_auto_sync", false),
        'post_type' => $cpt->name
    );
}
?>

<style>
/* KB Sources Styles */
.aethos-kb-source-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 20px;
    transition: all 0.2s;
}

.aethos-kb-source-card:hover {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.aethos-kb-source-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
}

.aethos-kb-icon {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
}

.aethos-kb-header-info h4 {
    margin: 0 0 4px 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.aethos-kb-header-info p {
    margin: 0;
    font-size: 14px;
    color: #6b7280;
}

.aethos-kb-mode-selection {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
}

.aethos-kb-mode-option {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}

.aethos-kb-mode-option input[type="radio"] {
    margin: 0;
}

.aethos-kb-mode-option label {
    margin: 0;
    cursor: pointer;
    font-size: 14px;
    color: #374151;
}

.aethos-kb-include-section,
.aethos-kb-exclude-section {
    margin-bottom: 16px;
}

.aethos-kb-section-label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.aethos-kb-select {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
    background: white;
}

.aethos-kb-select:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.aethos-kb-selected-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
    min-height: 32px;
}

.aethos-kb-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #ede9fe;
    color: #5b21b6;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
}

.aethos-kb-tag-remove {
    cursor: pointer;
    font-weight: bold;
    opacity: 0.7;
    transition: opacity 0.2s;
}

.aethos-kb-tag-remove:hover {
    opacity: 1;
}

.aethos-kb-auto-sync {
    padding: 16px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    margin-top: 16px;
}

.aethos-kb-auto-sync label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 0;
    cursor: pointer;
    font-size: 14px;
    color: #166534;
}

.aethos-kb-auto-sync input[type="checkbox"] {
    margin: 0;
}

.aethos-kb-empty-state {
    padding: 12px;
    text-align: center;
    color: #9ca3af;
    font-size: 13px;
    font-style: italic;
}
</style>

<div class="aethos-content-card">
    <h3>Knowledge Base Content Sources</h3>
    <p class="description">Control exactly what content the AI chatbot can access. Choose to include all content, select specific items, or exclude certain pages.</p>
    
    <?php foreach ($kb_content_types as $type): ?>
        <div class="aethos-kb-source-card" data-kb-type="<?php echo esc_attr($type['key']); ?>">
            <div class="aethos-kb-source-header">
                <div class="aethos-kb-icon">
                    <span class="dashicons <?php echo esc_attr($type['icon']); ?>"></span>
                </div>
                <div class="aethos-kb-header-info">
                    <h4><?php echo esc_html($type['name']); ?></h4>
                    <p><?php echo esc_html($type['count']); ?> published <?php echo strtolower($type['name']); ?></p>
                </div>
            </div>
            
            <div class="aethos-kb-mode-selection">
                <div class="aethos-kb-mode-option">
                    <input type="radio" 
                           name="aethos_kb_<?php echo esc_attr($type['key']); ?>_mode" 
                           value="none" 
                           id="<?php echo esc_attr($type['key']); ?>_mode_none"
                           <?php checked($type['mode'], 'none'); ?>>
                    <label for="<?php echo esc_attr($type['key']); ?>_mode_none">
                        Don't include <?php echo strtolower($type['name']); ?> in knowledge base
                    </label>
                </div>
                
                <div class="aethos-kb-mode-option">
                    <input type="radio" 
                           name="aethos_kb_<?php echo esc_attr($type['key']); ?>_mode" 
                           value="include_all" 
                           id="<?php echo esc_attr($type['key']); ?>_mode_all"
                           <?php checked($type['mode'], 'include_all'); ?>>
                    <label for="<?php echo esc_attr($type['key']); ?>_mode_all">
                        Include all <?php echo strtolower($type['name']); ?>
                    </label>
                </div>
                
                <div class="aethos-kb-mode-option">
                    <input type="radio" 
                           name="aethos_kb_<?php echo esc_attr($type['key']); ?>_mode" 
                           value="include_specific" 
                           id="<?php echo esc_attr($type['key']); ?>_mode_specific"
                           <?php checked($type['mode'], 'include_specific'); ?>>
                    <label for="<?php echo esc_attr($type['key']); ?>_mode_specific">
                        Include specific <?php echo strtolower($type['name']); ?> only
                    </label>
                </div>
            </div>
            
            <!-- Include Specific Section -->
            <div class="aethos-kb-include-section" style="display: <?php echo $type['mode'] === 'include_specific' ? 'block' : 'none'; ?>;">
                <label class="aethos-kb-section-label">Select <?php echo strtolower($type['name']); ?> to include:</label>
                <select class="aethos-kb-select" data-target="<?php echo esc_attr($type['key']); ?>_included">
                    <option value="">Select a <?php echo strtolower(rtrim($type['name'], 's')); ?>...</option>
                </select>
                <input type="hidden" name="aethos_kb_<?php echo esc_attr($type['key']); ?>_included" value="<?php echo esc_attr(json_encode($type['included'])); ?>">
                <div class="aethos-kb-selected-tags" data-for="<?php echo esc_attr($type['key']); ?>_included">
                    <?php if (empty($type['included'])): ?>
                        <div class="aethos-kb-empty-state">No items selected</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Exclude Section -->
            <div class="aethos-kb-exclude-section" style="display: <?php echo $type['mode'] !== 'none' ? 'block' : 'none'; ?>;">
                <label class="aethos-kb-section-label">Exclude specific <?php echo strtolower($type['name']); ?>:</label>
                <select class="aethos-kb-select" data-target="<?php echo esc_attr($type['key']); ?>_excluded">
                    <option value="">Select a <?php echo strtolower(rtrim($type['name'], 's')); ?>...</option>
                </select>
                <input type="hidden" name="aethos_kb_<?php echo esc_attr($type['key']); ?>_excluded" value="<?php echo esc_attr(json_encode($type['excluded'])); ?>">
                <div class="aethos-kb-selected-tags" data-for="<?php echo esc_attr($type['key']); ?>_excluded">
                    <?php if (empty($type['excluded'])): ?>
                        <div class="aethos-kb-empty-state">No items excluded</div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Auto-sync Toggle -->
            <div class="aethos-kb-auto-sync" style="display: <?php echo $type['mode'] !== 'none' ? 'block' : 'none'; ?>;">
                <label>
                    <input type="checkbox" 
                           name="aethos_kb_<?php echo esc_attr($type['key']); ?>_auto_sync" 
                           value="1"
                           <?php checked($type['auto_sync'], true); ?>>
                    Automatically add new <?php echo strtolower($type['name']); ?> to knowledge base
                </label>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
// KB Sources data for JavaScript
const kbSourcesData = <?php echo json_encode($kb_content_types); ?>;
</script>
