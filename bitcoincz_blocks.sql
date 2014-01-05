CREATE TABLE IF NOT EXISTS `bitcoincz_blocks` (
  `block_no` bigint(20) NOT NULL,
  `is_mature` tinyint(4) NOT NULL,
  `total_score` double NOT NULL,
  `mining_duration` time NOT NULL,
  `date_found` datetime NOT NULL,
  `confirmations` bigint(20) NOT NULL,
  `total_shares` double NOT NULL,
  `date_started` datetime NOT NULL,
  `reward` text NOT NULL,
  `nmc_reward` text NOT NULL,
  `created` timestamp NOT NULL default '0000-00-00 00:00:00',
  `updated` timestamp NOT NULL default '0000-00-00 00:00:00' on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`block_no`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
