<?php

return [

    'demo' => (bool) env('DENIMFORGE_DEMO', true),

    'demo_readonly' => (bool) env('DENIMFORGE_DEMO_READONLY', false),

    'ai_ready' => (bool) env('DENIMFORGE_AI', false),

    'timezone' => env('APP_TIMEZONE', 'Asia/Dhaka'),

    'plant' => [
        'name' => 'DenimForge Mills Ltd.',
        'name_bn' => 'ডেনিমফোর্জ মিলস লিমিটেড',
        'location' => 'Konabari, Gazipur, Bangladesh',
        'location_bn' => 'কোনাবাড়ি, গাজীপুর, বাংলাদেশ',
    ],

    'alerts' => [
        'mail' => (bool) env('DENIMFORGE_ALERT_MAIL', true),
        'sms' => (bool) env('DENIMFORGE_ALERT_SMS', false),
        'whatsapp' => (bool) env('DENIMFORGE_ALERT_WHATSAPP', false),
        'recipients' => array_filter(explode(',', (string) env('DENIMFORGE_ALERT_EMAILS', 'manager@denimforge.test'))),
    ],

    'quality' => [
        'four_point_limit' => (float) env('DENIMFORGE_FOUR_POINT_LIMIT', 28),
        'delta_e_head_tail' => 1.0,
        'delta_e_side_center' => 0.8,
    ],

    'modules' => [
        'commercial' => ['name' => 'Commercial', 'description' => 'Buyers and commercial masters.'],
        'inventory' => ['name' => 'Raw material & inventory', 'description' => 'Cotton, yarn, dyes, chemicals, trims, spares, FIFO lots.'],
        'yarn' => ['name' => 'Yarn / spinning', 'description' => 'Count, twist, strength and yarn lot traceability.'],
        'catalog' => ['name' => 'Denim catalog', 'description' => 'Public and internal fabric constructions.'],
        'planning' => ['name' => 'Orders & planning', 'description' => 'Buyer orders, samples, work orders, lot links.'],
        'dyeing' => ['name' => 'Indigo dyeing', 'description' => 'Rope and sheet ranges, shade cards, lab readings.'],
        'weaving' => ['name' => 'Weaving', 'description' => 'Loom production and defect logging.'],
        'finishing' => ['name' => 'Fabric finishing', 'description' => 'Singeing through sanforizing and skew.'],
        'cutting' => ['name' => 'Cutting', 'description' => 'Markers, lays and fabric consumption.'],
        'sewing' => ['name' => 'Sewing lines', 'description' => 'Line balance, hourly output and bundles.'],
        'washing' => ['name' => 'Laundry', 'description' => 'Wash recipes, shade approval and consumption.'],
        'quality' => ['name' => 'Quality control', 'description' => '4-point inspection, issues and the remedy library.'],
        'maintenance' => ['name' => 'Maintenance', 'description' => 'Machines, stoppages and work orders.'],
        'finance' => ['name' => 'Finance & costing', 'description' => 'Style costing, invoices and payments.'],
        'hr' => ['name' => 'HR', 'description' => 'People, shifts and the skill matrix.'],
        'website' => ['name' => 'Public website', 'description' => 'Pages, news, gallery, careers and inquiries.'],
        'documents' => ['name' => 'Documents', 'description' => 'Lab reports, shade cards and OCR intake.'],
        'automation' => ['name' => 'Automation', 'description' => 'Parameter alerts and recommended actions.'],
        'core' => ['name' => 'System', 'description' => 'Modules, settings and audit. Always available.'],
    ],

];
