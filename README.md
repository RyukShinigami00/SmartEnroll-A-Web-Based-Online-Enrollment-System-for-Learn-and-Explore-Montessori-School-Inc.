# SmartEnroll

A web-based online enrollment and student scheduling system for Learn and Explore Montessori School.

## Overview

SmartEnroll is a web-based system designed to streamline the enrollment process and class scheduling for Learn and Explore Montessori School. It replaces manual, paper-based procedures with a centralized digital platform, allowing parents to enroll students online and administrators to manage records, sections, and schedules efficiently.

## Features

- **Online Enrollment**: Parents can submit enrollment applications online.
- **Admin Dashboard**: Manage enrollment requests, approve/reject students, and assign sections.
- **Section Management**: Create sections per grade level with capacity limits.
- **Schedule Management**: Define class schedules per section with conflict detection.
- **Student Schedule Viewing**: Students can view their assigned class schedules and download as PDF.
- **Audit Log**: Track all admin actions for accountability.
- **Email Verification**: Secure registration with 6-digit email verification.

## Technology Stack

- **Backend**: PHP 8.x (MVC architecture)
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Email**: PHPMailer with SMTP
- **Version Control**: Git & GitHub

## Project Structure
SmartEnroll/
├── app/
│ ├── Config/ # Environment configuration
│ ├── Controllers/ # Application logic
│ ├── Core/ # Router, Session, View
│ ├── Helpers/ # Mailer, utilities
│ └── Models/ # Database models
├── database/ # Database migrations and seeders
├── public/ # Public assets (CSS, JS, images)
├── storage/ # Logs and cache
├── vendor/ # Composer dependencies
