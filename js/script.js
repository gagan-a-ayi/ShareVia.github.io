// Change view between grid and list
function changeView(view) {
    const container = document.getElementById('filesContainer');
    const buttons = document.querySelectorAll('.view-btn');

    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');

    if (view === 'grid') {
        container.classList.remove('list-view');
        container.classList.add('grid-view');
    } else {
        container.classList.remove('grid-view');
        container.classList.add('list-view');
    }
}

// Preview file
function previewFile(filename, fileType) {
    const modal = document.getElementById('previewModal');
    const content = document.getElementById('previewContent');
    const filepath = 'uploads/' + filename;

    if (fileType.includes('pdf')) {
        content.innerHTML = `
            <div style="text-align: center;">
                <iframe src="${filepath}" style="width: 100%; height: 600px; border: none; border-radius: 8px;" title="PDF Preview"></iframe>
                <p style="margin-top: 15px; color: #666; font-size: 14px;">Note: Use the print button below to print the PDF</p>
            </div>
        `;
    } else if (fileType.includes('image')) {
        content.innerHTML = `
            <div style="text-align: center;">
                <img src="${filepath}" alt="Image Preview" class="preview-image">
            </div>
        `;
    } else {
        content.innerHTML = `
            <div class="preview-error">
                <p>Preview not available for this file type</p>
                <p style="font-size: 14px; margin-top: 10px;">Please download the file to view it</p>
            </div>
        `;
    }

    modal.classList.add('active');
}

// Close preview modal
function closePreview() {
    document.getElementById('previewModal').classList.remove('active');
}

// Print file
function printFile(filename) {
    const filepath = 'uploads/' + filename;
    const printWindow = window.open(filepath, '_blank');
    printWindow.onload = function() {
        printWindow.print();
    };
}

// Delete file with confirmation
function deleteFile(token) {
    const modal = document.getElementById('deleteModal');
    const tokenInput = document.getElementById('deleteToken');
    tokenInput.value = token;
    modal.classList.add('active');
}

// Cancel delete
function cancelDelete() {
    document.getElementById('deleteModal').classList.remove('active');
}

// Sync files
function syncFiles() {
    const btn = event.target;
    btn.style.transform = 'rotate(360deg)';
    btn.style.transition = 'transform 0.6s ease';

    setTimeout(() => {
        btn.style.transform = 'rotate(0deg)';
        // In real implementation, this would fetch updated file list from server
        location.reload();
    }, 600);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const previewModal = document.getElementById('previewModal');
    const deleteModal = document.getElementById('deleteModal');

    if (event.target === previewModal) {
        previewModal.classList.remove('active');
    }
    if (event.target === deleteModal) {
        deleteModal.classList.remove('active');
    }
};

// File input label update
document.addEventListener('DOMContentLoaded', function() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const label = this.nextElementSibling;
            if (this.files.length > 0) {
                label.textContent = this.files[0].name;
                label.style.color = '#2d7d2d';
            } else {
                label.textContent = 'Choose file...';
                label.style.color = '#4A90E2';
            }
        });
    });
});