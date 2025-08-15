# CSS File Structure

This directory contains the organized CSS files for the EquipRent ecommerce website.

## File Organization

### `main.css`
- **Purpose**: Main CSS file that imports all component styles
- **Contains**: Only @import statements for all other CSS files
- **Usage**: Include this file in your HTML to load all styles

### `reset.css`
- **Purpose**: Global reset styles and base typography
- **Contains**: 
  - CSS reset rules
  - Global button styles
  - Section title styles
  - Container utilities

### `navigation.css`
- **Purpose**: Navigation bar and menu styles
- **Contains**:
  - Navbar layout and positioning
  - Navigation menu styles
  - Search, cart, and login button styles
  - Mobile hamburger menu styles

### `hero.css`
- **Purpose**: Hero section styles
- **Contains**:
  - Hero container layout
  - Hero title and subtitle typography
  - Hero button layouts
  - Hero image placeholder styles

### `categories.css`
- **Purpose**: Categories section styles
- **Contains**:
  - Categories grid layout (horizontal)
  - Category item card styles
  - Category icon styles
  - Hover effects and animations

### `products.css`
- **Purpose**: Product grid and card styles
- **Contains**:
  - Products grid layout
  - Product card styles
  - Product image placeholders
  - Product information layout
  - Price and rating styles

### `cta.css`
- **Purpose**: Call-to-action section styles
- **Contains**:
  - CTA background and layout
  - CTA typography
  - CTA button styles

### `footer.css`
- **Purpose**: Footer section styles
- **Contains**:
  - Footer layout and grid
  - Footer section typography
  - Social media link styles
  - Footer bottom styles

### `responsive.css`
- **Purpose**: Responsive design and media queries
- **Contains**:
  - Mobile navigation styles
  - Tablet and mobile breakpoints
  - Responsive typography adjustments
  - Mobile-specific layout changes

## Benefits of This Structure

1. **Modularity**: Each component has its own CSS file
2. **Maintainability**: Easy to find and modify specific styles
3. **Reusability**: Components can be reused across different pages
4. **Organization**: Clear separation of concerns
5. **Team Collaboration**: Multiple developers can work on different components
6. **Debugging**: Easier to identify and fix styling issues

## Usage

To use all styles, simply include `main.css` in your HTML:

```html
<link rel="stylesheet" href="css/main.css">
```

To use only specific components, include individual CSS files:

```html
<link rel="stylesheet" href="css/reset.css">
<link rel="stylesheet" href="css/navigation.css">
<link rel="stylesheet" href="css/hero.css">
```

## Color Palette

- **Primary Blue**: #2563eb
- **Secondary Blue**: #1d4ed8
- **Light Blue**: #3b82f6
- **Dark Text**: #1e293b
- **Medium Text**: #64748b
- **Light Text**: #cbd5e1
- **Background**: #f8fafc
- **Border**: #f1f5f9
- **Success**: #10b981
- **Warning**: #f59e0b
- **Error**: #ef4444
