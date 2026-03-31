# Guia para la creacion de la base de datos
## Para el proyecto de la materia Base de Datos Multimedia

---

## 1. Estructura de las tablas

### 1.0. Creacion de la base de datos y usarla

```sql
CREATE DATABASE IF NOT EXISTS dfmSecure;
USE dfmSecure;
```

### 1.1. Tablas de usuarios y seguridad

#### Tabla `roles`(Roles de los usuarios)
|   Campo   |   Tipo   |   Restricciones   |
|   :---   |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   name   |   VARCHAR(50)   |   NOT NULL   |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |


#### Tabla `addresses`(Direcciones de los usuarios)
|   Campo   |   Tipo   |   Restricciones   |
|   :---   |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   type   |   VARCHAR(255)   |   NOT NULL   |
|   country   |   VARCHAR(255)   |   NOT NULL   |
|   state   |   VARCHAR(255)   |   NOT NULL   |
|   city   |   VARCHAR(255)   |   NOT NULL   |
|   neighborhood   |   VARCHAR(255)   |   NOT NULL   |
|   street   |   VARCHAR(255)   |   NOT NULL   |
|   external_number   |   VARCHAR(10)   |   NOT NULL   |
|   internal_number   |   VARCHAR(10)   |   NULL   |
|   zip_code   |   VARCHAR(10)   |   NOT NULL   |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |


#### Tabla `users` (Usuarios)
|   Campo   |   Tipo   |   Restricciones   |
|   :---    |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   name   |   VARCHAR(80)   |   NOT NULL   |
|   father_lastname   |   VARCHAR(50)   |   NOT NULL   |
|   mother_lastname   |   VARCHAR(50)   |   NULL   |
|   username   |   VARCHAR(30)   |   UNIQUE  NOT NULL   |
|   profile_picture_url   |   TEXT   |   NULL   |
|   profile_picture_blob   |   LONGBLOB   |   NULL   |
|   email   |   VARCHAR(80)   |   UNIQUE  NOT NULL   |
|   password   |   VARCHAR(255)   |   NOT NULL   |
|   phone   |   VARCHAR(20)   |   UNIQUE  NOT NULL   |
|   birth_date   |   DATE   |   NULL   |
|   gender   |   VARCHAR(50)   |   NOT NULL DEFAULT 'other'  |
|   email_verified_at   |   TIMESTAMP   |   NULL   |
|   role_id   |   BIGINT   |   UNSIGNED NOT NULL   |
|   address_id   |   BIGINT   |   UNSIGNED NULL   |
|   remember_token   |   VARCHAR(100)   |   NULL   |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |


#### Tabla `fiscals`(Datos fiscales de los usuarios)
|   Campo   |   Tipo   |   Restricciones   |
|   :---    |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   rfc |   VARCHAR(13)   |   UNIQUE  NOT NULL   |
|   fiscal_type |   VARCHAR(255)   |   NOT NULL DEFAULT 'natural_person'   |
|   company_name    | VARCHAR(255) | NULL |
|   tax_regime | VARCHAR(5) | NOT NULL |
|   user_id | BIGINT | UNSIGNED NOT NULL |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |


### 1.2. Tablas de gestion de vehiculos

#### Tabla `vehicle_models` (Marcas de los vehiculos)
|   Campo   |   Tipo   |   Restricciones   |
|   :---    |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   year   |   YEAR |   NOT NULL   |
|   brand   |   VARCHAR(255)   |   NOT NULL   |
|   sub_brand   |   VARCHAR(255)   |   NOT NULL   |
|   version   |   VARCHAR(255)   |   NOT NULL   |
|   color   |   VARCHAR(255)   |   NOT NULL   |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |


#### Tabla `insured_vehicles` (Vehiculos asegurados)
|   Campo   |   Tipo   |   Restricciones   |
|   :---   |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   vin   |   VARCHAR(17)   |   UNIQUE  NOT NULL   |
|   plate   |   VARCHAR(10)   |   UNIQUE  NOT NULL   |
|   vehicle_model_id   |   BIGINT   |  UNSIGNED NOT NULL   |
|   user_id   |   BIGINT   |  UNSIGNED NOT NULL   |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |


### 1.3. Tablas de seguros y polizas

#### Tabla `plans` (Planes de seguro)
|   Campo   |   Tipo   |   Restricciones   |
|   :---   |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   name   |   VARCHAR(255)   |   UNIQUE NOT NULL   |
|   status   |   VARCHAR(255)   |   NOT NULL   |
|   info   |   JSON   |   NOT NULL   |
|   price   |   DECIMAL(10,2)   |   NOT NULL   |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |

#### Tabla `policies` (Polizas de seguro)
|   Campo   |   Tipo   |   Restricciones   |
|   :---    |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   folio   |   CHAR(36)   |   UNIQUE NOT NULL   |
|   status   |   VARCHAR(255)   |   NOT NULL DEFAULT 'pending'  |
|   begin_validity   |   DATE   |   NOT NULL   |
|   end_validity   |   DATE   |   NOT NULL   |
|   vehicle_id   |   BIGINT   |  UNSIGNED NOT NULL   |
|   insured_id   |   BIGINT   |  UNSIGNED NOT NULL   |
|   plan_id   |   BIGINT   |  UNSIGNED NOT NULL   |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |


### 1.4. Tablas de siniestros

#### Tabla `sinisters` (Siniestros)
|   Campo   |   Tipo   |   Restricciones   |
|   :---    |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   occur_date   |   DATE   |   NOT NULL   |
|   report_date   |   DATE   |   NOT NULL   |
|   close_date   |   DATE   |   NULL   |
|   description   |   TEXT   |   NOT NULL   |
|   location   |   VARCHAR(255)   |  NOT NULL   |
|   status  |   VARCHAR(255)   |  NOT NULL DEFAULT 'reported'  |
|   adjuster_id  |   BIGINT   |  UNSIGNED NULL  |
|   supervisor_id  |   BIGINT   |  UNSIGNED NULL  |
|   policy_id  |   BIGINT   |  UNSIGNED NULL  |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |


#### Tabla `sinister_multimedia`(Multimedia de los siniestros)
|   Campo   |   Tipo   |   Restricciones   |
|   :---    |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   type   |   VARCHAR(255)   |   NOT NULL   |
|   blob_file   |   LONGBLOB   |   NULL   |
|   path_file   |   LONGTEXT   |   NULL   |
|   description   |   TEXT   |   NULL   |
|   mime    |   VARCHAR(255)   |   NULL   |
|   size    |   INT   |   NULL   |
|   thumbnail   |   VARCHAR(255)    | NULL |
|   sinister_id   |   BIGINT   |  UNSIGNED NOT NULL   |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |


#### Tabla `sinister_comments`(Comentarios de los siniestros)
|   Campo   |   Tipo   |   Restricciones   |
|   :---    |   :---:  |   :---:   |
|   id   |   BIGINT   |   UNSIGNED AUTO_INCREMENT PRIMARY KEY   |
|   comment   |   LONGTEXT   |   NOT NULL   |
|   sinister_id   |   BIGINT   |  UNSIGNED NOT NULL   |
|   user_id   |   BIGINT   |  UNSIGNED NOT NULL   |
|   created_at   |   TIMESTAMP   |   NULL   |
|   updated_at   |   TIMESTAMP   |   NULL   |
|   deleted_at   |   TIMESTAMP   |   NULL   |
