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
  PRIMARY KEY (`proposal_id`),
  KEY `fk_proposal_event` (`event_id`),
  CONSTRAINT `fk_proposal_event` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

CREATE TABLE `proposal_detail` (
  `proposal_detail_id` int(11) NOT NULL AUTO_INCREMENT,
  `proposal_id` int(11) NOT NULL,
  `title` varchar(128) NOT NULL,
  `presentation_type` varchar(128) NOT NULL,
  `presentation_type_other` varchar(128) NOT NULL,
  `target_audience` varchar(128) NOT NULL,
  `age` varchar(128) NOT NULL,
  `age_other` varchar(128) NOT NULL,
  `time_preference` varchar(128) NOT NULL,
  `time_preference_other` varchar(128) NOT NULL,
  `space_preference` varchar(256) NOT NULL,
  `space_preference_other` varchar(256) NOT NULL,
  `participant_limit` tinyint(1) NOT NULL,
  `participant_limit_detail` varchar(128) DEFAULT NULL,
  `fee` tinyint(1) NOT NULL,
  `fee_detail` varchar(128) DEFAULT NULL,
  `presentation` text NOT NULL,
  PRIMARY KEY (`proposal_detail_id`),
  KEY `fk_proposal_detail_proposal` (`proposal_id`),
  CONSTRAINT `fk_proposal_detail_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `proposal` (`proposal_id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8;

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
