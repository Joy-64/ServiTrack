/*
SQLyog Ultimate v9.63 
MySQL - 5.5.5-10.4.32-MariaDB : Database - servitrackapp_servitrack
*********************************************************************
*/carga_datos_personales

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`servitrack` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `servitrack`;

/*Table structure for table `carga_datos_personales` */

DROP TABLE IF EXISTS `carga_datos_personales`;

CREATE TABLE `carga_datos_personales` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `apellido` VARCHAR(50) NOT NULL,
  `telefono` VARCHAR(50) NOT NULL,
  `ciudad` VARCHAR(50) NOT NULL,
  `calle` VARCHAR(50) NOT NULL,
  `numero_calle` DECIMAL(25,0) NOT NULL,
  `oficios` VARCHAR(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `carga_datos_personales` */

/*Table structure for table `contactanos` */

DROP TABLE IF EXISTS `contactanos`;

CREATE TABLE `contactanos` (
  `nombre` VARCHAR(50) DEFAULT NULL,
  `correo_electronico` VARCHAR(50) DEFAULT NULL,
  `telefono` VARCHAR(50) DEFAULT NULL,
  `mensaje` VARCHAR(255) DEFAULT NULL
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `contactanos` */

/*Table structure for table `oficios` */

DROP TABLE IF EXISTS `oficios`;

CREATE TABLE `oficios` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre_del oficio` VARCHAR(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `oficios` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;