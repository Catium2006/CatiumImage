CREATE DATABASE `CatiumImage` /*!40100 DEFAULT CHARACTER SET utf8mb3 */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE CatiumImage;

CREATE TABLE `mgmt` (
  `id` int NOT NULL,
  `password_md5` varchar(32) DEFAULT NULL,
  `background_img` varchar(64) DEFAULT NULL,
  `grid_img` varchar(64) DEFAULT NULL,
  `visit_count` int DEFAULT NULL,
  `file_count` int DEFAULT NULL,
  `notice` varchar(1024) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE TABLE `records` (
  `file_name_original` char(255) NOT NULL,
  `file_name_short` char(16) NOT NULL,
  `file_md5` char(32) NOT NULL,
  `file_size` int unsigned NOT NULL,
  `upload_time` timestamp NOT NULL,
  PRIMARY KEY (`file_name_original`,`file_size`,`upload_time`,`file_name_short`),
  UNIQUE KEY `file_md5_UNIQUE` (`file_md5`),
  UNIQUE KEY `file_name_short_UNIQUE` (`file_name_short`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
