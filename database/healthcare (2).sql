-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 09:43 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `healthcare`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `facility_id` int(11) DEFAULT NULL,
  `appointment_date` datetime DEFAULT NULL,
  `reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointment_id`, `patient_id`, `doctor_id`, `facility_id`, `appointment_date`, `reason`) VALUES
(4, NULL, 2002, NULL, '0000-00-00 00:00:00', 'backpain'),
(5, NULL, 2005, NULL, '2025-05-15 08:00:00', ''),
(6, NULL, 2003, NULL, '2025-05-15 08:30:00', '');

-- --------------------------------------------------------

--
-- Table structure for table `diagnostic_procedures`
--

CREATE TABLE `diagnostic_procedures` (
  `procedure_id` int(11) NOT NULL,
  `procedure_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_invasive` tinyint(1) DEFAULT 0,
  `preparation_instructions` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diagnostic_procedures`
--

INSERT INTO `diagnostic_procedures` (`procedure_id`, `procedure_name`, `description`, `is_invasive`, `preparation_instructions`) VALUES
(1, 'MRI', 'Magnetic Resonance Imaging scan', 0, 'Remove all metal objects. Inform staff of any implants.'),
(2, 'CT Scan', 'Computed Tomography scan', 0, 'You may be asked not to eat for 4 hours before the scan.'),
(3, 'Ultrasound', 'Ultrasound imaging', 0, 'Preparation varies by body part. Follow specific instructions.'),
(4, 'X-Ray', 'X-Ray imaging', 0, 'Remove jewelry and metal objects from the area being examined.'),
(5, 'Colonoscopy', 'Examination of the large intestine', 1, 'Special diet for 1-3 days before. Bowel preparation required.'),
(6, 'Endoscopy', 'Examination of the digestive tract', 1, 'No food or drink for 6-8 hours before the procedure.'),
(7, 'Biopsy', 'Removal of tissue sample', 1, 'Varies by biopsy type. Follow doctor instructions.');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `doctor_id` int(11) NOT NULL,
  `specialization` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`doctor_id`, `specialization`) VALUES
(2001, 'Cardiology'),
(2002, 'Dermatology'),
(2003, 'Neurology'),
(2004, 'Pediatrics'),
(2005, 'Orthopedics'),
(2006, 'Ophthalmology'),
(2007, 'Psychiatry'),
(2008, 'Oncology'),
(2009, 'Internal Medicine'),
(2010, 'Family Medicine');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_facilities`
--

CREATE TABLE `doctor_facilities` (
  `doctor_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule`
--

CREATE TABLE `doctor_schedule` (
  `schedule_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `facility_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `duration` int(11) DEFAULT 30
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `facility_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_orders`
--

CREATE TABLE `medical_orders` (
  `order_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `order_text` text DEFAULT NULL,
  `order_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_reports`
--

CREATE TABLE `medical_reports` (
  `report_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `report_text` text DEFAULT NULL,
  `report_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medications`
--

CREATE TABLE `medications` (
  `medication_id` int(11) NOT NULL,
  `prescription_id` int(11) DEFAULT NULL,
  `medication_name` varchar(100) DEFAULT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `currently_used` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL,
  `type` enum('appointment','procedure','result','general') DEFAULT 'general',
  `reference_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `is_insured` tinyint(1) DEFAULT 1,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `date_of_birth`, `gender`, `is_insured`, `user_id`) VALUES
(3002, '1995-07-12', 'female', 1, NULL),
(3003, '1992-11-03', 'male', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `patient_parameters`
--

CREATE TABLE `patient_parameters` (
  `parameter_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `date_recorded` date DEFAULT NULL,
  `weight` decimal(5,2) DEFAULT NULL,
  `height` decimal(5,2) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `sugar_level` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `prescription_id` int(11) NOT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `prescription_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `procedure_appointments`
--

CREATE TABLE `procedure_appointments` (
  `appointment_id` int(11) NOT NULL,
  `referral_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `procedure_id` int(11) NOT NULL,
  `facility_id` int(11) DEFAULT NULL,
  `requested_date` datetime NOT NULL,
  `scheduled_date` datetime DEFAULT NULL,
  `status` enum('requested','scheduled','completed','cancelled') DEFAULT 'requested',
  `notification_sent` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `procedure_referrals`
--

CREATE TABLE `procedure_referrals` (
  `referral_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `procedure_id` int(11) NOT NULL,
  `referral_date` date NOT NULL,
  `expiration_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','used','expired') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `reminder_id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `reminder_text` text DEFAULT NULL,
  `reminder_date` date DEFAULT NULL,
  `type` enum('medication','appointment','vaccination') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shared_information`
--

CREATE TABLE `shared_information` (
  `share_id` int(11) NOT NULL,
  `from_doctor_id` int(11) DEFAULT NULL,
  `to_doctor_id` int(11) DEFAULT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `shared_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('doctor','patient') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`) VALUES
(1, 'TestUser', '$2y$10$dXJMpEXG2Yb9jH3WLJ2SH.S.4Lko8KFmgOuW3QJrr36VdwMjX8tOe', 'test@example.com', 'patient'),
(2001, 'dr.ahmed', 'test123', 'dr.ahmed@example.com', 'doctor'),
(2002, 'dr.lejla', 'test123', 'dr.lejla@example.com', 'doctor'),
(2003, 'dr.nermin', 'test123', 'dr.nermin@example.com', 'doctor'),
(2004, 'dr.sanja', 'test123', 'dr.sanja@example.com', 'doctor'),
(2005, 'dr.emir', 'test123', 'dr.emir@example.com', 'doctor'),
(2006, 'dr.alma', 'test123', 'dr.alma@example.com', 'doctor'),
(2007, 'dr.tarik', 'test123', 'dr.tarik@example.com', 'doctor'),
(2008, 'dr.amela', 'test123', 'dr.amela@example.com', 'doctor'),
(2009, 'dr.samir', 'test123', 'dr.samir@example.com', 'doctor'),
(2010, 'dr.selma', 'test123', 'dr.selma@example.com', 'doctor'),
(3001, 'selma', 'pass123', 'selma@example.com', 'patient'),
(3002, 'ajla.k', 'test123', 'ajla.k@example.com', 'patient'),
(3003, 'edin.s', 'test123', 'edin.s@example.com', 'patient'),
(3005, 'selmapacijent', 'hashed_password', 'selma2@example.com', 'patient'),
(3006, 'TestPatient', '$2y$10$dXJMpEXG2Yb9jH3WLJ2SH.S.4Lko8KFmgOuW3QJrr36VdwMjX8tOe', 'patient@example.com', 'patient');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `diagnostic_procedures`
--
ALTER TABLE `diagnostic_procedures`
  ADD PRIMARY KEY (`procedure_id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`doctor_id`);

--
-- Indexes for table `doctor_facilities`
--
ALTER TABLE `doctor_facilities`
  ADD PRIMARY KEY (`doctor_id`,`facility_id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD PRIMARY KEY (`schedule_id`),
  ADD UNIQUE KEY `doctor_id` (`doctor_id`,`facility_id`,`date`,`time`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`facility_id`);

--
-- Indexes for table `medical_orders`
--
ALTER TABLE `medical_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `medical_reports`
--
ALTER TABLE `medical_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `medications`
--
ALTER TABLE `medications`
  ADD PRIMARY KEY (`medication_id`),
  ADD KEY `prescription_id` (`prescription_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`),
  ADD KEY `fk_patients_user` (`user_id`);

--
-- Indexes for table `patient_parameters`
--
ALTER TABLE `patient_parameters`
  ADD PRIMARY KEY (`parameter_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `procedure_appointments`
--
ALTER TABLE `procedure_appointments`
  ADD PRIMARY KEY (`appointment_id`),
  ADD KEY `referral_id` (`referral_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `procedure_id` (`procedure_id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `procedure_referrals`
--
ALTER TABLE `procedure_referrals`
  ADD PRIMARY KEY (`referral_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `procedure_id` (`procedure_id`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`reminder_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `shared_information`
--
ALTER TABLE `shared_information`
  ADD PRIMARY KEY (`share_id`),
  ADD KEY `from_doctor_id` (`from_doctor_id`),
  ADD KEY `to_doctor_id` (`to_doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `diagnostic_procedures`
--
ALTER TABLE `diagnostic_procedures`
  MODIFY `procedure_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `facility_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_orders`
--
ALTER TABLE `medical_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medical_reports`
--
ALTER TABLE `medical_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `medications`
--
ALTER TABLE `medications`
  MODIFY `medication_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient_parameters`
--
ALTER TABLE `patient_parameters`
  MODIFY `parameter_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `procedure_appointments`
--
ALTER TABLE `procedure_appointments`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `procedure_referrals`
--
ALTER TABLE `procedure_referrals`
  MODIFY `referral_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `reminder_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shared_information`
--
ALTER TABLE `shared_information`
  MODIFY `share_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3007;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`);

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `doctor_facilities`
--
ALTER TABLE `doctor_facilities`
  ADD CONSTRAINT `doctor_facilities_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `doctor_facilities_ibfk_2` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_schedule`
--
ALTER TABLE `doctor_schedule`
  ADD CONSTRAINT `doctor_schedule_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `doctor_schedule_ibfk_2` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`);

--
-- Constraints for table `medical_orders`
--
ALTER TABLE `medical_orders`
  ADD CONSTRAINT `medical_orders_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `medical_orders_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `medical_reports`
--
ALTER TABLE `medical_reports`
  ADD CONSTRAINT `medical_reports_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `medical_reports_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `medications`
--
ALTER TABLE `medications`
  ADD CONSTRAINT `medications_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`prescription_id`);

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patients_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `patient_parameters`
--
ALTER TABLE `patient_parameters`
  ADD CONSTRAINT `patient_parameters_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `procedure_appointments`
--
ALTER TABLE `procedure_appointments`
  ADD CONSTRAINT `procedure_appointments_ibfk_1` FOREIGN KEY (`referral_id`) REFERENCES `procedure_referrals` (`referral_id`),
  ADD CONSTRAINT `procedure_appointments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `procedure_appointments_ibfk_3` FOREIGN KEY (`procedure_id`) REFERENCES `diagnostic_procedures` (`procedure_id`),
  ADD CONSTRAINT `procedure_appointments_ibfk_4` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`);

--
-- Constraints for table `procedure_referrals`
--
ALTER TABLE `procedure_referrals`
  ADD CONSTRAINT `procedure_referrals_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `medical_orders` (`order_id`),
  ADD CONSTRAINT `procedure_referrals_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `procedure_referrals_ibfk_3` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `procedure_referrals_ibfk_4` FOREIGN KEY (`procedure_id`) REFERENCES `diagnostic_procedures` (`procedure_id`);

--
-- Constraints for table `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);

--
-- Constraints for table `shared_information`
--
ALTER TABLE `shared_information`
  ADD CONSTRAINT `shared_information_ibfk_1` FOREIGN KEY (`from_doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `shared_information_ibfk_2` FOREIGN KEY (`to_doctor_id`) REFERENCES `doctors` (`doctor_id`),
  ADD CONSTRAINT `shared_information_ibfk_3` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
