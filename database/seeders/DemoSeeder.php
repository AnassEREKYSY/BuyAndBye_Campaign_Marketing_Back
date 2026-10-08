<?php

namespace Database\Seeders;

use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Demo data so every screen has something to show.
 * Deterministic (seeded RNG) and dependency-free (no Faker), so it also runs with --no-dev.
 *
 * Accounts (password for all: "password"):
 *   brand@kickback.demo     Maison Nour (brand)
 *   coffee@kickback.demo    Atlas Coffee (brand)
 *   creator@kickback.demo   Lina Benali (creator)
 *   admin@kickback.demo     Admin
 */
class DemoSeeder extends Seeder
{
    private CarbonImmutable $now;
    private string $password;

    public function run(): void
    {
        mt_srand(42);
        $this->now = CarbonImmutable::now();
        $this->password = Hash::make('password');

        DB::transaction(function () {
            $this->user('admin@kickback.demo', 'Admin', 'admin');

            // ---- Brands -------------------------------------------------------------
            $nour = $this->user('brand@kickback.demo', 'Maison Nour', 'brand');
            $this->brandProfile($nour, 'Maison Nour', 'Beauty & skincare', 'https://maisonnour.example', 'Clean skincare made in Marrakech with argan and rose.');

            $atlas = $this->user('coffee@kickback.demo', 'Atlas Coffee', 'brand');
            $this->brandProfile($atlas, 'Atlas Coffee', 'Food & drinks', 'https://atlascoffee.example', 'Small-batch coffee roasted in Casablanca.');

            // ---- Creators -----------------------------------------------------------
            $creators = [];
            $people = [
                ['creator@kickback.demo', 'Lina Benali', 'Beauty', 'MA', 'fr', 48200, 91000, 0, 4.8],
                ['yasmine@kickback.demo', 'Yasmine Haddad', 'Lifestyle', 'FR', 'fr', 121000, 54000, 18000, 3.6],
                ['omar@kickback.demo', 'Omar Tazi', 'Food', 'MA', 'ar', 22000, 140000, 0, 6.1],
                ['sara@kickback.demo', 'Sara Lemoine', 'Skincare', 'FR', 'fr', 67000, 0, 32000, 4.1],
                ['karim@kickback.demo', 'Karim Alaoui', 'Coffee & travel', 'MA', 'fr', 15400, 38000, 9100, 5.4],
                ['ines@kickback.demo', 'Inès Martin', 'Fashion', 'BE', 'fr', 203000, 76000, 0, 2.9],
            ];
            foreach ($people as [$email, $name, $niche, $country, $lang, $ig, $tt, $yt, $er]) {
                $u = $this->user($email, $name, 'influencer');
                $slug = Str::slug($name, '');
                DB::table('influencer_profiles')->insert([
                    'user_id' => $u,
                    'niche' => $niche,
                    'instagram_url' => "https://instagram.com/{$slug}",
                    'tiktok_url' => $tt ? "https://tiktok.com/@{$slug}" : null,
                    'youtube_url' => $yt ? "https://youtube.com/@{$slug}" : null,
                    'followers_instagram' => $ig,
                    'followers_tiktok' => $tt ?: null,
                    'followers_youtube' => $yt ?: null,
                    'avg_engagement_rate' => $er,
                    'country_code' => $country,
                    'language' => $lang,
                    'created_at' => $this->now,
                    'updated_at' => $this->now,
                ]);
                $creators[] = $u;
            }
            [$lina, $yasmine, $omar, $sara, $karim, $ines] = $creators;

            // ---- Products -----------------------------------------------------------
            $serum = $this->product($nour, 'Rose & Argan Face Serum', 'Lightweight daily serum with cold-pressed argan oil.', 189, 'https://maisonnour.example/serum');
            $mask = $this->product($nour, 'Rhassoul Clay Mask', 'Purifying clay mask from the Atlas mountains.', 129, 'https://maisonnour.example/mask');
            $beans = $this->product($atlas, 'Atlas Signature Beans 1kg', 'Medium roast, notes of cocoa and dates.', 240, 'https://atlascoffee.example/signature');
            $kit = $this->product($atlas, 'Pour-over Starter Kit', 'Dripper, filters and a 250g bag to get started.', 390, 'https://atlascoffee.example/kit');

            // ---- Campaigns ----------------------------------------------------------
            $c1 = $this->campaign($nour, $serum, 'Summer glow with the Rose Serum', 'Show your morning routine with the serum and share your link.', 'percent', 12, 15000, -75, 30, 'published');
            $c2 = $this->campaign($nour, $mask, 'Rhassoul Sunday reset', 'A calm weekend self-care moment featuring the clay mask.', 'fixed', 25, 8000, -40, 45, 'published');
            $c3 = $this->campaign($nour, $serum, 'Back-to-school skincare', 'Simple 3-step routine for busy mornings.', 'percent', 10, 6000, 10, 70, 'draft');
            $c4 = $this->campaign($atlas, $beans, 'Slow mornings with Atlas', 'Your coffee ritual, filmed in one take.', 'percent', 15, 12000, -85, 20, 'published');
            $c5 = $this->campaign($atlas, $kit, 'Ramadan iftar coffee', 'Coffee after iftar with family and friends.', 'fixed', 40, 5000, -160, -100, 'closed');
            // Open campaign nobody joined yet, so the demo creator has something to apply to.
            $c6 = $this->campaign($atlas, $kit, 'Pour-over at home', 'Teach your audience a simple pour-over in under a minute.', 'fixed', 35, 7000, -2, 60, 'published');

            foreach ([$c1, $c2, $c3, $c4, $c5, $c6] as $c) {
                $this->tiers($c);
            }

            // ---- Applications & collaborations -------------------------------------
            // [campaign, creator, status, daily click base]
            $plan = [
                [$c1, $lina, 'accepted', 22], [$c1, $sara, 'accepted', 14], [$c1, $ines, 'accepted', 9],
                [$c1, $yasmine, 'shortlisted', 0], [$c1, $omar, 'rejected', 0],
                [$c2, $sara, 'accepted', 11], [$c2, $yasmine, 'accepted', 7], [$c2, $lina, 'pending', 0], [$c2, $karim, 'pending', 0],
                [$c4, $karim, 'accepted', 16], [$c4, $omar, 'accepted', 19], [$c4, $lina, 'accepted', 6], [$c4, $ines, 'pending', 0],
                [$c5, $omar, 'accepted', 12], [$c5, $karim, 'accepted', 8],
            ];

            $campaignMeta = DB::table('campaigns')->whereIn('id', [$c1, $c2, $c4, $c5])->get()->keyBy('id');

            foreach ($plan as [$campaignId, $creatorId, $status, $base]) {
                $campaign = $campaignMeta[$campaignId] ?? DB::table('campaigns')->find($campaignId);
                $appliedAt = CarbonImmutable::parse($campaign->start_at)->subDays(mt_rand(3, 9));
                $appId = $this->application($campaignId, $creatorId, $status, $appliedAt);

                if ($status !== 'accepted') {
                    continue;
                }

                $acceptedAt = $appliedAt->addDays(mt_rand(1, 3));
                $collabStatus = $campaign->status === 'closed' ? 'closed' : 'active';
                $collabId = $this->collaboration($campaignId, $campaign->brand_id, $creatorId, $acceptedAt, $collabStatus);
                $linkId = $this->trackingLink($collabId, $campaign->product_id);
                DB::table('promo_codes')->insert([
                    'id' => (string) Str::uuid(),
                    'collaboration_id' => $collabId,
                    'code' => 'KB-' . Str::upper(Str::random(8)),
                    'created_at' => $acceptedAt,
                    'updated_at' => $acceptedAt,
                ]);

                $endAt = min(CarbonImmutable::parse($campaign->end_at), $this->now);
                $this->clicks($linkId, $campaignId, $creatorId, $acceptedAt, $endAt, $base);
                $this->payouts($collabId, $campaignId, $acceptedAt, $endAt);
                $this->conversation($campaignId, $appId, $campaign->brand_id, $creatorId, $acceptedAt);
            }

            $this->notifications($nour, $lina, $c1);
        });
    }

