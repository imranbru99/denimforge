<?php

namespace App\Domain;

/**
 * Shop-floor remedies for the faults that actually stop a denim mill.
 * Used by the seeder, the rule engine, and the AI prompt.
 */
final class DenimKnowledge
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public static function articles(): array
    {
        return [
            self::article(
                'streaky-dyeing',
                'dyeing',
                'Streaky dyeing / warp streaks',
                'স্ট্রিকি ডাইং / ওয়ার্প স্ট্রিক',
                'Longitudinal light-dark lines that follow warp ends. Often appears after the first wash, not on the loom-state roll.',
                'Uneven rope tension from the ball warper, a weak circulation pump in one dye box, oxidized indigo skin on the liquor, a crowned or dirty nip roll, or a rope that sat in the creel while the range stopped.',
                'Compare ropes across the ball warper tension board. Check each box pump and the dye circulation line. Wipe the nip and measure left/center/right pressure. Skim oxidized foam before restart. Confirm squeeze-roll hardness.',
                'Equalize rope tension, restore box circulation, clean and crown the nip, hold range speed, and do not restart a soaked rope without a leader. If the streak repeats every rope, stop and fix the warper before dyeing more sets.',
                ['ph' => '11.2–11.8', 'orp_mv' => '-760 to -860', 'nip' => 'even across width'],
            ),
            self::article(
                'shade-head-tail',
                'dyeing',
                'Shade variation, head to tail',
                'হেড-টু-টেইল শেড ভ্যারিয়েশন',
                'The end of the set is lighter or redder than the head. Delta E against the shade card climbs through the lot.',
                'Indigo, caustic, or hydrosulfite feed not matching consumption. Liquor level drop. A speed change mid-set. Fresh feed mixed in a dead corner of the tank. Sulfur dye exhaustion on long sets.',
                'Titrate indigo g/L, caustic g/L, and hydrosulfite every 30 minutes. Record speed. Compare head, middle, and tail lab readings. Watch ORP drift toward zero.',
                'Continuous dosing, not bucket additions. Hold m/min. If tail delta E exceeds 1.0 against the head, quarantine the tail and re-dip or downgrade. Do not ship head and tail in the same shade lot.',
                ['delta_e_max' => 1.0, 'feed' => 'continuous', 'check_every_min' => 30],
            ),
            self::article(
                'shade-side-center',
                'dyeing',
                'Shade variation, side to center',
                'সাইড-টু-সেন্টার শেড ভ্যারিয়েশন',
                'Selvedges and the body do not match. Sheet-dye ranges show this as a smile across the width; rope dye shows it after rebeaming if one side of the warp sheet was wetter.',
                'Uneven nip, dye-box level sensor stuck, temperature gradient across the sheet, guide-roll skew, or a selvedge that is tighter than the body.',
                'Cut a full-width swatch and read left, center, and right. Check liquor level and temperature at both sides. Look at the squeeze roll for a hollow center.',
                'Regrind or replace the nip, correct guide-roll alignment, and balance box temperature. Accept side-to-center delta E only under 0.8 unless the buyer agrees a larger tolerance.',
                ['delta_e_max' => 0.8],
            ),
            self::article(
                'poor-washdown',
                'washing',
                'Poor washdown / abrasion marks',
                'দুর্বল ওয়াশডাউন / অ্যাব্রেশন মার্ক',
                'The wash does not open the indigo, or it opens in patches and leaves abrasion bars.',
                'Residual size (iodine test still blue-black), enzyme overdose, too much pumice, a tight weave with low hairiness, or a laser pass that burned the same ridge twice.',
                'Iodine test after desize. Compare enzyme percent, time, and pH with the approved recipe. Weigh stones. Check laser DPI and the fixture gap.',
                'Desize until the iodine test is clear. Then reduce enzyme or time before adding more. Lower the stone ratio. Softener belongs in the final rinse, not as a cover for an unfinished desize.',
                ['iodine' => 'clear before enzyme', 'liquor_ratio' => 'as recipe'],
            ),
            self::article(
                'broken-end',
                'weaving',
                'Broken end',
                'ব্রোকেন এন্ড',
                'Warp ends break and the loom stops, or a short end is woven in and shows as a missing line.',
                'Weak or high-CV yarn, warp tension too high, a cut heald or reed dent, a bad knot from drawing-in, or a weaving shed below about 65% RH.',
                'Check Ne, strength RKM, and CV% on the yarn lot. Inspect the heald frame and reed at the stop mark. Read loom tension and shed humidity.',
                'Slow the loom, replace damaged healds, humidify the shed, and do not weave a yarn lot whose strength CV is outside the mill limit. Mark the pick so cutting can dodge it.',
                ['shed_rh' => '65–70%', 'action' => 'mark the meter'],
            ),
            self::article(
                'double-pick',
                'weaving',
                'Double pick',
                'ডাবল পিক',
                'Two weft insertions in the same shed. A bar across the cloth.',
                'Weft stop-motion missed a break, or the weaver restarted without pulling out the partial pick.',
                'Replay the stop. Look at the stop-motion sensor and the restart habit on that loom.',
                'Pull the partial pick before restart. Service the stop motion the same shift. Cutters must bag the meter; do not “blend” a double pick into a garment panel.',
                ['restart' => 'remove partial pick'],
            ),
            self::article(
                'reed-marks',
                'weaving',
                'Reed marks',
                'রিড মার্ক',
                'Vertical lines at a regular dent spacing, still visible after finishing.',
                'Bent reed dent, wrong reed count for the ends per inch, or a warp that was sized too dry and is scraping the reed.',
                'Count EPI against the reed. Sight down the reed. Check size add-on and moisture at the sizing head.',
                'Change the reed. Match reed count to the construction. Raise size moisture so the warp does not polish the dent. A reed mark rarely finishes out; do not promise the buyer that it will.',
                ['check' => 'EPI vs reed'],
            ),
            self::article(
                'slubs',
                'weaving',
                'Slubs',
                'স্লাব',
                'Thick places in warp or weft. Some are deliberate; a random slub in a clear yarn is a defect.',
                'Spinning draft fault, a bad splice, or the wrong yarn lot issued to the loom.',
                'Match the cone ticket to the work order. Open the slub and see if it is one end or a doubled end.',
                'Quarantine the cone lot. If the slub is in the warp, trace it to the yarn lot before the next beam is sized.',
                ['trace' => 'cone ticket to yarn lot'],
            ),
            self::article(
                'uneven-shrinkage',
                'finishing',
                'Uneven shrinkage / skew',
                'অসম সংকোচন / স্কিউ',
                'After wash, one leg twists or the width shrinks more than the length. Residual shrinkage is outside the buyer window, often 0 to -3%.',
                'Sanforizer rubber-belt pressure or overfeed not stable. Weft was already skewed on the loom and the straightener was bypassed. Mercerizer tension differed from side to side.',
                'Mark a square on greige and measure length, width, and diagonal after sanfor and again after garment wash. Read skew percent at both selvedges.',
                'Straighten weft before the sanforizer. Set compression for the tested residual, not for the feel of the cloth. If skew exceeds 2.5% of width, do not cut that roll into legs.',
                ['residual_shrinkage' => '0 to -3%', 'skew_max_percent' => 2.5],
            ),
            self::article(
                'dye-spots',
                'dyeing',
                'Dye spots / stains',
                'ডাই স্পট / দাগ',
                'Dark specks, oil halos, or bronze patches that are not part of the weave.',
                'Undissolved dye, foam carried onto the warp, oil from a bearing, hard water, or sulfur dye bronzing when sodium sulfide is low.',
                'Filter the addition. Check foam, roll oil, and water hardness. For sulfur, test sulfide and look for a bronze sheen on the dry cloth.',
                'Filter liquor, kill foam, degrease the rolls, and hold hardness under 50 ppm. On sulfur, restore sulfide balance and use an anti-bronzing finish. Cut spotted meters out; do not try to wash a bronze patch into an even shade.',
                ['hardness_ppm_max' => 50],
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $parameters
     * @return array<string, mixed>
     */
    private static function article(
        string $code,
        string $category,
        string $title,
        string $titleBn,
        string $symptoms,
        string $causes,
        string $checks,
        string $remedies,
        array $parameters,
    ): array {
        return compact('code', 'category', 'title', 'titleBn', 'symptoms', 'causes', 'checks', 'remedies', 'parameters');
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function find(string $code): ?array
    {
        foreach (self::articles() as $article) {
            if ($article['code'] === $code) {
                return $article;
            }
        }

        return null;
    }
}
