<?php
/**
 * Model Selection Content Section - Modernized with Preline UI
 */

// Get current model
$current_model = get_option('aethos_ai_model', 'gpt-4o-mini');

// Define model data
$models = array(
    'gpt-4.1-mini' => array(
        'name' => 'GPT-4.1 Mini',
        'provider' => 'OpenAI',
        'category' => 'Fast',
        'description' => 'Enhanced version with improved reasoning and faster response times. Ideal for quick, accurate responses.',
        'speed' => 95,
        'quality' => 88,
        'recommended' => false,
        'features' => array('Ultra Fast', 'Enhanced Reasoning', 'Latest Model'),
        'icon' => '⚡'
    ),
    'gpt-4o-mini' => array(
        'name' => 'GPT-4o Mini',
        'provider' => 'OpenAI',
        'category' => 'Balanced',
        'description' => 'Balanced performance ideal for most general queries with excellent reasoning capabilities.',
        'speed' => 90,
        'quality' => 85,
        'recommended' => true,
        'features' => array('Fast Response', 'Balanced', 'General Purpose'),
        'icon' => '⚖️'
    ),
    'gemini-2.5-flash' => array(
        'name' => 'Gemini 2.5 Flash',
        'provider' => 'Google',
        'category' => 'Advanced',
        'description' => 'Google\'s powerful multimodal AI with exceptional reasoning and creative capabilities.',
        'speed' => 85,
        'quality' => 92,
        'recommended' => false,
        'features' => array('Multimodal', 'Advanced Reasoning', 'Creative'),
        'icon' => '✨'
    ),
    'gemini-2.5-flash-lite' => array(
        'name' => 'Gemini 2.5 Flash Lite',
        'provider' => 'Google',
        'category' => 'Fast',
        'description' => 'Lightweight version of Gemini Flash optimized for speed while maintaining quality.',
        'speed' => 95,
        'quality' => 82,
        'recommended' => false,
        'features' => array('Ultra Fast', 'Efficient', 'Lightweight'),
        'icon' => '🚀'
    ),
    'gemini-3-pro' => array(
        'name' => 'Gemini 3 Pro',
        'provider' => 'Google',
        'category' => 'Advanced',
        'description' => 'Google\'s most advanced model with superior reasoning, analysis, and creative capabilities.',
        'speed' => 80,
        'quality' => 96,
        'recommended' => false,
        'features' => array('Most Advanced', 'Superior Quality', 'Complex Tasks'),
        'icon' => '🌟'
    ),
    'claude-sonnet-4' => array(
        'name' => 'Claude Sonnet 4',
        'provider' => 'Anthropic',
        'category' => 'Advanced',
        'description' => 'Anthropic\'s balanced model with excellent reasoning, analysis, and natural conversation abilities.',
        'speed' => 85,
        'quality' => 90,
        'recommended' => false,
        'features' => array('Natural Conversation', 'Analytical', 'Balanced'),
        'icon' => '🎭'
    ),
    'claude-haiku-3.5' => array(
        'name' => 'Claude Haiku 3.5',
        'provider' => 'Anthropic',
        'category' => 'Fast',
        'description' => 'Anthropic\'s fastest model designed for quick responses while maintaining high quality.',
        'speed' => 98,
        'quality' => 85,
        'recommended' => false,
        'features' => array('Fastest', 'Efficient', 'High Quality'),
        'icon' => '⚡'
    )
);
?>

<!-- Preline UI Styles -->
<style>
.aethos-model-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.aethos-model-card {
    position: relative;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    overflow: hidden;
}

.aethos-model-card:hover {
    border-color: #a5b4fc;
    box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.1), 0 8px 10px -6px rgba(79, 70, 229, 0.1);
    transform: translateY(-2px);
}

.aethos-model-card.selected {
    border-color: #4f46e5;
    background: linear-gradient(135deg, #eff6ff 0%, #ffffff 100%);
    box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.2), 0 8px 10px -6px rgba(79, 70, 229, 0.2);
}

