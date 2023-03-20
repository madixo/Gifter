--
-- PostgreSQL database dump
--

-- Dumped from database version 15.1
-- Dumped by pg_dump version 15.2 (Debian 15.2-1)

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
-- Name: delete_old(); Type: FUNCTION; Schema: public; Owner: gifter
--

CREATE FUNCTION public.delete_old() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
	begin
		execute 'delete from ' || TG_TABLE_NAME || ' where ' || TG_ARGV[0] || ' < current_timestamp - interval ''' || TG_ARGV[1] || '''';
		return null;
	END;
$$;


ALTER FUNCTION public.delete_old() OWNER TO gifter;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: lists; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.lists (
    list_id bigint NOT NULL,
    user_id bigint NOT NULL,
    name text NOT NULL,
    access_code numeric(8,0) NOT NULL,
    CONSTRAINT lists_name_check CHECK ((name <> ''::text))
);


ALTER TABLE public.lists OWNER TO gifter;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.roles (
    role_id bigint NOT NULL,
    name text DEFAULT ''::text NOT NULL,
    CONSTRAINT roles_name_check CHECK ((name <> ''::text))
);


ALTER TABLE public.roles OWNER TO gifter;

--
-- Name: users; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.users (
    user_id bigint NOT NULL,
    email text DEFAULT ''::text NOT NULL,
    password text,
    role_id bigint DEFAULT 1 NOT NULL,
    CONSTRAINT users_email_check CHECK ((email <> ''::text)),
    CONSTRAINT users_password_check CHECK ((password <> ''::text))
);


ALTER TABLE public.users OWNER TO gifter;

--
-- Name: get_lists; Type: VIEW; Schema: public; Owner: gifter
--

CREATE VIEW public.get_lists AS
 SELECT lists.list_id,
    lists.user_id AS owner_id,
    users.email AS owner_email,
    users.password AS owner_password,
    users.role_id AS owner_role_id,
    roles.name AS owner_role_name,
    lists.name,
    lists.access_code
   FROM ((public.lists
     JOIN public.users USING (user_id))
     JOIN public.roles USING (role_id));


ALTER TABLE public.get_lists OWNER TO gifter;

--
-- Name: get_contributed_lists(bigint); Type: FUNCTION; Schema: public; Owner: gifter
--

CREATE FUNCTION public.get_contributed_lists(v_user_id bigint) RETURNS SETOF public.get_lists
    LANGUAGE plpgsql
    AS $$
	BEGIN
        return query SELECT lists.list_id,
    lists.user_id AS owner_id,
    users.email AS owner_email,
    users.password AS owner_password,
    users.role_id AS owner_role_id,
    roles.name AS owner_role_name,
    lists.name,
    lists.access_code
   FROM contributed_lists
     JOIN lists USING (list_id)
     JOIN users on lists.user_id = users.user_id
     JOIN roles USING (role_id)
    where contributed_lists.user_id = v_user_id;
	END;
$$;


ALTER FUNCTION public.get_contributed_lists(v_user_id bigint) OWNER TO gifter;

--
-- Name: new_list(bigint, text, integer, integer); Type: FUNCTION; Schema: public; Owner: gifter
--

CREATE FUNCTION public.new_list(owner_id bigint, name text, code_length integer, max_iter integer) RETURNS SETOF public.get_lists
    LANGUAGE plpgsql
    AS $$
    declare
        v_list_id bigint;
    BEGIN
        for i in 1..max_iter loop
            begin
                insert into lists (user_id, name, access_code)
                values (owner_id, name, (floor(random() * 10 ^ code_length))::int)
                returning list_id into v_list_id;
                return query select * from get_lists where list_id = v_list_id;
                return;
            exception when unique_violation then
            end;
        end loop;
    END;
$$;


ALTER FUNCTION public.new_list(owner_id bigint, name text, code_length integer, max_iter integer) OWNER TO gifter;

--
-- Name: password_reset_request_exists(uuid); Type: FUNCTION; Schema: public; Owner: gifter
--

CREATE FUNCTION public.password_reset_request_exists(v_password_reset_id uuid) RETURNS bigint
    LANGUAGE plpgsql
    AS $$
    declare
        v_user_id bigint := null;
    BEGIN
        select user_id
        into v_user_id
        from password_resets
        where password_reset_id = v_password_reset_id
        and timestamp + interval '30 minutes' >= current_timestamp;
        return v_user_id;
    END;
$$;


ALTER FUNCTION public.password_reset_request_exists(v_password_reset_id uuid) OWNER TO gifter;

--
-- Name: request_password_reset(bigint); Type: FUNCTION; Schema: public; Owner: gifter
--

CREATE FUNCTION public.request_password_reset(v_user_id bigint) RETURNS uuid
    LANGUAGE plpgsql
    AS $$
    declare 
        v_id uuid := uuid_generate_v4();
	BEGIN
        insert into password_resets (password_reset_id, user_id)
        values (v_id, v_user_id)
        on conflict (user_id)
        do update set password_reset_id = v_id;
        return v_id;
	END;
$$;


ALTER FUNCTION public.request_password_reset(v_user_id bigint) OWNER TO gifter;

--
-- Name: contributed_lists; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.contributed_lists (
    user_id bigint NOT NULL,
    list_id bigint NOT NULL
);


ALTER TABLE public.contributed_lists OWNER TO gifter;

--
-- Name: gifts; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.gifts (
    gift_id bigint NOT NULL,
    list_id bigint NOT NULL,
    name text NOT NULL,
    image text NOT NULL,
    price numeric(10,2),
    description text,
    taken_by_id bigint,
    CONSTRAINT gifts_image_check CHECK ((image <> ''::text)),
    CONSTRAINT gifts_name_check CHECK ((name <> ''::text))
);


ALTER TABLE public.gifts OWNER TO gifter;

--
-- Name: get_contributions; Type: VIEW; Schema: public; Owner: gifter
--

CREATE VIEW public.get_contributions AS
 SELECT contributors.user_id,
    contributors.email AS user_email,
    contributors.password AS user_password,
    contributors.role_id AS user_role_id,
    contributors_roles.name AS user_role_name,
    lists.list_id,
    lists.user_id AS owner_id,
    owners.email AS owner_email,
    owners.password AS owner_password,
    owners.role_id AS owner_role_id,
    owners_roles.name AS owner_role_name,
    lists.name AS list_name,
    lists.access_code
   FROM ((((((public.contributed_lists contributed_lists(user_id, list_id_1)
     JOIN public.users contributors USING (user_id))
     JOIN public.roles contributors_roles USING (role_id))
     JOIN public.gifts ON ((gifts.taken_by_id = contributors.user_id)))
     JOIN public.lists USING (list_id))
     JOIN public.users owners ON ((lists.user_id = owners.user_id)))
     JOIN public.roles owners_roles ON ((owners.role_id = owners_roles.role_id)));


ALTER TABLE public.get_contributions OWNER TO gifter;

--
-- Name: get_gifts; Type: VIEW; Schema: public; Owner: gifter
--

CREATE VIEW public.get_gifts AS
 SELECT gifts.gift_id,
    gifts.list_id,
    lists.user_id AS owner_id,
    owners.email AS owner_email,
    owners.password AS owner_password,
    owners.role_id AS owner_role_id,
    owners_roles.name AS owner_role_name,
    lists.name AS list_name,
    lists.access_code AS list_access_code,
    gifts.name,
    gifts.image,
    gifts.price,
    gifts.description,
    gifts.taken_by_id,
    users.email AS taken_by_email,
    users.password AS taken_by_password,
    users.role_id AS taken_by_role_id,
    roles.name AS taken_by_role_name
   FROM (((((public.gifts
     JOIN public.lists USING (list_id))
     JOIN public.users owners USING (user_id))
     JOIN public.roles owners_roles USING (role_id))
     JOIN public.users ON ((gifts.taken_by_id = users.user_id)))
     JOIN public.roles ON ((users.role_id = roles.role_id)));


ALTER TABLE public.get_gifts OWNER TO gifter;

--
-- Name: get_users; Type: VIEW; Schema: public; Owner: gifter
--

CREATE VIEW public.get_users AS
 SELECT users.user_id,
    users.email,
    users.password,
    users.role_id,
    roles.name AS role_name
   FROM (public.users
     JOIN public.roles USING (role_id));


ALTER TABLE public.get_users OWNER TO gifter;

--
-- Name: gifts_gift_id_seq; Type: SEQUENCE; Schema: public; Owner: gifter
--

CREATE SEQUENCE public.gifts_gift_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.gifts_gift_id_seq OWNER TO gifter;

--
-- Name: gifts_gift_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gifter
--

ALTER SEQUENCE public.gifts_gift_id_seq OWNED BY public.lists.list_id;


--
-- Name: gifts_gift_id_seq1; Type: SEQUENCE; Schema: public; Owner: gifter
--

CREATE SEQUENCE public.gifts_gift_id_seq1
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.gifts_gift_id_seq1 OWNER TO gifter;

--
-- Name: gifts_gift_id_seq1; Type: SEQUENCE OWNED BY; Schema: public; Owner: gifter
--

ALTER SEQUENCE public.gifts_gift_id_seq1 OWNED BY public.gifts.gift_id;


--
-- Name: password_resets; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.password_resets (
    password_reset_id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_id bigint NOT NULL,
    "timestamp" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.password_resets OWNER TO gifter;

--
-- Name: roles_role_id_seq; Type: SEQUENCE; Schema: public; Owner: gifter
--

CREATE SEQUENCE public.roles_role_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_role_id_seq OWNER TO gifter;

--
-- Name: roles_role_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gifter
--

ALTER SEQUENCE public.roles_role_id_seq OWNED BY public.roles.role_id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: gifter
--

CREATE TABLE public.sessions (
    session_id uuid DEFAULT public.uuid_generate_v4() NOT NULL,
    user_id bigint NOT NULL,
    "timestamp" timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.sessions OWNER TO gifter;

--
-- Name: users_user_id_seq; Type: SEQUENCE; Schema: public; Owner: gifter
--

CREATE SEQUENCE public.users_user_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_user_id_seq OWNER TO gifter;

--
-- Name: users_user_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: gifter
--

ALTER SEQUENCE public.users_user_id_seq OWNED BY public.users.user_id;


--
-- Name: gifts gift_id; Type: DEFAULT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.gifts ALTER COLUMN gift_id SET DEFAULT nextval('public.gifts_gift_id_seq1'::regclass);


--
-- Name: lists list_id; Type: DEFAULT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.lists ALTER COLUMN list_id SET DEFAULT nextval('public.gifts_gift_id_seq'::regclass);


--
-- Name: roles role_id; Type: DEFAULT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.roles ALTER COLUMN role_id SET DEFAULT nextval('public.roles_role_id_seq'::regclass);


--
-- Name: users user_id; Type: DEFAULT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.users ALTER COLUMN user_id SET DEFAULT nextval('public.users_user_id_seq'::regclass);


--
-- Data for Name: contributed_lists; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.contributed_lists (user_id, list_id) FROM stdin;
2	3097
\.


--
-- Data for Name: gifts; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.gifts (gift_id, list_id, name, image, price, description, taken_by_id) FROM stdin;
\.


--
-- Data for Name: lists; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.lists (list_id, user_id, name, access_code) FROM stdin;
3091	2	1	25425880
3093	2	3	46342687
3094	2	4	93200382
3097	1	asd	12312312
3092	2	asd	40598876
\.


--
-- Data for Name: password_resets; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.password_resets (password_reset_id, user_id, "timestamp") FROM stdin;
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.roles (role_id, name) FROM stdin;
1	User
2	Admin
3	Anon
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.sessions (session_id, user_id, "timestamp") FROM stdin;
609dbf8c-a4fa-4930-b7ff-4bdadee2118e	2	2023-03-06 20:43:15.192165
e98164dc-5921-4462-a496-e8bdf192cc2b	2	2023-03-08 17:06:42.176195
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: gifter
--

COPY public.users (user_id, email, password, role_id) FROM stdin;
1	test@gifter.pl	$2y$10$EHK6I7OMhNU6n/t3Bn9HPusukRqO/CrKuzjm9pDLo4REdehXsu56y	1
2	admin@gifter.pl	$2y$10$okLh6qzPZa.TUHgUmhSGSulfMqDPS24GUu9DDL3Z.SBuoWzG48xOe	1
\.


--
-- Name: gifts_gift_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gifter
--

SELECT pg_catalog.setval('public.gifts_gift_id_seq', 3097, true);


--
-- Name: gifts_gift_id_seq1; Type: SEQUENCE SET; Schema: public; Owner: gifter
--

SELECT pg_catalog.setval('public.gifts_gift_id_seq1', 1, false);


--
-- Name: roles_role_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gifter
--

SELECT pg_catalog.setval('public.roles_role_id_seq', 3, true);


--
-- Name: users_user_id_seq; Type: SEQUENCE SET; Schema: public; Owner: gifter
--

SELECT pg_catalog.setval('public.users_user_id_seq', 12, true);


--
-- Name: contributed_lists contributions_pk; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.contributed_lists
    ADD CONSTRAINT contributions_pk PRIMARY KEY (user_id, list_id);


--
-- Name: gifts gifts_pk; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.gifts
    ADD CONSTRAINT gifts_pk PRIMARY KEY (gift_id);


--
-- Name: lists lists_pk; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.lists
    ADD CONSTRAINT lists_pk PRIMARY KEY (list_id);


--
-- Name: lists lists_un; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.lists
    ADD CONSTRAINT lists_un UNIQUE (access_code);


--
-- Name: password_resets password_resets_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.password_resets
    ADD CONSTRAINT password_resets_pkey PRIMARY KEY (password_reset_id);


--
-- Name: password_resets password_resets_un; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.password_resets
    ADD CONSTRAINT password_resets_un UNIQUE (user_id);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (role_id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (session_id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (user_id);


--
-- Name: password_resets after_insert_garbage_collection_password_resets_trigger; Type: TRIGGER; Schema: public; Owner: gifter
--

CREATE TRIGGER after_insert_garbage_collection_password_resets_trigger AFTER INSERT ON public.password_resets FOR EACH STATEMENT EXECUTE FUNCTION public.delete_old('timestamp', '30 minutes');


--
-- Name: contributed_lists contributions_gift_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.contributed_lists
    ADD CONSTRAINT contributions_gift_id_fk FOREIGN KEY (list_id) REFERENCES public.lists(list_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: contributed_lists contributions_user_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.contributed_lists
    ADD CONSTRAINT contributions_user_id_fk FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gifts gifts_list_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.gifts
    ADD CONSTRAINT gifts_list_id_fk FOREIGN KEY (list_id) REFERENCES public.lists(list_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: gifts gifts_taken_by_fk; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.gifts
    ADD CONSTRAINT gifts_taken_by_fk FOREIGN KEY (taken_by_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- Name: lists lists_user_id_fk; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.lists
    ADD CONSTRAINT lists_user_id_fk FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: password_resets password_resets_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.password_resets
    ADD CONSTRAINT password_resets_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: sessions sessions_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(user_id) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: users users_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: gifter
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_fkey FOREIGN KEY (role_id) REFERENCES public.roles(role_id) ON UPDATE CASCADE ON DELETE SET NULL;


--
-- PostgreSQL database dump complete
--

