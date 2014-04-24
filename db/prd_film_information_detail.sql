-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 28, 2013 at 08:33 PM
-- Server version: 5.0.51b-community-nt-log
-- PHP Version: 5.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `db_tstandardsteel`
--

-- --------------------------------------------------------

--
-- Table structure for table `prd_film_information_detail`
--

CREATE TABLE IF NOT EXISTS `prd_film_information_detail` (
  `temp` int(50) NOT NULL auto_increment,
  `film_id` varchar(255) NOT NULL,
  `new_film_id` varchar(255) NOT NULL,
  `coil_lot_no` varchar(255) NOT NULL,
  `coil_group_code` varchar(255) NOT NULL,
  `product_dtl_id` varchar(255) NOT NULL,
  `strip` varchar(255) NOT NULL,
  `iron_machine` varchar(255) NOT NULL,
  `duration` varchar(255) NOT NULL,
  `start_date` date default NULL,
  `status` varchar(255) NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY  (`temp`)
) ENGINE=MyISAM  DEFAULT CHARSET=tis620 AUTO_INCREMENT=162 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
