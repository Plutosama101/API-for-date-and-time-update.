# API-for-date-and-time-update.
# Date & Time API

This is a RESTful API I built for handling date and time operations with a MySQL backend. It's perfect for applications that need to store, retrieve, and manage timestamp data.

✨ What It Does

· Gets current server time
· Creates new datetime records
· Updates existing records
· Handles CORS for cross-domain requests
· Validates datetime formats
· Returns clean JSON responses

🛠️ What You'll Need

· PHP 7.0+
· MySQL 5.7+
· PDO extension enabled

🗄️ Database Setup

First, create the table in your MySQL database:

```sql
CREATE TABLE datetime_records (
    id INT AUTO_INCREMENT PRIMARY KEY,
    datetime_field DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

⚙️ Configuration

Edit these values in the PHP file with your actual database credentials:

```php
$host = 'localhost';          // Your MySQL host
$dbname = 'your_database';    // Your database name
$username = 'your_username';  // Your MySQL username
$password = 'your_password';  // Your MySQL password
```

🚀 API Endpoints

GET / - Get Current Time

Returns server time and latest record

```bash
curl -X GET http://yourdomain.com/API_FOR_DATE_AND_TIME.php
```

POST / - Create Record

Adds a new datetime entry

```bash
curl -X POST http://yourdomain.com/API_FOR_DATE_AND_TIME.php \
  -H "Content-Type: application/json" \
  -d '{"datetime": "2023-11-15 10:30:00"}'
```

PUT / - Update Record

Modifies an existing record

```bash
curl -X PUT http://yourdomain.com/API_FOR_DATE_AND_TIME.php \
  -H "Content-Type: application/json" \
  -d '{"id": 1, "datetime": "2023-11-15 11:30:00"}'
```

📋 Example Responses

Success Response:

```json
{
    "success": true,
    "message": "DateTime record created successfully",
    "id": 1,
    "datetime": "2023-11-15 10:30:00"
}
```

Error Response:

```json
{
    "error": "datetime field is required"
}
```

💡 How You can Use It

I typically use this API for:

· Time tracking applications
· Event scheduling systems
· Logging timestamps for user actions
· Any app that needs consistent time recording

🔒 Security Notes

⚠️ Remember to:

· Use HTTPS in production
· Add authentication if needed
· Validate inputs on client side too
· Keep your database credentials secure

🆘 Need Help?

If you run into issues:

1. Check your database connection settings
2. Make sure the table exists with correct structure
3. Verify PDO extension is enabled in PHP
4. Check server error logs for details

Let me know if you have any questions or suggestions!
