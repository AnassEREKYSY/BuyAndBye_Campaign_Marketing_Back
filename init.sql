-- Create the app user if it doesn't exist
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

-- Grant full access to the app user
DO
$$
BEGIN
   EXECUTE format(
      'GRANT ALL PRIVILEGES ON DATABASE %I TO %I',
      current_database(),
      current_setting('app.db_user')
   );
   EXECUTE format(
      'ALTER DATABASE %I OWNER TO %I',
      current_database(),
      current_setting('app.db_user')
   );
END
$$;
