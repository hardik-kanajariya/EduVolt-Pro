#!/bin/bash

# Script to fix HTML rendering issues in Filament view pages
# This script will:
# 1. Add HtmlString import to files that need it
# 2. Replace content() return type from string to HtmlString
# 3. Wrap HTML content with new HtmlString() and escape user data

echo "Starting Filament HTML rendering fixes..."

# List of files to fix based on the grep search
files=(
    "app/Filament/Admin/Resources/Students/Pages/ViewStudent.php"
    "app/Filament/Admin/Resources/Students/Schemas/StudentForm.php"
    "app/Filament/Admin/Resources/Attendances/Schemas/AttendanceForm.php"
    "app/Filament/Admin/Resources/SchoolClasses/Schemas/SchoolClassForm.php"
    "app/Filament/Admin/Resources/SchoolClasses/Pages/ViewSchoolClass.php"
    "app/Filament/Admin/Resources/Teachers/Pages/ViewTeacher.php"
    "app/Filament/Admin/Resources/Teachers/Schemas/TeacherForm.php"
    "app/Filament/Admin/Resources/AcademicYears/Schemas/AcademicYearForm.php"
    "app/Filament/Admin/Resources/Staff/Schemas/StaffForm.php"
    "app/Filament/Admin/Resources/AcademicYears/Pages/ViewAcademicYear.php"
)

for file in "${files[@]}"; do
    if [ -f "/home/hardik/Documents/GitHub/EduVolt-Pro/$file" ]; then
        echo "Processing: $file"
        
        # Create backup
        cp "/home/hardik/Documents/GitHub/EduVolt-Pro/$file" "/home/hardik/Documents/GitHub/EduVolt-Pro/$file.bak"
        
        # Add HtmlString import if not present
        if ! grep -q "use Illuminate\\Support\\HtmlString;" "/home/hardik/Documents/GitHub/EduVolt-Pro/$file"; then
            # Find the line with the last use statement and add HtmlString import after it
            sed -i '/^use .*$/a use Illuminate\\Support\\HtmlString;' "/home/hardik/Documents/GitHub/EduVolt-Pro/$file"
        fi
        
        # Replace ->content(function () use (...): string { with ->content(function () use (...): HtmlString {
        sed -i 's/->content(function (\([^)]*\)): string {/->content(function (\1): HtmlString {/g' "/home/hardik/Documents/GitHub/EduVolt-Pro/$file"
        
        # Replace return '<...'; with return new HtmlString('<...');
        sed -i "s/return '<\([^']*\)';/return new HtmlString('<\1');/g" "/home/hardik/Documents/GitHub/EduVolt-Pro/$file"
        
        echo "  ✓ Updated: $file"
    else
        echo "  ! File not found: $file"
    fi
done

echo "Filament HTML rendering fixes completed!"
echo "Backup files (.bak) created for changed files."
echo "Note: You may need to manually review and fix complex HTML content and add proper escaping."
