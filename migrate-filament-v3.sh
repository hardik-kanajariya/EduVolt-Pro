#!/bin/bash

# Filament v4 to v3 Migration Script
echo "Starting Filament v4 to v3 migration..."

# Step 1: Update composer dependencies
echo "Step 1: Removing vendor directory and updating dependencies..."
cd /home/hardik/Documents/GitHub/EduVolt-Pro

# Remove vendor and lock file to ensure clean install
rm -rf vendor composer.lock

# Install Filament v3
composer require filament/filament:^3.2 --no-update
composer update

echo "Dependencies updated successfully!"

# Step 2: Fix Schema imports and method signatures
echo "Step 2: Updating Schema usage throughout the codebase..."

# Find and replace Filament\Schemas\Schema with Filament\Forms\Form
find app/Filament -name "*.php" -type f -exec sed -i 's/use Filament\\Schemas\\Schema;/use Filament\\Forms\\Form;/g' {} \;

# Replace Schema parameter types with Form
find app/Filament -name "*.php" -type f -exec sed -i 's/Schema \$schema/Form \$form/g' {} \;
find app/Filament -name "*.php" -type f -exec sed -i 's/: Schema/: Form/g' {} \;

# Replace ->schema( calls with ->schema(
find app/Filament -name "*.php" -type f -exec sed -i 's/\$schema->schema(/\$form->schema(/g' {} \;

# Step 3: Fix BadgeColumn usage
echo "Step 3: Fixing BadgeColumn usage..."
find app/Filament -name "*.php" -type f -exec sed -i 's/Tables\\Columns\\BadgeColumn/Tables\\Columns\\TextColumn/g' {} \;

# Replace ->colors( method with ->badge()->color( pattern
find app/Filament -name "*.php" -type f -exec sed -i 's/->colors(/->badge()->color(fn (string $state): string => match ($state) {/g' {} \;

echo "Migration script completed! Please review the changes manually."
echo "You may need to:"
echo "1. Fix any remaining syntax errors"
echo "2. Update Panel Provider configurations if needed"
echo "3. Test the application thoroughly"
echo "4. Update any custom Filament components"
