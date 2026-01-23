# Admin Panel Documentation

## Overview

A modern, responsive admin panel built with Laravel 12, featuring a clean UI design using SASS and reusable components.

## Features

- ✅ Modern, responsive design
- ✅ Login authentication
- ✅ User profile management
- ✅ Change password functionality
- ✅ Reusable UI components
- ✅ SASS-based styling with variables and mixins
- ✅ Clean, maintainable code structure

## File Structure

### Controllers
- `app/Http/Controllers/Auth/LoginController.php` - Handles login/logout
- `app/Http/Controllers/Admin/ProfileController.php` - Manages user profile and password changes
- `app/Http/Controllers/Admin/DashboardController.php` - Dashboard controller

### Routes
- `/admin/login` - Login page (GET)
- `/admin/login` - Login submission (POST)
- `/admin/logout` - Logout (POST)
- `/admin/dashboard` - Dashboard (GET, requires auth)
- `/admin/profile` - Profile page (GET, requires auth)
- `/admin/profile` - Update profile (PUT, requires auth)
- `/admin/profile/password` - Update password (PUT, requires auth)

### Views

#### Layouts
- `resources/views/layouts/admin.blade.php` - Main admin layout with sidebar and header

#### Pages
- `resources/views/admin/auth/login.blade.php` - Login page
- `resources/views/admin/profile/show.blade.php` - Profile and password change page
- `resources/views/admin/dashboard.blade.php` - Dashboard page

#### Components
- `resources/views/components/button.blade.php` - Reusable button component
- `resources/views/components/input.blade.php` - Reusable input component
- `resources/views/components/card.blade.php` - Reusable card component

### SASS Structure

```
resources/sass/
├── app.scss              # Main SASS file
├── _variables.scss       # Color palette, spacing, typography variables
├── _mixins.scss          # Reusable mixins (buttons, inputs, cards, etc.)
├── _base.scss            # Base styles and resets
├── _components.scss      # Component styles (cards, alerts, badges, etc.)
├── _layouts.scss         # Admin layout styles
└── _pages.scss           # Page-specific styles
```

## Usage

### Authentication

The admin panel uses Laravel's built-in authentication. Users need to be logged in to access the dashboard and profile pages.

### Styling

The SASS files use a modular approach:

- **Variables** (`_variables.scss`): Define colors, spacing, typography, etc.
- **Mixins** (`_mixins.scss`): Reusable style patterns
- **Components**: Pre-styled UI components
- **Pages**: Page-specific styles

### Components

#### Button Component
```blade
<x-button type="submit" variant="primary" size="lg">Submit</x-button>
```

Variants: `primary`, `secondary`, `outline`, `ghost`, `danger`
Sizes: `sm`, `md`, `lg`

#### Input Component
```blade
<x-input 
    type="email" 
    name="email" 
    label="Email Address" 
    :required="true"
/>
```

#### Card Component
```blade
<x-card :hover="true">
    <div class="card-header">
        <h3>Card Title</h3>
    </div>
    <div class="card-body">
        Card content
    </div>
</x-card>
```

## Building Assets

To compile SASS and build assets:

```bash
npm run dev      # Development mode with hot reload
npm run build    # Production build
```

## Customization

### Colors

Edit `resources/sass/_variables.scss` to customize the color palette:

```scss
$primary: #6366f1;
$secondary: #8b5cf6;
// ... etc
```

### Components

Add new components in `resources/sass/_components.scss` or create new component files and import them in `app.scss`.

### Layout

Modify `resources/views/layouts/admin.blade.php` to customize the admin layout structure.

## Security

- All admin routes are protected by the `auth` middleware
- Password changes require current password verification
- CSRF protection is enabled on all forms
- Passwords are hashed using Laravel's default hashing

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Notes

- The admin panel uses Tailwind CSS 4.0 alongside SASS for utility classes
- All forms include proper validation and error handling
- The design is fully responsive and works on mobile, tablet, and desktop devices

