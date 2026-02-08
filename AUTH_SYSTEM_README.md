# Sign In & Registration System

## Files Created

1. **auth.php** - PHP backend for authentication
2. **Updated index (3).html** - Added sign-in/registration modal

## How It Works

### Frontend (HTML/JavaScript)
- Sign In button opens a modal with two tabs: "Sign In" and "Register"
- Sign In form: Email + Password
- Register form: Name + Email + Phone + Password + Confirm Password
- Forms submit via AJAX to auth.php

### Backend (PHP)
- `auth.php` handles both registration and sign-in
- User data stored in `users_data.json` (auto-created)
- Passwords are securely hashed using PHP's `password_hash()`
- Activity logged in `auth_log.txt`

## Upload to cPanel

1. Upload these files to your cPanel public_html directory:
   - index (3).html
   - auth.php
   - save_booking.php (already exists)

2. Make sure PHP is enabled on your hosting

3. Set proper permissions:
   ```
   chmod 644 auth.php
   chmod 666 users_data.json (will be auto-created)
   chmod 666 auth_log.txt (will be auto-created)
   ```

## File Structure Created Automatically

- **users_data.json** - Stores all registered users
  ```json
  [
    {
      "id": "user_abc123",
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+880 1234567890",
      "password": "hashed_password",
      "registered_at": "2026-02-08 10:30:00",
      "last_login": "2026-02-08 11:45:00"
    }
  ]
  ```

- **auth_log.txt** - Activity log
  ```
  [2026-02-08 10:30:00] Registration successful - User: John Doe, Email: john@example.com
  [2026-02-08 11:45:00] Sign in successful - User: John Doe, Email: john@example.com
  ```

## Features

✅ User Registration with validation
✅ Secure password hashing
✅ Email uniqueness check
✅ Sign in with credentials verification
✅ Password confirmation on registration
✅ Remember me checkbox (frontend only)
✅ Responsive modal design
✅ Tab switching between Sign In and Register
✅ Activity logging
✅ Error handling and user feedback

## Security Notes

⚠️ **Important for Production:**
1. Add HTTPS/SSL certificate
2. Implement session management
3. Add CSRF protection
4. Rate limiting for login attempts
5. Consider using MySQL database instead of JSON file
6. Add email verification
7. Implement password reset functionality

## Testing

1. Open your website
2. Click "Sign in" button
3. Switch to "Register" tab
4. Create a new account
5. Switch back to "Sign In" tab
6. Log in with your credentials
7. Check `users_data.json` and `auth_log.txt` on your server

## Troubleshooting

**"Connection error" message:**
- Make sure auth.php is uploaded to cPanel
- Check PHP is enabled
- Verify file permissions

**"Could not save user data":**
- Check write permissions on directory
- Ensure PHP can create files

**Users not saving:**
- Check auth_log.txt for error messages
- Verify JSON syntax in users_data.json
