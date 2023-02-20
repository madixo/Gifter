--
-- PostgreSQL database cluster dump
--

SET default_transaction_read_only = off;

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;

--
-- Drop databases (except postgres and template1)
--

DROP DATABASE "gifter-db";




--
-- Drop roles
--

DROP ROLE gifter;


--
-- Roles
--

CREATE ROLE gifter;
ALTER ROLE gifter WITH SUPERUSER INHERIT CREATEROLE CREATEDB LOGIN REPLICATION BYPASSRLS PASSWORD 'SCRAM-SHA-256$4096:zsQqGM9r3KiRZeEEYBmVog==$3M6/IThobSV7IKHRi+PNuiUa+aXjAzsxUJesFo83R6A=:UqOorxTX1mFUJzp3/z7oDakttPwgc3hoaYzz0onl+Bo=';

--
-- User Configurations
--








--
-- Databases
--

--
-- Database "template1" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 15.1
-- Dumped by pg_dump version 15.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

UPDATE pg_catalog.pg_database SET datistemplate = false WHERE datname = 'template1';
DROP DATABASE template1;
--
-- Name: template1; Type: DATABASE; Schema: -; Owner: gifter
--

CREATE DATABASE template1 WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'en_US.utf8';


ALTER DATABASE template1 OWNER TO gifter;

\connect template1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: DATABASE template1; Type: COMMENT; Schema: -; Owner: gifter
--

COMMENT ON DATABASE template1 IS 'default template for new databases';


--
-- Name: template1; Type: DATABASE PROPERTIES; Schema: -; Owner: gifter
--

ALTER DATABASE template1 IS_TEMPLATE = true;


\connect template1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: DATABASE template1; Type: ACL; Schema: -; Owner: gifter
--

REVOKE CONNECT,TEMPORARY ON DATABASE template1 FROM PUBLIC;
GRANT CONNECT ON DATABASE template1 TO PUBLIC;


--
-- PostgreSQL database dump complete
--

--
-- Database "gifter-db" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 15.1
-- Dumped by pg_dump version 15.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: gifter-db; Type: DATABASE; Schema: -; Owner: gifter
--

CREATE DATABASE "gifter-db" WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'en_US.utf8';


ALTER DATABASE "gifter-db" OWNER TO gifter;

\connect -reuse-previous=on "dbname='gifter-db'"

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: uuid-ossp; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS "uuid-ossp" WITH SCHEMA public;


--
-- Name: EXTENSION "uuid-ossp"; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION "uuid-ossp" IS 'generate universally unique identifiers (UUIDs)';


--
-- Name: delete_old_rows_password_reset(); Type: FUNCTION; Schema: public; Owner: gifter
--

CREATE FUNCTION public.delete_old_rows_password_reset() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
begin
delete from password_reset where timestamp < current_timestamp - interval '30 minutes';
return null;
end;
$$;


ALTER FUNCTION public.delete_old_rows_password_reset() OWNER TO gifter;

--
-- Name: update_timestamp(); Type: FUNCTION; Schema: public; Owner: gifter
--

CREATE FUNCTION public.update_timestamp() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ begin
NEW.timestamp := current_timestamp;
return NEW;
end;
$$;


ALTER FUNCTION public.update_timestamp() OWNER TO gifter;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: codes; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.codes (
    id integer NOT NULL,
    owner_id integer NOT NULL,
    list_id integer NOT NULL,
    code text NOT NULL
);


ALTER TABLE public.codes OWNER TO gifter;

--
-- Name: codes_id_seq; Type: SEQUENCE; Schema: public; Owner: gifter
--

CREATE SEQUENCE public.codes_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.codes_id_seq OWNER TO gifter;

--
-- Name: codes_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gifter
--

ALTER SEQUENCE public.codes_id_seq OWNED BY public.codes.id;


--
-- Name: gifts; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.gifts (
    id integer NOT NULL,
    list_id integer NOT NULL,
    name text NOT NULL,
    image text NOT NULL,
    price numeric(10,2) DEFAULT NULL::numeric,
    description text,
    taken_by_id integer
);


ALTER TABLE public.gifts OWNER TO gifter;

--
-- Name: gifts_id_seq; Type: SEQUENCE; Schema: public; Owner: gifter
--

CREATE SEQUENCE public.gifts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.gifts_id_seq OWNER TO gifter;

--
-- Name: gifts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gifter
--

ALTER SEQUENCE public.gifts_id_seq OWNED BY public.gifts.id;


--
-- Name: lists; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.lists (
    id integer NOT NULL,
    owner_id integer NOT NULL
);


ALTER TABLE public.lists OWNER TO gifter;

--
-- Name: lists_id_seq; Type: SEQUENCE; Schema: public; Owner: gifter
--

CREATE SEQUENCE public.lists_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.lists_id_seq OWNER TO gifter;

--
-- Name: lists_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gifter
--

ALTER SEQUENCE public.lists_id_seq OWNED BY public.lists.id;


--
-- Name: password_reset; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.password_reset (
    uuid text NOT NULL,
    "timestamp" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    user_id integer NOT NULL
);


ALTER TABLE public.password_reset OWNER TO gifter;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.roles (
    id integer NOT NULL,
    name text NOT NULL
);


