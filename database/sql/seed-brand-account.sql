-- Kickback: fill one brand account with realistic demo data.
-- Paste into Supabase > SQL Editor and click Run. Only writes to the "kickback" schema.
-- Change the email on the next line if needed. Safe to run once per account (it stops if already seeded).

set search_path to kickback;

create or replace function pg_temp.kb_pick(options text[], weights int[]) returns text language plpgsql as $$
declare total int := 0; r int; i int;
begin
  for i in 1..array_length(weights, 1) loop total := total + weights[i]; end loop;
  r := floor(random() * total)::int + 1;
  for i in 1..array_length(weights, 1) loop
    r := r - weights[i];
    if r <= 0 then return options[i]; end if;
  end loop;
  return options[1];
end $$;

-- One accepted creator on a campaign: application, collaboration, link, promo code, clicks, payouts, chat.
create or replace function pg_temp.kb_collab(p_campaign uuid, p_brand uuid, p_creator uuid, p_base int, p_message text)
returns void language plpgsql as $$
declare
  c record; app_id uuid := gen_random_uuid(); collab_id uuid := gen_random_uuid(); link_id uuid := gen_random_uuid();
  conv_id uuid := gen_random_uuid(); accepted timestamptz; ends timestamptz; d date; n int; k int; day_idx int := 0;
  ts timestamptz; ip text; ua text; p_start timestamptz; p_end timestamptz; clicks int; uniq int; tier record; idx int := 0; periods int;
