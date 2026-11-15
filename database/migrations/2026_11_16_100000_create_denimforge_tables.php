<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buyers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('country')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('incoterm')->nullable();
            $table->string('payment_terms')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->default('general');
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->integer('lead_time_days')->default(14);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->timestamps();
        });

        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('starts_at')->default('06');
            $table->string('ends_at')->default(14);
            $table->boolean('is_night')->default(false);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employee_no')->unique();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('shift_id')->nullable()->constrained('shifts')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('designation')->nullable();
            $table->string('skill_group')->default('general');
            $table->string('phone')->nullable();
            $table->date('joined_on')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->string('skill')->default('indigo_dyeing');
            $table->integer('level')->default(3);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('material_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('yarn');
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('name');
            $table->foreignId('material_category_id')->nullable()->constrained('material_categories')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('type')->default('yarn');
            $table->string('uom')->default('kg');
            $table->decimal('reorder_level', 12, 3)->default('0');
            $table->decimal('qty_on_hand', 12, 3)->default('0');
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inventory_lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('lot_no')->unique();
            $table->string('trace_code')->nullable()->unique();
            $table->decimal('qty_received', 12, 3);
            $table->decimal('qty_on_hand', 12, 3);
            $table->string('uom')->default('kg');
            $table->decimal('unit_cost', 12, 2)->nullable();
            $table->date('received_on');
            $table->date('expires_on')->nullable();
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained('materials')->restrictOnDelete();
            $table->foreignId('inventory_lot_id')->nullable()->constrained('inventory_lots')->nullOnDelete();
            $table->string('direction')->default('in');
            $table->decimal('qty', 12, 3);
            $table->string('reason');
            $table->string('reference')->nullable();
            $table->dateTime('moved_at');
            $table->timestamps();
        });

        Schema::create('yarn_lots', function (Blueprint $table) {
            $table->id();
            $table->string('lot_no')->unique();
            $table->string('trace_code')->nullable()->unique();
            $table->foreignId('inventory_lot_id')->nullable()->constrained('inventory_lots')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->decimal('count_ne', 8, 2)->nullable();
            $table->decimal('twist_tpi', 8, 2)->nullable();
            $table->decimal('strength_rkm', 8, 2)->nullable();
            $table->decimal('cv_percent', 6, 2)->nullable();
            $table->string('composition')->nullable();
            $table->string('end_use')->default('warp');
            $table->integer('cones')->nullable();
            $table->decimal('kg', 12, 3)->nullable();
            $table->date('received_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('machines', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('type')->default('loom');
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('status')->default('idle')->index();
            $table->string('brand')->nullable();
            $table->string('criticality')->default('medium');
            $table->date('installed_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('fabric_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_bn')->nullable();
            $table->string('construction')->nullable();
            $table->string('weave')->nullable();
            $table->decimal('weight_oz', 6, 2)->nullable();
            $table->decimal('weight_gsm', 8, 2)->nullable();
            $table->string('composition')->nullable();
            $table->string('shade')->nullable();
            $table->string('finish')->nullable();
            $table->boolean('stretch')->default(false);
            $table->decimal('width_inch', 6, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('description_bn')->nullable();
            $table->text('sustainability_note')->nullable();
            $table->boolean('is_published')->default(true);
            $table->string('slug')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('styles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('buyer_id')->nullable()->constrained('buyers')->nullOnDelete();
            $table->foreignId('fabric_product_id')->nullable()->constrained('fabric_products')->nullOnDelete();
            $table->string('season')->nullable();
            $table->string('wash_type')->nullable();
            $table->string('status')->default('development')->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tech_packs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained('styles')->restrictOnDelete();
            $table->string('version')->default('A');
            $table->string('file_path')->nullable();
            $table->string('status')->default('draft')->index();
            $table->dateTime('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('shade_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->foreignId('style_id')->nullable()->constrained('styles')->nullOnDelete();
            $table->string('method')->default('rope');
            $table->decimal('standard_l', 6, 2)->nullable();
            $table->decimal('standard_a', 6, 2)->nullable();
            $table->decimal('standard_b', 6, 2)->nullable();
            $table->decimal('ph_min', 4, 2)->nullable();
            $table->decimal('ph_max', 4, 2)->nullable();
            $table->decimal('orp_min', 8, 1)->nullable();
            $table->decimal('orp_max', 8, 1)->nullable();
            $table->decimal('temp_min', 5, 1)->nullable();
            $table->decimal('temp_max', 5, 1)->nullable();
            $table->decimal('concentration_gl', 8, 2)->nullable();
            $table->integer('dips')->default(6);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('buyer_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->foreignId('buyer_id')->constrained('buyers')->restrictOnDelete();
            $table->foreignId('style_id')->constrained('styles')->restrictOnDelete();
            $table->string('season')->nullable();
            $table->integer('qty');
            $table->string('uom')->default('pcs');
            $table->date('ex_factory')->nullable();
            $table->string('destination')->nullable();
            $table->string('status')->default('draft')->index();
            $table->string('currency')->default('USD');
            $table->decimal('contract_price', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('order_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_order_id')->constrained('buyer_orders')->restrictOnDelete();
            $table->foreignId('buyer_id')->constrained('buyers')->restrictOnDelete();
            $table->foreignId('style_id')->nullable()->constrained('styles')->nullOnDelete();
            $table->string('color');
            $table->string('size')->nullable();
            $table->integer('qty');
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('samples', function (Blueprint $table) {
            $table->id();
            $table->string('sample_no')->unique();
            $table->foreignId('style_id')->constrained('styles')->restrictOnDelete();
            $table->foreignId('buyer_id')->nullable()->constrained('buyers')->nullOnDelete();
            $table->foreignId('buyer_order_id')->nullable()->constrained('buyer_orders')->nullOnDelete();
            $table->string('type')->default('proto');
            $table->string('status')->default('pending')->index();
            $table->text('comments')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('buyer_order_id')->nullable()->constrained('buyer_orders')->nullOnDelete();
            $table->foreignId('buyer_id')->nullable()->constrained('buyers')->nullOnDelete();
            $table->foreignId('style_id')->nullable()->constrained('styles')->nullOnDelete();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('stage')->default('dyeing');
            $table->decimal('qty', 12, 2);
            $table->string('uom')->default('m');
            $table->string('status')->default('planned')->index();
            $table->date('planned_start')->nullable();
            $table->date('planned_end')->nullable();
            $table->string('trace_code')->nullable()->unique();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('dye_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->unique();
            $table->string('trace_code')->nullable()->unique();
            $table->string('method')->default('rope');
            $table->foreignId('shade_card_id')->nullable()->constrained('shade_cards')->nullOnDelete();
            $table->foreignId('style_id')->nullable()->constrained('styles')->nullOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignId('machine_id')->nullable()->constrained('machines')->nullOnDelete();
            $table->foreignId('yarn_lot_id')->nullable()->constrained('yarn_lots')->nullOnDelete();
            $table->foreignId('operator_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->decimal('set_length_m', 12, 2)->nullable();
            $table->decimal('actual_length_m', 12, 2)->nullable();
            $table->decimal('target_concentration_gl', 8, 2)->nullable();
            $table->string('status')->default('planned')->index();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('dye_parameters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dye_batch_id')->constrained('dye_batches')->restrictOnDelete();
            $table->dateTime('recorded_at');
            $table->string('station')->default('box-1');
            $table->decimal('ph', 4, 2)->nullable();
            $table->decimal('orp_mv', 8, 1)->nullable();
            $table->decimal('concentration_gl', 8, 2)->nullable();
            $table->decimal('temperature_c', 5, 1)->nullable();
            $table->decimal('speed_mpm', 6, 2)->nullable();
            $table->integer('oxidation_seconds')->nullable();
            $table->integer('dips')->nullable();
            $table->decimal('caustic_gl', 8, 2)->nullable();
            $table->decimal('hydrosulfite_gl', 8, 2)->nullable();
            $table->foreignId('recorded_by_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        Schema::create('shade_readings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dye_batch_id')->constrained('dye_batches')->restrictOnDelete();
            $table->string('position')->default('middle');
            $table->decimal('l_star', 6, 2)->nullable();
            $table->decimal('a_star', 6, 2)->nullable();
            $table->decimal('b_star', 6, 2)->nullable();
            $table->decimal('delta_e', 6, 3)->nullable();
            $table->string('photo_path')->nullable();
            $table->json('ai_summary')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('weave_productions', function (Blueprint $table) {
            $table->id();
            $table->string('roll_no')->unique();
            $table->string('trace_code')->nullable()->unique();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignId('machine_id')->nullable()->constrained('machines')->nullOnDelete();
            $table->foreignId('warp_yarn_lot_id')->nullable()->constrained('yarn_lots')->nullOnDelete();
            $table->foreignId('weft_yarn_lot_id')->nullable()->constrained('yarn_lots')->nullOnDelete();
            $table->decimal('meters', 12, 2);
            $table->decimal('epi', 6, 1)->nullable();
            $table->decimal('ppi', 6, 1)->nullable();
            $table->decimal('width_inch', 6, 2)->nullable();
            $table->string('status')->default('on_loom')->index();
            $table->date('produced_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('weave_defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weave_production_id')->constrained('weave_productions')->restrictOnDelete();
            $table->string('defect')->default('broken_end');
            $table->text('root_cause')->nullable();
            $table->text('remedy')->nullable();
            $table->decimal('meters', 8, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('finish_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->unique();
            $table->string('trace_code')->nullable()->unique();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignId('machine_id')->nullable()->constrained('machines')->nullOnDelete();
            $table->string('process')->default('sanforizing');
            $table->decimal('input_m', 12, 2)->nullable();
            $table->decimal('output_m', 12, 2)->nullable();
            $table->decimal('width_inch', 6, 2)->nullable();
            $table->decimal('weight_gsm', 8, 2)->nullable();
            $table->decimal('shrinkage_length', 6, 2)->nullable();
            $table->decimal('shrinkage_width', 6, 2)->nullable();
            $table->decimal('skew_percent', 6, 2)->nullable();
            $table->string('status')->default('running')->index();
            $table->date('processed_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('machine_stoppages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_id')->constrained('machines')->restrictOnDelete();
            $table->string('process')->default('weaving');
            $table->string('reason');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->integer('minutes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cutting_markers', function (Blueprint $table) {
            $table->id();
            $table->string('marker_no')->unique();
            $table->foreignId('style_id')->nullable()->constrained('styles')->nullOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->decimal('fabric_m', 12, 2)->nullable();
            $table->decimal('marker_efficiency', 5, 2)->nullable();
            $table->integer('lay_count')->default(1);
            $table->integer('pcs')->default(0);
            $table->decimal('consumption_m', 8, 3)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('bundles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('trace_code')->nullable()->unique();
            $table->foreignId('cutting_marker_id')->nullable()->constrained('cutting_markers')->nullOnDelete();
            $table->foreignId('style_id')->nullable()->constrained('styles')->nullOnDelete();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->integer('qty')->default(1);
            $table->string('status')->default('open')->index();
            $table->string('qr_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('production_lines', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->integer('target_per_hour')->default(40);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('hourly_outputs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_line_id')->constrained('production_lines')->restrictOnDelete();
            $table->foreignId('style_id')->nullable()->constrained('styles')->nullOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->dateTime('hour_start');
            $table->integer('target')->default(0);
            $table->integer('actual')->default(0);
            $table->integer('operators')->default(0);
            $table->decimal('efficiency', 6, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('wash_recipes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type')->default('enzyme');
            $table->decimal('temperature_c', 5, 1)->nullable();
            $table->integer('time_minutes')->nullable();
            $table->string('liquor_ratio')->nullable();
            $table->decimal('ph', 4, 2)->nullable();
            $table->decimal('water_liters_per_kg', 8, 2)->nullable();
            $table->json('chemicals')->nullable();
            $table->text('steps')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('wash_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_no')->unique();
            $table->string('trace_code')->nullable()->unique();
            $table->foreignId('wash_recipe_id')->nullable()->constrained('wash_recipes')->nullOnDelete();
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->integer('qty_pcs')->default(0);
            $table->boolean('shade_approved')->default(false);
            $table->string('handfeel')->nullable();
            $table->decimal('shrinkage_length', 6, 2)->nullable();
            $table->decimal('shrinkage_width', 6, 2)->nullable();
            $table->decimal('water_m3', 10, 3)->nullable();
            $table->decimal('chemical_kg', 10, 3)->nullable();
            $table->string('status')->default('loaded')->index();
            $table->date('washed_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            $table->string('inspection_no')->unique();
            $table->string('stage')->default('final');
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignId('style_id')->nullable()->constrained('styles')->nullOnDelete();
            $table->foreignId('inspector_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->decimal('yards', 12, 2)->nullable();
            $table->decimal('width_inch', 6, 2)->nullable();
            $table->integer('total_points')->default(0);
            $table->decimal('points_per_100', 8, 2)->nullable();
            $table->decimal('accept_limit', 6, 2)->default(28);
            $table->string('result')->default('pending');
            $table->dateTime('inspected_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('inspection_defects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inspection_id')->constrained('inspections')->restrictOnDelete();
            $table->string('classification')->default('weaving');
            $table->string('defect_code');
            $table->decimal('length_inch', 6, 2);
            $table->integer('points')->default(1);
            $table->string('location')->nullable();
            $table->string('photo_path')->nullable();
            $table->json('ai_detection')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('category')->default('dyeing');
            $table->text('symptoms')->nullable();
            $table->text('causes')->nullable();
            $table->text('checks')->nullable();
            $table->text('remedies')->nullable();
            $table->json('parameters')->nullable();
            $table->timestamps();
        });

        Schema::create('process_issues', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('title');
            $table->string('category');
            $table->string('severity')->default('warning');
            $table->string('status')->default('open')->index();
            $table->text('recommended_action')->nullable();
            $table->foreignId('knowledge_article_id')->nullable()->constrained('knowledge_articles')->nullOnDelete();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('maintenance_orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('machine_id')->constrained('machines')->restrictOnDelete();
            $table->string('type')->default('breakdown');
            $table->string('status')->default('open')->index();
            $table->text('symptom')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('action_taken')->nullable();
            $table->integer('downtime_minutes')->nullable();
            $table->dateTime('reported_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->foreignId('assigned_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('style_costings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('style_id')->constrained('styles')->restrictOnDelete();
            $table->string('currency')->default('USD');
            $table->decimal('fabric_cost', 12, 4)->default('0');
            $table->decimal('dyeing_cost', 12, 4)->default('0');
            $table->decimal('weaving_cost', 12, 4)->default('0');
            $table->decimal('washing_cost', 12, 4)->default('0');
            $table->decimal('labor_cost', 12, 4)->default('0');
            $table->decimal('trim_cost', 12, 4)->default('0');
            $table->decimal('overhead', 12, 4)->default('0');
            $table->decimal('total', 12, 4)->default('0');
            $table->decimal('quoted_price', 12, 4)->nullable();
            $table->decimal('variance', 12, 4)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('buyer_id')->constrained('buyers')->restrictOnDelete();
            $table->foreignId('buyer_order_id')->nullable()->constrained('buyer_orders')->nullOnDelete();
            $table->date('invoice_date');
            $table->string('currency')->default('USD');
            $table->decimal('subtotal', 14, 2)->default('0');
            $table->decimal('freight', 14, 2)->default('0');
            $table->decimal('total', 14, 2)->default('0');
            $table->string('status')->default('draft')->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->string('description');
            $table->decimal('qty', 12, 2);
            $table->decimal('unit_price', 12, 4);
            $table->decimal('amount', 14, 2);
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->restrictOnDelete();
            $table->foreignId('buyer_id')->nullable()->constrained('buyers')->nullOnDelete();
            $table->decimal('amount', 14, 2);
            $table->date('paid_on');
            $table->string('method')->default('tt');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('lot_links', function (Blueprint $table) {
            $table->id();
            $table->string('origin_type')->nullable();
            $table->unsignedBigInteger('origin_id')->nullable()->index();
            $table->string('destination_type')->nullable();
            $table->unsignedBigInteger('destination_id')->nullable()->index();
            $table->decimal('qty', 12, 3)->nullable();
            $table->string('uom')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->text('excerpt')->nullable();
            $table->text('excerpt_bn')->nullable();
            $table->text('body')->nullable();
            $table->text('body_bn')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->string('slug')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('album')->default('mill');
            $table->string('caption')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->foreignId('fabric_product_id')->nullable()->constrained('fabric_products')->nullOnDelete();
            $table->string('status')->default('new')->index();
            $table->timestamps();
        });

        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_bn')->nullable();
            $table->string('department')->nullable();
            $table->string('location')->nullable();
            $table->string('employment_type')->default('full_time');
            $table->text('description')->nullable();
            $table->text('description_bn')->nullable();
            $table->boolean('is_open')->default(true);
            $table->string('slug')->nullable()->unique();
            $table->timestamps();
        });

        Schema::create('factory_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('document_type')->default('other');
            $table->string('path');
            $table->string('status')->default('uploaded')->index();
            $table->json('extracted')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('module');
            $table->string('severity')->default('warning');
            $table->string('title');
            $table->text('message')->nullable();
            $table->text('recommended_action')->nullable();
            $table->json('channels')->nullable();
            $table->string('status')->default('open')->index();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->dateTime('notified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('module_toggles', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('enabled')->default(true);
            $table->integer('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('group')->default('general');
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable()->index();
            $table->string('event');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('hash')->nullable();
            $table->string('prev_hash')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('locale')->default('en');
            $table->boolean('is_active')->default(true);
            $table->foreignId('buyer_id')->nullable()->constrained('buyers')->nullOnDelete();
        });

        Schema::create('slug_histories', function (Blueprint $table) {
            $table->id();
            $table->morphs('sluggable');
            $table->string('slug')->index();
            $table->string('field')->default('slug');
            $table->timestamps();
            $table->unique(['sluggable_type', 'slug', 'field'], 'slug_histories_unique');
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->id();
            $table->dropConstrainedForeignId('buyer_id');
            $table->dropColumn(['locale', 'is_active']);
        });

        Schema::dropIfExists('notifications');
        Schema::dropIfExists('slug_histories');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('module_toggles');
        Schema::dropIfExists('alerts');
        Schema::dropIfExists('factory_documents');
        Schema::dropIfExists('job_openings');
        Schema::dropIfExists('inquiries');
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('lot_links');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoice_lines');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('style_costings');
        Schema::dropIfExists('maintenance_orders');
        Schema::dropIfExists('process_issues');
        Schema::dropIfExists('knowledge_articles');
        Schema::dropIfExists('inspection_defects');
        Schema::dropIfExists('inspections');
        Schema::dropIfExists('wash_batches');
        Schema::dropIfExists('wash_recipes');
        Schema::dropIfExists('hourly_outputs');
        Schema::dropIfExists('production_lines');
        Schema::dropIfExists('bundles');
        Schema::dropIfExists('cutting_markers');
        Schema::dropIfExists('machine_stoppages');
        Schema::dropIfExists('finish_batches');
        Schema::dropIfExists('weave_defects');
        Schema::dropIfExists('weave_productions');
        Schema::dropIfExists('shade_readings');
        Schema::dropIfExists('dye_parameters');
        Schema::dropIfExists('dye_batches');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('samples');
        Schema::dropIfExists('order_lines');
        Schema::dropIfExists('buyer_orders');
        Schema::dropIfExists('shade_cards');
        Schema::dropIfExists('tech_packs');
        Schema::dropIfExists('styles');
        Schema::dropIfExists('fabric_products');
        Schema::dropIfExists('machines');
        Schema::dropIfExists('yarn_lots');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('inventory_lots');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('material_categories');
        Schema::dropIfExists('employee_skills');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('suppliers');
        Schema::dropIfExists('buyers');
    }
};
