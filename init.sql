DO
$$
BEGIN
   IF NOT EXISTS (
      SELECT FROM pg_catalog.pg_roles
      WHERE rolname = current_setting('app.db_user')
   ) THEN
      EXECUTE format(
         'CREATE USER %I WITH PASSWORD %L',
         current_setting('app.db_user'),
         current_setting('app.db_password')
      );
   END IF;
END
$$;

ALTER DATABASE buyandbye_db OWNER TO buyandbye_user;

GRANT ALL PRIVILEGES ON DATABASE buyandbye_db TO buyandbye_user;
GRANT USAGE, CREATE ON SCHEMA public TO buyandbye_user;

ALTER DEFAULT PRIVILEGES IN SCHEMA public
GRANT ALL ON TABLES TO buyandbye_user;

ALTER DEFAULT PRIVILEGES IN SCHEMA public
GRANT ALL ON SEQUENCES TO buyandbye_user;
