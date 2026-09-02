/*
SQLyog Ultimate v9.63 
MySQL - 5.5.5-10.4.32-MariaDB : Database - servitrack
*********************************************************************
*/

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
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `cuit` decimal(50,0) NOT NULL,
  `telefono` varchar(50) NOT NULL,
  `ciudad` varchar(50) NOT NULL,
  `calle` varchar(50) NOT NULL,
  `numero_calle` decimal(25,0) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `carga_datos_personales` */

/*Table structure for table `contactanos` */

DROP TABLE IF EXISTS `contactanos`;

CREATE TABLE `contactanos` (
  `nombre` varchar(50) DEFAULT NULL,
  `correo_electronico` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `mensaje` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `contactanos` */

/*Table structure for table `oficios` */

DROP TABLE IF EXISTS `oficios`;

CREATE TABLE `oficios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_del oficio` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `oficios` */

insert  into `oficios`(`id`,`nombre_del oficio`) values (1,'Electricista'),(2,'Plomero'),(3,'Albañil'),(4,'Carpintero'),(5,'Pintor'),(6,'Cerrajero'),(7,'Herrero'),(8,'Gasista');

/*Table structure for table `persona_oficio` */

DROP TABLE IF EXISTS `persona_oficio`;

CREATE TABLE `persona_oficio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_persona` int(11) DEFAULT NULL,
  `id_oficio` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_persona` (`id_persona`),
  KEY `fk_oficio` (`id_oficio`),
  CONSTRAINT `fk_oficio` FOREIGN KEY (`id_oficio`) REFERENCES `oficios` (`id`),
  CONSTRAINT `fk_persona` FOREIGN KEY (`id_persona`) REFERENCES `carga_datos_personales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `persona_oficio` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