ALTER TABLE public.roles OWNER TO gifter;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: gifter
--

CREATE SEQUENCE public.roles_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_id_seq OWNER TO gifter;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gifter
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.sessions (
    user_id integer NOT NULL,
    session_id text NOT NULL,
    "timestamp" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.sessions OWNER TO gifter;

--
-- Name: users; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.users (
    id integer NOT NULL,
    email text NOT NULL,
    password text,
    role_id integer NOT NULL
);


ALTER TABLE public.users OWNER TO gifter;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: gifter
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO gifter;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gifter
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: codes id; Type: DEFAULT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.codes ALTER COLUMN id SET DEFAULT nextval('public.codes_id_seq'::regclass);


--
-- Name: gifts id; Type: DEFAULT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.gifts ALTER COLUMN id SET DEFAULT nextval('public.gifts_id_seq'::regclass);


--
-- Name: lists id; Type: DEFAULT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.lists ALTER COLUMN id SET DEFAULT nextval('public.lists_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Data for Name: codes; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.codes (id, owner_id, list_id, code) FROM stdin;
\.


--
-- Data for Name: gifts; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.gifts (id, list_id, name, image, price, description, taken_by_id) FROM stdin;
\.


--
-- Data for Name: lists; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.lists (id, owner_id) FROM stdin;
\.


--
-- Data for Name: password_reset; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.password_reset (uuid, "timestamp", user_id) FROM stdin;
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.roles (id, name) FROM stdin;
1	admin
2	user
3	anon
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.sessions (user_id, session_id, "timestamp") FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.users (id, email, password, role_id) FROM stdin;
40	test@test.pl	$2y$10$0S23kfozVDHitazWdNI/2uhKl7iA1AwDLJ76RTzL4WeosaJqwyrqe	2
39	admin@gifter.pl	$2y$10$BO3uK043Yjn7E.rxIjture0N/qa3qDnPEs9gBRI.xERsjTJhvLK6a	2
\.


--
-- Name: codes_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gifter
--

SELECT pg_catalog.setval('public.codes_id_seq', 1, false);


--
-- Name: gifts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gifter
--

SELECT pg_catalog.setval('public.gifts_id_seq', 1, false);


--
-- Name: lists_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gifter
--

SELECT pg_catalog.setval('public.lists_id_seq', 1, false);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gifter
--

SELECT pg_catalog.setval('public.roles_id_seq', 3, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gifter
--

SELECT pg_catalog.setval('public.users_id_seq', 40, true);


--
-- Name: codes codes_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.codes
    ADD CONSTRAINT codes_pkey PRIMARY KEY (id);


--
-- Name: gifts gifts_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.gifts
    ADD CONSTRAINT gifts_pkey PRIMARY KEY (id);


--
-- Name: lists lists_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.lists
    ADD CONSTRAINT lists_pkey PRIMARY KEY (id);


--
-- Name: password_reset password_reset_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.password_reset
    ADD CONSTRAINT password_reset_pkey PRIMARY KEY (user_id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (user_id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: password_reset trigger_insert_delete_old_rows; Type: TRIGGER; Schema: public; Owner: gifter
--

CREATE TRIGGER trigger_insert_delete_old_rows AFTER INSERT ON public.password_reset FOR EACH STATEMENT EXECUTE FUNCTION public.delete_old_rows_password_reset();


--
-- Name: password_reset trigger_update_timestamp; Type: TRIGGER; Schema: public; Owner: gifter
--

CREATE TRIGGER trigger_update_timestamp BEFORE UPDATE ON public.password_reset FOR EACH ROW EXECUTE FUNCTION public.update_timestamp();


--
-- Name: codes codes_owner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.codes
    ADD CONSTRAINT codes_owner_id_fkey FOREIGN KEY (owner_id) REFERENCES public.users(id);


--
-- Name: gifts gifts_list_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.gifts
    ADD CONSTRAINT gifts_list_id_fkey FOREIGN KEY (list_id) REFERENCES public.lists(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gifts gifts_taken_by_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.gifts
    ADD CONSTRAINT gifts_taken_by_id_fkey FOREIGN KEY (taken_by_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: lists lists_owner_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.lists
    ADD CONSTRAINT lists_owner_id_fkey FOREIGN KEY (owner_id) REFERENCES public.users(id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: password_reset password_reset_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.password_reset
    ADD CONSTRAINT password_reset_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: sessions sessions_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id);


--
-- Name: users users_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_fkey FOREIGN KEY (role_id) REFERENCES public.roles(id) ON UPDATE CASCADE ON DELETE RESTRICT;


--
-- PostgreSQL database dump complete
--

--
-- Database "postgres" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 15.1
-- Dumped by pg_dump version 15.1

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

DROP DATABASE postgres;
--
-- Name: postgres; Type: DATABASE; Schema: -; Owner: gifter
--

CREATE DATABASE postgres WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'en_US.utf8';


ALTER DATABASE postgres OWNER TO gifter;

\connect postgres

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Name: DATABASE postgres; Type: COMMENT; Schema: -; Owner: gifter
--

COMMENT ON DATABASE postgres IS 'default administrative connection database';


--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database cluster dump complete
--

