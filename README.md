# **Gym Membership Management System**

Welcome to the Gym Membership Management System! This project is designed to streamline the management of gym memberships, packages, and payments. The system includes an admin panel for managing members, payments, and packages, as well as a member panel for users to manage their profiles, view payment history, and subscribe to membership packages.

---

## **Features**
**1. Use Case Diagram**
![image](https://github.com/user-attachments/assets/ef321267-035b-4eda-b6d5-7aed1e5dd561)

**2. Entity Relationship Diagram (ERD)**
![image](https://github.com/user-attachments/assets/23c259dd-1019-4d23-9ba6-0ee516db47da)


### Admin Features
- View members: view member details.
- Manage packages: Add, update, and delete membership packages with descriptions and benefits.
- Manage payments: View and track all payments with filtering options (e.g., paid, canceled).
- Dashboard: Comprehensive overview of total members, active/inactive memberships, and monthly payment trends.
- Navigation: Responsive sidebar and hamburger menu.

### Member Features
- Profile management: Update personal details and upload profile pictures.
- Payment management: Make and View payment history with statuses like "Paid" or "Canceled."
- Package subscription: View packages benefits, and join available memberships.
- Dashboard: Personalized member dashboard with active package details and payment summary.

---

## **Technologies Used**

- **Frontend**:
  - HTML, CSS, JavaScript
  - Chart.js (for visualizing data)
- **Backend**:
  - PHP
  - MySQL
- **Tools**:
  - XAMPP (local development)
  - phpMyAdmin (database management)
  - GitHub (repository hosting)

---

## **Setup Instructions**

### Prerequisites
1. Install [XAMPP](https://www.apachefriends.org/index.html).
2. Clone this repository to your local machine:
   ```bash
   git clone https://github.com/your-username/gym-membership-system.git

## **Database Setup**
1. Open phpMyAdmin: http://localhost/phpmyadmin.
2. Create a database named gym_membership.
3. Import the SQL file provided in this repository (gym_membership.sql).
4. Ensure the database has the following tables:
    - members: Stores member details.
    - member_packages: Tracks membership subscriptions.
    - packages: Lists all available gym packages.
    - payments: Logs payment transactions.
    - admins: Stores admin credentials.
  
## **Configuration**
1. Configure database connection:
    - Open /includes/db.php.
    - Update the following lines with your database credentials:
      $conn = new mysqli('localhost', 'root', '', 'gym_membership');
2. Ensure the uploads/ directory has write permissions for storing profile pictures.

## **Running the System**
1. Access the system in your browser:
    - Admin Panel: http://localhost/gym_membership/admin/
    - Member Panel: http://localhost/gym_membership/member/
2. Use the following credentials:
   - Admin:
   Username: admin
   Password: admin

   - Member:
   Register as a new member through the member panel.
