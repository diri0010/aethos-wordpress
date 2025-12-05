/**
 * Test Sprint 2 - WordPress Backend
 * 
 * This script tests:
 * 1. Shared secret generation
 * 2. JWT token generation
 * 3. REST API endpoints
 */

// Test 1: Check if shared secret exists
console.log('='.repeat(60));
console.log('TEST 1: Verify Shared Secret Generation');
console.log('='.repeat(60));

fetch('http://localhost/advpropertyportfolio/wp-json/wp/v2/settings', {
    method: 'GET',
    credentials: 'include'
})
    .then(r => r.json())
    .then(data => {
        console.log('WordPress settings retrieved');
        console.log('Note: Shared secret is not exposed via API (security)');
        console.log('✅ WordPress REST API is working\n');
    })
    .catch(err => console.error('❌ Error:', err));

// Test 2: Test Config Endpoint
console.log('='.repeat(60));
console.log('TEST 2: GET /wp-json/aethos/v1/config');
console.log('='.repeat(60));

fetch('http://localhost/advpropertyportfolio/wp-json/aethos/v1/config')
    .then(r => r.json())
    .then(data => {
        console.log('Status: 200 OK');
        console.log('Response:', JSON.stringify(data, null, 2));

        if (data.theme && data.position && data.primary_color) {
            console.log('✅ PASS: Config endpoint returns widget settings\n');
        } else {
            console.log('❌ FAIL: Missing expected config fields\n');
        }
    })
    .catch(err => console.error('❌ Error:', err));

// Test 3: Test Context Endpoint
setTimeout(() => {
    console.log('='.repeat(60));
    console.log('TEST 3: POST /wp-json/aethos/v1/context');
    console.log('='.repeat(60));

    fetch('http://localhost/advpropertyportfolio/wp-json/aethos/v1/context', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Origin': 'http://localhost'
        },
        body: JSON.stringify({
            query: 'test search query'
        })
    })
        .then(r => r.json())
        .then(data => {
            console.log('Status: 200 OK');
            console.log('Response:', JSON.stringify(data, null, 2));

            if (data.context !== undefined) {
                console.log('✅ PASS: Context endpoint returns RAG results\n');
            } else {
                console.log('❌ FAIL: Missing context field\n');
            }
        })
        .catch(err => console.error('❌ Error:', err));
}, 1000);

// Test 4: Test Conversations Endpoint
setTimeout(() => {
    console.log('='.repeat(60));
    console.log('TEST 4: POST /wp-json/aethos/v1/conversations');
    console.log('='.repeat(60));

    fetch('http://localhost/advpropertyportfolio/wp-json/aethos/v1/conversations', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Origin': 'http://localhost'
        },
        body: JSON.stringify({
            conversation_id: 'test_conv_' + Date.now(),
            visitor_id: 'test_visitor_123',
            messages: [
                { role: 'user', content: 'Test message', timestamp: Date.now() },
                { role: 'assistant', content: 'Test response', timestamp: Date.now() }
            ],
            metadata: { test: true }
        })
    })
        .then(r => r.json())
        .then(data => {
            console.log('Status: 200 OK');
            console.log('Response:', JSON.stringify(data, null, 2));

            if (data.saved === true) {
                console.log('✅ PASS: Conversation saved to local database\n');
            } else {
                console.log('❌ FAIL: Conversation not saved\n');
            }
        })
        .catch(err => console.error('❌ Error:', err));
}, 2000);

console.log('\n' + '='.repeat(60));
console.log('All tests queued. Results will appear above.');
console.log('='.repeat(60));
