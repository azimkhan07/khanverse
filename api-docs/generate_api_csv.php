<?php

/*
 * Re-sync api-docs/SkillNest_API_Endpoints.csv from the existing CSV rows,
 * inserts any recently added routes (e.g. /api/profile), keeps URI-alphabetical
 * order and appends an 8th column "Description / Notes (for mobile dev)".
 *
 * Run from project root:  php api-docs/generate_api_csv.php
 */

$file = __DIR__ . '/SkillNest_API_Endpoints.csv';

$rows = [];
if (($h = fopen($file, 'r')) !== false) {
    $i = 0;
    while ($r = fgetcsv($h)) {
        $i++;
        if ($i <= 6) {
            continue;
        } // skip header block
        if (count($r) < 6) {
            continue;
        }
        $rows[] = $r;
    }
    fclose($h);
}

// Insert routes that exist but are missing from the CSV (kept in URI order).
$profile = [
    ['', 'GET', '/api/profile', 'api.profile.show', 'Public (no auth)', 'api | web | App\Http\Middleware\Authenticate', 'App\Http\Controllers\Api\PublicProfileController@show'],
    ['', 'POST', '/api/profile', 'api.profile.update', 'Public (no auth)', 'api | web | App\Http\Middleware\Authenticate', 'App\Http\Controllers\Api\PublicProfileController@update'],
];
$has = false;
foreach ($rows as $r) {
    if (($r[2] ?? '') === '/api/profile') {
        $has = true;
        break;
    }
}
if (!$has) {
    $rows = array_merge($rows, $profile);
}

// Drop stray duplicate-column rows produced by older generation scripts.
$rows = array_values(array_filter($rows, fn ($r) => ($r[1] ?? '') !== 'Method'));

usort($rows, fn ($a, $b) => strcmp($a[2] ?? '', $b[2] ?? ''));

$desc = [
    'POST /api/become-buyer'   => 'Request: first_name, last_name, mobile, country, state, city, pincode, latitude, longitude (geo tracking), is_consultancy. Activates BUYER role.',
    'POST /api/become-seller'  => 'Request: aadhaar_number (12-digit + Verhoeff check), pan_number (PAN format), category_id, category_details[] (dynamic fields from category form_fields), latitude, longitude (geo tracking), experience_level, is_consultancy, verification_document. Stores seller + category details + location.',
    'GET /api/frontend/categories' => 'All categories. Each item: id, name, slug, category_type (technical | field), form_fields[] (dynamic input schema; key/label/type/required/options).',
    'GET /api/frontend/search'      => 'Query: q, category_id, min_price, max_price, sort (latest | price_asc | price_desc | rating | nearby), near=1, lat, lng, radius_km (default 30, max 500). Nearby only returns sellers with saved location; each card adds distance_km.',
    'GET /api/frontend/services/{id}' => 'Full service detail incl category, seller, similar_services, images and working-hours fields (visit_start_time, visit_end_time, working_days[]) for field-type services.',
    'POST /api/login'           => 'IDENTIFIER = either email OR username, sent in the "email" field (both are unique). Request: email, password. Returns user + token. Throttled.',
    'POST /api/register'        => 'Request: name, username (unique; required in app, backend auto-generates when missing), email (unique), phone (unique), password, password_confirmation. Returns user + token.',
    'GET /api/profile'          => 'Own profile. Response includes user info, seller {experience_level, country, category_details[], location {latitude, longitude, source}} and profile completeness percent.',
    'POST /api/profile'         => 'Update own profile. Request: seller fields (full_name, phone, country, experience_level) + latitude, longitude (geo tracking) + category_id + category_details[] (keyed by the category form_fields). Validates Aadhaar (12-digit Verhoeff) + PAN format. Updates category details + seller location.',
    'POST /api/seller/services' => 'Create service. Fields: title, slug, description, price, delivery_days, revisions, delivery_method (digital | hosting), category_id, service_type_ids[], thumbnail (file), status (draft | active | paused), visit_start_time (HH:MM), visit_end_time (HH:MM), working_days[] (Mon..Sun). Timing fields are REQUIRED when category_type = field.',
    'PUT /api/seller/services/{id}' => 'Update service. Same request fields as create (all optional). Timing fields still required for field-type categories.',
    'POST /api/admin/categories' => 'Create category. Fields: name, slug, parent_id, category_type (technical | field), form_fields[] (JSON schema: key, label, type in text|number|textarea|select|multiselect|checkbox|tel, required, options[]).',
    'PUT /api/admin/categories/{id}' => 'Update category. Same fields as create.',
];

$out = fopen($file, 'w');
$header = [
    '"SKILLNEST API ENDPOINTS (for mobile app developers)"',
    '"All endpoints start with: BASE_URL (will be your live domain, e.g. https://skillnest.com)/api"',
    '"Host = localhost during local dev -> change to live domain after hosting"',
    '"Auth via session (web middleware). Mobile app should hit these over HTTP with cookies (same domain / session) OR expect JWT in phase 2."',
    '"Total routes: ' . count($rows) . '"',
    '',
    '#,Method,Endpoint,"Route Name",Auth,Middleware,Controller@Method,"Description / Notes (for mobile dev)"',
];
$i = 0;
foreach ($header as $line) {
    fwrite($out, $line . "\r\n");
}
foreach ($rows as $r) {
    $i++;
    $r[0] = (string) $i;
    $key = ($r[1] ?? '') . ' ' . ($r[2] ?? '');
    $r[7] = $desc[$key] ?? '';
    fputcsv($out, $r);
}
fclose($out);

echo "Wrote " . count($rows) . " routes to $file\n";