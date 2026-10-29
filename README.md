# CareMatch AI

**Student Name:** Hamed Zon
**Student ID:** C00259228
**Project:** Final Year Project
**Current Stage:** Iteration 1

## 1. Project Overview

CareMatch AI is a web-based healthcare recruitment system designed to connect healthcare workers with employers looking to recruit staff.

The aim of this project is to make the recruitment process easier by allowing healthcare workers to find suitable job opportunities and employers to manage recruitment through one platform.

The system is being developed using PHP, MySQL, HTML, CSS and JavaScript. AI-based matching features are planned for later stages of development.

## 2. Project Objectives

The main objectives of CareMatch AI are:

- Allow healthcare workers to register and log in.
- Allow healthcare employers to register and log in.
- Provide separate dashboards for healthcare workers, employers and administrators.
- Allow employers to advertise healthcare job opportunities.
- Allow healthcare workers to search and apply for jobs.
- Develop an AI-based matching feature to recommend suitable jobs to healthcare workers.
- Provide administrators with tools to manage the platform.

## 3. Technologies Used

- **HTML:** Website structure.
- **CSS:** Website design and layout.
- **JavaScript:** Interactive website features.
- **PHP:** Backend development and authentication.
- **MySQL:** Database management.
- **XAMPP:** Local development environment.
- **Git and GitHub:** Version control and project management.

## 4. Iteration 1 — Authentication and User Roles

The first iteration focuses on developing the foundation of the CareMatch AI system.

### Features Implemented

- Healthcare worker registration and login.
- Employer registration and login.
- Administrator authentication.
- Separate dashboards for different user roles.
- Session-based authentication.
- Role-based access restrictions.
- Logout functionality.
- Homepage.
- JavaScript password visibility toggle.

These features establish the initial structure of the system before adding the main recruitment functionality.

## 5. Project Structure

The project is organised into the following main folders:

- `admin/` — Administrator-related pages.
- `auth/` — Registration and login functionality.
- `worker/` — Healthcare worker features.
- `employer/` — Employer features.
- `assets/` — CSS and JavaScript files.
- `config/` — Database configuration.
- `database/` — Database-related files.
- `includes/` — Shared PHP components planned for later development.

Some folders and files are still under development and will be updated in future iterations.

## 6. Running the Project Locally

1. Install XAMPP.
2. Place the CareMatch project folder inside the XAMPP `htdocs` directory, or configure Apache to serve the project from its current location.
3. Start Apache and MySQL from the XAMPP Control Panel.
4. Create the required MySQL database and tables.
5. Configure the local database connection in `config/database.php`.
6. Open the project through `http://localhost/carematch/` if using the default `htdocs` setup.

**Note:** The database configuration file is excluded from GitHub because it may contain sensitive login credentials. The database setup must be configured locally.

## 7. Iteration 2 — Planned Development

The second iteration will focus on implementing the main recruitment features.

Planned work includes:

- Reorganising the project structure.
- Creating reusable PHP headers, footers and authentication checks.
- Allowing healthcare workers to complete their profiles.
- Adding CV upload functionality.
- Allowing employers to create and manage job advertisements.
- Allowing healthcare workers to search and apply for jobs.
- Allowing employers to review job applications.
- Improving administrator management features.
- Continuing development towards AI-based job matching.

## 8. Future Development

Future iterations will focus on improving the recruitment process, implementing AI-based matching, testing the system and improving the overall user experience.

## 9. Author

**Hamed Zon**  
**Student ID:** C00259228

This project is being developed as part of my final-year studies.