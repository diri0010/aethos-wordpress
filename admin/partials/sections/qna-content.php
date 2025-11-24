<?php
/**
 * Content Management - Q&A Section (Modernized)
 * 
 * Features:
 * - Modern card-based UI
 * - Clean CSS organization
 * - Better UX for Q&A management
 * - Prepared for chatbot integration
 * - Improved data management
 */

// Get saved options
$kb_pages = get_option('aethos_kb_pages', true);
$kb_posts = get_option('aethos_kb_posts', true);
$selected_cpts = get_option('aethos_kb_custom_post_types', array());
if (!is_array($selected_cpts)) {
    $selected_cpts = array();
}

// Get custom post types
$args = array(
    'public' => true,
    '_builtin' => false
);
$custom_post_types = get_post_types($args, 'objects');
?>

<style>
/* Container Layout */
.aethos-content-container {
    display: grid;
    grid-template-columns: 1fr;
    gap: 24px;
    margin-bottom: 32px;
}

/* Card Styling */
.aethos-content-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
}

.aethos-content-card h3 {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.aethos-content-card p.description {
    margin: 0 0 20px 0;
    color: #6b7280;
    font-size: 14px;
    line-height: 1.6;
}

/* Knowledge Base Sources */
.aethos-kb-sources {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.aethos-kb-source-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    background: #ffffff;
    transition: all 0.2s;
}

.aethos-kb-source-item:hover {
    border-color: #cbd5e1;
    background: #f9fafb;
}

.aethos-kb-source-item input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    margin: 0;
}

.aethos-kb-source-item .source-icon {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f9ff;
    border-radius: 6px;
    color: #0284c7;
    font-size: 18px;
}

.aethos-kb-source-item .source-info {
    flex: 1;
}

.aethos-kb-source-item .source-title {
    font-weight: 500;
    font-size: 15px;
    color: #111827;
    margin: 0 0 2px 0;
}

.aethos-kb-source-item .source-count {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
}

.aethos-kb-source-item.checked {
    border-color: #4f46e5;
    background: #f5f3ff;
}

/* Custom Post Types Nested */
.aethos-kb-cpt-container {
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
}

.aethos-kb-cpt-title {
    font-weight: 500;
    font-size: 15px;
    color: #111827;
    margin: 0 0 12px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.aethos-kb-cpt-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-left: 8px;
}

.aethos-kb-cpt-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px;
    cursor: pointer;
}

.aethos-kb-cpt-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.aethos-kb-cpt-item label {
    font-size: 14px;
    color: #374151;
    cursor: pointer;
    margin: 0;
}

/* Sync Button */
.aethos-sync-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #4f46e5;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.aethos-sync-button:hover {
    background: #4338ca;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
}

.aethos-sync-button:active {
    transform: translateY(0);
}

.aethos-sync-button:disabled {
    background: #9ca3af;
    cursor: not-allowed;
    transform: none;
}

.aethos-sync-button .dashicons {
    font-size: 18px;
    width: 18px;
    height: 18px;
}

/* Stats Grid */
.aethos-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.aethos-stat-card {
    padding: 16px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
}

.aethos-stat-value {
    font-size: 28px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 4px 0;
}

.aethos-stat-label {
    font-size: 13px;
    color: #6b7280;
    margin: 0;
}

/* Q&A Management Section */
.aethos-qna-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.aethos-qna-actions {
    display: flex;
    gap: 12px;
}

.aethos-add-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: #10b981;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.aethos-add-button:hover {
    background: #059669;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(16, 185, 129, 0.2);
}

/* Search and Filters */
.aethos-filters {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 12px;
    margin-bottom: 20px;
}

