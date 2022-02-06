--
-- PostgreSQL database dump
--

-- Dumped from database version 12.9 (Ubuntu 12.9-0ubuntu0.20.04.1)
-- Dumped by pg_dump version 12.9 (Ubuntu 12.9-0ubuntu0.20.04.1)

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
-- Name: notify_messenger_messages(); Type: FUNCTION; Schema: public; Owner: rc_super_admin
--

CREATE FUNCTION public.notify_messenger_messages() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
            BEGIN
                PERFORM pg_notify('messenger_messages', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$;


ALTER FUNCTION public.notify_messenger_messages() OWNER TO rc_super_admin;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: doctrine_migration_versions; Type: TABLE; Schema: public; Owner: rc_super_admin
--

CREATE TABLE public.doctrine_migration_versions (
    version character varying(191) NOT NULL,
    executed_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone,
    execution_time integer
);


ALTER TABLE public.doctrine_migration_versions OWNER TO rc_super_admin;

--
-- Name: link; Type: TABLE; Schema: public; Owner: rc_super_admin
--

CREATE TABLE public.link (
    id integer NOT NULL,
    name character varying(255) NOT NULL,
    url text NOT NULL,
    transitions integer,
    lifetime time(0) without time zone NOT NULL,
    create_at timestamp(0) without time zone NOT NULL
);


ALTER TABLE public.link OWNER TO rc_super_admin;

--
-- Name: link_id_seq; Type: SEQUENCE; Schema: public; Owner: rc_super_admin
--

CREATE SEQUENCE public.link_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.link_id_seq OWNER TO rc_super_admin;

--
-- Name: messenger_messages; Type: TABLE; Schema: public; Owner: rc_super_admin
--

CREATE TABLE public.messenger_messages (
    id bigint NOT NULL,
    body text NOT NULL,
    headers text NOT NULL,
    queue_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone NOT NULL,
    available_at timestamp(0) without time zone NOT NULL,
    delivered_at timestamp(0) without time zone DEFAULT NULL::timestamp without time zone
);


ALTER TABLE public.messenger_messages OWNER TO rc_super_admin;

--
-- Name: messenger_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: rc_super_admin
--

CREATE SEQUENCE public.messenger_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.messenger_messages_id_seq OWNER TO rc_super_admin;

--
-- Name: messenger_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: rc_super_admin
--

ALTER SEQUENCE public.messenger_messages_id_seq OWNED BY public.messenger_messages.id;


--
-- Name: messenger_messages id; Type: DEFAULT; Schema: public; Owner: rc_super_admin
--

ALTER TABLE ONLY public.messenger_messages ALTER COLUMN id SET DEFAULT nextval('public.messenger_messages_id_seq'::regclass);


--
-- Data for Name: doctrine_migration_versions; Type: TABLE DATA; Schema: public; Owner: rc_super_admin
--

COPY public.doctrine_migration_versions (version, executed_at, execution_time) FROM stdin;
DoctrineMigrations\\Version20220205071155	2022-02-05 09:12:57	59
\.


--
-- Data for Name: link; Type: TABLE DATA; Schema: public; Owner: rc_super_admin
--

COPY public.link (id, name, url, transitions, lifetime, create_at) FROM stdin;
72	831f908f	https://www.google.com/search?q=Test&oq=Test&aqs=chrome..69i57j0i433i512l3j46i131i433i512j69i60j69i61l2.9418j0j1&client=ubuntu&sourceid=chrome&ie=UTF-8	3	02:21:00	2022-02-06 17:11:18
76	ac8817eb	https://www.google.com/search?q=Test&oq=Test&aqs=chrome..69i57j0i433i512l3j46i131i433i512j69i60j69i61l2.9418j0j1&client=ubuntu&sourceid=chrome&ie=UTF-8	1	07:06:00	2022-02-06 17:44:21
79	aec98adc	https://www.netguru.com/blog/how-to-dump-and-restore-postgresql-database	1	02:00:00	2022-02-06 18:04:28
\.


--
-- Data for Name: messenger_messages; Type: TABLE DATA; Schema: public; Owner: rc_super_admin
--

COPY public.messenger_messages (id, body, headers, queue_name, created_at, available_at, delivered_at) FROM stdin;
\.


--
-- Name: link_id_seq; Type: SEQUENCE SET; Schema: public; Owner: rc_super_admin
--

SELECT pg_catalog.setval('public.link_id_seq', 80, true);


--
-- Name: messenger_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: rc_super_admin
--

SELECT pg_catalog.setval('public.messenger_messages_id_seq', 1, false);


--
-- Name: doctrine_migration_versions doctrine_migration_versions_pkey; Type: CONSTRAINT; Schema: public; Owner: rc_super_admin
--

ALTER TABLE ONLY public.doctrine_migration_versions
    ADD CONSTRAINT doctrine_migration_versions_pkey PRIMARY KEY (version);


--
-- Name: link link_pkey; Type: CONSTRAINT; Schema: public; Owner: rc_super_admin
--

ALTER TABLE ONLY public.link
    ADD CONSTRAINT link_pkey PRIMARY KEY (id);


--
-- Name: messenger_messages messenger_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: rc_super_admin
--

ALTER TABLE ONLY public.messenger_messages
    ADD CONSTRAINT messenger_messages_pkey PRIMARY KEY (id);


--
-- Name: idx_75ea56e016ba31db; Type: INDEX; Schema: public; Owner: rc_super_admin
--

CREATE INDEX idx_75ea56e016ba31db ON public.messenger_messages USING btree (delivered_at);


--
-- Name: idx_75ea56e0e3bd61ce; Type: INDEX; Schema: public; Owner: rc_super_admin
--

CREATE INDEX idx_75ea56e0e3bd61ce ON public.messenger_messages USING btree (available_at);


--
-- Name: idx_75ea56e0fb7336f0; Type: INDEX; Schema: public; Owner: rc_super_admin
--

CREATE INDEX idx_75ea56e0fb7336f0 ON public.messenger_messages USING btree (queue_name);


--
-- Name: messenger_messages notify_trigger; Type: TRIGGER; Schema: public; Owner: rc_super_admin
--

CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON public.messenger_messages FOR EACH ROW EXECUTE FUNCTION public.notify_messenger_messages();


--
-- PostgreSQL database dump complete
--

