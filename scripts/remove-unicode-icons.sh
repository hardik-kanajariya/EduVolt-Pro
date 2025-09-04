#!/bin/bash

# Script to remove Unicode character icons from Filament resources
# This will clean up emoji and Unicode characters from labels, descriptions, and content

echo "🔄 Starting Unicode character removal from Filament resources..."

# Define the directories to process
RESOURCES_DIR="app/Filament/Admin/Resources"
DOCS_DIR="docs"

# Function to remove Unicode characters from PHP files
remove_unicode_from_php() {
    local file="$1"
    echo "Processing: $file"
    
    # Create a backup
    cp "$file" "$file.backup"
    
    # Remove Unicode characters from labels, descriptions, and content
    # This will remove emoji and Unicode symbols but preserve text
    sed -i -E "s/->label\('([^']*[🎓📅🏫📊🗓️🏁⭐📏ℹ️🚀💡📈🔄📆⚙️🟢🔴🟡🏁⚪✅❌📚👥📖🎨📘🎯🔍💼👨‍🏫👩‍🏫📋🎖️🏖️📤🚫📝🎭🎪🎨🎬🎵🎶🎸🎹🎤🎧🎮🎲🎯🎱🎳🏀🏈⚽🏐🏉🎾🏓🏸🥅🏒🏑🥍🏏⛳🏹🎣🥊🥋🎽⛷️🏂🏄‍♂️🏄‍♀️🚣‍♂️🚣‍♀️🏊‍♂️🏊‍♀️⛹️‍♂️⛹️‍♀️🏋️‍♂️🏋️‍♀️🚴‍♂️🚴‍♀️🚵‍♂️🚵‍♀️🧘‍♀️🧘‍♂️🏇🏃‍♀️🏃‍♂️🤸‍♀️🤸‍♂️][^']*)/->label('\1/g" "$file"
    
    # More comprehensive Unicode removal
    sed -i 's/[🎓📅🏫📊🗓️🏁⭐📏ℹ️🚀💡📈🔄📆⚙️🟢🔴🟡🏁⚪✅❌📚👥📖🎨📘🎯🔍💼👨‍🏫👩‍🏫📋🎖️🏖️📤🚫📝🎭🎪🎨🎬🎵🎶🎸🎹🎤🎧🎮🎲🎯🎱🎳🏀🏈⚽🏐🏉🎾🏓🏸🥅🏒🏑🥍🏏⛳🏹🎣🥊🥋🎽⛷️🏂🏄‍♂️🏄‍♀️🚣‍♂️🚣‍♀️🏊‍♂️🏊‍♀️⛹️‍♂️⛹️‍♀️🏋️‍♂️🏋️‍♀️🚴‍♂️🚴‍♀️🚵‍♂️🚵‍♀️🧘‍♀️🧘‍♂️🏇🏃‍♀️🏃‍♂️🤸‍♀️🤸‍♂️] //g' "$file"
    
    # Clean up any double spaces created
    sed -i 's/  / /g' "$file"
    
    # Clean up labels that start with space
    sed -i "s/->label(' /->label('/g" "$file"
    
    echo "✓ Processed: $file"
}

# Function to remove Unicode characters using a more comprehensive approach
comprehensive_unicode_removal() {
    local file="$1"
    
    # Use Python for more precise Unicode removal
    python3 << EOF
import re
import sys

def remove_unicode_from_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Remove Unicode emoji and symbols from labels
        content = re.sub(r"->label\('([^']*?)([^\x00-\x7F]+)([^']*?)'\)", r"->label('\1\3')", content)
        
        # Remove Unicode from descriptions
        content = re.sub(r"->description\('([^']*?)([^\x00-\x7F]+)([^']*?)'\)", r"->description('\1\3')", content)
        
        # Remove Unicode from content functions
        content = re.sub(r'([^\x00-\x7F]+)', '', content)
        
        # Clean up multiple spaces
        content = re.sub(r' +', ' ', content)
        
        # Clean up labels that start with space
        content = re.sub(r"->label\(' ", "->label('", content)
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"✓ Cleaned Unicode from: {filepath}")
        
    except Exception as e:
        print(f"✗ Error processing {filepath}: {e}")

if __name__ == "__main__":
    remove_unicode_from_file("$file")
EOF
}

# Find and process all PHP files in resources
echo "📂 Processing Filament resource files..."
find "$RESOURCES_DIR" -name "*.php" -type f | while read file; do
    comprehensive_unicode_removal "$file"
done

# Process documentation files
echo "📚 Processing documentation files..."
find "$DOCS_DIR" -name "*.md" -type f | while read file; do
    if [ -f "$file" ]; then
        echo "Processing doc: $file"
        # Remove Unicode from markdown files
        python3 << EOF
import re

def clean_markdown(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Remove Unicode characters but preserve structure
        content = re.sub(r'[^\x00-\x7F]+', '', content)
        
        # Clean up multiple spaces
        content = re.sub(r' +', ' ', content)
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"✓ Cleaned Unicode from: {filepath}")
        
    except Exception as e:
        print(f"✗ Error processing {filepath}: {e}")

clean_markdown("$file")
EOF
    fi
done

echo ""
echo "🎯 Unicode character removal completed!"
echo "📋 Summary:"
echo "   - Processed all PHP files in $RESOURCES_DIR"
echo "   - Processed all Markdown files in $DOCS_DIR"
echo "   - Removed emoji and Unicode symbols"
echo "   - Cleaned up spacing and formatting"
echo "   - Backup files created with .backup extension"
echo ""
echo "⚠️  Note: Please review the changes and remove backup files when satisfied:"
echo "   find . -name '*.backup' -delete"
