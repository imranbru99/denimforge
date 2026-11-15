<?php

namespace App\Domain;

use Illuminate\Support\Str;
use RuntimeException;

/**
 * Single source of truth for DenimForge entities.
 * Column DSL: name:type[:flags]
 * Types: string, text, bool, int, uint, dec, date, datetime, json, fk, select, morph
 * Nullable: suffix ? on the type. Defaults: type=value or default=value.
 * Flags: unique, search, index. Foreign rename: table@relation. Select options use pipes.
 */
final class Schema
{
    /** @var array<int, array<string, mixed>>|null */
    private static ?array $cache = null;

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function all(): array
    {
        return self::$cache ??= array_map(
            fn (array $entity, int $index) => self::normalize($entity, $index),
            self::definitions(),
            array_keys(self::definitions()),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public static function forModel(string $model): array
    {
        $base = class_basename($model);

        foreach (self::all() as $entity) {
            if ($entity['model'] === $base) {
                return $entity;
            }
        }

        throw new RuntimeException("No DenimForge schema for [{$model}].");
    }

    /**
     * @return array<int, string>
     */
    public static function traceableModels(): array
    {
        $models = [];

        foreach (self::all() as $entity) {
            foreach ($entity['columns'] as $column) {
                if ($column['name'] === 'trace_code') {
                    $models[] = 'App\\Models\\'.$entity['model'];
                }
            }
        }

        return $models;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private static function definitions(): array
    {
        return [
            self::entity('buyers', 'Buyer', 'commercial', 'Commercial', 'heroicon-o-building-storefront', 'name', [
                'code:string:unique:search',
                'name:string:search',
                'country:string?',
                'contact_name:string?',
                'email:string?:search',
                'phone:string?',
                'incoterm:string?',
                'payment_terms:string?',
                'is_active:bool=1',
                'notes:text?',
            ], ['orders:BuyerOrder']),

            self::entity('suppliers', 'Supplier', 'inventory', 'Inventory', 'heroicon-o-truck', 'name', [
                'code:string:unique:search',
                'name:string:search',
                'category:select:cotton|yarn|dye|chemical|trim|spare|general:default=general',
                'country:string?',
                'phone:string?',
                'email:string?',
                'lead_time_days:int=14',
                'is_active:bool=1',
                'notes:text?',
            ], ['materials:Material', 'lots:InventoryLot']),

            self::entity('departments', 'Department', 'hr', 'People', 'heroicon-o-rectangle-stack', 'name', [
                'code:string:unique:search',
                'name:string:search',
                'name_bn:string?',
            ], ['employees:Employee', 'machines:Machine']),

            self::entity('shifts', 'Shift', 'hr', 'People', 'heroicon-o-clock', 'name', [
                'name:string:search',
                'starts_at:string=06:00',
                'ends_at:string=14:00',
                'is_night:bool=0',
            ], ['employees:Employee']),

            self::entity('employees', 'Employee', 'hr', 'People', 'heroicon-o-users', 'name', [
                'employee_no:string:unique:search',
                'name:string:search',
                'name_bn:string?',
                'department_id:fk?:departments',
                'shift_id:fk?:shifts',
                'user_id:fk?:users@user',
                'designation:string?',
                'skill_group:select:general|indigo_dyeing|weaving|wash_tech|qc|sewing|maintenance|cutting:default=general',
                'phone:string?',
                'joined_on:date?',
                'is_active:bool=1',
            ], ['skills:EmployeeSkill']),

            self::entity('employee_skills', 'EmployeeSkill', 'hr', 'People', 'heroicon-o-academic-cap', 'skill', [
                'employee_id:fk:employees',
                'skill:select:indigo_dyeing|rope_dyeing|sheet_dyeing|weaving|sizing|wash_tech|laser|qc|sewing|maintenance|cutting:default=indigo_dyeing:search',
                'level:int=3',
                'notes:text?',
            ]),

            self::entity('material_categories', 'MaterialCategory', 'inventory', 'Inventory', 'heroicon-o-squares-2x2', 'name', [
                'name:string:search',
                'type:select:cotton|yarn|dye|chemical|trim|spare:default=yarn',
            ], ['materials:Material']),

            self::entity('materials', 'Material', 'inventory', 'Inventory', 'heroicon-o-cube', 'name', [
                'sku:string:unique:search',
                'name:string:search',
                'material_category_id:fk?:material_categories@category',
                'supplier_id:fk?:suppliers',
                'type:select:cotton|yarn|dye|chemical|trim|spare:default=yarn:search',
                'uom:string=kg',
                'reorder_level:dec=0:12,3',
                'qty_on_hand:dec=0:12,3',
                'unit_cost:dec?:12,2',
                'is_active:bool=1',
                'notes:text?',
            ], ['lots:InventoryLot', 'movements:StockMovement']),

            self::entity('inventory_lots', 'InventoryLot', 'inventory', 'Inventory', 'heroicon-o-archive-box', 'lot_no', [
                'material_id:fk:materials',
                'supplier_id:fk?:suppliers',
                'lot_no:string:unique:search',
                'trace_code:string?:unique:search',
                'qty_received:dec:12,3',
                'qty_on_hand:dec:12,3',
                'uom:string=kg',
                'unit_cost:dec?:12,2',
                'received_on:date',
                'expires_on:date?',
                'location:string?',
                'notes:text?',
            ], ['movements:StockMovement']),

            self::entity('stock_movements', 'StockMovement', 'inventory', 'Inventory', 'heroicon-o-arrows-right-left', 'reason', [
                'material_id:fk:materials',
                'inventory_lot_id:fk?:inventory_lots@lot',
                'direction:select:in|out:default=in:search',
                'qty:dec:12,3',
                'reason:string:search',
                'reference:string?',
                'moved_at:datetime',
            ]),

            self::entity('yarn_lots', 'YarnLot', 'yarn', 'Yarn', 'heroicon-o-sparkles', 'lot_no', [
                'lot_no:string:unique:search',
                'trace_code:string?:unique:search',
                'inventory_lot_id:fk?:inventory_lots@inventoryLot',
                'supplier_id:fk?:suppliers',
                'count_ne:dec?:8,2',
                'twist_tpi:dec?:8,2',
                'strength_rkm:dec?:8,2',
                'cv_percent:dec?:6,2',
                'composition:string?',
                'end_use:select:warp|weft|both:default=warp',
                'cones:int?',
                'kg:dec?:12,3',
                'received_on:date?',
                'notes:text?',
            ]),

            self::entity('machines', 'Machine', 'maintenance', 'Maintenance', 'heroicon-o-cpu-chip', 'name', [
                'code:string:unique:search',
                'name:string:search',
                'type:select:dye_range|loom|singer|mercerizer|sanforizer|washer|laser|boiler|compressor|other:default=loom:search',
                'department_id:fk?:departments',
                'status:select:running|idle|down|maintenance:default=idle:search',
                'brand:string?',
                'criticality:select:low|medium|high:default=medium',
                'installed_on:date?',
                'notes:text?',
            ], ['stoppages:MachineStoppage', 'maintenanceOrders:MaintenanceOrder']),

            self::entity('fabric_products', 'FabricProduct', 'catalog', 'Catalog', 'heroicon-o-swatch', 'name', [
                'name:string:search',
                'name_bn:string?',
                'construction:string?',
                'weave:string?',
                'weight_oz:dec?:6,2',
                'weight_gsm:dec?:8,2',
                'composition:string?',
                'shade:string?:search',
                'finish:string?',
                'stretch:bool=0',
                'width_inch:dec?:6,2',
                'description:text?',
                'description_bn:text?',
                'sustainability_note:text?',
                'is_published:bool=1',
            ], ['styles:Style', 'inquiries:Inquiry'], slug: 'name', routeKey: true),

            self::entity('styles', 'Style', 'planning', 'Planning', 'heroicon-o-tag', 'name', [
                'code:string:unique:search',
                'name:string:search',
                'buyer_id:fk?:buyers',
                'fabric_product_id:fk?:fabric_products@fabricProduct',
                'season:string?',
                'wash_type:string?',
                'status:select:development|sampling|bulk|closed:default=development:search',
                'description:text?',
            ], ['orders:BuyerOrder', 'techPacks:TechPack', 'costings:StyleCosting']),

            self::entity('tech_packs', 'TechPack', 'planning', 'Planning', 'heroicon-o-document-text', 'version', [
                'style_id:fk:styles',
                'version:string=A',
                'file_path:string?',
                'status:select:draft|submitted|approved|rejected:default=draft',
                'approved_at:datetime?',
                'notes:text?',
            ]),

            self::entity('shade_cards', 'ShadeCard', 'dyeing', 'Dyeing', 'heroicon-o-eye-dropper', 'name', [
                'code:string:unique:search',
                'name:string:search',
                'style_id:fk?:styles',
                'method:select:rope|sheet|sulfur:default=rope:search',
                'standard_l:dec?:6,2',
                'standard_a:dec?:6,2',
                'standard_b:dec?:6,2',
                'ph_min:dec?:4,2',
                'ph_max:dec?:4,2',
                'orp_min:dec?:8,1',
                'orp_max:dec?:8,1',
                'temp_min:dec?:5,1',
                'temp_max:dec?:5,1',
                'concentration_gl:dec?:8,2',
                'dips:int=6',
                'notes:text?',
            ], ['batches:DyeBatch']),

            self::entity('buyer_orders', 'BuyerOrder', 'planning', 'Planning', 'heroicon-o-clipboard-document-list', 'order_no', [
                'order_no:string:unique:search',
                'buyer_id:fk:buyers',
                'style_id:fk:styles',
                'season:string?',
                'qty:int',
                'uom:string=pcs',
                'ex_factory:date?',
                'destination:string?',
                'status:select:draft|confirmed|in_production|shipped|closed:default=draft:search',
                'currency:string=USD',
                'contract_price:dec?:12,2',
                'notes:text?',
            ], ['lines:OrderLine', 'workOrders:WorkOrder', 'samples:Sample'], buyer: true),

            self::entity('order_lines', 'OrderLine', 'planning', 'Planning', 'heroicon-o-queue-list', 'color', [
                'buyer_order_id:fk:buyer_orders@order',
                'buyer_id:fk:buyers',
                'style_id:fk?:styles',
                'color:string:search',
                'size:string?',
                'qty:int',
                'unit_price:dec?:12,2',
            ], buyer: true),

            self::entity('samples', 'Sample', 'planning', 'Planning', 'heroicon-o-beaker', 'sample_no', [
                'sample_no:string:unique:search',
                'style_id:fk:styles',
                'buyer_id:fk?:buyers',
                'buyer_order_id:fk?:buyer_orders@order',
                'type:select:proto|fit|pp|sms|size_set:default=proto',
                'status:select:pending|approved|rejected:default=pending:search',
                'comments:text?',
                'approved_at:datetime?',
            ], buyer: true),

            self::entity('work_orders', 'WorkOrder', 'planning', 'Planning', 'heroicon-o-clipboard-document-check', 'number', [
                'number:string:unique:search',
                'buyer_order_id:fk?:buyer_orders@order',
                'buyer_id:fk?:buyers',
                'style_id:fk?:styles',
                'parent_id:uint?',
                'stage:select:dyeing|weaving|finishing|cutting|sewing|washing|packing:default=dyeing:search',
                'qty:dec:12,2',
                'uom:string=m',
                'status:select:planned|running|done|hold:default=planned:search',
                'planned_start:date?',
                'planned_end:date?',
                'trace_code:string?:unique:search',
                'notes:text?',
            ], ['children:WorkOrder:parent_id'], buyer: true),

            self::entity('dye_batches', 'DyeBatch', 'dyeing', 'Dyeing', 'heroicon-o-beaker', 'batch_no', [
                'batch_no:string:unique:search',
                'trace_code:string?:unique:search',
                'method:select:rope|sheet|sulfur:default=rope:search',
                'shade_card_id:fk?:shade_cards@shadeCard',
                'style_id:fk?:styles',
                'work_order_id:fk?:work_orders@workOrder',
                'machine_id:fk?:machines',
                'yarn_lot_id:fk?:yarn_lots@yarnLot',
                'operator_id:fk?:employees@operator',
                'set_length_m:dec?:12,2',
                'actual_length_m:dec?:12,2',
                'target_concentration_gl:dec?:8,2',
                'status:select:planned|running|hold|completed|rejected:default=planned:search',
                'started_at:datetime?',
                'ended_at:datetime?',
                'notes:text?',
            ], ['parameters:DyeParameter', 'readings:ShadeReading']),

            self::entity('dye_parameters', 'DyeParameter', 'dyeing', 'Dyeing', 'heroicon-o-variable', 'station', [
                'dye_batch_id:fk:dye_batches@batch',
                'recorded_at:datetime',
                'station:string=box-1:search',
                'ph:dec?:4,2',
                'orp_mv:dec?:8,1',
                'concentration_gl:dec?:8,2',
                'temperature_c:dec?:5,1',
                'speed_mpm:dec?:6,2',
                'oxidation_seconds:int?',
                'dips:int?',
                'caustic_gl:dec?:8,2',
                'hydrosulfite_gl:dec?:8,2',
                'recorded_by_id:fk?:employees@recordedBy',
                'remarks:text?',
            ]),

            self::entity('shade_readings', 'ShadeReading', 'dyeing', 'Dyeing', 'heroicon-o-sun', 'position', [
                'dye_batch_id:fk:dye_batches@batch',
                'position:select:head|middle|tail|left|center|right:default=middle:search',
                'l_star:dec?:6,2',
                'a_star:dec?:6,2',
                'b_star:dec?:6,2',
                'delta_e:dec?:6,3',
                'photo_path:string?',
                'ai_summary:json?',
                'notes:text?',
            ]),

            self::entity('weave_productions', 'WeaveProduction', 'weaving', 'Weaving', 'heroicon-o-view-columns', 'roll_no', [
                'roll_no:string:unique:search',
                'trace_code:string?:unique:search',
                'work_order_id:fk?:work_orders@workOrder',
                'machine_id:fk?:machines',
                'warp_yarn_lot_id:fk?:yarn_lots@warpYarnLot',
                'weft_yarn_lot_id:fk?:yarn_lots@weftYarnLot',
                'meters:dec:12,2',
                'epi:dec?:6,1',
                'ppi:dec?:6,1',
                'width_inch:dec?:6,2',
                'status:select:on_loom|doffed|inspected|rejected:default=on_loom:search',
                'produced_on:date?',
                'notes:text?',
            ], ['defects:WeaveDefect']),

            self::entity('weave_defects', 'WeaveDefect', 'weaving', 'Weaving', 'heroicon-o-exclamation-triangle', 'defect', [
                'weave_production_id:fk:weave_productions@production',
                'defect:select:broken_end|broken_pick|double_pick|reed_mark|slub|float|starting_mark:default=broken_end:search',
                'root_cause:text?',
                'remedy:text?',
                'meters:dec?:8,2',
                'notes:text?',
            ]),

            self::entity('finish_batches', 'FinishBatch', 'finishing', 'Finishing', 'heroicon-o-sparkles', 'batch_no', [
                'batch_no:string:unique:search',
                'trace_code:string?:unique:search',
                'work_order_id:fk?:work_orders@workOrder',
                'machine_id:fk?:machines',
                'process:select:singeing|desizing|mercerizing|sanforizing|skew|heatset:default=sanforizing:search',
                'input_m:dec?:12,2',
                'output_m:dec?:12,2',
                'width_inch:dec?:6,2',
                'weight_gsm:dec?:8,2',
                'shrinkage_length:dec?:6,2',
                'shrinkage_width:dec?:6,2',
                'skew_percent:dec?:6,2',
                'status:select:running|done|hold:default=running',
                'processed_on:date?',
                'notes:text?',
            ]),

            self::entity('machine_stoppages', 'MachineStoppage', 'maintenance', 'Maintenance', 'heroicon-o-pause-circle', 'reason', [
                'machine_id:fk:machines',
                'process:select:dyeing|weaving|finishing|washing|sewing|utility:default=weaving:search',
                'reason:string:search',
                'started_at:datetime',
                'ended_at:datetime?',
                'minutes:int?',
                'notes:text?',
            ]),

            self::entity('cutting_markers', 'CuttingMarker', 'cutting', 'Cutting', 'heroicon-o-scissors', 'marker_no', [
                'marker_no:string:unique:search',
                'style_id:fk?:styles',
                'work_order_id:fk?:work_orders@workOrder',
                'fabric_m:dec?:12,2',
                'marker_efficiency:dec?:5,2',
                'lay_count:int=1',
                'pcs:int=0',
                'consumption_m:dec?:8,3',
                'notes:text?',
            ], ['bundles:Bundle']),

            self::entity('bundles', 'Bundle', 'sewing', 'Sewing', 'heroicon-o-qr-code', 'code', [
                'code:string:unique:search',
                'trace_code:string?:unique:search',
                'cutting_marker_id:fk?:cutting_markers@marker',
                'style_id:fk?:styles',
                'size:string?',
                'color:string?:search',
                'qty:int=1',
                'status:select:open|in_sewing|washed|packed:default=open:search',
                'qr_payload:string?',
            ]),

            self::entity('production_lines', 'ProductionLine', 'sewing', 'Sewing', 'heroicon-o-bars-3', 'name', [
                'code:string:unique:search',
                'name:string:search',
                'target_per_hour:int=40',
                'is_active:bool=1',
            ], ['outputs:HourlyOutput']),

            self::entity('hourly_outputs', 'HourlyOutput', 'sewing', 'Sewing', 'heroicon-o-chart-bar', 'hour_start', [
                'production_line_id:fk:production_lines@line',
                'style_id:fk?:styles',
                'work_order_id:fk?:work_orders@workOrder',
                'hour_start:datetime',
                'target:int=0',
                'actual:int=0',
                'operators:int=0',
                'efficiency:dec?:6,2',
                'notes:text?',
            ]),

            self::entity('wash_recipes', 'WashRecipe', 'washing', 'Washing', 'heroicon-o-fire', 'name', [
                'name:string:search',
                'type:select:enzyme|stone|laser|chemical|rinse|bleach:default=enzyme:search',
                'temperature_c:dec?:5,1',
                'time_minutes:int?',
                'liquor_ratio:string?',
                'ph:dec?:4,2',
                'water_liters_per_kg:dec?:8,2',
                'chemicals:json?',
                'steps:text?',
                'notes:text?',
            ], ['batches:WashBatch']),

            self::entity('wash_batches', 'WashBatch', 'washing', 'Washing', 'heroicon-o-beaker', 'batch_no', [
                'batch_no:string:unique:search',
                'trace_code:string?:unique:search',
                'wash_recipe_id:fk?:wash_recipes@recipe',
                'work_order_id:fk?:work_orders@workOrder',
                'qty_pcs:int=0',
                'shade_approved:bool=0',
                'handfeel:string?',
                'shrinkage_length:dec?:6,2',
                'shrinkage_width:dec?:6,2',
                'water_m3:dec?:10,3',
                'chemical_kg:dec?:10,3',
                'status:select:loaded|running|approved|reprocess:default=loaded:search',
                'washed_on:date?',
                'notes:text?',
            ]),

            self::entity('inspections', 'Inspection', 'quality', 'Quality', 'heroicon-o-shield-check', 'inspection_no', [
                'inspection_no:string:unique:search',
                'stage:select:incoming|inline|final:default=final:search',
                'work_order_id:fk?:work_orders@workOrder',
                'style_id:fk?:styles',
                'inspector_id:fk?:employees@inspector',
                'yards:dec?:12,2',
                'width_inch:dec?:6,2',
                'total_points:int=0',
                'points_per_100:dec?:8,2',
                'accept_limit:dec=28:6,2',
                'result:select:pending|pass|fail|hold:default=pending:search',
                'inspected_at:datetime?',
                'notes:text?',
            ], ['defects:InspectionDefect']),

            self::entity('inspection_defects', 'InspectionDefect', 'quality', 'Quality', 'heroicon-o-exclamation-triangle', 'defect_code', [
                'inspection_id:fk:inspections',
                'classification:select:yarn|weaving|dyeing|finishing|washing|sewing|stain:default=weaving:search',
                'defect_code:string:search',
                'length_inch:dec:6,2',
                'points:int=1',
                'location:string?',
                'photo_path:string?',
                'ai_detection:json?',
                'notes:text?',
            ]),

            self::entity('knowledge_articles', 'KnowledgeArticle', 'quality', 'Knowledge', 'heroicon-o-book-open', 'title', [
                'code:string:unique:search',
                'title:string:search',
                'title_bn:string?',
                'category:select:dyeing|weaving|finishing|washing|sewing|quality:default=dyeing:search',
                'symptoms:text?',
                'causes:text?',
                'checks:text?',
                'remedies:text?',
                'parameters:json?',
            ]),

            self::entity('process_issues', 'ProcessIssue', 'quality', 'Quality', 'heroicon-o-ticket', 'title', [
                'code:string:search',
                'title:string:search',
                'category:string:search',
                'severity:select:info|warning|critical:default=warning',
                'status:select:open|in_progress|resolved:default=open:search',
                'recommended_action:text?',
                'knowledge_article_id:fk?:knowledge_articles@article',
                'source:morph',
                'assigned_employee_id:fk?:employees@assignee',
                'notes:text?',
            ]),

            self::entity('maintenance_orders', 'MaintenanceOrder', 'maintenance', 'Maintenance', 'heroicon-o-wrench-screwdriver', 'number', [
                'number:string:unique:search',
                'machine_id:fk:machines',
                'type:select:preventive|breakdown:default=breakdown:search',
                'status:select:open|in_progress|done:default=open:search',
                'symptom:text?',
                'root_cause:text?',
                'action_taken:text?',
                'downtime_minutes:int?',
                'reported_at:datetime?',
                'completed_at:datetime?',
                'assigned_employee_id:fk?:employees@assignee',
            ]),

            self::entity('style_costings', 'StyleCosting', 'finance', 'Finance', 'heroicon-o-calculator', 'currency', [
                'style_id:fk:styles',
                'currency:string=USD',
                'fabric_cost:dec=0:12,4',
                'dyeing_cost:dec=0:12,4',
                'weaving_cost:dec=0:12,4',
                'washing_cost:dec=0:12,4',
                'labor_cost:dec=0:12,4',
                'trim_cost:dec=0:12,4',
                'overhead:dec=0:12,4',
                'total:dec=0:12,4',
                'quoted_price:dec?:12,4',
                'variance:dec?:12,4',
                'notes:text?',
            ]),

            self::entity('invoices', 'Invoice', 'finance', 'Finance', 'heroicon-o-document-currency-dollar', 'number', [
                'number:string:unique:search',
                'buyer_id:fk:buyers',
                'buyer_order_id:fk?:buyer_orders@order',
                'invoice_date:date',
                'currency:string=USD',
                'subtotal:dec=0:14,2',
                'freight:dec=0:14,2',
                'total:dec=0:14,2',
                'status:select:draft|issued|partial|paid|void:default=draft:search',
                'notes:text?',
            ], ['lines:InvoiceLine', 'payments:Payment'], buyer: true),

            self::entity('invoice_lines', 'InvoiceLine', 'finance', 'Finance', 'heroicon-o-list-bullet', 'description', [
                'invoice_id:fk:invoices',
                'description:string:search',
                'qty:dec:12,2',
                'unit_price:dec:12,4',
                'amount:dec:14,2',
            ]),

            self::entity('payments', 'Payment', 'finance', 'Finance', 'heroicon-o-banknotes', 'reference', [
                'invoice_id:fk:invoices',
                'buyer_id:fk?:buyers',
                'amount:dec:14,2',
                'paid_on:date',
                'method:select:tt|lc|cash|cheque:default=tt',
                'reference:string?:search',
                'notes:text?',
            ], buyer: true),

            self::entity('lot_links', 'LotLink', 'planning', 'Traceability', 'heroicon-o-link', 'uom', [
                'origin:morph',
                'destination:morph',
                'qty:dec?:12,3',
                'uom:string?',
                'note:string?',
            ]),

            self::entity('posts', 'Post', 'website', 'Website', 'heroicon-o-newspaper', 'title', [
                'title:string:search',
                'title_bn:string?',
                'excerpt:text?',
                'excerpt_bn:text?',
                'body:text?',
                'body_bn:text?',
                'published_at:datetime?',
                'is_published:bool=0',
            ], slug: 'title', routeKey: true),

            self::entity('gallery_items', 'GalleryItem', 'website', 'Website', 'heroicon-o-photo', 'title', [
                'title:string:search',
                'album:select:mill|process|people|product:default=mill',
                'caption:string?',
                'image_path:string?',
            ]),

            self::entity('inquiries', 'Inquiry', 'website', 'Website', 'heroicon-o-envelope', 'subject', [
                'name:string:search',
                'company:string?',
                'email:string:search',
                'phone:string?',
                'subject:string:search',
                'message:text',
                'fabric_product_id:fk?:fabric_products@product',
                'status:select:new|read|closed:default=new:search',
            ]),

            self::entity('job_openings', 'JobOpening', 'website', 'Website', 'heroicon-o-briefcase', 'title', [
                'title:string:search',
                'title_bn:string?',
                'department:string?',
                'location:string?',
                'employment_type:select:full_time|contract|apprentice:default=full_time',
                'description:text?',
                'description_bn:text?',
                'is_open:bool=1',
            ], slug: 'title', routeKey: true),

            self::entity('factory_documents', 'FactoryDocument', 'documents', 'Documents', 'heroicon-o-document-magnifying-glass', 'title', [
                'title:string:search',
                'document_type:select:lab_report|invoice|shade_card|tech_pack|other:default=other:search',
                'path:string',
                'status:select:uploaded|extracted|failed:default=uploaded:search',
                'extracted:json?',
                'source:morph',
                'notes:text?',
            ]),

            self::entity('alerts', 'Alert', 'automation', 'Automation', 'heroicon-o-bell-alert', 'title', [
                'module:string:search',
                'severity:select:info|warning|critical:default=warning:search',
                'title:string:search',
                'message:text?',
                'recommended_action:text?',
                'channels:json?',
                'status:select:open|acknowledged|closed:default=open:search',
                'source:morph',
                'notified_at:datetime?',
            ]),

            self::entity('module_toggles', 'ModuleToggle', 'core', 'System', 'heroicon-o-adjustments-horizontal', 'name', [
                'key:string:unique:search',
                'name:string:search',
                'description:text?',
                'enabled:bool=1',
                'sort:int=0',
            ]),

            self::entity('settings', 'Setting', 'core', 'System', 'heroicon-o-cog-6-tooth', 'key', [
                'key:string:unique:search',
                'value:text?',
                'group:string=general:search',
            ]),

            self::entity('audit_logs', 'AuditLog', 'core', 'System', 'heroicon-o-clipboard-document-check', 'event', [
                'user_id:fk?:users',
                'auditable:morph',
                'event:string:search',
                'old_values:json?',
                'new_values:json?',
                'hash:string?',
                'prev_hash:string?',
                'ip:string?',
                'user_agent:string?',
            ], lock: true, audit: false),
        ];
    }

    /**
     * @param  array<int, string>  $columns
     * @param  array<int, string>  $has
     * @return array<string, mixed>
     */
    private static function entity(
        string $table,
        string $model,
        string $module,
        string $group,
        string $icon,
        string $label,
        array $columns,
        array $has = [],
        ?string $slug = null,
        bool $routeKey = false,
        bool $buyer = false,
        bool $lock = false,
        bool $audit = true,
    ): array {
        return compact('table', 'model', 'module', 'group', 'icon', 'label', 'columns', 'has', 'slug', 'routeKey', 'buyer', 'lock', 'audit');
    }

    /**
     * @param  array<string, mixed>  $entity
     * @return array<string, mixed>
     */
    private static function normalize(array $entity, int $index): array
    {
        $columns = [];
        $relations = [];

        foreach ($entity['columns'] as $dsl) {
            $parsed = self::parseColumn($dsl);

            if ($parsed['type'] === 'morph') {
                $columns[] = [
                    'name' => $parsed['name'].'_type',
                    'type' => 'string',
                    'nullable' => true,
                    'searchable' => false,
                    'form' => 'text',
                ];
                $columns[] = [
                    'name' => $parsed['name'].'_id',
                    'type' => 'unsignedBigInteger',
                    'nullable' => true,
                    'index' => true,
                    'searchable' => false,
                    'form' => 'number',
                ];
                $relations[] = [
                    'name' => $parsed['name'],
                    'type' => 'morphTo',
                ];

                continue;
            }

            $columns[] = $parsed;

            if ($parsed['type'] === 'foreignId') {
                $relations[] = [
                    'name' => $parsed['relation'],
                    'type' => 'belongsTo',
                    'model' => $parsed['related'],
                    'foreign' => $parsed['name'],
                ];
            }
        }

        if ($entity['slug']) {
            $columns[] = [
                'name' => 'slug',
                'type' => 'string',
                'nullable' => true,
                'unique' => true,
                'searchable' => true,
                'form' => 'text',
            ];
        }

        foreach ($entity['has'] as $has) {
            [$name, $related, $foreign] = array_pad(explode(':', $has), 3, null);
            $relations[] = [
                'name' => $name,
                'type' => 'hasMany',
                'model' => $related,
                'foreign' => $foreign ?: null,
            ];
        }

        return [
            'table' => $entity['table'],
            'model' => $entity['model'],
            'module' => $entity['module'],
            'group' => $entity['group'],
            'icon' => $entity['icon'],
            'label' => $entity['label'],
            'sort' => ($index + 1) * 10,
            'slug' => $entity['slug'],
            'route_key' => $entity['routeKey'],
            'buyer' => $entity['buyer'],
            'lock' => $entity['lock'],
            'audit' => $entity['audit'],
            'columns' => $columns,
            'relations' => $relations,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function parseColumn(string $dsl): array
    {
        $parts = explode(':', $dsl);
        $name = array_shift($parts);
        $type = (string) array_shift($parts);
        $nullable = false;
        $default = null;
        $hasDefault = false;
        $precision = 12;
        $scale = 2;
        $options = [];
        $flags = [];
        $table = null;
        $relation = null;

        if (str_contains($type, '=')) {
            [$type, $eq] = explode('=', $type, 2);
            $hasDefault = true;
            $default = $eq;
        }

        if (str_ends_with($type, '?')) {
            $nullable = true;
            $type = substr($type, 0, -1);
        }

        foreach ($parts as $part) {
            if (str_starts_with($part, 'default=')) {
                $hasDefault = true;
                $default = substr($part, 8);

                continue;
            }

            if ($type === 'dec' && str_contains($part, ',')) {
                [$precision, $scale] = array_map('intval', explode(',', $part, 2));

                continue;
            }

            if ($type === 'select' && str_contains($part, '|')) {
                $options = explode('|', $part);

                continue;
            }

            if ($type === 'fk' && $table === null && ! in_array($part, ['unique', 'search', 'index'], true)) {
                if (str_contains($part, '@')) {
                    [$table, $relation] = explode('@', $part, 2);
                } else {
                    $table = $part;
                }

                continue;
            }

            $flags[] = $part;
        }

        $column = [
            'name' => $name,
            'nullable' => $nullable,
            'unique' => in_array('unique', $flags, true),
            'index' => in_array('index', $flags, true),
            'searchable' => in_array('search', $flags, true) || in_array($name, ['name', 'title', 'code', 'email', 'status'], true),
            'has_default' => $hasDefault,
            'default' => $default,
            'options' => $options,
        ];

        return match ($type) {
            'string' => array_merge($column, ['type' => 'string', 'form' => 'text']),
            'text' => array_merge($column, ['type' => 'text', 'form' => 'textarea']),
            'bool' => array_merge($column, ['type' => 'boolean', 'form' => 'toggle', 'nullable' => false, 'has_default' => true, 'default' => ($default ?? '0') === '1']),
            'int' => array_merge($column, ['type' => 'integer', 'form' => 'number', 'default' => $hasDefault ? (int) $default : null]),
            'uint' => array_merge($column, ['type' => 'unsignedBigInteger', 'form' => 'number']),
            'dec' => array_merge($column, ['type' => 'decimal', 'form' => 'decimal', 'precision' => $precision, 'scale' => $scale, 'default' => $hasDefault ? $default : null]),
            'date' => array_merge($column, ['type' => 'date', 'form' => 'date']),
            'datetime' => array_merge($column, ['type' => 'datetime', 'form' => 'datetime']),
            'json' => array_merge($column, ['type' => 'json', 'form' => 'json', 'nullable' => true]),
            'select' => array_merge($column, ['type' => 'string', 'form' => 'select']),
            'morph' => ['name' => $name, 'type' => 'morph'],
            'fk' => array_merge($column, [
                'type' => 'foreignId',
                'form' => 'belongsTo',
                'table' => $table,
                'related' => Str::studly(Str::singular((string) $table)),
                'relation' => $relation ?: Str::camel(Str::beforeLast($name, '_id')),
                'constrained' => true,
            ]),
            default => throw new RuntimeException("Unknown column type [{$type}] in [{$dsl}]."),
        };
    }
}
