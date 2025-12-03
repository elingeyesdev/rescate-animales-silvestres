--
-- PostgreSQL database dump
--

\restrict XeiQiDIioK0dTwIm6Ghk8eE0JKfxMdVgy5XJB2f6fGuaqhgnxkBGHu8AqVHbts5

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
-- Name: animal_conditions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.animal_conditions (
    id bigint NOT NULL,
    nombre character varying(120) NOT NULL,
    severidad smallint DEFAULT '3'::smallint NOT NULL,
    activo boolean DEFAULT true NOT NULL
);


--
-- Name: animal_conditions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.animal_conditions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: animal_conditions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.animal_conditions_id_seq OWNED BY public.animal_conditions.id;


--
-- Name: animal_histories; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.animal_histories (
    id bigint NOT NULL,
    animal_file_id bigint,
    changed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    observaciones json,
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
    especie_id bigint NOT NULL,
    imagen_url character varying(255),
    estado_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    animal_id bigint,
    centro_id bigint
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
-- Name: care_feedings; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.care_feedings (
    id bigint NOT NULL,
    care_id bigint NOT NULL,
    feeding_type_id bigint NOT NULL,
    feeding_frequency_id bigint NOT NULL,
    feeding_portion_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: care_feedings_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.care_feedings_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: care_feedings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.care_feedings_id_seq OWNED BY public.care_feedings.id;


--
-- Name: care_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.care_types (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    es_alimentacion boolean DEFAULT false NOT NULL
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
-- Name: contact_messages; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.contact_messages (
    id bigint NOT NULL,
    user_id bigint NOT NULL,
    motivo character varying(255) NOT NULL,
    mensaje text NOT NULL,
    leido boolean DEFAULT false NOT NULL,
    leido_at timestamp(0) without time zone,
    leido_por bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: contact_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.contact_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: contact_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.contact_messages_id_seq OWNED BY public.contact_messages.id;


--
-- Name: feeding_frequencies; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.feeding_frequencies (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: feeding_frequencies_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.feeding_frequencies_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: feeding_frequencies_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.feeding_frequencies_id_seq OWNED BY public.feeding_frequencies.id;


--
-- Name: feeding_portions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.feeding_portions (
    id bigint NOT NULL,
    cantidad integer NOT NULL,
    unidad character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: feeding_portions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.feeding_portions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: feeding_portions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.feeding_portions_id_seq OWNED BY public.feeding_portions.id;


--
-- Name: feeding_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.feeding_types (
    id bigint NOT NULL,
    nombre character varying(255) NOT NULL,
    descripcion text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: feeding_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.feeding_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: feeding_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.feeding_types_id_seq OWNED BY public.feeding_types.id;


--
-- Name: incident_types; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.incident_types (
    id bigint NOT NULL,
    nombre character varying(120) NOT NULL,
    riesgo smallint DEFAULT '1'::smallint NOT NULL,
    activo boolean DEFAULT true NOT NULL
);


--
-- Name: incident_types_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.incident_types_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: incident_types_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.incident_types_id_seq OWNED BY public.incident_types.id;


--
-- Name: medical_evaluations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.medical_evaluations (
    id bigint NOT NULL,
    tratamiento_id bigint,
    descripcion text,
    fecha date,
    veterinario_id bigint NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    imagen_url character varying(255),
    animal_file_id bigint,
    diagnostico text,
    peso numeric(8,2),
    temperatura numeric(5,2),
    tratamiento_texto text,
    recomendacion character varying(255),
    apto_traslado character varying(255)
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
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


--
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


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
    updated_at timestamp(0) without time zone,
    foto_path character varying(255),
    cuidador_center_id bigint,
    cuidador_aprobado boolean,
    cuidador_motivo_revision text
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
-- Name: permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) DEFAULT 'web'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name text NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


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
    animal_file_id bigint,
    imagen_url character varying(255)
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
    persona_id bigint,
    aprobado boolean DEFAULT false NOT NULL,
    imagen_url character varying(255),
    observaciones character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    latitud numeric(10,7),
    longitud numeric(10,7),
    direccion character varying(255),
    condicion_inicial_id bigint,
    tipo_incidente_id bigint,
    tamano character varying(16),
    puede_moverse boolean,
    urgencia smallint,
    incendio_id bigint
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
    motivo_revision text,
    motivo_postulacion text
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
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) DEFAULT 'web'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


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
    persona_id bigint NOT NULL,
    primer_traslado boolean DEFAULT true NOT NULL,
    animal_id bigint,
    latitud numeric(10,7),
    longitud numeric(10,7),
    reporte_id bigint
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
    motivo_revision text,
    motivo_postulacion text
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
-- Name: animal_conditions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_conditions ALTER COLUMN id SET DEFAULT nextval('public.animal_conditions_id_seq'::regclass);


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
-- Name: animals id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animals ALTER COLUMN id SET DEFAULT nextval('public.animals_id_seq'::regclass);


--
-- Name: care_feedings id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.care_feedings ALTER COLUMN id SET DEFAULT nextval('public.care_feedings_id_seq'::regclass);


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
-- Name: contact_messages id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contact_messages ALTER COLUMN id SET DEFAULT nextval('public.contact_messages_id_seq'::regclass);


--
-- Name: feeding_frequencies id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feeding_frequencies ALTER COLUMN id SET DEFAULT nextval('public.feeding_frequencies_id_seq'::regclass);


--
-- Name: feeding_portions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feeding_portions ALTER COLUMN id SET DEFAULT nextval('public.feeding_portions_id_seq'::regclass);


--
-- Name: feeding_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feeding_types ALTER COLUMN id SET DEFAULT nextval('public.feeding_types_id_seq'::regclass);


--
-- Name: incident_types id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_types ALTER COLUMN id SET DEFAULT nextval('public.incident_types_id_seq'::regclass);


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
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


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
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


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
-- Name: animal_conditions animal_conditions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animal_conditions
    ADD CONSTRAINT animal_conditions_pkey PRIMARY KEY (id);


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
-- Name: animals animals_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animals
    ADD CONSTRAINT animals_pkey PRIMARY KEY (id);


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
-- Name: care_feedings care_feedings_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.care_feedings
    ADD CONSTRAINT care_feedings_pkey PRIMARY KEY (id);


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
-- Name: contact_messages contact_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contact_messages
    ADD CONSTRAINT contact_messages_pkey PRIMARY KEY (id);


--
-- Name: feeding_frequencies feeding_frequencies_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feeding_frequencies
    ADD CONSTRAINT feeding_frequencies_pkey PRIMARY KEY (id);


--
-- Name: feeding_portions feeding_portions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feeding_portions
    ADD CONSTRAINT feeding_portions_pkey PRIMARY KEY (id);


--
-- Name: feeding_types feeding_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.feeding_types
    ADD CONSTRAINT feeding_types_pkey PRIMARY KEY (id);


--
-- Name: incident_types incident_types_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.incident_types
    ADD CONSTRAINT incident_types_pkey PRIMARY KEY (id);


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
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


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
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


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
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


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
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- Name: personal_access_tokens_expires_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_expires_at_index ON public.personal_access_tokens USING btree (expires_at);


--
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


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
-- Name: animals animals_reporte_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.animals
    ADD CONSTRAINT animals_reporte_id_foreign FOREIGN KEY (reporte_id) REFERENCES public.reports(id) ON DELETE CASCADE;


--
-- Name: care_feedings care_feedings_care_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.care_feedings
    ADD CONSTRAINT care_feedings_care_id_foreign FOREIGN KEY (care_id) REFERENCES public.cares(id) ON DELETE CASCADE;


--
-- Name: care_feedings care_feedings_feeding_frequency_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.care_feedings
    ADD CONSTRAINT care_feedings_feeding_frequency_id_foreign FOREIGN KEY (feeding_frequency_id) REFERENCES public.feeding_frequencies(id) ON DELETE CASCADE;


--
-- Name: care_feedings care_feedings_feeding_portion_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.care_feedings
    ADD CONSTRAINT care_feedings_feeding_portion_id_foreign FOREIGN KEY (feeding_portion_id) REFERENCES public.feeding_portions(id) ON DELETE CASCADE;


--
-- Name: care_feedings care_feedings_feeding_type_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.care_feedings
    ADD CONSTRAINT care_feedings_feeding_type_id_foreign FOREIGN KEY (feeding_type_id) REFERENCES public.feeding_types(id) ON DELETE CASCADE;


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
-- Name: contact_messages contact_messages_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.contact_messages
    ADD CONSTRAINT contact_messages_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


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
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


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
-- Name: reports reports_condicion_inicial_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_condicion_inicial_id_foreign FOREIGN KEY (condicion_inicial_id) REFERENCES public.animal_conditions(id);


--
-- Name: reports reports_persona_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_persona_id_foreign FOREIGN KEY (persona_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: reports reports_tipo_incidente_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.reports
    ADD CONSTRAINT reports_tipo_incidente_id_foreign FOREIGN KEY (tipo_incidente_id) REFERENCES public.incident_types(id);


--
-- Name: rescuers rescuers_persona_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.rescuers
    ADD CONSTRAINT rescuers_persona_id_foreign FOREIGN KEY (persona_id) REFERENCES public.people(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


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

\unrestrict XeiQiDIioK0dTwIm6Ghk8eE0JKfxMdVgy5XJB2f6fGuaqhgnxkBGHu8AqVHbts5

--
-- PostgreSQL database dump
--

\restrict OAguxstSwcSHcKhJUjZnPoqDw46iWu6iQ03XwOyaWZIwkGXZ4BXlwGslU0olRZ0

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
3	2025_11_07_000001_create_people_table	1
4	2025_11_07_000002_create_reports_table	1
5	2025_11_07_000004_create_species_table	1
6	2025_11_07_000006_create_animal_statuses_table	1
7	2025_11_07_000007_create_centers_table	1
8	2025_11_07_000008_create_rescuers_table	1
9	2025_11_07_000009_create_transfers_table	1
10	2025_11_07_000011_create_releases_table	1
11	2025_11_07_000012_create_veterinarians_table	1
12	2025_11_07_000013_create_treatment_types_table	1
13	2025_11_07_000014_create_medical_evaluations_table	1
14	2025_11_07_000015_create_animal_files_table	1
15	2025_11_07_000016_create_care_types_table	1
16	2025_11_07_000017_create_cares_table	1
17	2025_11_07_000020_add_geo_fields_to_reports_table	1
18	2025_11_07_000021_add_cv_path_to_rescuers_and_veterinarians	1
19	2025_11_07_000022_merge_cv_columns_on_rescuers_and_veterinarians	1
20	2025_11_10_000023_create_animals_table	1
21	2025_11_10_000024_move_nombre_sexo_to_animals_and_update_animal_files	1
22	2025_11_10_000025_move_report_relation_to_animals	1
23	2025_11_12_000026_update_transfers_link_to_people	1
24	2025_11_12_000027_create_animal_file_history_and_trigger	1
25	2025_11_12_000028_create_animal_histories_table	1
26	2025_11_12_000029_add_imagen_url_to_cares_and_medical_evaluations	1
27	2025_11_12_000030_move_outcomes_to_adoptions_and_releases	1
28	2025_11_12_000031_update_animal_histories_columns	1
29	2025_11_19_000032_add_approval_fields_to_rescuers_and_veterinarians	1
30	2025_11_21_000033_add_es_alimentacion_to_care_types_table	1
31	2025_11_21_000034_create_feeding_types_table	1
32	2025_11_21_000035_create_feeding_frequencies_table	1
33	2025_11_21_000036_create_feeding_portions_table	1
34	2025_11_21_000037_create_care_feedings_table	1
35	2025_11_23_000040_add_animal_file_id_to_medical_evaluations	1
36	2025_11_23_000041_make_tratamiento_nullable_in_medical_evaluations	1
37	2025_11_23_000042_add_fields_to_transfers_for_animal_and_first	1
38	2025_11_23_000043_add_coords_to_transfers	1
39	2025_11_23_000044_make_animal_file_nullable_in_animal_histories	1
40	2025_11_23_000045_drop_transfers_animal_fk	1
41	2025_11_24_000050_make_observaciones_nullable_in_animal_histories	1
42	2025_11_25_000100_create_animal_conditions_table	1
43	2025_11_25_000110_create_incident_types_table	1
44	2025_11_25_000120_add_incident_fields_to_reports_table	1
45	2025_11_25_000200_add_fields_to_medical_evaluations	1
46	2025_11_25_000210_drop_tratamientos_aplicados_from_medical_evaluations	1
47	2025_11_25_000220_add_reporte_id_to_transfers	1
48	2025_11_25_162646_create_personal_access_tokens_table	1
49	2025_11_26_000230_add_center_to_animal_files	1
50	2025_11_26_000300_seed_basic_catalogs	1
51	2025_11_29_000310_add_motivo_postulacion_to_rescuers_and_veterinarians	1
52	2025_11_29_000320_add_foto_path_to_people_table	1
53	2025_11_29_000330_create_permission_tables	1
54	2025_11_30_000340_add_caregiver_fields_to_people_table	1
55	2025_11_30_103639_create_contact_messages_table	1
56	2025_12_01_000350_make_persona_id_nullable_in_reports_table	2
57	2025_12_01_072517_add_incendio_id_to_reports_table	3
58	2025_12_01_114634_add_imagen_url_to_releases_table	4
\.


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 58, true);


--
-- PostgreSQL database dump complete
--

\unrestrict OAguxstSwcSHcKhJUjZnPoqDw46iWu6iQ03XwOyaWZIwkGXZ4BXlwGslU0olRZ0

