<?php

namespace Database\Seeders;

use App\Domain\DenimKnowledge;
use App\Domain\Schema;
use App\Models\Buyer;
use App\Models\BuyerOrder;
use App\Models\Bundle;
use App\Models\CuttingMarker;
use App\Models\Department;
use App\Models\DyeBatch;
use App\Models\DyeParameter;
use App\Models\Employee;
use App\Models\EmployeeSkill;
use App\Models\FabricProduct;
use App\Models\FinishBatch;
use App\Models\GalleryItem;
use App\Models\HourlyOutput;
use App\Models\Inquiry;
use App\Models\Inspection;
use App\Models\InspectionDefect;
use App\Models\InventoryLot;
use App\Models\Invoice;
use App\Models\InvoiceLine;
use App\Models\JobOpening;
use App\Models\KnowledgeArticle;
use App\Models\Machine;
use App\Models\MachineStoppage;
use App\Models\MaintenanceOrder;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\ModuleToggle;
use App\Models\OrderLine;
use App\Models\Payment;
use App\Models\Post;
use App\Models\ProductionLine;
use App\Models\Sample;
use App\Models\Setting;
use App\Models\ShadeCard;
use App\Models\ShadeReading;
use App\Models\Shift;
use App\Models\Style;
use App\Models\StyleCosting;
use App\Models\Supplier;
use App\Models\TechPack;
use App\Models\User;
use App\Models\WashBatch;
use App\Models\WashRecipe;
use App\Models\WeaveDefect;
use App\Models\WeaveProduction;
use App\Models\YarnLot;
use App\Services\DocumentIntake;
use App\Services\StockService;
use App\Services\TraceabilityService;
use App\Services\WorkOrderService;
use App\Support\Modules;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ImranDevBd\AttendanceHub\Facades\AttendanceHub;
use ImranDevBd\AttendanceHub\Models\AttendanceDevice;
use ImranDevBd\AttendanceHub\Models\AttendanceLog;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Throwable;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::beginTransaction();

        try {
            $this->seed();
            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            throw $exception;
        }
    }

    private function seed(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Modules::forget();

        $sort = 0;

        foreach (config('denimforge.modules') as $key => $module) {
            ModuleToggle::query()->create([
                'key' => $key,
                'name' => $module['name'],
                'description' => $module['description'],
                'enabled' => true,
                'sort' => $sort++,
            ]);
        }

        $tables = collect(Schema::all())->pluck('table');

        foreach ($tables as $table) {
            foreach (['view', 'create', 'update', 'delete'] as $ability) {
                Permission::findOrCreate($table.'.'.$ability);
            }
        }

        $byModule = [];

        foreach (Schema::all() as $entity) {
            $byModule[$entity['module']][] = $entity['table'];
        }

        $grant = function (Role $role, array $tables, array $abilities = ['view', 'create', 'update', 'delete']): void {
            $names = [];

            foreach ($tables as $table) {
                foreach ($abilities as $ability) {
                    $names[] = $table.'.'.$ability;
                }
            }

            $role->givePermissionTo($names);
        };

        $allTables = $tables->all();
        $view = ['view'];

        $roles = [
            'super-admin' => Role::findOrCreate('super-admin'),
            'factory-manager' => Role::findOrCreate('factory-manager'),
            'dyeing-supervisor' => Role::findOrCreate('dyeing-supervisor'),
            'weaving-supervisor' => Role::findOrCreate('weaving-supervisor'),
            'qc' => Role::findOrCreate('qc'),
            'production' => Role::findOrCreate('production'),
            'hr' => Role::findOrCreate('hr'),
            'finance' => Role::findOrCreate('finance'),
            'buyer' => Role::findOrCreate('buyer'),
            'maintenance' => Role::findOrCreate('maintenance'),
            'store-keeper' => Role::findOrCreate('store-keeper'),
        ];

        $grant($roles['super-admin'], $allTables);
        $grant($roles['factory-manager'], $allTables);
        $grant($roles['dyeing-supervisor'], array_merge($byModule['dyeing'], $byModule['quality'], $byModule['automation']));
        $grant($roles['dyeing-supervisor'], array_merge($byModule['inventory'], $byModule['yarn'], $byModule['planning']), $view);
        $grant($roles['weaving-supervisor'], $byModule['weaving']);
        $grant($roles['weaving-supervisor'], array_merge($byModule['yarn'], $byModule['planning'], $byModule['quality']), $view);
        $grant($roles['qc'], $byModule['quality']);
        $grant($roles['qc'], array_merge($byModule['dyeing'], $byModule['weaving'], $byModule['finishing'], $byModule['washing']), $view);
        $grant($roles['production'], array_merge($byModule['planning'], $byModule['weaving'], $byModule['finishing'], $byModule['cutting'], $byModule['sewing'], $byModule['washing']));
        $grant($roles['production'], $byModule['quality'], $view);
        $grant($roles['hr'], $byModule['hr']);
        $grant($roles['finance'], $byModule['finance']);
        $grant($roles['finance'], array_merge($byModule['commercial'], $byModule['planning']), $view);
        $grant($roles['buyer'], ['buyer_orders', 'order_lines', 'samples', 'work_orders', 'invoices', 'payments', 'styles'], $view);
        $grant($roles['maintenance'], $byModule['maintenance']);
        $grant($roles['store-keeper'], $byModule['inventory']);
        $grant($roles['store-keeper'], $byModule['yarn'], $view);

        $accounts = [
            'super-admin' => ['Mill Super Admin', 'super@denimforge.test'],
            'factory-manager' => ['Factory Manager', 'manager@denimforge.test'],
            'dyeing-supervisor' => ['Dyeing Supervisor', 'dyeing@denimforge.test'],
            'weaving-supervisor' => ['Weaving Supervisor', 'weaving@denimforge.test'],
            'qc' => ['Quality Controller', 'qc@denimforge.test'],
            'production' => ['Production Planner', 'production@denimforge.test'],
            'hr' => ['HR Officer', 'hr@denimforge.test'],
            'finance' => ['Finance Officer', 'finance@denimforge.test'],
            'maintenance' => ['Maintenance Engineer', 'maintenance@denimforge.test'],
            'store-keeper' => ['Store Keeper', 'store@denimforge.test'],
        ];

        $users = [];

        foreach ($accounts as $role => [$name, $email]) {
            $users[$role] = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => 'password',
                'locale' => 'en',
                'is_active' => true,
            ]);
            $users[$role]->assignRole($role);
        }

        $harbor = Buyer::query()->create([
            'code' => 'HARBOR',
            'name' => 'Harbor & Co',
            'country' => 'Netherlands',
            'contact_name' => 'Buyer Desk',
            'email' => 'desk@harbor-co.test',
            'incoterm' => 'FOB Chattogram',
            'payment_terms' => 'LC at sight',
        ]);

        $northline = Buyer::query()->create([
            'code' => 'NORTH',
            'name' => 'Northline Denim',
            'country' => 'Germany',
            'contact_name' => 'Buying Office',
            'email' => 'office@northline.test',
            'incoterm' => 'FOB Chattogram',
            'payment_terms' => 'TT 30 days',
        ]);

        $buyerUser = User::query()->create([
            'name' => 'Harbor Buyer',
            'email' => 'buyer@denimforge.test',
            'password' => 'password',
            'locale' => 'en',
            'is_active' => true,
            'buyer_id' => $harbor->id,
        ]);
        $buyerUser->assignRole('buyer');

        $issued = $users['super-admin']->createToken('public-demo');
        $issued->accessToken->forceFill(['token' => hash('sha256', 'denimforge-demo-token')])->save();

        Setting::query()->create([
            'key' => 'demo_api_token',
            'value' => $issued->accessToken->getKey().'|denimforge-demo-token',
            'group' => 'api',
        ]);

        Setting::query()->create([
            'key' => 'plant_shift_note',
            'value' => 'Rope range runs 24 hours. Sheet range runs A and B shift.',
            'group' => 'mill',
        ]);

        $shifts = [
            'A' => Shift::query()->create(['name' => 'A shift', 'starts_at' => '06:00', 'ends_at' => '14:00']),
            'B' => Shift::query()->create(['name' => 'B shift', 'starts_at' => '14:00', 'ends_at' => '22:00']),
            'C' => Shift::query()->create(['name' => 'C shift', 'starts_at' => '22:00', 'ends_at' => '06:00', 'is_night' => true]),
        ];

        $departments = [];

        foreach ([
            'DYE' => 'Indigo dyeing',
            'WEV' => 'Weaving',
            'FIN' => 'Finishing',
            'SEW' => 'Sewing',
            'QC' => 'Quality',
            'MNT' => 'Maintenance',
            'HR' => 'Human resources',
            'STR' => 'Raw store',
        ] as $code => $name) {
            $departments[$code] = Department::query()->create(['code' => $code, 'name' => $name, 'name_bn' => null]);
        }

        $dyer = Employee::query()->create([
            'employee_no' => 'E-014',
            'name' => 'Rope Range Operator',
            'department_id' => $departments['DYE']->id,
            'shift_id' => $shifts['A']->id,
            'user_id' => $users['dyeing-supervisor']->id,
            'designation' => 'Range operator',
            'skill_group' => 'indigo_dyeing',
            'phone' => '01700000014',
            'joined_on' => '2018-03-01',
        ]);

        EmployeeSkill::query()->create([
            'employee_id' => $dyer->id,
            'skill' => 'rope_dyeing',
            'level' => 5,
            'notes' => 'Leads the night recovery when ORP drifts.',
        ]);

        Employee::query()->create([
            'employee_no' => 'E-088',
            'name' => 'Loom Technician',
            'department_id' => $departments['WEV']->id,
            'shift_id' => $shifts['B']->id,
            'designation' => 'Weaver',
            'skill_group' => 'weaving',
            'joined_on' => '2020-07-12',
        ]);

        Employee::query()->create([
            'employee_no' => 'E-120',
            'name' => 'Wash Technician',
            'department_id' => $departments['FIN']->id,
            'shift_id' => $shifts['A']->id,
            'designation' => 'Laundry technician',
            'skill_group' => 'wash_tech',
            'joined_on' => '2019-11-04',
        ]);

        $cottonSupplier = Supplier::query()->create([
            'code' => 'PADMA',
            'name' => 'Padma Cotton',
            'category' => 'cotton',
            'country' => 'Bangladesh',
            'lead_time_days' => 7,
        ]);

        $yarnSupplier = Supplier::query()->create([
            'code' => 'MEGHNA',
            'name' => 'Meghna Yarn',
            'category' => 'yarn',
            'country' => 'Bangladesh',
            'lead_time_days' => 10,
        ]);

        $chemSupplier = Supplier::query()->create([
            'code' => 'BENGAL',
            'name' => 'Bengal Indigo Chemicals',
            'category' => 'dye',
            'country' => 'India',
            'lead_time_days' => 21,
        ]);

        $categories = [
            'cotton' => MaterialCategory::query()->create(['name' => 'Cotton bales', 'type' => 'cotton']),
            'yarn' => MaterialCategory::query()->create(['name' => 'Yarn', 'type' => 'yarn']),
            'dye' => MaterialCategory::query()->create(['name' => 'Dyes', 'type' => 'dye']),
            'chemical' => MaterialCategory::query()->create(['name' => 'Chemicals', 'type' => 'chemical']),
            'trim' => MaterialCategory::query()->create(['name' => 'Trims', 'type' => 'trim']),
            'spare' => MaterialCategory::query()->create(['name' => 'Spares', 'type' => 'spare']),
        ];

        $stock = app(StockService::class);

        $cotton = Material::query()->create([
            'sku' => 'CTN-MID',
            'name' => 'Middling cotton',
            'material_category_id' => $categories['cotton']->id,
            'supplier_id' => $cottonSupplier->id,
            'type' => 'cotton',
            'uom' => 'kg',
            'reorder_level' => 2000,
            'qty_on_hand' => 0,
            'unit_cost' => 1.85,
        ]);

        $indigo = Material::query()->create([
            'sku' => 'DYE-IND',
            'name' => 'Indigo powder 94%',
            'material_category_id' => $categories['dye']->id,
            'supplier_id' => $chemSupplier->id,
            'type' => 'dye',
            'uom' => 'kg',
            'reorder_level' => 80,
            'qty_on_hand' => 0,
            'unit_cost' => 9.4,
        ]);

        $caustic = Material::query()->create([
            'sku' => 'CH-NAOH',
            'name' => 'Caustic soda',
            'material_category_id' => $categories['chemical']->id,
            'supplier_id' => $chemSupplier->id,
            'type' => 'chemical',
            'uom' => 'kg',
            'reorder_level' => 400,
            'qty_on_hand' => 0,
            'unit_cost' => 0.62,
        ]);

        Material::query()->create([
            'sku' => 'TRM-BTN',
            'name' => 'Antique brass button 17 mm',
            'material_category_id' => $categories['trim']->id,
            'type' => 'trim',
            'uom' => 'gross',
            'reorder_level' => 40,
            'qty_on_hand' => 120,
            'unit_cost' => 3.1,
        ]);

        Material::query()->create([
            'sku' => 'SPR-REED',
            'name' => 'Reed 58 inch 14.5 dent',
            'material_category_id' => $categories['spare']->id,
            'type' => 'spare',
            'uom' => 'pcs',
            'reorder_level' => 2,
            'qty_on_hand' => 4,
            'unit_cost' => 180,
        ]);

        $cottonLot = $stock->receive($cotton, 8400, 'BALE-24016', [
            'trace_code' => 'CTN-24016',
            'unit_cost' => 1.85,
            'received_on' => now()->subDays(40)->toDateString(),
            'location' => 'Bale shed A',
        ]);

        $stock->receive($cotton, 2100, 'BALE-24022', [
            'trace_code' => 'CTN-24022',
            'received_on' => now()->subDays(6)->toDateString(),
            'location' => 'Bale shed A',
        ]);

        $stock->receive($indigo, 260, 'IND-778', [
            'trace_code' => 'IND-778',
            'received_on' => now()->subDays(20)->toDateString(),
            'location' => 'Dye store',
        ]);

        $stock->receive($caustic, 180, 'NAOH-19', [
            'trace_code' => 'NAOH-19',
            'received_on' => now()->subDays(15)->toDateString(),
            'location' => 'Chemical store',
        ]);

        $warpLot = YarnLot::query()->create([
            'lot_no' => 'YRN-7-118',
            'trace_code' => 'YRN-7-118',
            'inventory_lot_id' => $cottonLot->id,
            'supplier_id' => $yarnSupplier->id,
            'count_ne' => 7.2,
            'twist_tpi' => 14.5,
            'strength_rkm' => 16.8,
            'cv_percent' => 1.4,
            'composition' => '100% cotton',
            'end_use' => 'warp',
            'cones' => 480,
            'kg' => 2400,
            'received_on' => now()->subDays(12)->toDateString(),
            'notes' => 'Ring spun, low hairiness, issued to rope dye.',
        ]);

        YarnLot::query()->create([
            'lot_no' => 'YRN-10-044',
            'trace_code' => 'YRN-10-044',
            'supplier_id' => $yarnSupplier->id,
            'count_ne' => 10,
            'twist_tpi' => 16.2,
            'strength_rkm' => 15.1,
            'cv_percent' => 1.8,
            'composition' => '100% cotton',
            'end_use' => 'weft',
            'cones' => 360,
            'kg' => 1600,
            'received_on' => now()->subDays(9)->toDateString(),
        ]);

        $range = Machine::query()->create([
            'code' => 'RNG-01',
            'name' => 'Rope dye range 1',
            'type' => 'dye_range',
            'department_id' => $departments['DYE']->id,
            'status' => 'running',
            'brand' => 'Morrison',
            'criticality' => 'high',
            'installed_on' => '2014-06-01',
            'notes' => '8 boxes, 6 dips, sky oxidation.',
        ]);

        $loom = Machine::query()->create([
            'code' => 'LOOM-18',
            'name' => 'Rapier loom 18',
            'type' => 'loom',
            'department_id' => $departments['WEV']->id,
            'status' => 'running',
            'brand' => 'Picanol',
            'criticality' => 'high',
            'installed_on' => '2017-01-15',
        ]);

        Machine::query()->create([
            'code' => 'SAN-02',
            'name' => 'Sanforizer 2',
            'type' => 'sanforizer',
            'department_id' => $departments['FIN']->id,
            'status' => 'idle',
            'criticality' => 'high',
        ]);

        Machine::query()->create([
            'code' => 'WSH-03',
            'name' => 'Front load washer 3',
            'type' => 'washer',
            'department_id' => $departments['FIN']->id,
            'status' => 'idle',
            'criticality' => 'medium',
        ]);

        foreach ([now()->subDays(2), now()->subDays(4), now()->subDays(6)] as $index => $when) {
            MachineStoppage::query()->create([
                'machine_id' => $loom->id,
                'process' => 'weaving',
                'reason' => $index === 0 ? 'Warp break' : 'Weft stop',
                'started_at' => $when,
                'ended_at' => (clone $when)->addMinutes(18),
                'minutes' => 18,
                'notes' => 'Logged for the predictive maintenance rule.',
            ]);
        }

        MaintenanceOrder::query()->create([
            'number' => 'MO-1044',
            'machine_id' => $range->id,
            'type' => 'preventive',
            'status' => 'open',
            'symptom' => 'Box 4 pump vibration rising.',
            'reported_at' => now()->subDay(),
        ]);

        $fabric = FabricProduct::query()->create([
            'name' => '12.5 oz rigid indigo',
            'name_bn' => '১২.৫ আউন্স রিজিড ইন্ডিগো',
            'construction' => '3/1 RHT',
            'weave' => '3/1 right-hand twill',
            'weight_oz' => 12.5,
            'weight_gsm' => 424,
            'composition' => '100% cotton',
            'shade' => 'Indigo rope',
            'finish' => 'Sanforized, skew controlled',
            'stretch' => false,
            'width_inch' => 58,
            'description' => 'Classic rope-dyed warp, white weft, dry rigid hand. Built for a deep washdown that stays blue in the seam.',
            'description_bn' => 'রজ্জু রং করা ওয়ার্প, সাদা ওয়েফট। ধোয়ার পর সিমে নীল থাকে।',
            'sustainability_note' => 'Indigo recovery on the range and a measured enzyme wash. Water per kilogram is logged on the wash batch.',
            'is_published' => true,
        ]);

        FabricProduct::query()->create([
            'name' => '11 oz comfort stretch',
            'construction' => '3/1 RHT stretch',
            'weave' => '3/1 twill',
            'weight_oz' => 11,
            'weight_gsm' => 373,
            'composition' => '98% cotton 2% elastane',
            'shade' => 'Vintage sulfur bottom',
            'finish' => 'Mercerized, sanforized',
            'stretch' => true,
            'width_inch' => 57,
            'description' => 'Rope indigo over a sulfur bottom, with a light elastane weft for a five-pocket that recovers.',
            'is_published' => true,
        ]);

        $style = Style::query()->create([
            'code' => 'HF-501',
            'name' => 'Harbor five-pocket rigid',
            'buyer_id' => $harbor->id,
            'fabric_product_id' => $fabric->id,
            'season' => 'SS27',
            'wash_type' => 'Enzyme rinse',
            'status' => 'bulk',
            'description' => 'Straight fit, copper rivets, no whisker. Shade is the rope-dyed standard card IND-12.',
        ]);

        TechPack::query()->create([
            'style_id' => $style->id,
            'version' => 'C',
            'status' => 'approved',
            'approved_at' => now()->subDays(18),
            'notes' => 'Approved fit. No contrast stitch.',
        ]);

        $card = ShadeCard::query()->create([
            'code' => 'IND-12',
            'name' => 'Indigo 12 rope standard',
            'style_id' => $style->id,
            'method' => 'rope',
            'standard_l' => 22.0,
            'standard_a' => -1.2,
            'standard_b' => -8.0,
            'ph_min' => 11.2,
            'ph_max' => 11.8,
            'orp_min' => -860,
            'orp_max' => -740,
            'temp_min' => 20,
            'temp_max' => 32,
            'concentration_gl' => 2.4,
            'dips' => 6,
            'notes' => 'Head-to-tail delta E must stay inside 1.0.',
        ]);

        $order = BuyerOrder::query()->create([
            'order_no' => 'PO-24088',
            'buyer_id' => $harbor->id,
            'style_id' => $style->id,
            'season' => 'SS27',
            'qty' => 8000,
            'uom' => 'pcs',
            'ex_factory' => now()->addDays(45)->toDateString(),
            'destination' => 'Rotterdam',
            'status' => 'draft',
            'currency' => 'USD',
            'contract_price' => 8.4,
        ]);

        OrderLine::query()->create([
            'buyer_order_id' => $order->id,
            'buyer_id' => $harbor->id,
            'style_id' => $style->id,
            'color' => 'Indigo',
            'size' => '32',
            'qty' => 2000,
            'unit_price' => 8.4,
        ]);

        Sample::query()->create([
            'sample_no' => 'PP-501-C',
            'style_id' => $style->id,
            'buyer_id' => $harbor->id,
            'buyer_order_id' => $order->id,
            'type' => 'pp',
            'status' => 'approved',
            'comments' => 'Washdown and hand feel approved. Do not lighten the seam.',
            'approved_at' => now()->subDays(16),
        ]);

        BuyerOrder::query()->create([
            'order_no' => 'PO-24102',
            'buyer_id' => $northline->id,
            'style_id' => $style->id,
            'season' => 'AW27',
            'qty' => 3000,
            'status' => 'confirmed',
            'currency' => 'USD',
            'contract_price' => 8.1,
        ]);

        foreach (DenimKnowledge::articles() as $article) {
            KnowledgeArticle::query()->create([
                'code' => $article['code'],
                'title' => $article['title'],
                'title_bn' => $article['titleBn'],
                'category' => $article['category'],
                'symptoms' => $article['symptoms'],
                'causes' => $article['causes'],
                'checks' => $article['checks'],
                'remedies' => $article['remedies'],
                'parameters' => $article['parameters'],
            ]);
        }

        $workOrders = app(WorkOrderService::class)->fromOrder($order->fresh());
        $dyeOrder = $workOrders->firstWhere('stage', 'dyeing');
        $weaveOrder = $workOrders->firstWhere('stage', 'weaving');
        $cutOrder = $workOrders->firstWhere('stage', 'cutting');

        $good = DyeBatch::query()->create([
            'batch_no' => 'DYE-R-24088',
            'trace_code' => 'DYE-R-24088',
            'method' => 'rope',
            'shade_card_id' => $card->id,
            'style_id' => $style->id,
            'work_order_id' => $dyeOrder?->id,
            'machine_id' => $range->id,
            'yarn_lot_id' => $warpLot->id,
            'operator_id' => $dyer->id,
            'set_length_m' => 4200,
            'actual_length_m' => 4180,
            'target_concentration_gl' => 2.4,
            'status' => 'completed',
            'started_at' => now()->subDays(3),
            'ended_at' => now()->subDays(3)->addHours(6),
            'notes' => 'Held speed. Continuous feed.',
        ]);

        DyeParameter::query()->create([
            'dye_batch_id' => $good->id,
            'recorded_at' => now()->subDays(3),
            'station' => 'box-4',
            'ph' => 11.5,
            'orp_mv' => -800,
            'concentration_gl' => 2.4,
            'temperature_c' => 26,
            'speed_mpm' => 22,
            'oxidation_seconds' => 90,
            'dips' => 6,
            'caustic_gl' => 3.2,
            'hydrosulfite_gl' => 2.1,
            'recorded_by_id' => $dyer->id,
        ]);

        foreach (['head' => 22.1, 'tail' => 22.3] as $position => $lightness) {
            ShadeReading::query()->create([
                'dye_batch_id' => $good->id,
                'position' => $position,
                'l_star' => $lightness,
                'a_star' => -1.1,
                'b_star' => -8.0,
            ]);
        }

        $bad = DyeBatch::query()->create([
            'batch_no' => 'DYE-R-24091',
            'trace_code' => 'DYE-R-24091',
            'method' => 'rope',
            'shade_card_id' => $card->id,
            'style_id' => $style->id,
            'work_order_id' => $dyeOrder?->id,
            'machine_id' => $range->id,
            'yarn_lot_id' => $warpLot->id,
            'operator_id' => $dyer->id,
            'set_length_m' => 3800,
            'target_concentration_gl' => 2.4,
            'status' => 'running',
            'started_at' => now()->subHours(2),
            'notes' => 'Tail is running light. Range speed was changed after a creel stop.',
        ]);

        DyeParameter::query()->create([
            'dye_batch_id' => $bad->id,
            'recorded_at' => now()->subHour(),
            'station' => 'box-4',
            'ph' => 10.2,
            'orp_mv' => -640,
            'concentration_gl' => 1.7,
            'temperature_c' => 41,
            'speed_mpm' => 28,
            'oxidation_seconds' => 55,
            'dips' => 6,
            'recorded_by_id' => $dyer->id,
            'remarks' => 'Circulation on box 4 looks weak.',
        ]);

        ShadeReading::query()->create([
            'dye_batch_id' => $bad->id,
            'position' => 'head',
            'l_star' => 22.4,
            'a_star' => -1.0,
            'b_star' => -7.8,
            'photo_path' => 'shades/dye-r-24091-head.jpg',
            'notes' => 'Head is close to the card.',
        ]);

        ShadeReading::query()->create([
            'dye_batch_id' => $bad->id,
            'position' => 'tail',
            'l_star' => 27.2,
            'a_star' => -0.2,
            'b_star' => -5.4,
            'notes' => 'Tail is light. Streak visible on rope 6.',
        ]);

        $roll = WeaveProduction::query()->create([
            'roll_no' => 'WV-4412',
            'trace_code' => 'WV-4412',
            'work_order_id' => $weaveOrder?->id,
            'machine_id' => $loom->id,
            'warp_yarn_lot_id' => $warpLot->id,
            'meters' => 980,
            'epi' => 68,
            'ppi' => 44,
            'width_inch' => 58,
            'status' => 'doffed',
            'produced_on' => now()->subDay()->toDateString(),
        ]);

        WeaveDefect::query()->create([
            'weave_production_id' => $roll->id,
            'defect' => 'broken_end',
            'root_cause' => 'Yarn CV on the selvedge cone.',
            'remedy' => 'Marked the meter. Slow the loom and replace the cone before the next beam.',
            'meters' => 412,
        ]);

        $finish = FinishBatch::query()->create([
            'batch_no' => 'FIN-220',
            'trace_code' => 'FIN-220',
            'work_order_id' => $workOrders->firstWhere('stage', 'finishing')?->id,
            'process' => 'sanforizing',
            'input_m' => 960,
            'output_m' => 930,
            'width_inch' => 57.5,
            'weight_gsm' => 418,
            'shrinkage_length' => -2.1,
            'shrinkage_width' => -1.4,
            'skew_percent' => 1.6,
            'status' => 'done',
            'processed_on' => now()->toDateString(),
        ]);

        $marker = CuttingMarker::query()->create([
            'marker_no' => 'MK-501-32',
            'style_id' => $style->id,
            'work_order_id' => $cutOrder?->id,
            'fabric_m' => 186,
            'marker_efficiency' => 86.4,
            'lay_count' => 40,
            'pcs' => 480,
            'consumption_m' => 1.42,
        ]);

        $bundle = Bundle::query()->create([
            'code' => 'BDL-32-30',
            'trace_code' => 'BDL-32-30',
            'cutting_marker_id' => $marker->id,
            'style_id' => $style->id,
            'size' => '32',
            'color' => 'Indigo',
            'qty' => 30,
            'status' => 'in_sewing',
        ]);

        $line = ProductionLine::query()->create([
            'code' => 'LINE-2',
            'name' => 'Five-pocket line 2',
            'target_per_hour' => 40,
        ]);

        HourlyOutput::query()->create([
            'production_line_id' => $line->id,
            'style_id' => $style->id,
            'work_order_id' => $workOrders->firstWhere('stage', 'sewing')?->id,
            'hour_start' => now()->startOfHour(),
            'target' => 40,
            'actual' => 31,
            'operators' => 28,
        ]);

        $recipe = WashRecipe::query()->create([
            'name' => 'Enzyme rinse rigid',
            'type' => 'enzyme',
            'temperature_c' => 45,
            'time_minutes' => 18,
            'liquor_ratio' => '1:8',
            'ph' => 6.5,
            'water_liters_per_kg' => 8,
            'chemicals' => [
                ['name' => 'Desize enzyme', 'g_l' => 1.2],
                ['name' => 'Cellulase', 'g_l' => 0.6],
            ],
            'steps' => 'Desize until the iodine test is clear. Enzyme 18 minutes. Two rinses. Softener only in the last rinse.',
        ]);

        $wash = WashBatch::query()->create([
            'batch_no' => 'WSH-077',
            'trace_code' => 'WSH-077',
            'wash_recipe_id' => $recipe->id,
            'work_order_id' => $workOrders->firstWhere('stage', 'washing')?->id,
            'qty_pcs' => 240,
            'shade_approved' => true,
            'handfeel' => 'Dry, seam still blue',
            'shrinkage_length' => -2.4,
            'shrinkage_width' => -1.8,
            'water_m3' => 4.6,
            'chemical_kg' => 3.1,
            'status' => 'approved',
            'washed_on' => now()->toDateString(),
        ]);

        $inspection = Inspection::query()->create([
            'inspection_no' => 'QC-4P-190',
            'stage' => 'final',
            'work_order_id' => $weaveOrder?->id,
            'style_id' => $style->id,
            'yards' => 100,
            'width_inch' => 58,
            'accept_limit' => 28,
            'result' => 'pending',
            'inspected_at' => now(),
            'notes' => 'Incoming greige from WV-4412.',
        ]);

        for ($i = 1; $i <= 12; $i++) {
            InspectionDefect::query()->create([
                'inspection_id' => $inspection->id,
                'classification' => 'weaving',
                'defect_code' => 'reed-mark',
                'length_inch' => 10,
                'location' => 'meter '.($i * 7),
                'notes' => 'Reed mark repeating at the same dent.',
            ]);
        }

        StyleCosting::query()->create([
            'style_id' => $style->id,
            'currency' => 'USD',
            'fabric_cost' => 3.15,
            'dyeing_cost' => 0.72,
            'weaving_cost' => 0.64,
            'washing_cost' => 0.48,
            'labor_cost' => 1.35,
            'trim_cost' => 0.41,
            'overhead' => 0.55,
            'quoted_price' => 8.4,
            'notes' => 'Rigid enzyme rinse, no laser.',
        ]);

        $invoice = Invoice::query()->create([
            'number' => 'INV-24088',
            'buyer_id' => $harbor->id,
            'buyer_order_id' => $order->id,
            'invoice_date' => now()->toDateString(),
            'currency' => 'USD',
            'subtotal' => 16800,
            'freight' => 640,
            'total' => 17440,
            'status' => 'partial',
            'notes' => 'First shipment, 2,000 pieces.',
        ]);

        InvoiceLine::query()->create([
            'invoice_id' => $invoice->id,
            'description' => 'HF-501 indigo rigid, size run',
            'qty' => 2000,
            'unit_price' => 8.4,
            'amount' => 16800,
        ]);

        Payment::query()->create([
            'invoice_id' => $invoice->id,
            'buyer_id' => $harbor->id,
            'amount' => 8000,
            'paid_on' => now()->toDateString(),
            'method' => 'tt',
            'reference' => 'TT-88421',
        ]);

        $trace = app(TraceabilityService::class);
        $trace->link($cottonLot, $warpLot, 2400, 'kg', 'Bale issued to spinning then warp cones');
        $trace->link($warpLot, $bad, 860, 'kg', 'Warp cones on the running set');
        $trace->link($good, $roll, 980, 'm', 'Dyed warp woven');
        $trace->link($roll, $finish, 960, 'm', 'Greige to sanfor');
        $trace->link($finish, $bundle, 30, 'pcs', 'Cut and bundled');
        $trace->link($bundle, $wash, 30, 'pcs', 'Bundle through laundry');

        Post::query()->create([
            'title' => 'Rope range recovery after a creel stop',
            'excerpt' => 'Why a soaked rope needs a leader, and what the lab reads before the range starts again.',
            'body' => 'A rope that sits in the creel while the range is down does not dye like the rope that was moving. The mill rule is simple: leader cloth, fresh circulation, then a head reading before the set is released to weaving.',
            'published_at' => now()->subDays(2),
            'is_published' => true,
        ]);

        Post::query()->create([
            'title' => 'Water on the enzyme rinse',
            'title_bn' => 'এনজাইম রিন্সের পানি',
            'excerpt' => 'The laundry logs liters and chemical kilograms on every approved wash batch.',
            'body' => 'Shade approval is not only a color call. The wash batch records water, chemical weight, shrinkage, and hand feel. A lot that misses the iodine test does not go to enzyme.',
            'published_at' => now()->subDay(),
            'is_published' => true,
        ]);

        foreach ([
            ['Rope dye boxes', 'process'],
            ['Loom shed', 'mill'],
            ['Shade library', 'product'],
            ['Finishing line', 'process'],
        ] as [$title, $album]) {
            GalleryItem::query()->create([
                'title' => $title,
                'album' => $album,
                'caption' => $title.' at the Gazipur plant.',
            ]);
        }

        JobOpening::query()->create([
            'title' => 'Indigo dyeing supervisor',
            'title_bn' => 'ইন্ডিগো ডাইং সুপারভাইজার',
            'department' => 'Dyeing',
            'location' => 'Konabari, Gazipur',
            'employment_type' => 'full_time',
            'description' => 'Own the rope range: pH, ORP, concentration, and the shade card. Night shift experience is useful.',
            'is_open' => true,
        ]);

        JobOpening::query()->create([
            'title' => 'Quality inspector, 4-point',
            'department' => 'Quality',
            'location' => 'Konabari, Gazipur',
            'employment_type' => 'full_time',
            'description' => 'Incoming greige and final garments. You will use the 4-point system and the defect library.',
            'is_open' => true,
        ]);

        Inquiry::query()->create([
            'name' => 'Sample Desk',
            'company' => 'Harbor & Co',
            'email' => 'desk@harbor-co.test',
            'subject' => 'SS27 repeat of HF-501',
            'message' => 'Please confirm capacity for 8,000 pieces, ex-factory in 45 days, same shade card.',
            'fabric_product_id' => $fabric->id,
            'status' => 'new',
        ]);

        Storage::disk('local')->put('lab-reports/shade-24088.txt', "Lab dip IND-12\nBatch DYE-R-24088\npH 11.5\nORP -800 mV\nIndigo 2.4 g/L\n");

        app(DocumentIntake::class)->store('lab-reports/shade-24088.txt', [
            'title' => 'Lab dip IND-12',
            'document_type' => 'lab_report',
            'source_type' => DyeBatch::class,
            'source_id' => $good->id,
        ]);

        try {
            $device = AttendanceDevice::query()->create([
                'name' => 'Gate A virtual terminal',
                'provider' => 'fake',
                'model' => 'simulation',
                'ip' => '10.10.0.21',
                'port' => 4370,
                'status' => 'offline',
                'timezone' => 'Asia/Dhaka',
            ]);
            AttendanceHub::sync($device);
        } catch (Throwable) {
            $deviceId = AttendanceDevice::query()->value('id');
            AttendanceLog::query()->create([
                'device_id' => $deviceId,
                'device_user_id' => '101',
                'employee_id' => 'E-014',
                'punched_at' => now()->subHours(2),
                'verify_mode' => 'fingerprint',
                'punch_type' => 'check_in',
                'punch_hash' => hash('sha256', 'denimforge-seed-punch'),
            ]);
        }

        Modules::forget();

        unset($users, $buyerUser);
    }
}
