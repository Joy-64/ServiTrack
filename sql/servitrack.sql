/*
SQLyog Ultimate v9.63 
MySQL - 5.5.5-10.1.38-MariaDB : Database - servitrack
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`servitrack` /*!40100 DEFAULT CHARACTER SET utf8mb4 */;

USE `servitrack`;

/*Table structure for table `codigo_postal` */

DROP TABLE IF EXISTS `codigo_postal`;

CREATE TABLE `codigo_postal` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `codpostal` varchar(10) NOT NULL,
  `localidad` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `codigo_postal` */

/*Table structure for table `contactanos` */

DROP TABLE IF EXISTS `contactanos`;

CREATE TABLE `contactanos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `correo_electronico` varchar(50) DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `mensaje` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

/*Data for the table `contactanos` */

insert  into `contactanos`(`id`,`nombre`,`correo_electronico`,`telefono`,`mensaje`) values (1,'','','',''),(2,'Agus','agus@gmail.com','','Prueba de mensaje numero uno'),(3,'Agus','agus@gmail.com','','Prueba de mensaje numero uno'),(4,'Agus','agus@gmail.com','','Prueba de mensaje numero dos.'),(5,'loremipsum','agus@gmail.com','1111111111','Prueba de mensaje numero infinito. Prueba de mensaje numero infinito. Prueba de mensaje numero infinito. Prueba de mensaje numero infinito. Prueba de mensaje numero infinito. Prueba de mensaje numero infinito. Prueba de mensaje numero infinito. Prueba de ');

/*Table structure for table `domicilio_trabajador` */

DROP TABLE IF EXISTS `domicilio_trabajador`;

CREATE TABLE `domicilio_trabajador` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_trabajador` bigint(20) DEFAULT NULL,
  `id_cod_postal` bigint(20) DEFAULT NULL,
  `valor` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_trabajador` (`id_trabajador`),
  KEY `id_cod_postal` (`id_cod_postal`),
  CONSTRAINT `domicilio_trabajador_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id`),
  CONSTRAINT `domicilio_trabajador_ibfk_2` FOREIGN KEY (`id_cod_postal`) REFERENCES `codigo_postal` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `domicilio_trabajador` */

/*Table structure for table `medios_contactos` */

DROP TABLE IF EXISTS `medios_contactos`;

CREATE TABLE `medios_contactos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `medios_contactos` */

/*Table structure for table `oficios` */

DROP TABLE IF EXISTS `oficios`;

CREATE TABLE `oficios` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `oficios` */

/*Table structure for table `tipos_documentos` */

DROP TABLE IF EXISTS `tipos_documentos`;

CREATE TABLE `tipos_documentos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

/*Data for the table `tipos_documentos` */

insert  into `tipos_documentos`(`id`,`descripcion`) values (1,'DNI'),(2,'LE'),(3,'LC'),(4,'PASAPORTE');

/*Table structure for table `trabajador_medios_contactos` */

DROP TABLE IF EXISTS `trabajador_medios_contactos`;

CREATE TABLE `trabajador_medios_contactos` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_trabajador` bigint(20) DEFAULT NULL,
  `id_medio_contacto` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_trabajador` (`id_trabajador`),
  KEY `id_medio_contacto` (`id_medio_contacto`),
  CONSTRAINT `trabajador_medios_contactos_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id`),
  CONSTRAINT `trabajador_medios_contactos_ibfk_2` FOREIGN KEY (`id_medio_contacto`) REFERENCES `medios_contactos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `trabajador_medios_contactos` */

/*Table structure for table `trabajador_oficio` */

DROP TABLE IF EXISTS `trabajador_oficio`;

CREATE TABLE `trabajador_oficio` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_trabajador` bigint(20) DEFAULT NULL,
  `id_oficio` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_trabajador` (`id_trabajador`),
  KEY `id_oficio` (`id_oficio`),
  CONSTRAINT `trabajador_oficio_ibfk_1` FOREIGN KEY (`id_trabajador`) REFERENCES `trabajadores` (`id`),
  CONSTRAINT `trabajador_oficio_ibfk_2` FOREIGN KEY (`id_oficio`) REFERENCES `oficios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `trabajador_oficio` */

/*Table structure for table `trabajadores` */

DROP TABLE IF EXISTS `trabajadores`;

CREATE TABLE `trabajadores` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `idTDoc` bigint(50) NOT NULL,
  `NDoc` decimal(9,0) NOT NULL,
  `FNacimiento` date NOT NULL,
  `Sexo` varchar(20) NOT NULL,
  `Cuil` decimal(11,0) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idTDoc` (`idTDoc`),
  CONSTRAINT `trabajadores_ibfk_1` FOREIGN KEY (`idTDoc`) REFERENCES `tipos_documentos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*Data for the table `trabajadores` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
