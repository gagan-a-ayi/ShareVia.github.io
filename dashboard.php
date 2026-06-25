<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

// Create data directory if it doesn't exist
if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
}

if (!is_dir('data')) {
    mkdir('data', 0755, true);
}

// Load or create metadata file
$metaFile = 'data/files_meta.json';
if (!file_exists($metaFile)) {
    file_put_contents($metaFile, json_encode([]));
}

$filesMetadata = json_decode(file_get_contents($metaFile), true) ?? [];

// Handle file upload
$uploadMessage = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $note = trim($_POST['note'] ?? '');
    $priority = isset($_POST['priority']) ? 1 : 0;
    $deleteDays = intval($_POST['delete_days'] ?? 3);
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $token = generateToken();
        $filename = $file['name'];
        $originalName = $filename;
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $filename = $token . '.' . $ext;
        $filepath = 'uploads/' . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $fileData = [
                'token' => $token,
                'original_name' => $originalName,
                'filename' => $filename,
                'size' => filesize($filepath),
                'upload_date' => date('Y-m-d H:i:s'),
                'delete_date' => date('Y-m-d H:i:s', strtotime('+' . $deleteDays . ' days')),
                'delete_days' => $deleteDays,
                'note' => $note,
                'priority' => $priority,
                'file_type' => mime_content_type($filepath)
            ];
            
            $filesMetadata[] = $fileData;
            file_put_contents($metaFile, json_encode($filesMetadata, JSON_PRETTY_PRINT));
            $uploadMessage = 'File uploaded successfully! Token: ' . $token;
        }
    }
}

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $token = $_POST['token'];
    $key = array_search($token, array_column($filesMetadata, 'token'));
    
    if ($key !== false) {
        $file = $filesMetadata[$key];
        if (file_exists('uploads/' . $file['filename'])) {
            unlink('uploads/' . $file['filename']);
        }
        unset($filesMetadata[$key]);
        $filesMetadata = array_values($filesMetadata);
        file_put_contents($metaFile, json_encode($filesMetadata, JSON_PRETTY_PRINT));
    }
}

// Auto-delete expired files
foreach ($filesMetadata as $key => $file) {
    if ($file['delete_days'] !== -1) {
        if (strtotime($file['delete_date']) < time()) {
            if (file_exists('uploads/' . $file['filename'])) {
                unlink('uploads/' . $file['filename']);
            }
            unset($filesMetadata[$key]);
        }
    }
}
file_put_contents($metaFile, json_encode(array_values($filesMetadata), JSON_PRETTY_PRINT));

function generateToken() {
    return strtoupper(substr(md5(time() . rand()), 0, 8));
}

function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareVia - Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="dashboard-page">
    <div class="dashboard-container">
        <!-- Header -->
        <header class="dashboard-header">
            <div class="header-content">
                <div class="logo-section">
                    <svg width="40" height="40" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="30" cy="30" r="28" stroke="#4A90E2" stroke-width="2"/>
                        <path d="M20 30L28 38L40 22" stroke="#4A90E2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <h1>ShareVia</h1>
                </div>
                <div class="header-actions">
                    <span class="user-info">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="logout.php" class="btn-logout">Logout</a>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Upload Section -->
            <section class="upload-section">
                <div class="upload-card">
                    <h2>Upload New File</h2>
                    
                    <?php if ($uploadMessage): ?>
                        <div class="success-message"><?php echo htmlspecialchars($uploadMessage); ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data" class="upload-form">
                        <div class="form-group">
                            <label for="file">Select File</label>
                            <div class="file-input-wrapper">
                                <input type="file" id="file" name="file" required>
                                <span class="file-input-label">Choose file...</span>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="note">Note (Optional)</label>
                                <input type="text" id="note" name="note" placeholder="Add a note about this file...">
                            </div>
                            
                            <div class="form-group">
                                <label for="delete_days">Auto-Delete After</label>
                                <select id="delete_days" name="delete_days">
                                    <option value="3">3 Days (Default)</option>
                                    <option value="1">1 Day</option>
                                    <option value="7">7 Days</option>
                                    <option value="14">14 Days</option>
                                    <option value="30">30 Days</option>
                                    <option value="-1">Never Delete</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" id="priority" name="priority" value="1">
                                <span class="checkbox-label">Mark as Priority/Important</span>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn-primary">Upload File</button>
                    </form>
                </div>
            </section>

            <!-- Files List Section -->
            <section class="files-section">
                <div class="files-header">
                    <h2>Uploaded Files</h2>
                    <div class="files-controls">
                        <button class="btn-sync" onclick="syncFiles()">🔄 Sync Now</button>
                        <div class="view-toggle">
                            <button class="view-btn active" data-view="grid" onclick="changeView('grid')">⊞ Grid</button>
                            <button class="view-btn" data-view="list" onclick="changeView('list')">☰ List</button>
                        </div>
                    </div>
                </div>
                
                <div class="files-container grid-view" id="filesContainer">
                    <?php if (empty($filesMetadata)): ?>
                        <div class="empty-state">
                            <p>No files uploaded yet. Upload your first file to get started!</p>
                        </div>
                    <?php else: ?>
                        <?php foreach (array_reverse($filesMetadata) as $file): ?>
                            <div class="file-item" data-token="<?php echo htmlspecialchars($file['token']); ?>">
                                <div class="file-header">
                                    <div class="file-info">
                                        <h3><?php echo htmlspecialchars($file['original_name']); ?></h3>
                                        <?php if ($file['priority']): ?>
                                            <span class="badge-priority">★ Priority</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="file-token">Token: <?php echo htmlspecialchars($file['token']); ?></span>
                                </div>
                                
                                <div class="file-meta">
                                    <span class="meta-item">📦 <?php echo formatFileSize($file['size']); ?></span>
                                    <span class="meta-item">📅 <?php echo date('M d, Y', strtotime($file['upload_date'])); ?></span>
                                    <span class="meta-item">⏰ Expires: <?php echo date('M d, Y', strtotime($file['delete_date'])); ?></span>
                                </div>
                                
                                <?php if ($file['note']): ?>
                                    <div class="file-note">
                                        <p><strong>Note:</strong> <?php echo htmlspecialchars($file['note']); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="file-actions">
                                    <button class="btn-action btn-preview" onclick="previewFile('<?php echo htmlspecialchars($file['filename']); ?>', '<?php echo htmlspecialchars($file['file_type']); ?>')">👁️ Preview</button>
                                    <a href="download.php?file=<?php echo urlencode($file['filename']); ?>" class="btn-action btn-download">⬇️ Download</a>
                                    <button class="btn-action btn-print" onclick="printFile('<?php echo htmlspecialchars($file['filename']); ?>')">🖨️ Print</button>
                                    <button class="btn-action btn-delete" onclick="deleteFile('<?php echo htmlspecialchars($file['token']); ?>')">🗑️ Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </section>
        </main>
    </div>

    <!-- Preview Modal -->
    <div class="modal" id="previewModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>File Preview</h2>
                <button class="btn-close" onclick="closePreview()">&times;</button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal" id="deleteModal">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h2>Confirm Delete</h2>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this file? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="cancelDelete()">Cancel</button>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" id="deleteToken" name="token" value="">
                    <button type="submit" class="btn-danger">Delete File</button>
                </form>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
        // Auto-sync every 5 minutes
        setInterval(() => {
            console.log('Auto-syncing files...');
            location.reload();
        }, 300000); // 5 minutes
    </script>
</body>
</html>