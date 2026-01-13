# TODO: Change "subscribers" to "users"

## Database Changes

- [x] Update create_database.sql: Rename table from newsletter_subscribers to newsletter_users
- [x] Update sample_data.sql: Change INSERT INTO newsletter_subscribers to newsletter_users

## PHP Files

- [x] admin/subscribers.php: Rename file to users.php, update all text, variables, and queries
- [x] admin/includes/sidebar.php: Change href="subscribers.php" to "users.php", text "Subscribers" to "Users"
- [x] admin/dashboard.php: Update queries, text "Subscribers" to "Users", "Subscriber Terbaru" to "User Terbaru", variable $recent_subscribers to $recent_users
- [x] api/newsletter.php: Update table name in queries
- [x] test_connection.php: Update test description and query

## Documentation

- [x] README.md: Update table name and description
- [x] INSTALL.md: Update table name
- [x] TESTING_REPORT.md: Update table name and description

## Verification

- [x] Test the changes by running the application
