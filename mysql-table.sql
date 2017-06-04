// table in my-sql DB

CREATE TABLE users (
id INT(50) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(50) NOT NULL,
username VARCHAR(50) NOT NULL UNIQUE,
email VARCHAR(50) NOT NULL UNIQUE,
password VARCHAR(200),
salt VARCHAR(200)
)

// table in pgsql DB
﻿CREATE TABLE public.user_salt
(
    id serial NOT NULL,
    name character varying(50) NOT NULL,
    username character varying(50) NOT NULL,
    email character varying(50) NOT NULL,
    password character varying(200) NOT NULL,
    salt character varying(200) NOT NULL,
    PRIMARY KEY (id)
)
