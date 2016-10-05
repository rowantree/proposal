
CREATE TABLE `event` (
  `event_id` int(11) NOT NULL,
  `event_code` varchar(45) DEFAULT NULL,
  `event_year` int(11) DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `proposal` (
  `proposal_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_id` int(11) NOT NULL,
  `legal_name` varchar(128) NOT NULL,
  `program_name` varchar(128) NOT NULL,
  `email_address` varchar(128) NOT NULL,
  `telephone_number` varchar(128) NOT NULL,
  `unavailable_times` varchar(128) NULL,
  `biography` text NOT NULL,
  `when_arriving` varchar(128) NULL,
  `last_attended` varchar(128) NULL,
  `entry_date` DATETIME NULL,
  PRIMARY KEY (`proposal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

CREATE TABLE `proposal_detail` (
  `proposal_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `proposal_id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `presentation_type` varchar(128) NULL,
  `presentation_type_other` varchar(128) NULL,
  `target_audience` varchar(128) NULL,
  `age` varchar(128) NULL,
  `age_other` varchar(128) NULL,
  `time_preference` varchar(128) NULL,
  `time_preference_other` varchar(128) NULL,
  `space_preference` varchar(256) NULL,
  `space_preference_other` varchar(256) NULL,
  `participant_limit` tinyint(1) NULL,
  `participant_limit_detail` varchar(128) NULL,
  `fee` tinyint(1) NULL,
  `fee_detail` varchar(128) NULL,
  `presentation` text NOT NULL,
  PRIMARY KEY (`proposal_detail_id`),
  KEY `fk_proposal_detail_proposal` (`proposal_id`),
  CONSTRAINT `fk_proposal_detail_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`proposal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `proposal_person` (
  `proposal_person_id` int(11) NOT NULL AUTO_INCREMENT,
  `proposal_id` int(11) NOT NULL,
  `program_name` varchar(128) NOT NULL,
  `bio` text NOT NULL,
  `legal_name` varchar(128) NOT NULL,
  PRIMARY KEY (`proposal_person_id`),
  KEY `fk_proposal_person_proposal` (`proposal_id`),
  CONSTRAINT `fk_proposal_person_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`proposal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