.aethos-filter-input {
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.aethos-filter-input:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

/* Q&A List (Card View) */
.aethos-qna-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.aethos-qna-item {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 16px;
    padding: 16px;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    transition: all 0.2s;
}

.aethos-qna-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.aethos-qna-checkbox {
    display: flex;
    align-items: flex-start;
    padding-top: 4px;
}

.aethos-qna-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.aethos-qna-content {
    flex: 1;
}

.aethos-qna-question {
    font-size: 15px;
    font-weight: 500;
    color: #111827;
    margin: 0 0 8px 0;
}

.aethos-qna-answer {
    font-size: 14px;
    color: #6b7280;
    margin: 0 0 12px 0;
    line-height: 1.6;
}

.aethos-qna-meta {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.aethos-qna-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
}

.aethos-qna-badge.category {
    background: #dbeafe;
    color: #1e40af;
}

.aethos-qna-badge.priority-high {
    background: #fee2e2;
    color: #991b1b;
}

.aethos-qna-badge.priority-normal {
    background: #fef3c7;
    color: #92400e;
}

.aethos-qna-badge.priority-low {
    background: #f3f4f6;
    color: #6b7280;
}

.aethos-qna-badge.status-published {
    background: #d1fae5;
    color: #065f46;
}

.aethos-qna-badge.status-draft {
    background: #f3f4f6;
    color: #6b7280;
}

.aethos-qna-actions-cell {
    display: flex;
    gap: 8px;
    align-items: flex-start;
}

.aethos-action-button {
    padding: 6px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: white;
    color: #6b7280;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.aethos-action-button:hover {
    border-color: #cbd5e1;
    background: #f9fafb;
    color: #374151;
}

.aethos-action-button.edit:hover {
    border-color: #4f46e5;
    color: #4f46e5;
}

.aethos-action-button.delete:hover {
    border-color: #ef4444;
    color: #ef4444;
}

/* Empty State */
.aethos-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.aethos-empty-state .dashicons {
    font-size: 64px;
    opacity: 0.3;
    margin-bottom: 16px;
}

.aethos-empty-state p {
    font-size: 15px;
    margin: 0;
}

/* Bulk Actions */
.aethos-bulk-actions {
    display: flex;
    gap: 12px;
    margin-top: 20px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.aethos-bulk-select {
    padding: 8px 12px;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    font-size: 14px;
}

.aethos-bulk-button {
    padding: 8px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 6px;
    background: white;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.aethos-bulk-button:hover {
    border-color: #4f46e5;
    color: #4f46e5;
}

/* Modal */
.aethos-modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999999;
    align-items: center;
    justify-content: center;
}

.aethos-modal-overlay.active {
    display: flex;
}

.aethos-modal {
    background: white;
    border-radius: 12px;
    padding: 32px;
    max-width: 600px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.aethos-modal h3 {
    margin: 0 0 24px 0;
    font-size: 20px;
    font-weight: 600;
    color: #111827;
}

.aethos-form-group {
    margin-bottom: 20px;
}

.aethos-form-label {
    display: block;
    font-weight: 500;
    font-size: 14px;
    color: #374151;
    margin-bottom: 8px;
}

.aethos-form-input,
.aethos-form-textarea,
.aethos-form-select {
    width: 100%;
    padding: 10px 14px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.2s;
}

.aethos-form-input:focus,
.aethos-form-textarea:focus,
.aethos-form-select:focus {
    outline: none;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.aethos-form-textarea {
    resize: vertical;
    min-height: 120px;
    font-family: inherit;
}

.aethos-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.aethos-form-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
}

.aethos-form-checkbox input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.aethos-form-checkbox label {
    font-size: 14px;
    color: #374151;
    cursor: pointer;
    margin: 0;
}

.aethos-modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.aethos-button-secondary {
    padding: 10px 20px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    background: white;
    color: #374151;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.aethos-button-secondary:hover {
    border-color: #cbd5e1;
    background: #f9fafb;
}

.aethos-button-primary {
    padding: 10px 20px;
    border: none;
    border-radius: 8px;
    background: #4f46e5;
    color: white;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.aethos-button-primary:hover {
    background: #4338ca;
    transform: translateY(-1px);
    box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
}

/* Animations */
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.spinning {
    animation: spin 1s linear infinite;
}

/* Responsive */
@media (max-width: 768px) {
    .aethos-filters {
        grid-template-columns: 1fr;
    }
    
    .aethos-qna-item {
        grid-template-columns: 1fr;
    }
    
    .aethos-qna-actions-cell {
        justify-content: flex-start;
    }
    
    .aethos-form-row {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="aethos-content-container">
    <!-- Knowledge Base Sources (New Granular Control) -->
    <!-- DEBUG: About to include KB component -->
    <?php 
    $kb_component_path = plugin_dir_path(__FILE__) . 'kb-sources-component.php';
    if (file_exists($kb_component_path)) {
        include $kb_component_path;
    } else {
        echo '<div style="padding: 20px; background: #fee; border: 2px solid #f00; margin: 20px 0;">ERROR: KB component file not found at: ' . esc_html($kb_component_path) . '</div>';
    }
    ?>
    <!-- DEBUG: KB component include complete -->
    
    <!-- Statistics -->
    <div class="aethos-content-card">
        <h3>Knowledge Base Statistics</h3>
        <p class="description">Overview of your chatbot's knowledge base and Q&A content.</p>
        
        <div class="aethos-stats-grid">
            <div class="aethos-stat-card">
                <div class="aethos-stat-value" id="aethos-stat-qna">0</div>
                <div class="aethos-stat-label">Q&A Pairs</div>
            </div>
            <div class="aethos-stat-card">
                <div class="aethos-stat-value" id="aethos-stat-published">0</div>
                <div class="aethos-stat-label">Published</div>
            </div>
            <div class="aethos-stat-card">
                <div class="aethos-stat-value" id="aethos-stat-draft">0</div>
                <div class="aethos-stat-label">Drafts</div>
            </div>
            <div class="aethos-stat-card">
                <div class="aethos-stat-value" id="aethos-stat-sources">0</div>
                <div class="aethos-stat-label">Content Sources</div>
            </div>
        </div>
    </div>
    
    <!-- Q&A Management -->
    <div class="aethos-content-card">
        <div class="aethos-qna-header">
            <div>
                <h3>Q&A Management</h3>
                <p class="description" style="margin-bottom: 0;">Manage custom questions and answers for your chatbot.</p>
            </div>
            <div class="aethos-qna-actions">
                <button type="button" id="aethos-add-qna-btn" class="aethos-add-button">
                    <span class="dashicons dashicons-plus"></span>
                    Add New Q&A
                </button>
            </div>
        </div>
        
        <!-- Search and Filters -->
        <div class="aethos-filters">
            <input type="text" 
                   id="aethos-qna-search" 
                   class="aethos-filter-input" 
                   placeholder="Search questions or answers...">
            <select id="aethos-qna-category-filter" class="aethos-filter-input">
                <option value="">All Categories</option>
                <option value="general">General</option>
                <option value="product">Product</option>
                <option value="support">Support</option>
                <option value="billing">Billing</option>
            </select>
            <select id="aethos-qna-status-filter" class="aethos-filter-input">
                <option value="">All Status</option>
                <option value="published">Published</option>
                <option value="draft">Draft</option>
            </select>
        </div>
        
        <!-- Q&A List -->
        <div class="aethos-qna-list" id="aethos-qna-list">
            <div class="aethos-empty-state">
                <span class="dashicons dashicons-format-chat"></span>
                <p>No Q&A entries yet. Click "Add New Q&A" to create your first entry.</p>
            </div>
        </div>
        
        <!-- Bulk Actions -->
        <div class="aethos-bulk-actions" style="display: none;" id="aethos-bulk-actions">
            <select id="aethos-qna-bulk-action" class="aethos-bulk-select">
                <option value="">Bulk Actions</option>
                <option value="publish">Publish Selected</option>
                <option value="draft">Set as Draft</option>
                <option value="delete">Delete Selected</option>
            </select>
            <button type="button" id="aethos-qna-bulk-apply" class="aethos-bulk-button">Apply</button>
            <span id="aethos-selected-count" style="margin-left: auto; align-self: center; color: #6b7280; font-size: 14px;"></span>
        </div>
    </div>
</div>

<!-- Add/Edit Q&A Modal -->
<div id="aethos-qna-modal" class="aethos-modal-overlay">
    <div class="aethos-modal">
        <h3 id="aethos-qna-modal-title">Add New Q&A</h3>
        
        <div class="aethos-form-group">
            <label class="aethos-form-label">Question *</label>
            <input type="text" 
                   id="aethos-qna-question" 
                   class="aethos-form-input" 
                   placeholder="Enter the question...">
        </div>
        
        <div class="aethos-form-group">
            <label class="aethos-form-label">Answer *</label>
            <textarea id="aethos-qna-answer" 
                      class="aethos-form-textarea" 
                      placeholder="Enter the answer..."></textarea>
        </div>
        
        <div class="aethos-form-row">
            <div class="aethos-form-group">
                <label class="aethos-form-label">Category</label>
                <select id="aethos-qna-category" class="aethos-form-select">
                    <option value="general">General</option>
                    <option value="product">Product</option>
                    <option value="support">Support</option>
                    <option value="billing">Billing</option>
                </select>
            </div>
            <div class="aethos-form-group">
                <label class="aethos-form-label">Priority</label>
                <select id="aethos-qna-priority" class="aethos-form-select">
                    <option value="normal">Normal</option>
                    <option value="high">High</option>
                    <option value="low">Low</option>
                </select>
            </div>
        </div>
        
        <div class="aethos-form-group">
            <div class="aethos-form-checkbox">
                <input type="checkbox" id="aethos-qna-status" checked>
                <label for="aethos-qna-status">Publish immediately</label>
            </div>
        </div>
        
        <div class="aethos-modal-actions">
            <button type="button" id="aethos-qna-modal-cancel" class="aethos-button-secondary">Cancel</button>
            <button type="button" id="aethos-qna-modal-save" class="aethos-button-primary">Save Q&A</button>
        </div>
    </div>
</div>

<script>
const aethosQnANonce = '<?php echo wp_create_nonce('aethos_qna_nonce'); ?>';

jQuery(document).ready(function($) {
    'use strict';
    
    let qnaData = [];
    let editingQnaId = null;
    let kbItemsLookup = {}; // Store all loaded KB items for quick lookup
    
    /**
     * Initialize Content Management
     */
    function initContentManagement() {
        loadQnAData();
        setupEventHandlers();
        setupKBControls();
        updateStats();
        updateSourceCheckboxes();
        
        console.log('Content Management initialized');
    }
    
    /**
     * Setup event handlers
     */
    function setupEventHandlers() {
        // Sync content button
        $('#aethos-sync-content-btn').on('click', handleSyncContent);
        
        // Add Q&A button
        $('#aethos-add-qna-btn').on('click', openAddModal);
        
        // Modal actions
        $('#aethos-qna-modal-cancel').on('click', closeModal);
        $('#aethos-qna-modal-save').on('click', saveQnA);
        
        // Close modal on overlay click
        $('#aethos-qna-modal').on('click', function(e) {
            if ($(e.target).is('#aethos-qna-modal')) {
                closeModal();
            }
        });
        
        // Search and filters
        $('#aethos-qna-search').on('input', filterQnA);
        $('#aethos-qna-category-filter').on('change', filterQnA);
        $('#aethos-qna-status-filter').on('change', filterQnA);
        
        // Bulk actions
        $('#aethos-qna-bulk-apply').on('click', applyBulkAction);
        
        // Source checkboxes
        $('.aethos-kb-source-item input[type="checkbox"]').on('change', function() {
            $(this).closest('.aethos-kb-source-item').toggleClass('checked', this.checked);
            updateStats();
        });
    }
    
    /**
     * Load Q&A data from server
     */
    function loadQnAData() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_load_qna',
                nonce: aethosQnANonce
            },
            success: function(response) {
                if (response.success) {
                    qnaData = response.data.entries || [];
                    renderQnAList();
                    updateStats();
                } else {
                    console.error('Failed to load Q&A data:', response);
                    qnaData = [];
                    renderQnAList();
                    updateStats();
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error loading Q&A:', error);
                qnaData = [];
                renderQnAList();
                updateStats();
            }
        });
    }
    
    /**
     * Render Q&A list
     */
    function renderQnAList(data = qnaData) {
        const $list = $('#aethos-qna-list');
        
        if (data.length === 0) {
            $list.html(`
                <div class="aethos-empty-state">
                    <span class="dashicons dashicons-format-chat"></span>
                    <p>No Q&A entries found. Try adjusting your filters or add a new Q&A.</p>
                </div>
            `);
            $('#aethos-bulk-actions').hide();
            return;
        }
        
        let html = '';
        data.forEach(function(item) {
            html += renderQnAItem(item);
        });
        
        $list.html(html);
        $('#aethos-bulk-actions').show();
        
        // Attach event handlers
        $('.aethos-qna-item-checkbox').on('change', updateBulkActions);
        $('.aethos-action-button.edit').on('click', function() {
            const id = $(this).data('id');
            openEditModal(id);
        });
        $('.aethos-action-button.delete').on('click', function() {
            const id = $(this).data('id');
            deleteQnA(id);
        });
    }
    
    /**
     * Render single Q&A item
     */
    function renderQnAItem(item) {
        const priorityClass = 'priority-' + item.priority;
        const statusClass = 'status-' + item.status;
        
        return `
            <div class="aethos-qna-item" data-id="${item.id}">
                <div class="aethos-qna-checkbox">
                    <input type="checkbox" class="aethos-qna-item-checkbox" data-id="${item.id}">
                </div>
                <div class="aethos-qna-content">
                    <div class="aethos-qna-question">${escapeHtml(item.question)}</div>
                    <div class="aethos-qna-answer">${escapeHtml(item.answer)}</div>
                    <div class="aethos-qna-meta">
                        <span class="aethos-qna-badge category">${item.category}</span>
                        <span class="aethos-qna-badge ${priorityClass}">${item.priority}</span>
                        <span class="aethos-qna-badge ${statusClass}">${item.status}</span>
                    </div>
                </div>
                <div class="aethos-qna-actions-cell">
                    <button type="button" class="aethos-action-button edit" data-id="${item.id}">Edit</button>
                    <button type="button" class="aethos-action-button delete" data-id="${item.id}">Delete</button>
                </div>
            </div>
        `;
    }
    
    /**
     * Open add modal
     */
    function openAddModal() {
        editingQnaId = null;
        $('#aethos-qna-modal-title').text('Add New Q&A');
        $('#aethos-qna-question').val('');
        $('#aethos-qna-answer').val('');
        $('#aethos-qna-category').val('general');
        $('#aethos-qna-priority').val('normal');
        $('#aethos-qna-status').prop('checked', true);
        $('#aethos-qna-modal').addClass('active');
    }
    
    /**
     * Open edit modal
     */
    function openEditModal(id) {
        const item = qnaData.find(q => q.id === id);
        if (!item) return;
        
        editingQnaId = id;
        $('#aethos-qna-modal-title').text('Edit Q&A');
        $('#aethos-qna-question').val(item.question);
        $('#aethos-qna-answer').val(item.answer);
        $('#aethos-qna-category').val(item.category);
        $('#aethos-qna-priority').val(item.priority);
        $('#aethos-qna-status').prop('checked', item.status === 'published');
        $('#aethos-qna-modal').addClass('active');
    }
    
    /**
     * Close modal
     */
    function closeModal() {
        $('#aethos-qna-modal').removeClass('active');
        editingQnaId = null;
    }
    
    /**
     * Save Q&A
     */
    function saveQnA() {
        const question = $('#aethos-qna-question').val().trim();
        const answer = $('#aethos-qna-answer').val().trim();
        const category = $('#aethos-qna-category').val();
        const priority = $('#aethos-qna-priority').val();
        const status = $('#aethos-qna-status').is(':checked') ? 'published' : 'draft';
        
        if (!question || !answer) {
            alert('Please fill in both question and answer fields.');
            return;
        }
        
        const $saveBtn = $('#aethos-qna-modal-save');
        const originalText = $saveBtn.text();
        $saveBtn.prop('disabled', true).text('Saving...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_save_qna',
                nonce: aethosQnANonce,
                data: {
                    id: editingQnaId || 0,
                    question: question,
                    answer: answer,
                    category: category,
                    priority: priority,
                    status: status
                }
            },
            success: function(response) {
                $saveBtn.prop('disabled', false).text(originalText);
                
                if (response.success) {
                    closeModal();
                    loadQnAData(); // Reload from server
                } else {
                    alert('Error saving Q&A: ' + (response.data.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                $saveBtn.prop('disabled', false).text(originalText);
                alert('Error saving Q&A: ' + error);
                console.error('AJAX error saving Q&A:', error);
            }
        });
    }
    
    /**
     * Delete Q&A
     */
    function deleteQnA(id) {
        if (!confirm('Are you sure you want to delete this Q&A entry?')) {
            return;
        }
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_delete_qna',
                nonce: aethosQnANonce,
                id: id
            },
            success: function(response) {
                if (response.success) {
                    loadQnAData(); // Reload from server
                } else {
                    alert('Error deleting Q&A: ' + (response.data.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error deleting Q&A: ' + error);
                console.error('AJAX error deleting Q&A:', error);
            }
        });
    }
    
    /**
     * Filter Q&A
     */
    function filterQnA() {
        const search = $('#aethos-qna-search').val().toLowerCase();
        const category = $('#aethos-qna-category-filter').val();
        const status = $('#aethos-qna-status-filter').val();
        
        let filtered = qnaData.filter(function(item) {
            const matchSearch = !search || 
                item.question.toLowerCase().includes(search) || 
                item.answer.toLowerCase().includes(search);
            const matchCategory = !category || item.category === category;
            const matchStatus = !status || item.status === status;
            
            return matchSearch && matchCategory && matchStatus;
        });
        
        renderQnAList(filtered);
    }
    
    /**
     * Update bulk actions
     */
    function updateBulkActions() {
        const selectedCount = $('.aethos-qna-item-checkbox:checked').length;
        if (selectedCount > 0) {
            $('#aethos-selected-count').text(selectedCount + ' selected');
        } else {
            $('#aethos-selected-count').text('');
        }
    }
    
    /**
     * Apply bulk action
     */
    function applyBulkAction() {
        const action = $('#aethos-qna-bulk-action').val();
        if (!action) {
            alert('Please select an action.');
            return;
        }
        
        const selectedIds = [];
        $('.aethos-qna-item-checkbox:checked').each(function() {
            selectedIds.push(parseInt($(this).data('id')));
        });
        
        if (selectedIds.length === 0) {
            alert('Please select at least one Q&A entry.');
            return;
        }
        
        if (action === 'delete') {
            if (!confirm('Are you sure you want to delete ' + selectedIds.length + ' Q&A entries?')) {
                return;
            }
        }
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'aethos_bulk_action_qna',
                nonce: aethosQnANonce,
                action_type: action,
                ids: selectedIds
            },
            success: function(response) {
                if (response.success) {
                    $('#aethos-qna-bulk-action').val('');
                    loadQnAData(); // Reload from server
                } else {
                    alert('Error performing bulk action: ' + (response.data.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                alert('Error performing bulk action: ' + error);
                console.error('AJAX error on bulk action:', error);
            }
        });
    }
    
    /**
     * Setup Knowledge Base Controls
     */
    function setupKBControls() {
        // Load all content for dropdowns
        loadKBContent();
        
        // Handle mode changes
        $('input[name^="aethos_kb_"][name$="_mode"]').on('change', function() {
            const $card = $(this).closest('.aethos-kb-source-card');
            const mode = $(this).val();
            
            if (mode === 'none') {
                $card.find('.aethos-kb-include-section').hide();
                $card.find('.aethos-kb-exclude-section').hide();
                $card.find('.aethos-kb-auto-sync').hide();
            } else if (mode === 'include_all') {
                $card.find('.aethos-kb-include-section').hide();
                $card.find('.aethos-kb-exclude-section').show();
                $card.find('.aethos-kb-auto-sync').show();
            } else if (mode === 'include_specific') {
                $card.find('.aethos-kb-include-section').show();
                $card.find('.aethos-kb-exclude-section').show();
                $card.find('.aethos-kb-auto-sync').show();
            }
        });
        
        // Handle dropdown selections
        $('.aethos-kb-select').on('change', function() {
            const $select = $(this);
            const target = $select.data('target');
            const value = $select.val();
            const text = $select.find('option:selected').text();
            
            if (value) {
                addKBTag(target, value, text);
                $select.val(''); // Reset dropdown
                updateKBDropdownOptions(target);
            }
        });
        
        // Existing tags will be loaded after dropdowns are populated in populateKBDropdown()
    }
    
    /**
     * Load KB content for dropdowns
     */
    function loadKBContent() {
        kbSourcesData.forEach(function(source) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'aethos_search_content',
                    nonce: '<?php echo wp_create_nonce('aethos_search_nonce'); ?>',
                    type: source.post_type === 'page' ? 'pages' : (source.post_type === 'post' ? 'posts' : 'custom'),
                    query: '',
                    post_type: source.post_type
                },
                success: function(response) {
                    if (response.success && response.data) {
                        populateKBDropdown(source.key, response.data);
                    }
                }
            });
        });
    }
    
    /**
     * Populate KB dropdown with options
     */
    function populateKBDropdown(key, items) {
        const $includeSelect = $('select[data-target="' + key + '_included"]');
        const $excludeSelect = $('select[data-target="' + key + '_excluded"]');
        
        // Store items in lookup for later use
        if (!kbItemsLookup[key]) {
            kbItemsLookup[key] = {};
        }
        
        items.forEach(function(item) {
            // Store in lookup
            kbItemsLookup[key][item.id] = item;
            
            // Add to dropdowns
            const option = '<option value="' + item.id + '">' + item.title + ' (' + item.type + ')</option>';
            $includeSelect.append(option);
            $excludeSelect.append(option);
        });
        
        // Load existing tags now that we have the data
        loadExistingTags(key);
        
        // Update to hide already selected
        updateKBDropdownOptions(key + '_included');
        updateKBDropdownOptions(key + '_excluded');
    }
    
    /**
     * Add KB tag
     */
    function addKBTag(target, id, title) {
        const $container = $('.aethos-kb-selected-tags[data-for="' + target + '"]');
        const $hidden = $('input[name="aethos_kb_' + target + '"]');
        
        // Get current values
        let values = [];
        try {
            values = JSON.parse($hidden.val() || '[]');
        } catch(e) {
            values = [];
        }
        
        // Add if not already present
        if (!values.includes(parseInt(id))) {
            values.push(parseInt(id));
            $hidden.val(JSON.stringify(values));
            renderKBTag(target, id, title);
        }
    }
    
    /**
     * Render KB tag
     */
    function renderKBTag(target, id, title) {
        const $container = $('.aethos-kb-selected-tags[data-for="' + target + '"]');
        
        // Remove empty state
        $container.find('.aethos-kb-empty-state').remove();
        
        const tag = $('<div class="aethos-kb-tag" data-id="' + id + '">' +
            '<span>' + title + '</span>' +
            '<span class="aethos-kb-tag-remove">×</span>' +
            '</div>');
        
        tag.find('.aethos-kb-tag-remove').on('click', function() {
            removeKBTag(target, id);
        });
        
        $container.append(tag);
    }
    
    /**
     * Remove KB tag
     */
    function removeKBTag(target, id) {
        const $container = $('.aethos-kb-selected-tags[data-for="' + target + '"]');
        const $hidden = $('input[name="aethos_kb_' + target + '"]');
        
        // Remove from values
        let values = [];
        try {
            values = JSON.parse($hidden.val() || '[]');
        } catch(e) {
            values = [];
        }
        
        values = values.filter(function(v) { return v !== parseInt(id); });
        $hidden.val(JSON.stringify(values));
        
        // Remove tag element
        $container.find('.aethos-kb-tag[data-id="' + id + '"]').remove();
        
        // Add empty state if no tags
        if ($container.find('.aethos-kb-tag').length === 0) {
            $container.html('<div class="aethos-kb-empty-state">No items selected</div>');
        }
        
        // Update dropdown
        updateKBDropdownOptions(target);
    }
    
    /**
     * Update KB dropdown options to hide selected
     */
    function updateKBDropdownOptions(target) {
        const $select = $('select[data-target="' + target + '"]');
        const $hidden = $('input[name="aethos_kb_' + target + '"]');
        
        let values = [];
        try {
            values = JSON.parse($hidden.val() || '[]');
        } catch(e) {
            values = [];
        }
        
        $select.find('option').each(function() {
            const $option = $(this);
            const optionValue = parseInt($option.val());
            
            if (values.includes(optionValue)) {
                $option.hide();
            } else {
                $option.show();
            }
        });
    }
    
    /**
     * Find KB item by post type and ID
     */
    function findKBItem(key, id) {
        if (kbItemsLookup[key] && kbItemsLookup[key][id]) {
            return kbItemsLookup[key][id];
        }
        return null;
    }
    
    /**
     * Load existing tags for a specific KB source
     */
    function loadExistingTags(key) {
        // Find the source data
        const source = kbSourcesData.find(function(s) { return s.key === key; });
        if (!source) return;
        
        // Load included tags
        if (source.included && source.included.length > 0) {
            source.included.forEach(function(id) {
                const item = findKBItem(key, id);
                if (item) {
                    renderKBTag(key + '_included', id, item.title);
                }
            });
        }
        
        // Load excluded tags
        if (source.excluded && source.excluded.length > 0) {
            source.excluded.forEach(function(id) {
                const item = findKBItem(key, id);
                if (item) {
                    renderKBTag(key + '_excluded', id, item.title);
                }
            });
        }
    }
    
    /**
     * Handle sync content
     */
    function handleSyncContent() {
        const $btn = $('#aethos-sync-content-btn');
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<span class="dashicons dashicons-update spinning"></span> Syncing...');
        
        // Simulate sync (placeholder)
        setTimeout(function() {
            $btn.prop('disabled', false).html(originalHtml);
            alert('Content synced successfully!');
            updateStats();
        }, 2000);
        
        // TODO: Implement actual AJAX sync
    }
    
    /**
     * Update statistics
     */
    function updateStats() {
        const published = qnaData.filter(q => q.status === 'published').length;
        const draft = qnaData.filter(q => q.status === 'draft').length;
        const sources = $('.aethos-kb-source-item input:checked').length + 
                       $('.aethos-kb-cpt-item input:checked').length;
        
        $('#aethos-stat-qna').text(qnaData.length);
        $('#aethos-stat-published').text(published);
        $('#aethos-stat-draft').text(draft);
        $('#aethos-stat-sources').text(sources);
    }
    
    /**
     * Update source checkboxes visual state
     */
    function updateSourceCheckboxes() {
        $('.aethos-kb-source-item input[type="checkbox"]').each(function() {
            $(this).closest('.aethos-kb-source-item').toggleClass('checked', this.checked);
        });
    }
    
    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    // Initialize
    initContentManagement();
});
</script>
