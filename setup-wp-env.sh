#!/bin/bash

# Setup script for wp-env environment
# This script creates symlinks for the autoloader-coordinator package inside each plugin

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

echo "🔧 Setting up autoloader-coordinator for wp-env..."

# Create symlinks for autoloader-coordinator package in each plugin
create_symlink() {
    local plugin_name=$1
    local plugin_packages_dir="${SCRIPT_DIR}/${plugin_name}/packages"
    local target_dir="${plugin_packages_dir}/autoloader-coordinator"
    local source_dir="${SCRIPT_DIR}/packages/autoloader-coordinator"

    echo "📦 Setting up ${plugin_name}..."

    # Create packages directory if it doesn't exist
    mkdir -p "${plugin_packages_dir}"

    # Remove existing autoloader-coordinator (file, directory, or symlink)
    if [ -e "${target_dir}" ] || [ -L "${target_dir}" ]; then
        echo "   Removing existing autoloader-coordinator..."
        rm -rf "${target_dir}"
    fi

    # Create symlink
    echo "   Creating symlink: ${target_dir} -> ${source_dir}"
    ln -s "${source_dir}" "${target_dir}"

    echo "   ✅ ${plugin_name} setup complete!"
}

# Setup both plugins
create_symlink "plugin-a"
create_symlink "plugin-b"

# Create mu-plugins directory for our coordinator checker
echo ""
echo "📁 Creating mu-plugins directory..."
mkdir -p "${SCRIPT_DIR}/mu-plugins"

echo ""
echo "✅ Setup complete!"
echo ""
echo "Next steps:"
echo "  1. Run: npm install -g @wordpress/env (if not already installed)"
echo "  2. Run: wp-env start"
echo "  3. Visit: http://localhost:8888 to see which plugin loaded the autoloader-coordinator"
echo ""

