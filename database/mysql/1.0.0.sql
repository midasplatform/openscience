CREATE TABLE IF NOT EXISTS `openscience_anatomicalarea` (
  `anatomicalarea_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`anatomicalarea_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `openscience_anatomicalarea2algorithm` (
 `anatomicalarea_id` bigint(20) NOT NULL,
 `algorithm_id` bigint(20) NOT NULL
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `openscience_algorithm` (
  `algorithm_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `publications` text DEFAULT NULL,
  `data` text DEFAULT NULL,
  `folder_id` bigint(20) NOT NULL,
  `performance` double DEFAULT NULL,
  `dashboard` text DEFAULT NULL,
  `sourcecode` text DEFAULT NULL,
  PRIMARY KEY (`algorithm_id`)
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `openscience_algorithm2resultset` (
 `algorithm_id` bigint(20) NOT NULL,
 `resultset_id` bigint(20) NOT NULL
) DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `openscience_resultset` (
  `resultset_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `dashboard` text DEFAULT NULL,
  `performance` double DEFAULT NULL,
  `data` text DEFAULT NULL,
  `folder_id` bigint(20) NOT NULL,
  `contents` text DEFAULT NULL,
  PRIMARY KEY (`resultset_id`)
) DEFAULT CHARSET=utf8;
