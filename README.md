# ShareVia - Secure Cross-Device File Sharing Platform

## Overview
ShareVia is a modern, minimalist file-sharing platform designed for cyber centers and organizations that need secure cross-device file transfer, printing, and management capabilities.

## Features

### Core Functionality
- ✅ **Cross-Device File Sharing** - Access files from any device on the network
- ✅ **Admin Login** - Secure authentication with MD5 encryption
- ✅ **File Upload** - Simple drag-and-drop file upload interface
- ✅ **File Preview** - View PDFs and images directly in the browser
- ✅ **Download Files** - Download files with original names preserved
- ✅ **Print PDFs** - Print files using the browser's print dialog
- ✅ **File Deletion** - Delete files with confirmation dialog
- ✅ **Grid/List View** - Toggle between grid and list view for files

### Advanced Features
- ✅ **File Tokens** - Generate unique tokens for easy file identification across devices
- ✅ **Optional Notes** - Add notes to files for documentation
- ✅ **Priority Marking** - Mark files as important/priority
- ✅ **Auto-Delete** - Configurable auto-delete (default 3 days, can be set to never)
- ✅ **Real-time Syncing** - Auto-sync every 5 minutes or manual sync button
- ✅ **File Metadata** - Store and display file upload date, size, expiry date

### Design Features
- 🎨 Modern, minimalist light theme
- 💻 Fully responsive design (desktop, tablet, mobile)
- ⚡ Fast and lightweight
- 🎯 Intuitive user interface

## Technology Stack

- **Backend**: PHP (no SQL database required)
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Storage**: JSON-based metadata, file system storage
- **Security**: MD5 password encryption, session-based authentication
- **Hosting**: Compatible with cPanel/Shared Hosting (PHP-enabled)

## Installation & Setup

### Prerequisites
- PHP 7.0+ enabled hosting
- cPanel or Shared Hosting environment
- FTP access to upload files

### Installation Steps

1. **Clone or Download the Repository**
   ```bash
   git clone https://github.com/gagan-a-ayi/ShareVia.git
   ```

2. **Upload Files to Hosting**
   - Connect via FTP to your hosting
   - Upload all files to your public_html folder (or subdirectory)
   - Ensure directory structure is preserved

3. **Create Required Directories**
   - `/uploads` - for storing uploaded files
   - `/data` - for storing metadata (JSON files)
   - These will be created automatically on first access

4. **Set Permissions**
   ```bash
   chmod 755 uploads/
   chmod 755 data/
   chmod 644 *.php
   chmod 644 *.json
   ```

5. **Access the Platform**
   - Open your browser and go to: `https://yourdomain.com/sharevia/` (or your configured path)
   - Login with credentials:
     - **Username**: admin
     - **Password**: Bone@97

## File Structure

```
ShareVia/
├── index.php                 # Login page
├── dashboard.php             # Main dashboard with file management
├── download.php              # File download handler
├── logout.php                # Logout handler
├── css/
│   └── style.css            # Complete styling
├── js/
│   └── script.js            # JavaScript functionality
├── uploads/                 # Uploaded files (auto-created)
├── data/
│   └── files_meta.json      # Metadata storage (auto-created)
└── README.md                # This file
```

## Usage Guide

### Login
1. Navigate to the login page
2. Enter username: `admin`
3. Enter password: `Bone@97`
4. Click "Login"

### Upload a File
1. Click the file input or drag-and-drop a file
2. (Optional) Add a note about the file
3. (Optional) Mark as Priority/Important
4. Select auto-delete timeframe (default: 3 days)
5. Click "Upload File"

### Preview Files
1. Click "👁️ Preview" button on any file
2. For PDFs: View in browser with built-in PDF viewer
3. For Images: View directly in modal
4. Click X to close preview

### Print Files
1. Click "🖨️ Print" button
2. This opens the file in a new window
3. Use browser's print dialog (Ctrl+P or Cmd+P)
4. Select your printer and print settings
5. Click "Print"

### Download Files
1. Click "⬇️ Download" button
2. File will be downloaded with original filename
3. Location depends on your browser's download settings

### Delete Files
1. Click "🗑️ Delete" button
2. Confirm deletion in the modal
3. File will be permanently removed

### Change View
1. Use "⊞ Grid" and "☰ List" buttons in the files header
2. Toggle between grid and list view

### Manual Sync
1. Click "🔄 Sync Now" button
2. Files list will refresh with latest data
3. Auto-sync also runs every 5 minutes

## File Token System

Each uploaded file receives a unique token (8-character alphanumeric string) that allows easy identification across devices without relying on filenames. Example: `3F7A2E9C`

**Why Tokens Matter:**
- Same filename uploaded multiple times gets unique tokens
- Easy to reference files in conversations
- Prevents filename conflicts

## Auto-Delete Configuration

### Default Behavior
- Files auto-delete after **3 days** by default

### Available Options
- 1 Day
- 3 Days (default)
- 7 Days
- 14 Days
- 30 Days
- Never Delete (requires manual deletion)

### How It Works
- Expiry date is calculated on upload
- System checks for expired files on each page load
- Expired files are automatically removed
- Deletion date is visible in file metadata

## Security Features

1. **Session-Based Authentication**
   - Uses PHP sessions for secure login
   - Sessions expire based on browser/server settings

2. **MD5 Password Encryption**
   - Password stored as MD5 hash
   - Login verification compares MD5 hashes

3. **File Access Control**
   - Only logged-in users can upload/download/preview files
   - Download handler verifies session

4. **Path Traversal Protection**
   - File paths validated to prevent directory traversal attacks
   - Filename sanitization in downloads

## Metadata Storage

File metadata is stored in `/data/files_meta.json` with the following structure:

```json
[
  {
    "token": "3F7A2E9C",
    "original_name": "document.pdf",
    "filename": "3F7A2E9C.pdf",
    "size": 2048576,
    "upload_date": "2024-01-15 10:30:45",
    "delete_date": "2024-01-18 10:30:45",
    "delete_days": 3,
    "note": "Important financial report",
    "priority": 1,
    "file_type": "application/pdf"
  }
]
```

## Troubleshooting

### Files Not Uploading
- Check `/uploads` directory permissions (should be 755)
- Verify PHP upload_max_filesize setting
- Ensure sufficient disk space

### Preview Not Working
- Browser must support PDF viewing (most modern browsers do)
- Check file hasn't been deleted
- Verify file_type in metadata is correct

### Login Issues
- Ensure correct credentials: `admin` / `Bone@97`
- Clear browser cookies and try again
- Check PHP sessions are enabled

### Auto-Delete Not Working
- Verify `/data` directory has correct permissions
- Check `files_meta.json` is readable/writable
- Delete date should be in the past for auto-delete to trigger

## Browser Compatibility

- Chrome/Chromium 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance Notes

- System scales to thousands of files
- Metadata stored in single JSON file for simplicity
- File preview cached by browser
- Grid view optimized for responsiveness

## Future Enhancement Ideas

- [ ] Database integration (MySQL/MariaDB)
- [ ] User management system
- [ ] File sharing with download links
- [ ] Notification system
- [ ] File versioning
- [ ] Compression before download
- [ ] Video file preview
- [ ] File search functionality

## License

This project is created for cyber center file management. Feel free to modify and use as needed.

## Support

For issues or questions:
1. Check the troubleshooting section
2. Review file permissions and directory setup
3. Verify PHP configuration meets requirements
4. Check browser console for JavaScript errors

## Version

**ShareVia v1.0** - Initial Release

---

**Built with ❤️ for secure file sharing**