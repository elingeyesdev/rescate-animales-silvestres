--
-- PostgreSQL database dump
--

\restrict 58Lbl83JXbf5luUNU135Wda3GUDPB5hApeVjqgQsVPltdpBW0amu4GwRX5NF4RG

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: adoptions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.adoptions (
    id bigint NOT NULL,
    direccion character varying(255),
    latitud numeric(10,7),
    longitud numeric(10,7),
    detalle text,
    aprobada boolean DEFAULT false NOT NULL,
    adoptante_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    animal_file_id bigint
);


--
-- Name: adoptions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.adoptions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: adoptions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.adoptions_id_seq OWNED BY public.adoptions.id;


--
-- Name: animal_histories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.animal_histories (
    id bigint NOT NULL,
    animal_file_id bigint NOT NULL,
    changed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    observaciones json NOT NULL,
    valores_antiguos json,
    valores_nuevos json
);


--
-- Name: animal_file_history_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.animal_file_history_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: animal_file_history_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.animal_file_history_id_seq OWNED BY public.animal_histories.id;


--
-- Name: animal_files; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.animal_files (
    id bigint NOT NULL,
    tipo_id bigint NOT NULL,
    especie_id bigint NOT NULL,
    imagen_url character varying(255),
    raza_id bigint NOT NULL,
    estado_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    animal_id bigint
);


--
-- Name: animal_files_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.animal_files_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: animal_files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.animal_files_id_seq OWNED BY public.animal_files.id;


