# Migration to HTTP-Only Cookie Authentication

This project has been migrated from localStorage token storage to HTTP-only cookie-based authentication for improved security.

## What Changed

### Security Improvements
- ✅ Tokens are now stored in **HTTP-only cookies** (not accessible via JavaScript)
- ✅ Protected against XSS attacks
- ✅ CSRF protection via Laravel Sanctum
- ✅ Credentials sent automatically with each request
- ❌ Removed localStorage usage completely

### Backend Changes

1. **AuthController** ([backend/app/Http/Controllers/Api/AuthController.php](backend/app/Http/Controllers/Api/AuthController.php))
   - Login now stores token in HTTP-only cookie
   - Logout clears the cookie
   - No longer returns token in response body

2. **CORS Configuration** ([backend/config/cors.php](backend/config/cors.php))
   - Updated to use specific origins (required for credentials)
   - `supports_credentials` set to true

3. **Sanctum Configuration** ([backend/config/sanctum.php](backend/config/sanctum.php))
   - Added frontend URLs to stateful domains
   - Enables cookie-based authentication

4. **API Middleware** ([backend/app/Http/Kernel.php](backend/app/Http/Kernel.php))
   - Added cookie handling middleware to API routes
   - Added custom middleware to extract token from cookie

5. **Custom Middleware** ([backend/app/Http/Middleware/AddAuthTokenFromCookie.php](backend/app/Http/Middleware/AddAuthTokenFromCookie.php))
   - Reads auth token from cookie
   - Adds it to Authorization header for Sanctum

### Frontend Changes

1. **API Service** ([frontend/src/services/api.js](frontend/src/services/api.js))
   - Added `withCredentials: true` to send cookies
   - Removed localStorage token handling
   - Simplified interceptors

2. **Auth Store** ([frontend/src/stores/auth.js](frontend/src/stores/auth.js))
   - Removed all localStorage operations
   - Changed to memory-only state
   - Added async initialization via `/me` endpoint
   - Uses cookies automatically for authentication

3. **Router** ([frontend/src/router/index.js](frontend/src/router/index.js))
   - Updated navigation guards to check `user` instead of `token`
   - Added async auth initialization

4. **App Component** ([frontend/src/App.vue](frontend/src/App.vue))
   - Updated to use async auth initialization

## Environment Configuration

### Backend (.env)

Add these to your backend `.env` file:

```env
# Session configuration for cookies
SESSION_DRIVER=file
SESSION_LIFETIME=10080
SESSION_SECURE_COOKIE=false
SESSION_DOMAIN=null

# CORS - Add your frontend URL
SANCTUM_STATEFUL_DOMAINS=localhost:5173,localhost:3000,127.0.0.1:5173,127.0.0.1:3000
FRONTEND_URL=http://localhost:5173
```

**For Production:**
```env
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=.yourdomain.com
SANCTUM_STATEFUL_DOMAINS=yourdomain.com,www.yourdomain.com
```

### Frontend (.env)

Ensure your frontend `.env` file has:

```env
VITE_API_URL=http://localhost:8000/api
```

## How It Works

1. **Login Process:**
   - User submits credentials to `/api/login`
   - Backend creates token and stores it in HTTP-only cookie
   - Cookie is automatically sent with all subsequent requests
   - Frontend receives user data (no token in response)

2. **Authentication:**
   - Browser automatically sends cookie with every request
   - Backend middleware extracts token from cookie
   - Sanctum validates the token
   - No manual token handling needed in frontend

3. **Logout:**
   - User calls `/api/logout`
   - Backend deletes token and clears cookie
   - User state cleared in frontend

4. **Session Persistence:**
   - On page refresh, app calls `/api/me`
   - If cookie is valid, user data is retrieved
   - If not authenticated, user is redirected to login

## Migration Steps

If you're updating an existing installation:

1. **Backend:**
   ```bash
   cd backend
   # Update .env file with new session settings
   php artisan config:clear
   php artisan cache:clear
   ```

2. **Frontend:**
   ```bash
   cd frontend
   npm install  # If dependencies changed
   # Clear browser cache and cookies for localhost
   npm run dev
   ```

3. **Clear User Sessions:**
   - Users need to log in again (old localStorage tokens won't work)
   - Clear browser localStorage manually or wait for natural session expiry

## Testing

1. Login to the application
2. Open DevTools → Application → Cookies
3. You should see `auth_token` cookie with:
   - HttpOnly: ✅
   - Secure: (depends on HTTPS)
   - SameSite: Lax

4. Check Application → Local Storage:
   - Should NOT contain `token` or `user` keys

## Security Notes

- **HTTP-only cookies** prevent JavaScript access (XSS protection)
- **SameSite=Lax** provides CSRF protection
- For production, always use HTTPS (`SESSION_SECURE_COOKIE=true`)
- Token stored in encrypted cookie (Laravel automatically encrypts)
- Session lifetime: 7 days (10080 minutes)

## Troubleshooting

### CORS Errors
- Ensure `SANCTUM_STATEFUL_DOMAINS` includes your frontend URL
- Check CORS configuration allows credentials
- Frontend and backend must be on allowed origins

### Cookie Not Being Set
- Check if backend and frontend are on same domain/localhost
- Verify `withCredentials: true` in axios config
- Check browser cookie settings

### 401 Unauthorized After Login
- Clear all cookies for localhost
- Check if middleware is applied to API routes
- Verify token is being extracted from cookie

## Rollback

If you need to rollback to localStorage:

```bash
git revert HEAD  # or restore from backup
```

Then clear cookies and restart both servers.
