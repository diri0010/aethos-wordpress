<?php
/**
 * Advanced Configuration Page
 * Contains: Advanced Settings
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Include header
include_once AETHOS_PLUGIN_DIR . 'admin/partials/admin-header.php';
?>

<div class="aethos-page-header" style="margin-bottom: 32px;">
    <h1 style="font-size: 28px; font-weight: 600; color: #111827; margin: 0;">Advanced Configuration</h1>
    <p style="color: #6b7280; margin-top: 8px;">Configure advanced settings, privacy controls, and performance options</p>
</div>

<form method="post" action="options.php">
    <?php settings_fields( 'aethos_options' ); ?>
    
    <?php include AETHOS_PLUGIN_DIR . 'admin/partials/sections/advanced-content.php'; ?>
    
    <?php submit_button( 'Save Changes', 'primary', 'submit', true, array( 'style' => 'margin-top: 24px;' ) ); ?>
</form>

<?php
// Include footer
include_once AETHOS_PLUGIN_DIR . 'admin/partials/admin-footer.php';
?>