begin
  select * into c from campaigns where id = p_campaign;
  accepted := c.start_at - interval '2 days';
  ends := least(c.end_at, now());

  insert into campaign_applications (id, campaign_id, influencer_id, message, status, created_at, updated_at)
  values (app_id, p_campaign, p_creator, p_message, 'accepted', accepted - interval '3 days', accepted);

  insert into collaborations (id, campaign_id, brand_id, influencer_id, accepted_at, status, created_at, updated_at)
  values (collab_id, p_campaign, p_brand, p_creator, accepted, case when c.status = 'closed' then 'closed' else 'active' end, accepted, accepted);

  insert into tracking_links (id, collaboration_id, code, destination_url, created_at, updated_at)
  values (link_id, collab_id, 't_' || substr(md5(random()::text), 1, 10),
          (select landing_url from products where id = c.product_id), accepted, accepted);

  insert into promo_codes (id, collaboration_id, code, created_at, updated_at)
  values (gen_random_uuid(), collab_id, 'KB-' || upper(substr(md5(random()::text), 1, 8)), accepted, accepted);

  -- Daily clicks: launch spike, weekend bump, slow decay, noise.
  for d in select generate_series(accepted::date, ends::date, interval '1 day')::date loop
    n := greatest(0, round(p_base
           * (case when extract(dow from d) in (0, 6) then 1.35 else 1 end)
           * (case when day_idx < 5 then 2.2 - day_idx * 0.25 else 1 end)
           * greatest(0.45, 1 - day_idx * 0.006)
           * (0.7 + random() * 0.6))::int);
    for k in 1..n loop
      ts := d + interval '7 hours' + random() * interval '17 hours';
      continue when ts > now();
      ip := '10.' || floor(random() * 255) || '.' || floor(random() * 255) || '.' || (floor(random() * 253) + 1);
      ua := pg_temp.kb_pick(array[
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) AppleWebKit/605.1.15 Mobile/15E148 Instagram',
        'Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 Chrome/125.0 Mobile Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_5) AppleWebKit/605.1.15 Version/17.5 Safari/605.1.15',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/125.0 Safari/537.36'], array[44, 31, 14, 11]);
      insert into click_events (id, tracking_link_id, campaign_id, influencer_id, ip, user_agent, referrer, unique_key, created_at, updated_at)
      values (gen_random_uuid(), link_id, p_campaign, p_creator, ip, ua,
              nullif(pg_temp.kb_pick(array['https://www.instagram.com/', 'https://www.tiktok.com/', '', 'https://www.youtube.com/', 'https://www.google.com/', 'https://t.co/'],
                                     array[46, 28, 12, 7, 4, 3]), ''),
              case when random() < 0.8 then md5(ip || ua || d::text) else md5('repeat' || p_creator::text || d::text || floor(random() * 3)::text) end,
              ts, ts);
    end loop;
    day_idx := day_idx + 1;
  end loop;

  -- Monthly payout periods: older paid, previous approved, latest pending.
  periods := ceil(extract(epoch from (ends - accepted)) / (30 * 86400))::int;
  p_start := accepted::date;
  while p_start < ends loop
    p_end := least(p_start + interval '30 days' - interval '1 second', ends);
    select count(*), count(distinct unique_key) into clicks, uniq
      from click_events where tracking_link_id = link_id and created_at between p_start and p_end;
    select * into tier from campaign_payout_tiers
      where campaign_id = p_campaign and clicks >= from_value and (to_value is null or clicks <= to_value) limit 1;
    idx := idx + 1;
    insert into collaboration_payouts (id, collaboration_id, period_start, period_end, clicks_total, clicks_unique, tier_id, amount, currency, status, created_at, updated_at)
    values (gen_random_uuid(), collab_id, p_start, p_end, clicks, uniq, tier.id, coalesce(tier.payout_amount, 0), 'MAD',
            case when idx = periods then 'pending' when idx = periods - 1 then 'approved' else 'paid' end, p_end, p_end);
    p_start := p_start + interval '30 days';
  end loop;

  -- Conversation with a few messages.
  insert into conversations (id, campaign_id, campaign_application_id, brand_user_id, influencer_user_id, status, created_at, updated_at)
  values (conv_id, p_campaign, app_id, p_brand, p_creator, 'open', accepted, accepted);
  insert into conversation_participants (id, conversation_id, user_id, last_read_at, created_at, updated_at) values
    (gen_random_uuid(), conv_id, p_brand, now() - interval '1 day', accepted, accepted),
    (gen_random_uuid(), conv_id, p_creator, now(), accepted, accepted);
  insert into messages (id, conversation_id, sender_id, body, created_at, updated_at) values
    (gen_random_uuid(), conv_id, p_brand, 'Welcome on board! Your link and promo code are ready in the collaboration page.', accepted + interval '2 hours', accepted + interval '2 hours'),
    (gen_random_uuid(), conv_id, p_creator, 'Thanks! First post goes live this weekend.', accepted + interval '5 hours', accepted + interval '5 hours'),
    (gen_random_uuid(), conv_id, p_brand, 'Great. Share a draft if you want feedback before posting.', accepted + interval '9 hours', accepted + interval '9 hours'),
    (gen_random_uuid(), conv_id, p_creator, 'Here is the draft, natural light, one take. Let me know!', now() - interval '20 hours', now() - interval '20 hours');
end $$;

do $$
declare
  v_email text := 'brand@brand.com';   -- <== your account
  b uuid; cr uuid[]; p1 uuid := gen_random_uuid(); p2 uuid := gen_random_uuid(); p3 uuid := gen_random_uuid();
  c1 uuid := gen_random_uuid(); c2 uuid := gen_random_uuid(); c3 uuid := gen_random_uuid(); c4 uuid := gen_random_uuid(); c uuid;
begin
  select id into b from users where lower(email) = lower(v_email);
  if b is null then raise exception 'No Kickback account with email %', v_email; end if;
  if exists (select 1 from products where brand_id = b and name = 'Velvet Matte Lipstick') then
    raise exception 'This account already has the demo data. Nothing was changed.';
  end if;

  select array_agg(id order by created_at, email) into cr from users where role = 'influencer' and deleted_at is null;
  if coalesce(array_length(cr, 1), 0) < 5 then
    raise exception 'Need at least 5 creator accounts (run the demo setup first). Nothing was changed.';
  end if;

  update users set status = 'active', profile_completed = true, updated_at = now() where id = b;
  insert into brand_profiles (user_id, brand_name, website_url, industry, contact_email, description, created_at, updated_at)
  values (b, (select display_name from users where id = b), 'https://yourbrand.example', 'Beauty & cosmetics', v_email,
          'Cruelty-free makeup designed in Casablanca.', now(), now())
  on conflict (user_id) do nothing;

  insert into products (id, brand_id, name, description, price, currency, landing_url, status, images, created_at, updated_at) values
    (p1, b, 'Velvet Matte Lipstick', 'Long-wear matte lipstick in 8 shades.', 149, 'MAD', 'https://yourbrand.example/lipstick', 'active', '[]', now() - interval '150 days', now() - interval '150 days'),
    (p2, b, 'Glow Highlighter Duo', 'Two buildable highlighter shades for every skin tone.', 199, 'MAD', 'https://yourbrand.example/highlighter', 'active', '[]', now() - interval '150 days', now() - interval '150 days'),
    (p3, b, 'Everyday Brush Set', 'Five vegan brushes in a travel pouch.', 259, 'MAD', 'https://yourbrand.example/brushes', 'active', '[]', now() - interval '150 days', now() - interval '150 days');

  insert into campaigns (id, brand_id, product_id, title, objective, commission_type, commission_value, budget, start_at, end_at, status, created_at, updated_at) values
    (c1, b, p1, 'Autumn lip looks', 'Three looks with the Velvet Matte Lipstick, link in bio.', 'percent', 12, 18000, now()::date - 72, now()::date + 30, 'published', now() - interval '86 days', now() - interval '86 days'),
    (c2, b, p2, 'Glow in 60 seconds', 'A one-minute highlighter tutorial for reels or TikTok.', 'fixed', 30, 9000, now()::date - 38, now()::date + 50, 'published', now() - interval '52 days', now() - interval '52 days'),
    (c3, b, p3, 'Brush basics for beginners', 'Explain which brush does what, simply.', 'percent', 10, 6000, now()::date + 12, now()::date + 70, 'draft', now() - interval '3 days', now() - interval '3 days'),
    (c4, b, p1, 'Summer nude collection', 'Light, everyday summer looks.', 'percent', 15, 7000, now()::date - 150, now()::date - 95, 'closed', now() - interval '165 days', now() - interval '165 days');

  foreach c in array array[c1, c2, c3, c4] loop
    insert into campaign_payout_tiers (id, campaign_id, metric, from_value, to_value, payout_amount, currency, created_at, updated_at) values
      (gen_random_uuid(), c, 'clicks', 0, 499, 150, 'MAD', now(), now()),
      (gen_random_uuid(), c, 'clicks', 500, 1499, 600, 'MAD', now(), now()),
      (gen_random_uuid(), c, 'clicks', 1500, null, 1500, 'MAD', now(), now());
  end loop;

  perform pg_temp.kb_collab(c1, b, cr[1], 24, 'I do a weekly makeup reel, this fits perfectly.');
  perform pg_temp.kb_collab(c1, b, cr[2], 15, 'Happy to share the code in stories every week.');
  perform pg_temp.kb_collab(c1, b, cr[4], 10, 'Matte lipsticks are my most requested topic.');
  perform pg_temp.kb_collab(c2, b, cr[3], 17, 'One-take tutorials are my thing.');
  perform pg_temp.kb_collab(c2, b, cr[6], 9, 'My audience loves quick glow routines.');
  perform pg_temp.kb_collab(c4, b, cr[5], 12, 'Summer looks are my best performing content.');
  perform pg_temp.kb_collab(c4, b, cr[1], 8, 'Would love to do a nude look series.');

  -- Applications still waiting for a decision.
  insert into campaign_applications (id, campaign_id, influencer_id, message, status, created_at, updated_at) values
    (gen_random_uuid(), c1, cr[3], 'I can film a before/after with the three shades.', 'shortlisted', now() - interval '4 days', now() - interval '2 days'),
    (gen_random_uuid(), c1, cr[5], 'Big beauty audience in Morocco and France.', 'pending', now() - interval '1 day', now() - interval '1 day'),
    (gen_random_uuid(), c2, cr[2], 'I post glow tutorials every Sunday.', 'pending', now() - interval '6 hours', now() - interval '6 hours');

  insert into user_notifications (id, user_id, type, title, body, entity_type, entity_id, data, read_at, created_at, updated_at) values
    (gen_random_uuid(), b, 'campaign.applied', 'New application', 'A creator applied to "Glow in 60 seconds".', 'Campaign', c2, '{}', null, now() - interval '6 hours', now() - interval '6 hours'),
    (gen_random_uuid(), b, 'campaign.applied', 'New application', 'A creator applied to "Autumn lip looks".', 'Campaign', c1, '{}', null, now() - interval '1 day', now() - interval '1 day'),
    (gen_random_uuid(), b, 'payout.closed', 'Payout period closed', 'A payout period is ready for approval.', 'Campaign', c1, '{}', now() - interval '2 days', now() - interval '3 days', now() - interval '3 days');

  raise notice 'Done: demo data added to %', v_email;
end $$;

-- Quick check
select
  (select count(*) from campaigns c join users u on u.id = c.brand_id where u.email = 'brand@brand.com') as campaigns,
  (select count(*) from collaborations co join users u on u.id = co.brand_id where u.email = 'brand@brand.com') as collaborations,
  (select count(*) from click_events ce join campaigns c on c.id = ce.campaign_id join users u on u.id = c.brand_id where u.email = 'brand@brand.com') as clicks;
