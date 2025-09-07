--
-- Create this database if it does not already exist`
--

CREATE DATABASE IF NOT EXISTS quiz;

--
-- Use this database for the rest of the query executions`
--

USE quiz;

--
-- Start query executions`
--

START TRANSACTION;

--
-- Create the users table`
--

CREATE TABLE `users` (
  `userID` int NOT NULL AUTO_INCREMENT,
  `user_profile` varchar(1000) NOT NULL,
  `fullname` varchar(1000) NOT NULL,
  `email` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `country` varchar(1000) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `created_on` varchar(255) NOT NULL,
  `user_type` varchar(255) NOT NULL,
  `user_status` varchar(255) NOT NULL,
   PRIMARY KEY  (`userID`)
)  ENGINE = InnoDB;

--
-- Create the wallet table`
--

CREATE TABLE `wallet` (
  `walletID` int NOT NULL AUTO_INCREMENT,
  `wallet_amount` int NOT NULL,
  `wallet_currency` varchar(2000) NOT NULL,
  `wallet_status` varchar(2000) NOT NULL,
  `account_number` bigint NOT NULL,
  `bank` varchar(2000) NOT NULL,
  `bank_code` varchar(100) NOT NULL,
  `recipient_code` varchar(100) NOT NULL,
  `userID` int NOT NULL,
  PRIMARY KEY  (`walletID`),
  FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
)  ENGINE = InnoDB;

--
-- Create the questions table`
--

CREATE TABLE `questions` (
  `questionID` int NOT NULL AUTO_INCREMENT, 
  `question_title` varchar(2000) NOT NULL,
  `question_details` varchar(5000) NOT NULL,
  `option_one` varchar(2000) NOT NULL,
  `option_two` varchar(2000) NOT NULL,
  `option_three` varchar(2000) NOT NULL,
  `option_four` varchar(2000) NOT NULL,
  `correct_option` varchar(10) NOT NULL,
   PRIMARY KEY  (`questionID`)
) ENGINE = InnoDB;

--
-- Create the quiz_trials table`
--

CREATE TABLE `quiz_trials` (
  `trialID` int NOT NULL AUTO_INCREMENT, 
  `trial_plan` int NOT NULL,
  `correct_trials` int NOT NULL,
  `trial_date` varchar(2000) NOT NULL,
  `userID` int NOT NULL,
  PRIMARY KEY  (`trialID`),
  FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB;


--
-- Create the general_notifications table`
--

CREATE TABLE `general_notifications` (
  `notificationID` int NOT NULL AUTO_INCREMENT,
  `notification_title` varchar(1000) NOT NULL,
  `notification_details` varchar(1000) NOT NULL,
  `notification_type` varchar(255) NOT NULL,
  `notification_receiver` varchar(255) NOT NULL,
  `notification_date` varchar(50) NOT NULL,
  `notification_status` varchar(50) NOT NULL,
  PRIMARY KEY  (`notificationID`)
) ENGINE = InnoDB;


--
-- Create the mailbox table`
--

CREATE TABLE `mailbox` (
  `mailID` int NOT NULL AUTO_INCREMENT,
  `mail_type` varchar(100) NOT NULL,
  `mail_subject` varchar(1000) NOT NULL,
  `mail_sender` varchar(2000) NOT NULL,
  `mail_receiver` varchar(2000) NOT NULL,
  `mail_date` varchar(100) NOT NULL,
  `mail_time` varchar(100) NOT NULL,
  `mail_message` varchar(5000) NOT NULL,
  `mail_filename` varchar(255) NOT NULL,
  `mail_extension` varchar(20) NOT NULL,
   PRIMARY KEY  (`mailID`)
) ENGINE = InnoDB;

--
-- Create the transaction_payments table`
--

CREATE TABLE `payments` (
  `paymentID` int NOT NULL AUTO_INCREMENT, 
  `payment_email` varchar(100) NOT NULL,
  `payment_amount` int NOT NULL,
  `payment_account` varchar(255) NOT NULL,
  `payment_bank` varchar(255) NOT NULL,
  `payment_date` varchar(255) NOT NULL,
  `payment_status` varchar(255) NOT NULL,
  `payment_txref` varchar(255) NOT NULL,
  `userID` int NOT NULL,
  FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE ON UPDATE CASCADE
  PRIMARY KEY (`paymentID`)
) ENGINE = InnoDB;

--
-- Finish up the query executions and commit the changes to the server`
--

COMMIT;
