# Demo Mode Feature Documentation

## Overview

EduVault Pro includes a powerful demo mode feature that automatically fills login forms with sample credentials across all panels. This is designed for demonstration purposes, making it easy for potential customers to test the system without needing to remember multiple login credentials.

## Features

### 🔧 **Environment-Based Control**
- Toggle demo mode via environment variable `DEMO_MODE=true/false`
- Easy to enable/disable for production vs demo environments

### 🎯 **Auto-Fill Login Forms**
- Automatically fills email and password fields on all panel login pages
- Works with Filament's authentication system
- Supports all 5 panels: Admin, Faculty, Student, Parent, School

### 📱 **Visual Demo Indicators**
- Shows demo notice banner on login pages when active
- Displays role and credentials being used
- Clean, professional UI integration

### 🏠 **Homepage Integration**
- Demo credentials section on homepage (when demo mode is enabled)
- Quick access buttons for each panel
- Credential display for easy reference

## Panel Credentials

| Panel | Email | Password | Role |
|-------|-------|----------|------|
| Admin | admin@eduvaultpro.com | admin123 | Super Administrator |
| Faculty | teacher@eduvaultpro.com | teacher123 | John Teacher |
| Student | student@eduvaultpro.com | student123 | Jane Student |
| Parent | parent@eduvaultpro.com | parent123 | Parent Smith |
| School | schooladmin@eduvaultpro.com | admin123 | School Administrator |

## Configuration

### Environment Variable
Add to your `.env` file:
```env
DEMO_MODE=true
```

### Artisan Commands

#### Check Demo Status
```bash
php artisan demo:info
```

#### Enable Demo Mode
```bash
php artisan demo:toggle --enable
```

#### Disable Demo Mode
```bash
php artisan demo:toggle --disable
```

#### Toggle Current State
```bash
php artisan demo:toggle
```

## Implementation Details

### Files Created/Modified

1. **`app/Services/DemoCredentialsService.php`**
   - Central service for managing demo credentials
   - Provides credentials for each panel
   - Checks demo mode status

2. **`app/Providers/DemoModeServiceProvider.php`**
   - Service provider for demo mode functionality
   - Registers Filament render hooks
   - Handles auto-fill JavaScript injection

3. **`resources/views/components/demo-credentials.blade.php`**
   - Demo credentials display component
   - Shows all available panels and credentials
   - Only visible when demo mode is enabled

4. **`app/Console/Commands/ToggleDemoMode.php`**
   - Artisan command to toggle demo mode
   - Updates .env file automatically
   - Provides feedback on current status

5. **`app/Console/Commands/ShowDemoInfo.php`**
   - Shows current demo mode status
   - Lists all available credentials
   - Displays panel URLs and management commands

### Integration Points

- **Bootstrap/providers.php**: Added DemoModeServiceProvider
- **Config/app.php**: Added demo_mode configuration
- **Home page**: Includes demo credentials component
- **Pricing page**: Includes demo credentials component
- **All Filament panels**: Auto-fill functionality via render hooks

## Security Considerations

### ⚠️ **Production Safety**
- Demo mode should be DISABLED in production environments
- Credentials are visible to anyone when demo mode is active
- Only use for demonstration and testing purposes

### 🔒 **Best Practices**
- Set `DEMO_MODE=false` in production `.env`
- Use different credentials in production database
- Monitor access logs when demo mode is active

## Usage Scenarios

### 🎭 **Sales Demonstrations**
1. Enable demo mode before client presentations
2. Show homepage with credential display
3. Click any panel to auto-login
4. Demonstrate features without credential management

### 🧪 **Testing & Development**
1. Quick access to all panels during development
2. Easy testing of multi-user scenarios
3. No need to remember multiple passwords

### 📚 **Training & Onboarding**
1. Train new team members on different panels
2. Show role-based access controls
3. Demonstrate user experience for each role

## Troubleshooting

### Demo Mode Not Working
1. Check `.env` file for `DEMO_MODE=true`
2. Clear application cache: `php artisan cache:clear`
3. Check if DemoModeServiceProvider is registered

### Auto-Fill Not Working
1. Ensure JavaScript is enabled in browser
2. Check browser console for errors
3. Verify Filament render hooks are working

### Credentials Not Displaying
1. Check if demo users exist in database
2. Run database seeders if needed
3. Verify demo mode is enabled

## Future Enhancements

- [ ] Admin panel toggle for demo mode
- [ ] Custom demo credentials configuration
- [ ] Demo session time limits
- [ ] Usage analytics for demo mode
- [ ] Role-specific feature highlighting

---

## Quick Start

1. **Enable Demo Mode**:
   ```bash
   php artisan demo:toggle --enable
   ```

2. **Visit Homepage**:
   Navigate to your homepage to see demo credentials section

3. **Test Login**:
   Click any panel link and credentials will auto-fill

4. **Check Status**:
   ```bash
   php artisan demo:info
   ```

This demo mode feature significantly improves the user experience for demonstrations and makes EduVault Pro more accessible for potential customers to evaluate.
