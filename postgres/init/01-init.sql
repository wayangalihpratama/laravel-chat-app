-- PostgreSQL initialization script for Laravel applications

-- Create extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";      -- For UUID generation
CREATE EXTENSION IF NOT EXISTS "pg_trgm";        -- For fast text search/similarity
CREATE EXTENSION IF NOT EXISTS "unaccent";       -- For accent-insensitive search
CREATE EXTENSION IF NOT EXISTS "citext";         -- For case-insensitive text fields

-- Create database
-- Note: We create the database only if connecting to the default postgres database
-- In docker-compose setup, POSTGRES_DB environment variable typically does this automatically
DO $$
BEGIN
    IF EXISTS (SELECT FROM pg_database WHERE datname = current_database() AND datname = 'postgres') THEN
        CREATE DATABASE laravel_db;
    END IF;
END
$$;

-- Create user with password (if doesn't exist)
DO $$
BEGIN
    IF NOT EXISTS (SELECT FROM pg_catalog.pg_roles WHERE rolname = 'laravel_user') THEN
        CREATE USER laravel_user WITH ENCRYPTED PASSWORD 'laravel_password';
    END IF;
END
$$;

-- Grant privileges (connect to default postgres database first)
GRANT ALL PRIVILEGES ON DATABASE laravel_db TO laravel_user;

-- Connect to the Laravel database and set up schema permissions
\c laravel_db;

-- Grant usage on schema
GRANT USAGE ON SCHEMA public TO laravel_user;

-- Grant permissions on all tables
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON TABLES TO laravel_user;

-- Grant permissions on sequences
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON SEQUENCES TO laravel_user;

-- Grant permissions on functions
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON FUNCTIONS TO laravel_user;

-- Enable required extensions in the laravel database
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pg_trgm";
CREATE EXTENSION IF NOT EXISTS "unaccent";
CREATE EXTENSION IF NOT EXISTS "citext";

-- Set timezone to UTC (Laravel typically uses UTC internally)
ALTER DATABASE laravel_db SET timezone TO 'UTC';

-- Optimize for Laravel
ALTER DATABASE laravel_db SET standard_conforming_strings TO on;
ALTER DATABASE laravel_db SET client_min_messages TO warning;

