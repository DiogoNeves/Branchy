-- phpMyAdmin SQL Dump
-- version 3.3.5
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2010 at 01:12 AM
-- Server version: 5.1.49
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: 'branchy'
--

-- --------------------------------------------------------

--
-- Table structure for table 'content'
--

DROP TABLE IF EXISTS content;
CREATE TABLE IF NOT EXISTS content (
  target_name varchar(64) NOT NULL,
  path varchar(128) NOT NULL,
  PRIMARY KEY (target_name),
  KEY path (path)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table 'content'
--

INSERT INTO content (target_name, path) VALUES
('lookup', '/lookup/lookup_test.php');

-- --------------------------------------------------------

--
-- Table structure for table 'main_branch'
--

DROP TABLE IF EXISTS main_branch;
CREATE TABLE IF NOT EXISTS main_branch (
  branch_uid int(10) unsigned NOT NULL,
  path varchar(128) NOT NULL,
  allow_default tinyint(1) NOT NULL DEFAULT '1' COMMENT 'If a target doesn''t exist, allow to search using default behaviour',
  PRIMARY KEY (branch_uid),
  KEY path (path)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table 'main_branch'
--

INSERT INTO main_branch (branch_uid, path, allow_default) VALUES
(0, '/test1', 1);
