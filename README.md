# Staff Management - Sergio

This is a web application for managing Employee Hours and Vacation Records using Calendars and Departments.  
The web app is built using: [filamentPHP](https://filamentphp.com)

The system has 3 types of users with different roles and 2 panels located in `/admin` and `/personal`:

- **Super Admin**:
    - Full permissions.

- **Admin**:
    - Permissions for CRUD (Create, Read, Update, Delete) of all resources except Roles.
    - Access to the Admin panel and their own Personal panel as a user.

- **Personal**:
    - Access and permissions only over their own records.
    - CRUD on Timesheets and access to custom buttons for ease of use.
    - CRUD on Holidays. They can only edit if the status is "pending."

---

## Super Admin

<details>
<summary><strong>Roles</strong></summary>

![roles](resources/img/super_admin/Roles.PNG)  
![roles](resources/img/super_admin/Roles%20Form.PNG)

Permissions are managed using the Shield Plugin (link at the end).

</details>

---

## Admin

<details>
<summary><strong>Dashboard</strong></summary>

The Dashboard provides various utility widgets for a quick overview of important admin-related tasks.  
The charts have functional filters to change search ranges between week/month/year...
![Dashboard](resources/img/admin/Dashboard.PNG)

</details>

<details>
<summary><strong>Timesheets</strong></summary>

![Timesheets](resources/img/admin/Timesheets.PNG)

Example of filtering in Timesheets.
![Timesheets Filter](resources/img/admin/Timesheets%20Filter.PNG)

</details>

<details>
<summary><strong>Holidays</strong></summary>

![Holidays](resources/img/admin/Holidays.PNG)

</details>

<details>
<summary><strong>Employees</strong></summary>

Admins cannot view Super Admin Users.
![Employees](resources/img/admin/Employees.PNG)  

The selectors for State and City automatically filter based on the selected Country and State.
![Employees Form](resources/img/admin/Employees%20Form.PNG)

</details>

<details>
<summary><strong>Departments</strong></summary>

The table shows a count of Users for each Department.
![Departments](resources/img/admin/Departments.PNG)

</details>

<details>
<summary><strong>Calendars</strong></summary>

Calendars have an "active" field. By default, all new entries for Timesheets/Holidays use the active Calendar.
![Calendars](resources/img/admin/Calendars.PNG)

</details>

---

## Personal

<details>
<summary><strong>Dashboard</strong></summary>

The Dashboard provides various utility widgets for a quick overview of the most important user-related tasks.
![Dashboard](resources/img/personal/Dashboard.PNG)

</details>

<details>
<summary><strong>Timesheets</strong></summary>

The top buttons change depending on the last entry, allowing users to start/stop work or take breaks.
![Timesheets](resources/img/personal/Timesheets.PNG)

</details>

<details>
<summary><strong>Holidays</strong></summary>

Holidays can only be edited/deleted if their status is still "pending."
![Holidays](resources/img/personal/Holidays.PNG)

User functionalities are presented in modals rather than separate pages.
![Holidays Modal](resources/img/personal/Holidays%20Modal.PNG)

</details>

---

## Emails and Notifications

<details>
<summary><strong>Notification</strong></summary>

Whenever a resource is created/edited/deleted, a temporary notification appears on the page to inform the user.  
For Holidays, creating/editing automatically sends email notifications.
![Notification](resources/img/email/Notification.PNG)

</details>

<details>
<summary><strong>Emails</strong></summary>

![New](resources/img/email/New.PNG)  
![Updated](resources/img/email/Updated.PNG)  
![Resolved](resources/img/email/Resolved.PNG)

</details>

---

## Extras

<details>
<summary><strong>altwaireb/laravel-countries</strong></summary>

To populate the database with Country/State/City, I used:  
[altwaireb/laravel-countries](https://packagist.org/packages/altwaireb/laravel-countries)

</details>

<details>
<summary><strong>Mailer</strong></summary>

For email testing, I used [Mailtrap](https://mailtrap.io)

</details>

<details>
<summary><strong>Plugins</strong></summary>

- For assigning permissions and roles: [Shield](https://filamentphp.com/plugins/bezhansalleh-shield)  
- For exporting to Excel: [Excel](https://filamentphp.com/plugins/pxlrbt-excel)

</details>
