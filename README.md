# Staff Management - Sergio

Esta es una web para la gestión de Registro de Horas y Vacaciones para Empleados usando Calendarios y Departamentos.  
La web está creada utilizando: [filamentPHP](https://filamentphp.com)

La web tiene 3 tipos de usuarios con distintos roles y 2 paneles en `/admin` y `/personal`:

- **Super Admin**:
    - Permisos completos.

- **Admin**:
    - Permisos para el CRUD de todo excepto Roles
    - Acceso al panel de Admin y a su Personal como usuario.

- **Personal**:
    - Acceso y permisos únicamente sobre sus registros.
    - CRUD de Timesheets y acceso a botones custom para facilitar su uso.
    - CRUD de Holidays. Solo puede editar si el estado es "pending".

---

## Super Admin

<details>
<summary><strong>Roles</strong></summary>

![roles](resources/img/super_admin/Roles.PNG)  
![roles](resources/img/super_admin/Roles%20Form.PNG)

Los permisos son creados con el Plugin de Shield (link al final)

</details>

---

## Admin

<details>
<summary><strong>Dashboard</strong></summary>

El Dashboard tiene distintos Widgets de utilidad para tener una vista rápida de lo importante para un Administrador.  
Los gráficos tienen filtros funcionales para cambiar las franjas de búsqueda entre semana/mes/año...
![Dashboard](resources/img/admin/Dashboard.PNG)

</details>

<details>
<summary><strong>Timesheets</strong></summary>

![Timesheets](resources/img/admin/Timesheets.PNG)

Ejemplo del filtrado en Timesheets.
![Timesheets Filter](resources/img/admin/Timesheets%20Filter.PNG)


</details>

<details>
<summary><strong>Holidays</strong></summary>

![Holidays](resources/img/admin/Holidays.PNG)

</details>

<details>
<summary><strong>Employees</strong></summary>

Los admins no pueden ver a Super Admin Users.
![Employees](resources/img/admin/Employees.PNG)  

Los selectores de State y City filtran automáticamente según el Country y State elegidos.
![Employees Form](resources/img/admin/Employees%20Form.PNG)  

</details>

<details>
<summary><strong>Departments</strong></summary>

En la tabla se muestra un count de Users para cada Department.
![Departments](resources/img/admin/Departments.PNG)  

</details>

<details>
<summary><strong>Calendars</strong></summary>

Los calendarios tienen el campo active. Por defecto, todas las nuevas entradas de Timesheet/Holiday utilizan el Calendar activo.
![Calendars](resources/img/admin/Calendars.PNG)  

</details>

---

## Personal

<details>
<summary><strong>Dashboard</strong></summary>

El Dashboard tiene distintos Widgets de Utilidad para tener una vista rápida de lo importante para un Usuario.
![Dashboard](resources/img/personal/Dashboard.PNG)  

</details>

<details>
<summary><strong>Timesheets</strong></summary>

En la parte superior los botones van cambiando para comenzar/parar trabajos/pausas según la última entrada.
![Timesheets](resources/img/personal/Timesheets.PNG)  

</details>

<details>
<summary><strong>Holidays</strong></summary>

Las Holidays solo son editables/eliminables si aún no han sido revisadas.
![Holidays](resources/img/personal/Holidays.PNG)  

Las funcionalidades para el Usuario son Modales en vez de páginas nuevas.
![Holidays Modal](resources/img/personal/Holidays%20Modal.PNG)  

</details>

---

## Emails and Notifications

<details>
<summary><strong>Notification</strong></summary>

Al crear/editar/eliminar cualquier recurso la página muestra una notifiación temporal para informar.  
En el caso de las Holidays, crear/editar envía automáticamente correos electrónicos.
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

Para poblar la base de datos con Country/State/City he utlizado:  
[altwaireb/laravel-countries](https://packagist.org/packages/altwaireb/laravel-countries)

</details>

<details>
<summary><strong>Mailer</strong></summary>

Para probar el envío de correos he utilizado [Mailtrap](https://mailtrap.io)

</details>

<details>
<summary><strong>Plugins</strong></summary>

- Para asignar permisos y roles: [Shield](https://filamentphp.com/plugins/bezhansalleh-shield)  
- Para exportar a excel: [Excel](https://filamentphp.com/plugins/pxlrbt-excel)

</details>