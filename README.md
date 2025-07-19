# Staff Management - Sergio

Esta es una web para la gestión de Registro de Horas y Vacaciones para Empleados usando Calendarios y Departamentos.
La web está creada utilizando: [filamentPHP](https://filamentphp.com)

La web tiene 3 tipos de usuarios con distintos roles y 2 paneles en /admin y /personal:
- Super Admin:
    - Permisos completos.
- Admin: 
    - Permisos para el CRUD de todo excepto Roles
    - Acceso al panel de Admin y a su Personal como usuario.
- Personal: 
    - Acceso y permisos únicamente sobre sus registros.
    - CRUD de Timesheets y acceso a botones custom para facilitar su uso.
    - CRUD de Holidays. Solo puede editar si el estado es "pending".

## Super Admin

![roles](resources/img/super_admin/Roles.PNG)
![roles](resources/img/super_admin/Roles%20Form.PNG)
Los permisos son creados con el Plugin de Shield (link al final)

## Admin

Admin tiene acceso a todos los registros de los recursos excepto a los Roles.
Las vistas bases son tablas y los botones de crear/editar redirigen a sus respectivos formularios.

![Dashboard](resources/img/admin/Dashboard.PNG)
El Dashboard tiene distintos Widgets de Utilidad para tener una vista rápida de lo importante para un Administrador.
Los gráficos tienen filtros funcionales para cambiar las franjas de búsqueda entre semana/mes/año...

![Timesheets](resources/img/admin/Timesheets.PNG)
![Timesheets Filter](resources/img/admin/Timesheets%20Filter.PNG)
Ejemplo del filtrado en Timesheets

![Holidays](resources/img/admin/Holidays.PNG)

![Employees](resources/img/admin/Employees.PNG)
Los admins no pueden ver a Super Admin Users

![Employees Form](resources/img/admin/Employees%20Form.PNG)
Los selectores de State y City filtran automáticamente según el Country y State elegidos.

![Departments](resources/img/admin/Departments.PNG)
En la tabla se muestra un count de Users para cada Department

![Calendars](resources/img/admin/Calendars.PNG)
Los calendarios tienen el campo active. Por defecto, todas las nuevas entradas de Timesheet/Holiday utilizan el Calendar activo.

## Personal

![Dashboard](resources/img/personal/Dashboard.PNG)
El Dashboard tiene distintos Widgets de Utilidad para tener una vista rápida de lo importante para un Usuario.

![Timesheets](resources/img/personal/Timesheets.PNG)
En la parte superior los botones van cambiando para comenzar/parar trabajos/pausas según la última entrada.

![Holidays](resources/img/personal/Holidays.PNG)
Las Holidays solo son editables/eliminables si aún no han sido revisadas.

![Holidays Modal](resources/img/personal/Holidays%20Modal.PNG)
Las funcionalidades para el Usuario son Modales en vez de páginas nuevas.

## Emails and Notifications

![Notification](resources/img/email/Notification.PNG)
Al crear/editar/eliminar cualquier recurso la página muestra una notifiación temporal para informar.
En el caso de las Holidays, crear/editar envía automáticamente correos electrónicos.

![New](resources/img/email/New.PNG)
![Updated](resources/img/email/Updated.PNG)
![Resolved](resources/img/email/Resolved.PNG)

## Extras

### altwaireb/laravel-countries

Para poblar la base de datos con Country/State/City he utlizado: [altwaireb/laravel-countries en Packagist](https://packagist.org/packages/altwaireb/laravel-countries)

### Mailer

Para probar el envío de correos he utilizado [mailtrap](https://mailtrap.io)

### Plugins

Para asignar permisos y roles: [shield](https://filamentphp.com/plugins/bezhansalleh-shield)
Para exportar a excel: [excel](https://filamentphp.com/plugins/pxlrbt-excel)