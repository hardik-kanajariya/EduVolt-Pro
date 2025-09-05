#!/bin/bash

# Script to fix Filament v3 to v3 action compatibility issues
# This script will:
# 1. Replace recordActions with actions
# 2. Replace recordBulkActions with bulkActions
# 3. Replace Filament\Actions\ imports with Filament\Tables\Actions\ for table actions

echo "Starting Filament v3 action fixes..."

# Find all PHP files in the Filament directories
find /home/hardik/Documents/GitHub/EduVolt-Pro/app/Filament -name "*.php" -type f | while read -r file; do
    echo "Processing: $file"
    
    # Create backup
    cp "$file" "$file.bak"
    
    # Fix recordActions -> actions
    sed -i 's/->recordActions(/->actions(/g' "$file"
    
    # Fix recordBulkActions -> bulkActions
    sed -i 's/->recordBulkActions(/->bulkActions(/g' "$file"
    
    # Check if file contains table-related content (Tables, RelationManager, or Table class)
    if grep -q -E "(Tables\\\\|RelationManager|class.*Table)" "$file"; then
        # Only update action imports in table-related files
        sed -i 's/use Filament\\Actions\\ViewAction;/use Filament\\Tables\\Actions\\ViewAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\EditAction;/use Filament\\Tables\\Actions\\EditAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\DeleteAction;/use Filament\\Tables\\Actions\\DeleteAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\CreateAction;/use Filament\\Tables\\Actions\\CreateAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\BulkActionGroup;/use Filament\\Tables\\Actions\\BulkActionGroup;/g' "$file"
        sed -i 's/use Filament\\Actions\\DeleteBulkAction;/use Filament\\Tables\\Actions\\DeleteBulkAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\ForceDeleteBulkAction;/use Filament\\Tables\\Actions\\ForceDeleteBulkAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\RestoreBulkAction;/use Filament\\Tables\\Actions\\RestoreBulkAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\ExportBulkAction;/use Filament\\Tables\\Actions\\ExportBulkAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\AssociateAction;/use Filament\\Tables\\Actions\\AssociateAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\DissociateAction;/use Filament\\Tables\\Actions\\DissociateAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\DissociateBulkAction;/use Filament\\Tables\\Actions\\DissociateBulkAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\BulkAction;/use Filament\\Tables\\Actions\\BulkAction;/g' "$file"
        sed -i 's/use Filament\\Actions\\Action;/use Filament\\Tables\\Actions\\Action;/g' "$file"
    fi
    
    # Check if changes were made
    if ! diff -q "$file" "$file.bak" > /dev/null; then
        echo "  ✓ Updated: $file"
    else
        echo "  - No changes: $file"
        rm "$file.bak"
    fi
done

echo "Filament v3 action fixes completed!"
echo "Backup files (*.bak) created for changed files."
