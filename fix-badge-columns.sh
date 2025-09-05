#!/bin/bash

# Fix all remaining BadgeColumn usages in Filament files
echo "Fixing BadgeColumn usages in Filament v3 migration..."

# Convert BadgeColumn::make() calls to TextColumn::make()->badge()
find app/Filament -name "*.php" -type f -exec sed -i 's/BadgeColumn::/TextColumn::/g' {} \;

# Find and fix ->colors([]) method calls to use ->color() with match expressions
echo "Note: Manual review needed for complex color mappings in these files:"
grep -r "->colors(" app/Filament/ | cut -d: -f1 | sort | uniq

echo "BadgeColumn migration completed!"
echo "You may need to manually convert ->colors([]) to ->badge()->color() patterns"