.aethos-model-card.selected::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
}

.aethos-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.aethos-badge-success {
    background: #d1fae5;
    color: #065f46;
}

.aethos-badge-info {
    background: #dbeafe;
    color: #1e40af;
}

.aethos-badge-purple {
    background: #ede9fe;
    color: #5b21b6;
}

.aethos-progress-bar {
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
    margin-top: 8px;
}

.aethos-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4f46e5, #7c3aed);
    border-radius: 3px;
    transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.aethos-feature-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    font-size: 12px;
    color: #374151;
    font-weight: 500;
}

.aethos-comparison-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
}

.aethos-comparison-table th {
    background: #f9fafb;
    padding: 16px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.aethos-comparison-table td {
    padding: 16px;
    border-bottom: 1px solid #f3f4f6;
    font-size: 14px;
    color: #6b7280;
}

.aethos-comparison-table tr:last-child td {
    border-bottom: none;
}

.aethos-active-indicator {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #10b981;
    font-size: 13px;
    font-weight: 600;
}

.aethos-active-dot {
    width: 8px;
    height: 8px;
    background: #10b981;
    border-radius: 50%;
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.aethos-category-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.category-fast { background: #fef3c7; color: #92400e; }
.category-balanced { background: #dbeafe; color: #1e40af; }
.category-advanced { background: #ede9fe; color: #5b21b6; }

.provider-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: 600;
    margin-left: 8px;
}

.provider-openai { background: #dcfce7; color: #166534; }
.provider-google { background: #fef3c7; color: #92400e; }
.provider-anthropic { background: #fee2e2; color: #991b1b; }
</style>

<div style="margin-bottom: 32px;">
    <h1 style="margin: 0 0 8px 0; font-size: 32px; font-weight: 700; color: #111827;">AI Model Selection</h1>
    <p style="margin: 0; color: #6b7280; font-size: 14px; line-height: 1.6;">Choose the AI model that powers your chatbot. Each model offers different trade-offs between speed and quality.</p>
</div>

<!-- Filter Tabs -->
<div style="display: flex; gap: 12px; margin-bottom: 24px; border-bottom: 2px solid #e5e7eb; padding-bottom: 0; flex-wrap: wrap;">
    <button type="button" class="aethos-filter-tab active" data-category="all" style="padding: 12px 20px; background: none; border: none; border-bottom: 2px solid #4f46e5; color: #4f46e5; font-weight: 600; font-size: 14px; cursor: pointer; margin-bottom: -2px; transition: all 0.2s;">
        All Models
    </button>
    <button type="button" class="aethos-filter-tab" data-provider="OpenAI" style="padding: 12px 20px; background: none; border: none; border-bottom: 2px solid transparent; color: #6b7280; font-weight: 600; font-size: 14px; cursor: pointer; margin-bottom: -2px; transition: all 0.2s;">
        OpenAI
    </button>
    <button type="button" class="aethos-filter-tab" data-provider="Google" style="padding: 12px 20px; background: none; border: none; border-bottom: 2px solid transparent; color: #6b7280; font-weight: 600; font-size: 14px; cursor: pointer; margin-bottom: -2px; transition: all 0.2s;">
        Google
    </button>
    <button type="button" class="aethos-filter-tab" data-provider="Anthropic" style="padding: 12px 20px; background: none; border: none; border-bottom: 2px solid transparent; color: #6b7280; font-weight: 600; font-size: 14px; cursor: pointer; margin-bottom: -2px; transition: all 0.2s;">
        Anthropic
    </button>
</div>

<!-- Model Cards Grid -->
<div class="aethos-model-grid">
    <?php foreach ($models as $model_id => $model): ?>
    <label class="aethos-model-card <?php echo $current_model === $model_id ? 'selected' : ''; ?>" data-model="<?php echo esc_attr($model_id); ?>" data-category="<?php echo esc_attr($model['category']); ?>" data-provider="<?php echo esc_attr($model['provider']); ?>">
        <input type="radio" name="aethos_ai_model" value="<?php echo esc_attr($model_id); ?>" <?php checked($current_model, $model_id); ?> style="position: absolute; opacity: 0; pointer-events: none;">
        
        <!-- Header -->
        <div style="display: flex; align-items: start; justify-content: space-between; margin-bottom: 16px;">
            <div style="display: flex; align-items: center; gap: 12px;">
                <span style="font-size: 32px;"><?php echo $model['icon']; ?></span>
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 18px; font-weight: 700; color: #111827;"><?php echo esc_html($model['name']); ?></h3>
                    <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                        <span class="provider-badge provider-<?php echo strtolower($model['provider']); ?>"><?php echo esc_html($model['provider']); ?></span>
                        <span class="aethos-category-badge category-<?php echo strtolower($model['category']); ?>"><?php echo esc_html($model['category']); ?></span>
                    </div>
                </div>
            </div>
            
            <?php if ($model['recommended']): ?>
            <span class="aethos-badge aethos-badge-success">Recommended</span>
            <?php endif; ?>
        </div>
        
        <!-- Active Indicator -->
        <?php if ($current_model === $model_id): ?>
        <div class="aethos-active-indicator" style="margin-bottom: 16px;">
            <span class="aethos-active-dot"></span>
            Currently Active
        </div>
        <?php endif; ?>
        
        <!-- Description -->
        <p style="margin: 0 0 20px 0; color: #6b7280; font-size: 14px; line-height: 1.6;"><?php echo esc_html($model['description']); ?></p>
        
        <!-- Performance Metrics -->
        <div style="margin-bottom: 16px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <span style="font-size: 12px; font-weight: 600; color: #374151;">Speed</span>
                <span style="font-size: 12px; font-weight: 600; color: #4f46e5;"><?php echo $model['speed']; ?>%</span>
            </div>
            <div class="aethos-progress-bar">
                <div class="aethos-progress-fill" style="width: <?php echo $model['speed']; ?>%;"></div>
            </div>
        </div>
        
        <div style="margin-bottom: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                <span style="font-size: 12px; font-weight: 600; color: #374151;">Quality</span>
                <span style="font-size: 12px; font-weight: 600; color: #4f46e5;"><?php echo $model['quality']; ?>%</span>
            </div>
            <div class="aethos-progress-bar">
                <div class="aethos-progress-fill" style="width: <?php echo $model['quality']; ?>%;"></div>
            </div>
        </div>
        
        <!-- Features -->
        <div style="display: flex; flex-wrap: wrap; gap: 8px;">
            <?php foreach ($model['features'] as $feature): ?>
            <span class="aethos-feature-tag"><?php echo esc_html($feature); ?></span>
            <?php endforeach; ?>
        </div>
    </label>
    <?php endforeach; ?>
</div>

<!-- Comparison Table Toggle -->
<div style="margin: 32px 0; text-align: center;">
    <button type="button" id="toggle-comparison" style="display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; color: #374151; font-weight: 600; font-size: 14px; cursor: pointer; transition: all 0.2s;">
        <span class="dashicons dashicons-editor-table" style="font-size: 18px;"></span>
        Compare All Models
    </button>
</div>

<!-- Comparison Table (Hidden by default) -->
<div id="comparison-table" style="display: none; margin-bottom: 32px; animation: fadeIn 0.3s ease-in-out;">
    <h2 style="margin: 0 0 16px 0; font-size: 20px; font-weight: 700; color: #111827;">Model Comparison</h2>
    <div style="overflow-x: auto;">
        <table class="aethos-comparison-table">
            <thead>
                <tr>
                    <th>Model</th>
                    <th>Provider</th>
                    <th>Category</th>
                    <th>Speed</th>
                    <th>Quality</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($models as $model_id => $model): ?>
                <tr>
                    <td style="font-weight: 600; color: #111827;">
                        <?php echo esc_html($model['name']); ?>
                        <?php if ($current_model === $model_id): ?>
                        <span style="display: inline-block; width: 6px; height: 6px; background: #10b981; border-radius: 50%; margin-left: 8px;"></span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo esc_html($model['provider']); ?></td>
                    <td><span class="aethos-category-badge category-<?php echo strtolower($model['category']); ?>"><?php echo esc_html($model['category']); ?></span></td>
                    <td><?php echo $model['speed']; ?>%</td>
                    <td><?php echo $model['quality']; ?>%</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Help Section -->
<div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-top: 32px;">
    <div style="display: flex; align-items: start; gap: 16px;">
        <span style="font-size: 24px;">💡</span>
        <div style="flex: 1;">
            <h3 style="margin: 0 0 8px 0; font-size: 16px; font-weight: 600; color: #111827;">Need Help Choosing?</h3>
            <p style="margin: 0 0 16px 0; color: #6b7280; font-size: 14px; line-height: 1.6;">
                For most use cases, we recommend <strong>GPT-4o Mini</strong> as it offers the best balance of speed and quality. 
                If you need faster responses, try <strong>Claude Haiku 3.5</strong> or <strong>Gemini 2.5 Flash Lite</strong>. 
                For advanced reasoning and complex tasks, consider <strong>Gemini 3 Pro</strong> or <strong>Claude Sonnet 4</strong>.
            </p>
            <a href="https://aethoslogic.com/models" target="_blank" style="display: inline-flex; align-items: center; gap: 6px; color: #4f46e5; text-decoration: none; font-weight: 600; font-size: 14px;">
                Learn More About Models
                <span class="dashicons dashicons-external" style="font-size: 16px;"></span>
            </a>
        </div>
    </div>
</div>

<style>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Model card selection
    $('.aethos-model-card').on('click', function() {
        $('.aethos-model-card').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
    });
    
    // Filter tabs
    $('.aethos-filter-tab').on('click', function() {
        const category = $(this).data('category');
        const provider = $(this).data('provider');
        
        // Update active tab
        $('.aethos-filter-tab').removeClass('active').css({
            'border-bottom-color': 'transparent',
            'color': '#6b7280'
        });
        $(this).addClass('active').css({
            'border-bottom-color': '#4f46e5',
            'color': '#4f46e5'
        });
        
        // Filter cards
        if (category === 'all') {
            $('.aethos-model-card').fadeIn(300);
        } else if (provider) {
            $('.aethos-model-card').each(function() {
                if ($(this).data('provider') === provider) {
                    $(this).fadeIn(300);
                } else {
                    $(this).fadeOut(300);
                }
            });
        } else {
            $('.aethos-model-card').each(function() {
                if ($(this).data('category') === category) {
                    $(this).fadeIn(300);
                } else {
                    $(this).fadeOut(300);
                }
            });
        }
    });
    
    // Toggle comparison table
    $('#toggle-comparison').on('click', function() {
        const $table = $('#comparison-table');
        const $button = $(this);
        
        if ($table.is(':visible')) {
            $table.slideUp(300);
            $button.html('<span class="dashicons dashicons-editor-table" style="font-size: 18px;"></span> Compare All Models');
        } else {
            $table.slideDown(300);
            $button.html('<span class="dashicons dashicons-arrow-up-alt2" style="font-size: 18px;"></span> Hide Comparison');
            
            // Scroll to table
            $('html, body').animate({
                scrollTop: $table.offset().top - 100
            }, 500);
        }
    });
    
    // Animate progress bars on load
    setTimeout(function() {
        $('.aethos-progress-fill').each(function() {
            const width = $(this).css('width');
            $(this).css('width', '0').animate({ width: width }, 1000);
        });
    }, 100);
});
</script>
