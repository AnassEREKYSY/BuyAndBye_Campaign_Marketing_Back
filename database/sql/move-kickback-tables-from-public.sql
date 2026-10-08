-- Moves Kickback's 19 tables (and their data) from "public" to the "kickback" schema.
-- Other apps' tables in "public" are not touched. Runs as one transaction: all or nothing.
begin;

do $$
begin
  if not exists (select 1 from information_schema.columns
                 where table_schema = 'public' and table_name = 'users' and column_name = 'display_name') then
    raise exception 'public.users does not look like Kickback''s table. Nothing was changed.';
  end if;
  if not exists (select 1 from public.migrations where migration like '%create_click_events_table') then
    raise exception 'public.migrations does not look like Kickback''s table. Nothing was changed.';
  end if;
end $$;

create schema if not exists kickback;

alter table public.migrations                set schema kickback;
alter table public.personal_access_tokens    set schema kickback;
alter table public.users                     set schema kickback;
alter table public.auth                      set schema kickback;
alter table public.brand_profiles            set schema kickback;
alter table public.influencer_profiles       set schema kickback;
alter table public.products                  set schema kickback;
alter table public.campaigns                 set schema kickback;
alter table public.campaign_applications     set schema kickback;
alter table public.collaborations            set schema kickback;
alter table public.tracking_links            set schema kickback;
alter table public.promo_codes               set schema kickback;
alter table public.click_events              set schema kickback;
alter table public.campaign_payout_tiers     set schema kickback;
alter table public.collaboration_payouts     set schema kickback;
alter table public.user_notifications        set schema kickback;
alter table public.conversations             set schema kickback;
alter table public.conversation_participants set schema kickback;
alter table public.messages                  set schema kickback;

commit;

select table_schema, count(*) as tables from information_schema.tables
where table_schema in ('public', 'kickback') group by 1 order by 1;