--
-- Name: animal_statuses; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.animal_statuses (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: animal_statuses_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.animal_statuses_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: animal_statuses_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.animal_statuses_id_seq OWNED BY public.animal_statuses.id;


--
-- Name: animal_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.animal_types (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    permite_adopcion boolean DEFAULT false NOT NULL,
    permite_liberacion boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: animal_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.animal_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: animal_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.animal_types_id_seq OWNED BY public.animal_types.id;


--
-- Name: animals; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.animals (
    id bigint NOT NULL,
    nombre character varying(100),
    sexo character varying(20) NOT NULL,
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    reporte_id bigint
);


--
-- Name: animals_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.animals_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: animals_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.animals_id_seq OWNED BY public.animals.id;


--
-- Name: breeds; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.breeds (
    id bigint NOT NULL,
    especie_id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: breeds_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.breeds_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: breeds_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.breeds_id_seq OWNED BY public.breeds.id;


--
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


--
-- Name: care_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.care_types (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: care_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.care_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: care_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.care_types_id_seq OWNED BY public.care_types.id;


--
-- Name: cares; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cares (
    id bigint NOT NULL,
    hoja_animal_id bigint NOT NULL,
    tipo_cuidado_id bigint NOT NULL,
    descripcion text,
    fecha date,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    imagen_url character varying(255)
);


--
-- Name: cares_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.cares_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: cares_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.cares_id_seq OWNED BY public.cares.id;


--
-- Name: centers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.centers (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    direccion character varying(255),
    latitud numeric(10,7),
    longitud numeric(10,7),
    contacto character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: centers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.centers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: centers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.centers_id_seq OWNED BY public.centers.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: medical_evaluations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.medical_evaluations (
    id bigint NOT NULL,
    tratamiento_id bigint NOT NULL,
    descripcion text,
    fecha date,
    veterinario_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    imagen_url character varying(255)
);


--
-- Name: medical_evaluations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.medical_evaluations_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: medical_evaluations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.medical_evaluations_id_seq OWNED BY public.medical_evaluations.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: people; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.people (
    id bigint NOT NULL,
    usuario_id bigint,
    nombre character varying(255) NOT NULL,
    ci character varying(255) NOT NULL,
    telefono character varying(255),
    es_cuidador boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: people_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.people_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: people_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.people_id_seq OWNED BY public.people.id;


--
-- Name: releases; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.releases (
    id bigint NOT NULL,
    direccion character varying(255),
    detalle text,
    latitud numeric(10,7),
    longitud numeric(10,7),
    aprobada boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    animal_file_id bigint
);


--
-- Name: releases_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.releases_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: releases_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.releases_id_seq OWNED BY public.releases.id;


--
-- Name: reports; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.reports (
    id bigint NOT NULL,
    persona_id bigint NOT NULL,
    aprobado boolean DEFAULT false NOT NULL,
    imagen_url character varying(255),
    observaciones character varying(255),
    cantidad_animales integer DEFAULT 1 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    latitud numeric(10,7),
    longitud numeric(10,7),
    direccion character varying(255)
);


--
-- Name: reports_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.reports_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: reports_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.reports_id_seq OWNED BY public.reports.id;


--
-- Name: rescuers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.rescuers (
    id bigint NOT NULL,
    persona_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    cv_documentado character varying(255),
    aprobado boolean,
    motivo_revision text
);


--
-- Name: rescuers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.rescuers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: rescuers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.rescuers_id_seq OWNED BY public.rescuers.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: species; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.species (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: species_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.species_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: species_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.species_id_seq OWNED BY public.species.id;


--
-- Name: transfers; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.transfers (
    id bigint NOT NULL,
    centro_id bigint NOT NULL,
    observaciones character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    persona_id bigint NOT NULL
);


--
-- Name: transfers_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.transfers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: transfers_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.transfers_id_seq OWNED BY public.transfers.id;


--
-- Name: treatment_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.treatment_types (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: treatment_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.treatment_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: treatment_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.treatment_types_id_seq OWNED BY public.treatment_types.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: veterinarians; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.veterinarians (
    id bigint NOT NULL,
    especialidad character varying(255),
    persona_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    cv_documentado character varying(255),
    aprobado boolean,
    motivo_revision text
);


--
-- Name: veterinarians_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.veterinarians_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: veterinarians_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.veterinarians_id_seq OWNED BY public.veterinarians.id;


--
-- Name: adoptions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.adoptions ALTER COLUMN id SET DEFAULT nextval('public.adoptions_id_seq'::regclass);


--
-- Name: animal_files id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_files ALTER COLUMN id SET DEFAULT nextval('public.animal_files_id_seq'::regclass);


--
-- Name: animal_histories id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_histories ALTER COLUMN id SET DEFAULT nextval('public.animal_file_history_id_seq'::regclass);


--
-- Name: animal_statuses id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_statuses ALTER COLUMN id SET DEFAULT nextval('public.animal_statuses_id_seq'::regclass);


--
-- Name: animal_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_types ALTER COLUMN id SET DEFAULT nextval('public.animal_types_id_seq'::regclass);


--
-- Name: animals id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animals ALTER COLUMN id SET DEFAULT nextval('public.animals_id_seq'::regclass);


--
-- Name: breeds id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.breeds ALTER COLUMN id SET DEFAULT nextval('public.breeds_id_seq'::regclass);


--
-- Name: care_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.care_types ALTER COLUMN id SET DEFAULT nextval('public.care_types_id_seq'::regclass);


--
-- Name: cares id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cares ALTER COLUMN id SET DEFAULT nextval('public.cares_id_seq'::regclass);


--
-- Name: centers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.centers ALTER COLUMN id SET DEFAULT nextval('public.centers_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: medical_evaluations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.medical_evaluations ALTER COLUMN id SET DEFAULT nextval('public.medical_evaluations_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: people id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.people ALTER COLUMN id SET DEFAULT nextval('public.people_id_seq'::regclass);


--
-- Name: releases id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.releases ALTER COLUMN id SET DEFAULT nextval('public.releases_id_seq'::regclass);


--
-- Name: reports id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports ALTER COLUMN id SET DEFAULT nextval('public.reports_id_seq'::regclass);


--
-- Name: rescuers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rescuers ALTER COLUMN id SET DEFAULT nextval('public.rescuers_id_seq'::regclass);


--
-- Name: species id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.species ALTER COLUMN id SET DEFAULT nextval('public.species_id_seq'::regclass);


--
-- Name: transfers id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transfers ALTER COLUMN id SET DEFAULT nextval('public.transfers_id_seq'::regclass);


--
-- Name: treatment_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.treatment_types ALTER COLUMN id SET DEFAULT nextval('public.treatment_types_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: veterinarians id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.veterinarians ALTER COLUMN id SET DEFAULT nextval('public.veterinarians_id_seq'::regclass);


--
-- Name: adoptions adoptions_animal_file_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.adoptions
    ADD CONSTRAINT adoptions_animal_file_id_unique UNIQUE (animal_file_id);


--
-- Name: adoptions adoptions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.adoptions
    ADD CONSTRAINT adoptions_pkey PRIMARY KEY (id);


--
-- Name: animal_histories animal_file_history_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_histories
    ADD CONSTRAINT animal_file_history_pkey PRIMARY KEY (id);


--
-- Name: animal_files animal_files_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_files
    ADD CONSTRAINT animal_files_pkey PRIMARY KEY (id);


--
-- Name: animal_statuses animal_statuses_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_statuses
    ADD CONSTRAINT animal_statuses_pkey PRIMARY KEY (id);


--
-- Name: animal_types animal_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_types
    ADD CONSTRAINT animal_types_pkey PRIMARY KEY (id);


--
-- Name: animals animals_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animals
    ADD CONSTRAINT animals_pkey PRIMARY KEY (id);


--
-- Name: breeds breeds_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.breeds
    ADD CONSTRAINT breeds_pkey PRIMARY KEY (id);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: care_types care_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.care_types
    ADD CONSTRAINT care_types_pkey PRIMARY KEY (id);


--
-- Name: cares cares_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cares
    ADD CONSTRAINT cares_pkey PRIMARY KEY (id);


--
-- Name: centers centers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.centers
    ADD CONSTRAINT centers_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: medical_evaluations medical_evaluations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.medical_evaluations
    ADD CONSTRAINT medical_evaluations_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: people people_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.people
    ADD CONSTRAINT people_pkey PRIMARY KEY (id);


--
-- Name: releases releases_animal_file_id_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.releases
    ADD CONSTRAINT releases_animal_file_id_unique UNIQUE (animal_file_id);


--
-- Name: releases releases_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.releases
    ADD CONSTRAINT releases_pkey PRIMARY KEY (id);


--
-- Name: reports reports_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_pkey PRIMARY KEY (id);


--
-- Name: rescuers rescuers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rescuers
    ADD CONSTRAINT rescuers_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: species species_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.species
    ADD CONSTRAINT species_pkey PRIMARY KEY (id);


--
-- Name: transfers transfers_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transfers
    ADD CONSTRAINT transfers_pkey PRIMARY KEY (id);


--
-- Name: treatment_types treatment_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.treatment_types
    ADD CONSTRAINT treatment_types_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: veterinarians veterinarians_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.veterinarians
    ADD CONSTRAINT veterinarians_pkey PRIMARY KEY (id);


--
-- Name: animal_file_history_animal_file_id_changed_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX animal_file_history_animal_file_id_changed_at_index ON public.animal_histories USING btree (animal_file_id, changed_at);


--
-- Name: animal_files_especie_id_raza_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX animal_files_especie_id_raza_id_index ON public.animal_files USING btree (especie_id, raza_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: adoptions adoptions_animal_file_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.adoptions
    ADD CONSTRAINT adoptions_animal_file_id_foreign FOREIGN KEY (animal_file_id) REFERENCES public.animal_files(id) ON DELETE CASCADE;


--
-- Name: animal_histories animal_file_history_animal_file_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_histories
    ADD CONSTRAINT animal_file_history_animal_file_id_foreign FOREIGN KEY (animal_file_id) REFERENCES public.animal_files(id) ON DELETE CASCADE;


--
-- Name: animal_files animal_files_animal_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_files
    ADD CONSTRAINT animal_files_animal_id_foreign FOREIGN KEY (animal_id) REFERENCES public.animals(id) ON DELETE CASCADE;


--
-- Name: animal_files animal_files_especie_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_files
    ADD CONSTRAINT animal_files_especie_id_foreign FOREIGN KEY (especie_id) REFERENCES public.species(id) ON DELETE CASCADE;


--
-- Name: animal_files animal_files_estado_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_files
    ADD CONSTRAINT animal_files_estado_id_foreign FOREIGN KEY (estado_id) REFERENCES public.animal_statuses(id) ON DELETE CASCADE;


--
-- Name: animal_files animal_files_tipo_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_files
    ADD CONSTRAINT animal_files_tipo_id_foreign FOREIGN KEY (tipo_id) REFERENCES public.animal_types(id) ON DELETE CASCADE;


--
-- Name: animals animals_reporte_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animals
    ADD CONSTRAINT animals_reporte_id_foreign FOREIGN KEY (reporte_id) REFERENCES public.reports(id) ON DELETE CASCADE;


--
-- Name: breeds breeds_especie_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.breeds
    ADD CONSTRAINT breeds_especie_id_foreign FOREIGN KEY (especie_id) REFERENCES public.species(id) ON DELETE CASCADE;


--
-- Name: cares cares_hoja_animal_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cares
    ADD CONSTRAINT cares_hoja_animal_id_foreign FOREIGN KEY (hoja_animal_id) REFERENCES public.animal_files(id) ON DELETE CASCADE;


--
-- Name: cares cares_tipo_cuidado_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cares
    ADD CONSTRAINT cares_tipo_cuidado_id_foreign FOREIGN KEY (tipo_cuidado_id) REFERENCES public.care_types(id) ON DELETE CASCADE;


--
-- Name: medical_evaluations medical_evaluations_tratamiento_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.medical_evaluations
    ADD CONSTRAINT medical_evaluations_tratamiento_id_foreign FOREIGN KEY (tratamiento_id) REFERENCES public.treatment_types(id) ON DELETE CASCADE;


--
-- Name: medical_evaluations medical_evaluations_veterinario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.medical_evaluations
    ADD CONSTRAINT medical_evaluations_veterinario_id_foreign FOREIGN KEY (veterinario_id) REFERENCES public.veterinarians(id) ON DELETE CASCADE;


--
-- Name: people people_usuario_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.people
    ADD CONSTRAINT people_usuario_id_foreign FOREIGN KEY (usuario_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: releases releases_animal_file_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.releases
    ADD CONSTRAINT releases_animal_file_id_foreign FOREIGN KEY (animal_file_id) REFERENCES public.animal_files(id) ON DELETE CASCADE;


--
-- Name: reports reports_persona_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_persona_id_foreign FOREIGN KEY (persona_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: rescuers rescuers_persona_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rescuers
    ADD CONSTRAINT rescuers_persona_id_foreign FOREIGN KEY (persona_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: transfers transfers_centro_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transfers
    ADD CONSTRAINT transfers_centro_id_foreign FOREIGN KEY (centro_id) REFERENCES public.centers(id) ON DELETE CASCADE;


--
-- Name: transfers transfers_persona_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.transfers
    ADD CONSTRAINT transfers_persona_id_foreign FOREIGN KEY (persona_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: veterinarians veterinarians_persona_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.veterinarians
    ADD CONSTRAINT veterinarians_persona_id_foreign FOREIGN KEY (persona_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict 58Lbl83JXbf5luUNU135Wda3GUDPB5hApeVjqgQsVPltdpBW0amu4GwRX5NF4RG

--
-- PostgreSQL database dump
--

\restrict mVfJDql6rSqSOgvqoTKW5xAT6j7xO9hXx5Naz9WBov5u2dVUIStcMmCdmhXLyKv

-- Dumped from database version 17.6
-- Dumped by pg_dump version 17.6

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2025_11_07_000001_create_people_table	1
5	2025_11_07_000002_create_reports_table	1
6	2025_11_07_000003_create_animal_types_table	1
7	2025_11_07_000004_create_species_table	1
8	2025_11_07_000005_create_breeds_table	1
9	2025_11_07_000006_create_animal_statuses_table	1
10	2025_11_07_000007_create_centers_table	1
11	2025_11_07_000008_create_rescuers_table	1
12	2025_11_07_000009_create_transfers_table	1
13	2025_11_07_000010_create_adoptions_table	1
14	2025_11_07_000011_create_releases_table	1
15	2025_11_07_000012_create_veterinarians_table	1
16	2025_11_07_000013_create_treatment_types_table	1
17	2025_11_07_000014_create_medical_evaluations_table	1
18	2025_11_07_000015_create_animal_files_table	1
19	2025_11_07_000016_create_care_types_table	1
20	2025_11_07_000017_create_cares_table	1
21	2025_11_07_000020_add_geo_fields_to_reports_table	1
22	2025_11_07_000021_add_cv_path_to_rescuers_and_veterinarians	1
23	2025_11_07_000022_merge_cv_columns_on_rescuers_and_veterinarians	1
24	2025_11_10_000023_create_animals_table	1
25	2025_11_10_000024_move_nombre_sexo_to_animals_and_update_animal_files	1
26	2025_11_10_000025_move_report_relation_to_animals	1
27	2025_11_12_000026_update_transfers_link_to_people	2
28	2025_11_12_000027_create_animal_file_history_and_trigger	2
29	2025_11_12_000028_create_animal_histories_table	2
30	2025_11_12_000029_add_imagen_url_to_cares_and_medical_evaluations	2
31	2025_11_12_000030_move_outcomes_to_adoptions_and_releases	2
32	2025_11_12_000031_update_animal_histories_columns	3
33	2025_11_19_000032_add_approval_fields_to_rescuers_and_veterinarians	4
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 33, true);


--
-- PostgreSQL database dump complete
--

\unrestrict mVfJDql6rSqSOgvqoTKW5xAT6j7xO9hXx5Naz9WBov5u2dVUIStcMmCdmhXLyKv

