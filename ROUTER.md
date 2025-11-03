# React Router DOM Setup Guide

## Overview

This project uses React Router DOM v7.9.5 for client-side routing, providing a single-page application (SPA) experience.

## Project Structure

```
resources/js/
├── app.jsx                    # Main app entry with BrowserRouter
├── routes.jsx                 # Centralized route definitions
├── bootstrap.js               # Bootstrap configuration
│
├── components/
│   └── Sidebar.jsx            # Reusable sidebar with navigation
│
├── layouts/
│   └── DashboardLayout.jsx    # Dashboard layout wrapper
│
└── pages/
    ├── Login.jsx              # Login page
    └── Dashboard.jsx          # Dashboard with stats & activities
```

## Routes Configuration

| Route | Component | Description |
|-------|-----------|-------------|
| `/` | Dashboard | Home page (Dashboard) |
| `/dashboard` | Dashboard | Dashboard page |
| `/login` | Login | Login page |
| `*` | Redirect to `/` | Catch-all route |

## Key Features

### 1. Client-Side Routing
- No page reloads when navigating
- Fast, seamless transitions
- Browser back/forward buttons work correctly

### 2. Active Link Highlighting
The sidebar automatically highlights the current active page:
```jsx
const isActive = location.pathname === item.href;
```

### 3. Programmatic Navigation
Use the `useNavigate` hook for navigation in code:
```jsx
import { useNavigate } from 'react-router-dom';

const navigate = useNavigate();
navigate('/dashboard'); // Navigate to dashboard
```

### 4. Link Component
Use `Link` instead of `<a>` tags for internal navigation:
```jsx
import { Link } from 'react-router-dom';

<Link to="/dashboard">Dashboard</Link>
```

## Adding New Routes

### 1. Create a new page component:
```jsx
// resources/js/pages/NewPage.jsx
import React from 'react';
import DashboardLayout from '../layouts/DashboardLayout';

export default function NewPage() {
  return (
    <DashboardLayout>
      <div className="p-8">
        <h1>New Page</h1>
      </div>
    </DashboardLayout>
  );
}
```

### 2. Add the route in `routes.jsx`:
```jsx
import NewPage from './pages/NewPage';

<Route path="/new-page" element={<NewPage />} />
```

### 3. Add link in Sidebar:
```jsx
{
  title: 'New Page',
  href: '/new-page',
  icon: <svg>...</svg>,
}
```

## Navigation Hooks

### useNavigate
Navigate programmatically:
```jsx
const navigate = useNavigate();
navigate('/path');           // Navigate to path
navigate(-1);                // Go back
navigate(1);                 // Go forward
```

### useLocation
Get current location:
```jsx
const location = useLocation();
console.log(location.pathname); // Current path
console.log(location.search);   // Query string
```

### useParams
Get route parameters:
```jsx
// Route: /users/:id
const { id } = useParams();
```

## Protected Routes (Future Implementation)

To add authentication:

```jsx
function ProtectedRoute({ children }) {
  const isAuthenticated = // check auth status
  
  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }
  
  return children;
}

// Usage in routes.jsx
<Route 
  path="/dashboard" 
  element={
    <ProtectedRoute>
      <Dashboard />
    </ProtectedRoute>
  } 
/>
```

## Laravel Integration

The Laravel backend uses a catch-all route to serve the React app:

```php
// routes/web.php
Route::get('/{any}', function () {
    return view('app');
})->where('any', '.*');
```

This ensures all routes are handled by React Router, not Laravel.

## Best Practices

1. **Use `Link` for internal navigation**: Don't use `<a href>` as it causes full page reloads
2. **Use `useNavigate` for programmatic navigation**: Better than `window.location.href`
3. **Centralize routes**: Keep all routes in `routes.jsx` for easy maintenance
4. **Use layouts**: Wrap pages in layout components for consistent UI
5. **Handle 404s**: Always include a catch-all route

## Common Issues

### Issue: Routes not working after page refresh
**Solution**: Make sure Laravel's catch-all route is configured correctly.

### Issue: Links cause full page reload
**Solution**: Use `Link` component from react-router-dom, not `<a>` tags.

### Issue: Active state not updating
**Solution**: Use `useLocation()` hook to track current path.

## Development Workflow

1. Start the dev server:
   ```bash
   make dev
   # or
   npm run dev
   ```

2. Navigate to http://localhost:8000

3. Changes to React files will hot-reload automatically

## Resources

- [React Router Documentation](https://reactrouter.com/)
- [React Router Tutorial](https://reactrouter.com/en/main/start/tutorial)
- [useNavigate Hook](https://reactrouter.com/en/main/hooks/use-navigate)
- [Link Component](https://reactrouter.com/en/main/components/link)
