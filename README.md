# Hostel Management System in PHP, MySQL, and Bootstrap

A modern Hostel Management System built with PHP, MySQL, and Bootstrap. This project features an improved, user-friendly interface and enhanced functionality for managing hostel operations efficiently.

## Features

- Responsive and intuitive UI with Bootstrap
- Student registration and management
- Room allocation and management
- Staff and warden management
- Hostel fee management and payment tracking
- Visitor and guest log management
- Room availability and occupancy dashboard
- Reports for students, rooms, and payments
- Secure login for admin, staff, and students
- Easy-to-use navigation and modern design

## Installation

Follow these steps to get the Hostel Management System up and running:

### 1. Install XAMPP

- Download XAMPP from [https://www.apachefriends.org/index.html](https://www.apachefriends.org/index.html) and install it on your system.

### 2. Place Project in htdocs

- Copy or move the entire project folder into the `htdocs` directory inside your XAMPP installation.  
  - Example: `C:\xampp\htdocs\hostel-management` (Windows) or `/Applications/XAMPP/htdocs/hostel-management` (macOS).

### 3. Start Apache and MySQL

- Open the XAMPP Control Panel.
- Start both **Apache** and **MySQL** services.

### 4. Create the Database

- Open your browser and go to [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
- Click on **Databases** and create a new database named `hostel`.
- With the `hostel` database selected, go to the **SQL** tab and run the following SQL statements to create the required tables:

```sql
CREATE TABLE `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fullname` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `room_id` int DEFAULT NULL,
  `admission_date` date NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `rooms` (
  `id` int NOT NULL AUTO_INCREMENT,
  `room_number` varchar(20) NOT NULL,
  `capacity` int NOT NULL,
  `current_occupancy` int NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`room_number`)
);

CREATE TABLE `staff` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `fees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `due_date` date NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);

CREATE TABLE `visitors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `visitor_name` varchar(255) NOT NULL,
  `visit_date` date NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`student_id`) REFERENCES `students`(`id`)
);
```

### 5. Configure Database Connection

- Open the `db-connect.php` file in your project folder.
- Update the database credentials as follows:
  - Host: `localhost`
  - Username: `root`
  - Password: (leave blank by default)
  - Database: `hostel`

### 6. Access the Application

- In your browser, go to [http://localhost/hostel-management](http://localhost/hostel-management) (replace `hostel-management` with your project folder name if different).
- On first access, you will be prompted to create an Administrator account.

### 7. Additional Notes

- If you change the database name, username, or password, update them in `db-connect.php`.
- Make sure Apache and MySQL are running whenever you use the application.
- For any issues with permissions or access, ensure your project folder and files have the correct read/write permissions.

## To Do

- Add password authentication and user roles
- Implement advanced reporting and analytics
- Add notifications for due fees and important events
- Improve mobile responsiveness and accessibility
- Integrate with email/SMS for alerts

---

Enjoy managing your hostel with a modern, efficient, and user-friendly system!