    // ------------------------------------------------------------------ helpers

    private function user(string $email, string $name, string $role): string
    {
        $id = (string) Str::uuid();
        DB::table('users')->insert([
            'id' => $id,
            'email' => $email,
            'password' => $this->password,
            'display_name' => $name,
            'role' => $role,
            'status' => 'active',
            'profile_completed' => true,
            'profile_skipped' => false,
            'email_verified_at' => $this->now,
            'created_at' => $this->now->subDays(200),
            'updated_at' => $this->now,
        ]);

        return $id;
    }

    private function brandProfile(string $userId, string $name, string $industry, string $site, string $about): void
    {
        DB::table('brand_profiles')->insert([
            'user_id' => $userId,
            'brand_name' => $name,
            'website_url' => $site,
            'industry' => $industry,
            'contact_email' => 'hello@' . parse_url($site, PHP_URL_HOST),
            'description' => $about,
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);
    }

    private function product(string $brandId, string $name, string $description, float $price, string $url): string
    {
        $id = (string) Str::uuid();
        DB::table('products')->insert([
            'id' => $id,
            'brand_id' => $brandId,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'currency' => 'MAD',
            'landing_url' => $url,
            'status' => 'active',
            'images' => json_encode([]),
            'created_at' => $this->now->subDays(180),
            'updated_at' => $this->now->subDays(180),
        ]);

        return $id;
    }

    private function campaign(string $brandId, string $productId, string $title, string $objective, string $type, float $value, float $budget, int $startOffset, int $endOffset, string $status): string
    {
        $id = (string) Str::uuid();
        DB::table('campaigns')->insert([
            'id' => $id,
            'brand_id' => $brandId,
            'product_id' => $productId,
            'title' => $title,
            'objective' => $objective,
            'commission_type' => $type,
            'commission_value' => $value,
            'budget' => $budget,
            'start_at' => $this->now->addDays($startOffset)->startOfDay(),
            'end_at' => $this->now->addDays($endOffset)->endOfDay(),
            'status' => $status,
            'created_at' => $this->now->addDays($startOffset - 14),
            'updated_at' => $this->now->addDays($startOffset - 14),
        ]);

        return $id;
    }

    private function tiers(string $campaignId): void
    {
        foreach ([[0, 499, 150], [500, 1499, 600], [1500, null, 1500]] as [$from, $to, $amount]) {
            DB::table('campaign_payout_tiers')->insert([
                'id' => (string) Str::uuid(),
                'campaign_id' => $campaignId,
                'metric' => 'clicks',
                'from_value' => $from,
                'to_value' => $to,
                'payout_amount' => $amount,
                'currency' => 'MAD',
                'created_at' => $this->now,
                'updated_at' => $this->now,
            ]);
        }
    }

    private function application(string $campaignId, string $creatorId, string $status, CarbonImmutable $at): string
    {
        $id = (string) Str::uuid();
        $messages = [
            'My audience loves honest routine content. I can post a reel and two stories.',
            'I already use similar products and would film it in natural light, no heavy edits.',
            'Happy to share a code in my bio for the whole campaign.',
        ];
        DB::table('campaign_applications')->insert([
            'id' => $id,
            'campaign_id' => $campaignId,
            'influencer_id' => $creatorId,
            'message' => $messages[mt_rand(0, 2)],
            'status' => $status,
            'created_at' => $at,
            'updated_at' => $at,
        ]);

        return $id;
    }

    private function collaboration(string $campaignId, string $brandId, string $creatorId, CarbonImmutable $at, string $status): string
    {
        $id = (string) Str::uuid();
        DB::table('collaborations')->insert([
            'id' => $id,
            'campaign_id' => $campaignId,
            'brand_id' => $brandId,
            'influencer_id' => $creatorId,
            'accepted_at' => $at,
            'status' => $status,
            'created_at' => $at,
            'updated_at' => $at,
        ]);

        return $id;
    }

    private function trackingLink(string $collabId, string $productId): string
    {
        $id = (string) Str::uuid();
        DB::table('tracking_links')->insert([
            'id' => $id,
            'collaboration_id' => $collabId,
            'code' => 't_' . Str::lower(Str::random(10)),
            'destination_url' => DB::table('products')->where('id', $productId)->value('landing_url'),
            'created_at' => $this->now,
            'updated_at' => $this->now,
        ]);

        return $id;
    }

    /** Daily clicks with a weekly rhythm, a launch spike and some noise. */
    private function clicks(string $linkId, string $campaignId, string $creatorId, CarbonImmutable $from, CarbonImmutable $to, int $base): void
    {
        $referrers = [
            ['https://www.instagram.com/', 46], ['https://www.tiktok.com/', 28], [null, 12],
            ['https://www.youtube.com/', 7], ['https://www.google.com/', 4], ['https://t.co/', 3],
        ];
        $agents = [
            ['Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Instagram', 44],
            ['Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 Chrome/125.0 Mobile Safari/537.36', 31],
            ['Mozilla/5.0 (Macintosh; Intel Mac OS X 14_5) AppleWebKit/605.1.15 Version/17.5 Safari/605.1.15', 14],
            ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/125.0 Safari/537.36', 11],
        ];

        $rows = [];
        $day = 0;
        for ($d = $from->startOfDay(); $d->lte($to); $d = $d->addDay(), $day++) {
            $weekly = in_array($d->dayOfWeek, [0, 6], true) ? 1.35 : 1.0;
            $launch = $day < 5 ? 2.2 - $day * 0.25 : 1.0;
            $decay = max(0.45, 1 - $day * 0.006);
            $count = max(0, (int) round($base * $weekly * $launch * $decay * (0.7 + mt_rand(0, 60) / 100)));

            for ($i = 0; $i < $count; $i++) {
                $at = $d->addMinutes(mt_rand(7 * 60, 23 * 60 + 59));
                if ($at->gt($this->now)) {
                    continue;
                }
                $ip = '10.' . mt_rand(0, 255) . '.' . mt_rand(0, 255) . '.' . mt_rand(1, 254);
                $ua = $this->weighted($agents);
                $rows[] = [
                    'id' => (string) Str::uuid(),
                    'tracking_link_id' => $linkId,
                    'campaign_id' => $campaignId,
                    'influencer_id' => $creatorId,
                    'ip' => $ip,
                    'user_agent' => $ua,
                    'referrer' => $this->weighted($referrers),
                    // ~80% unique visitors per day, like the real redirect use case.
                    'unique_key' => mt_rand(1, 100) <= 80 ? hash('sha256', $ip . '|' . $ua . '|' . $d->format('Y-m-d')) : hash('sha256', 'repeat|' . $creatorId . '|' . $d->format('Y-m-d') . '|' . mt_rand(1, 3)),
                    'created_at' => $at,
                    'updated_at' => $at,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('click_events')->insert($chunk);
        }
    }

    /** Monthly payout periods: older ones paid, the previous approved, the latest pending. */
    private function payouts(string $collabId, string $campaignId, CarbonImmutable $from, CarbonImmutable $to): void
    {
        $tiers = DB::table('campaign_payout_tiers')->where('campaign_id', $campaignId)->orderBy('from_value')->get();
        $periods = [];
        for ($start = $from->startOfDay(); $start->lt($to); $start = $start->addDays(30)) {
            $periods[] = [$start, min($start->addDays(30)->subSecond(), $to)];
        }

        $last = count($periods) - 1;
        foreach ($periods as $i => [$start, $end]) {
            $total = DB::table('click_events')->where('tracking_link_id', function ($q) use ($collabId) {
                $q->select('id')->from('tracking_links')->where('collaboration_id', $collabId)->limit(1);
            })->whereBetween('created_at', [$start, $end]);
            $clicks = (clone $total)->count();
            $unique = (clone $total)->distinct()->count('unique_key');

            $tier = $tiers->first(fn ($t) => $clicks >= $t->from_value && ($t->to_value === null || $clicks <= $t->to_value));
            $status = $i === $last ? 'pending' : ($i === $last - 1 ? 'approved' : 'paid');

            DB::table('collaboration_payouts')->insert([
                'id' => (string) Str::uuid(),
                'collaboration_id' => $collabId,
                'period_start' => $start,
                'period_end' => $end,
                'clicks_total' => $clicks,
                'clicks_unique' => $unique,
                'tier_id' => $tier?->id,
                'amount' => $tier?->payout_amount ?? 0,
                'currency' => 'MAD',
                'status' => $status,
                'created_at' => $end,
                'updated_at' => $end,
            ]);
        }
    }

    private function conversation(string $campaignId, string $appId, string $brandId, string $creatorId, CarbonImmutable $at): void
    {
        $id = (string) Str::uuid();
        DB::table('conversations')->insert([
            'id' => $id,
            'campaign_id' => $campaignId,
            'campaign_application_id' => $appId,
            'brand_user_id' => $brandId,
            'influencer_user_id' => $creatorId,
            'status' => 'open',
            'created_at' => $at,
            'updated_at' => $at,
        ]);

        foreach ([$brandId, $creatorId] as $uid) {
            DB::table('conversation_participants')->insert([
                'id' => (string) Str::uuid(),
                'conversation_id' => $id,
                'user_id' => $uid,
                'last_read_at' => $uid === $brandId ? $this->now : null,
                'created_at' => $at,
                'updated_at' => $at,
            ]);
        }

        $scripts = [
            [
                [$brandId, 'Welcome on board! Your link and promo code are ready in the collaboration page.'],
                [$creatorId, 'Thanks! I will post the first reel this weekend.'],
                [$brandId, 'Perfect. Feel free to share a draft if you want feedback before posting.'],
                [$creatorId, 'Sent you the draft, let me know what you think.'],
            ],
            [
                [$brandId, 'Hi! Happy to have you on this one. Any questions about the brief?'],
                [$creatorId, 'All clear. Can I mention the promo code in stories as well as in my bio?'],
                [$brandId, 'Yes, anywhere you like. The code and the link are both tracked.'],
            ],
            [
                [$creatorId, 'Hello, the package arrived today, thank you!'],
                [$brandId, 'Great. Take your time, natural light works best for this product.'],
                [$creatorId, 'Noted. First post goes live on Friday evening.'],
                [$brandId, 'Sounds good, we will share it from our account too.'],
            ],
            [
                [$brandId, 'Your clicks look strong this week, nice work.'],
                [$creatorId, 'Thanks! The tutorial format worked better than I expected.'],
            ],
        ];
        $script = $scripts[mt_rand(0, count($scripts) - 1)];
        foreach ($script as $i => [$sender, $body]) {
            $t = $at->addHours(2 + $i * 5);
            DB::table('messages')->insert([
                'id' => (string) Str::uuid(),
                'conversation_id' => $id,
                'sender_id' => $sender,
                'body' => $body,
                'created_at' => $t,
                'updated_at' => $t,
            ]);
        }
    }

    private function notifications(string $brandId, string $creatorId, string $campaignId): void
    {
        $items = [
            [$brandId, 'campaign.applied', 'New application', 'Yasmine Haddad applied to "Summer glow with the Rose Serum".', null, 2],
            [$brandId, 'campaign.applied', 'New application', 'Karim Alaoui applied to "Rhassoul Sunday reset".', null, 26],
            [$brandId, 'payout.closed', 'Payout period closed', 'A payout period was closed for one of your collaborations.', $this->now->subDays(3), 80],
            [$creatorId, 'application.accepted', 'Application accepted', 'Maison Nour accepted you for "Summer glow with the Rose Serum".', $this->now->subDays(60), 1500],
            [$creatorId, 'payout.paid', 'Payout sent', 'Your payout for "Slow mornings with Atlas" was marked as paid.', null, 5],
            [$creatorId, 'payout.approved', 'Payout approved', 'A payout of 600 MAD was approved.', null, 30],
        ];
        foreach ($items as [$user, $type, $title, $body, $readAt, $hoursAgo]) {
            $t = $this->now->subHours($hoursAgo);
            DB::table('user_notifications')->insert([
                'id' => (string) Str::uuid(),
                'user_id' => $user,
                'type' => $type,
                'title' => $title,
                'body' => $body,
                'entity_type' => 'Campaign',
                'entity_id' => $campaignId,
                'data' => json_encode([]),
                'read_at' => $readAt,
                'created_at' => $t,
                'updated_at' => $t,
            ]);
        }
    }

    /** @param array<int, array{0: mixed, 1: int}> $options */
    private function weighted(array $options): mixed
    {
        $roll = mt_rand(1, array_sum(array_column($options, 1)));
        foreach ($options as [$value, $weight]) {
            $roll -= $weight;
            if ($roll <= 0) {
                return $value;
            }
        }

        return $options[0][0];
    }
}
