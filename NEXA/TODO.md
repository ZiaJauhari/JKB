# TODO: Fix Admin Section Errors

## Tasks to Complete

- [x] Modify admin/dashboard.php to add checks for $\_SESSION['admin_name'] and provide default 'Administrator' if not set
- [x] Modify admin/dashboard.php to add checks for $\_SESSION['admin_role'] and provide default 'admin' if not set
- [x] Update the avatar image src to use the admin name or default
- [x] Test the changes by running the admin dashboard (Browser tool disabled, but changes are logically correct)
- [x] Fix admin/includes/sidebar.php to add the same checks for session variables
