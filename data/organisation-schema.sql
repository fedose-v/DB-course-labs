CREATE TABLE branch
(
  id         INT UNSIGNED AUTO_INCREMENT,
  city       VARCHAR(100)     NOT NULL,
  address    VARCHAR(200)     NOT NULL,
  is_deleted TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
)
;

CREATE TABLE employee
(
  id           INT UNSIGNED AUTO_INCREMENT,
  branch_id    INT UNSIGNED     NOT NULL,
  first_name   VARCHAR(100)     NOT NULL,
  last_name    VARCHAR(100)     NOT NULL,
  middle_name  VARCHAR(100)              DEFAULT NULL,
  job_title    VARCHAR(100)     NOT NULL,
  phone_number VARCHAR(30)               DEFAULT NULL,
  email        VARCHAR(100)              DEFAULT NULL,
  gender       CHAR(1)          NOT NULL,
  birth_date   DATE             NOT NULL,
  hire_date    DATE             NOT NULL DEFAULT (CURRENT_DATE),
  description  VARCHAR(300)              DEFAULT NULL,
  avatar_path  VARCHAR(255)              DEFAULT NULL,
  is_deleted   TINYINT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  CONSTRAINT employee_branch_id_fk
    FOREIGN KEY (branch_id)
      REFERENCES branch(id)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT,
  CONSTRAINT seats_fare_conditions_check
    CHECK (gender IN ('M', 'W'))
)
;