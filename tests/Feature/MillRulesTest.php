<?php

namespace Tests\Feature;

use App\Models\Buyer;
use App\Models\BuyerOrder;
use App\Models\DyeBatch;
use App\Models\DyeParameter;
use App\Models\Inspection;
use App\Models\InspectionDefect;
use App\Models\InventoryLot;
use App\Models\Material;
use App\Models\ModuleToggle;
use App\Models\ProcessIssue;
use App\Models\ShadeCard;
use App\Models\ShadeReading;
use App\Models\Style;
use App\Models\User;
use App\Models\YarnLot;
use App\Services\StockService;
use App\Services\TraceabilityService;
use App\Services\WorkOrderService;
use App\Support\Modules;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MillRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_weak_indigo_box_opens_a_streak_issue(): void
    {
        $card = ShadeCard::query()->create([
            'code' => 'IND-T',
            'name' => 'Test card',
            'method' => 'rope',
            'standard_l' => 22,
            'standard_a' => -1,
            'standard_b' => -8,
            'ph_min' => 11.2,
            'ph_max' => 11.8,
            'orp_min' => -860,
            'orp_max' => -740,
            'concentration_gl' => 2.4,
        ]);

        $batch = DyeBatch::query()->create([
            'batch_no' => 'DYE-T-1',
            'trace_code' => 'DYE-T-1',
            'method' => 'rope',
            'shade_card_id' => $card->id,
            'target_concentration_gl' => 2.4,
            'status' => 'running',
        ]);

        DyeParameter::query()->create([
            'dye_batch_id' => $batch->id,
            'recorded_at' => now(),
            'station' => 'box-4',
            'ph' => 10.2,
            'orp_mv' => -640,
            'concentration_gl' => 1.7,
            'temperature_c' => 40,
            'speed_mpm' => 28,
        ]);

        ShadeReading::query()->create([
            'dye_batch_id' => $batch->id,
            'position' => 'head',
            'l_star' => 22,
            'a_star' => -1,
            'b_star' => -8,
        ]);

        ShadeReading::query()->create([
            'dye_batch_id' => $batch->id,
            'position' => 'tail',
            'l_star' => 27,
            'a_star' => 0,
            'b_star' => -5,
        ]);

        $this->assertTrue(ProcessIssue::query()->where('code', 'streaky-dyeing')->exists());
        $this->assertTrue(ProcessIssue::query()->where('code', 'shade-head-tail')->exists());
        $this->assertGreaterThan(1, (float) ShadeReading::query()->where('position', 'tail')->value('delta_e'));
    }

    public function test_four_point_fails_a_reed_marked_roll(): void
    {
        $inspection = Inspection::query()->create([
            'inspection_no' => 'QC-T',
            'stage' => 'incoming',
            'yards' => 100,
            'width_inch' => 58,
            'accept_limit' => 28,
        ]);

        InspectionDefect::query()->create([
            'inspection_id' => $inspection->id,
            'classification' => 'weaving',
            'defect_code' => 'reed-mark',
            'length_inch' => 12,
        ]);

        for ($i = 0; $i < 11; $i++) {
            InspectionDefect::query()->create([
                'inspection_id' => $inspection->id,
                'classification' => 'weaving',
                'defect_code' => 'reed-mark',
                'length_inch' => 10,
            ]);
        }

        $inspection->refresh();

        $this->assertSame('fail', $inspection->result);
        $this->assertGreaterThan(28, (float) $inspection->points_per_100);
        $this->assertSame(4, (int) InspectionDefect::query()->first()->points);
    }

    public function test_stock_issues_the_oldest_lot_first(): void
    {
        $material = Material::query()->create([
            'sku' => 'CTN-T',
            'name' => 'Test cotton',
            'type' => 'cotton',
            'uom' => 'kg',
            'reorder_level' => 10,
            'qty_on_hand' => 0,
        ]);

        $stock = app(StockService::class);
        $older = $stock->receive($material, 30, 'OLD', ['received_on' => '2026-01-01']);
        $stock->receive($material->fresh(), 50, 'NEW', ['received_on' => '2026-02-01']);
        $stock->issue($material->fresh(), 40, 'dye set', 'DYE-T');

        $this->assertSame('0.000', (string) InventoryLot::query()->find($older->id)->qty_on_hand);
        $this->assertSame('40.000', (string) InventoryLot::query()->where('lot_no', 'NEW')->value('qty_on_hand'));
        $this->assertSame('40.000', (string) $material->fresh()->qty_on_hand);
    }

    public function test_a_bale_can_be_followed_to_the_yarn_lot(): void
    {
        $material = Material::query()->create([
            'sku' => 'CTN-T2',
            'name' => 'Trace cotton',
            'type' => 'cotton',
            'uom' => 'kg',
        ]);
        $lot = app(StockService::class)->receive($material, 100, 'BALE-1', ['trace_code' => 'CTN-TRACE']);
        $yarn = YarnLot::query()->create([
            'lot_no' => 'Y-1',
            'trace_code' => 'YRN-TRACE',
            'count_ne' => 7,
            'end_use' => 'warp',
        ]);

        $trace = app(TraceabilityService::class);
        $trace->link($lot, $yarn, 100, 'kg');

        $found = $trace->findByCode('YRN-TRACE');
        $this->assertNotNull($found);
        $lineage = $trace->lineage($found);
        $this->assertSame('CTN-TRACE', $lineage['upstream'][0]['trace_code']);
    }

    public function test_a_buyer_order_becomes_seven_work_orders(): void
    {
        $buyer = Buyer::query()->create(['code' => 'B1', 'name' => 'Buyer']);
        $style = Style::query()->create(['code' => 'ST', 'name' => 'Style', 'buyer_id' => $buyer->id]);
        $order = BuyerOrder::query()->create([
            'order_no' => 'PO-1',
            'buyer_id' => $buyer->id,
            'style_id' => $style->id,
            'qty' => 100,
            'status' => 'draft',
        ]);

        $orders = app(WorkOrderService::class)->fromOrder($order);

        $this->assertCount(7, $orders);
        $this->assertSame('confirmed', $order->fresh()->status);
        $this->assertSame('dyeing', $orders->first()->stage);
        $this->assertSame('packing', $orders->last()->stage);
    }

    public function test_a_disabled_module_is_closed_even_for_the_super_admin(): void
    {
        ModuleToggle::query()->create([
            'key' => 'dyeing',
            'name' => 'Dyeing',
            'enabled' => false,
            'sort' => 1,
        ]);
        Modules::forget();

        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('super-admin'));

        $this->assertFalse($user->can('viewAny', DyeBatch::class));
    }

    public function test_public_pages_and_an_inquiry_round_trip(): void
    {
        $this->get('/')->assertOk();
        $this->get('/process')->assertOk();
        $this->get('/quality')->assertOk();
        $this->get('/sustainability')->assertOk();
        $this->get('/products')->assertOk();
        $this->get('/gallery')->assertOk();
        $this->get('/careers')->assertOk();
        $this->get('/blog')->assertOk();
        $this->get('/contact')->assertOk();
        $this->from('/')->get('/locale/bn')->assertRedirect('/');

        $this->post('/contact', [
            'name' => 'Desk',
            'email' => 'desk@example.test',
            'subject' => 'Shade',
            'message' => 'Need a rope-dyed 12.5 oz.',
        ])->assertRedirect('/contact');

        $this->assertDatabaseHas('inquiries', ['email' => 'desk@example.test']);
    }
}
