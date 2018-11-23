CREATE TABLE `ntpc_openid_random_pass` (
  `uname` varchar(100) NOT NULL default '',
  `random_pass` varchar(255) NOT NULL default '',
  PRIMARY KEY (`uname`)
) ENGINE=MyISAM ;

CREATE TABLE `ntpc_openid_login_rules` (
  `rule` varchar(255) NOT NULL,
  `sort` int NOT NULL,
  `enable` tinyint(1) NOT NULL default 1,
  PRIMARY KEY (`rule`)
) ENGINE=MyISAM ;
