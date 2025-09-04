# Static Pages Structure

This document outlines the structure and implementation of static pages in EduVault Pro.

## Overview

EduVault Pro includes responsive static pages built with Laravel Blade templates and Tailwind CSS, following a mobile-first design approach.

## Page Structure

### Available Static Pages

1. **Landing Page** (`/`) - Hero section, features, testimonials, CTA
2. **About Us** (`/about`) - School management system overview
3. **Contact** (`/contact`) - Contact form, office details, map
4. **Terms of Service** (`/terms`) - Legal terms and conditions
5. **Privacy Policy** (`/privacy`) - Data protection and privacy policy
6. **Features** (`/features`) - Detailed feature breakdown
7. **Pricing** (`/pricing`) - One-time purchase pricing and licensing options

## File Structure

```
resources/views/
├── layouts/
│   ├── app.blade.php (main layout)
│   └── guest.blade.php (static pages layout)
├── pages/
│   ├── home.blade.php
│   ├── about.blade.php
│   ├── contact.blade.php
│   ├── terms.blade.php
│   ├── privacy.blade.php
│   ├── features.blade.php
│   └── pricing.blade.php
└── components/
    ├── navbar.blade.php
    └── hero.blade.php
```

## Layout System

### Guest Layout (`guest.blade.php`)
- Used for all public-facing static pages
- Includes responsive navigation
- Footer with contact information
- Mobile-first responsive design
- SEO meta tags
- Open Graph tags for social sharing

### App Layout (`app.blade.php`)
- Used for authenticated user interfaces
- Includes user navigation
- Dashboard layout structure

## Design Standards

### Mobile-First Approach
- Minimum width support: 320px
- Responsive breakpoints:
  - Mobile: 320px - 767px
  - Tablet: 768px - 1023px
  - Desktop: 1024px+

### Tailwind CSS Configuration
- Custom color palette for brand consistency
- Typography scale for content hierarchy
- Spacing system for consistent layouts
- Component classes for reusability

### Performance Optimization
- Optimized images with proper sizing
- Minimal JavaScript for static pages
- CSS optimization through Tailwind's purge
- Lazy loading for below-the-fold content

## SEO Implementation

### Meta Tags
- Title tags for each page
- Meta descriptions
- Keywords (where appropriate)
- Canonical URLs
- Language declarations

### Structured Data
- Organization schema markup
- Contact information schema
- Service schema for features

### Accessibility
- WCAG 2.1 AA compliance
- Proper heading hierarchy
- Alt text for images
- Keyboard navigation support
- Screen reader optimization

## Content Management

### Editable Content Areas
Static pages support easy content updates through:
- Blade template modifications
- Language files for text content
- Configuration files for contact details
- Asset management for images

### Internationalization Ready
- Language file structure prepared
- RTL support considerations
- Multi-currency pricing display

## Performance Metrics

### Target Performance
- Page load time: < 3 seconds
- First contentful paint: < 1.5 seconds
- Largest contentful paint: < 2.5 seconds
- Cumulative layout shift: < 0.1

### Optimization Techniques
- Image compression and WebP format
- Critical CSS inlining
- Resource preloading
- CDN integration ready

## Maintenance

### Content Updates
1. Edit Blade templates in `resources/views/pages/`
2. Update language files in `resources/lang/`
3. Modify configuration in `config/site.php`
4. Test responsive design across devices

### Version Control
- All static pages tracked in Git
- Staging environment for testing
- Production deployment via CI/CD

## Future Enhancements (v2.0)

- Multi-language support
- CMS integration for non-technical updates
- A/B testing capabilities
- Advanced analytics integration
- Progressive Web App features
