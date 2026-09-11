/**
 * Good Car Imports — Admin JavaScript
 */

// ====== MODAL HANDLING ======
function openModal(modalId) {
  const overlay = document.getElementById('modalOverlay');
  const modal = document.getElementById(modalId);
  
  if (overlay && modal) {
    // Hide all modals first
    document.querySelectorAll('.modal-container').forEach(m => m.style.display = 'none');
    
    // Show requested modal
    modal.style.display = 'flex';
    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
  }
}

function closeModal(modalId) {
  const overlay = document.getElementById('modalOverlay');
  const modal = document.getElementById(modalId);
  
  if (overlay && modal) {
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    
    // Wait for transition before hiding
    setTimeout(() => {
      modal.style.display = 'none';
      // Reset form if exists
      const form = modal.querySelector('form');
      if (form) form.reset();
      
      // Clear previews if applicable
      const preview = document.getElementById('uploadPreview');
      if (preview) preview.innerHTML = '';
      
      // Hide feedback messages
      const feedback = modal.querySelector('.form-message');
      if (feedback) feedback.style.display = 'none';
    }, 300);
  }
}

function closeAllModals(e) {
  // Only close if clicking the overlay background directly
  if (e.target.id === 'modalOverlay') {
    const overlay = document.getElementById('modalOverlay');
    overlay.classList.remove('active');
    document.body.style.overflow = '';
    
    setTimeout(() => {
      document.querySelectorAll('.modal-container').forEach(m => {
        m.style.display = 'none';
        const form = m.querySelector('form');
        if (form) form.reset();
      });
      const preview = document.getElementById('uploadPreview');
      if (preview) preview.innerHTML = '';
    }, 300);
  }
}


// ====== FILE UPLOAD PREVIEW ======
document.addEventListener('DOMContentLoaded', () => {
  function setupUploadZone(zoneId, inputId, previewId) {
    const dropZone = document.getElementById(zoneId);
    const fileInput = document.getElementById(inputId);
    const uploadPreview = document.getElementById(previewId);

    if (dropZone && fileInput && uploadPreview) {
      // Click to select
      dropZone.addEventListener('click', () => fileInput.click());

      // Drag and drop events
      dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
      });

      dropZone.addEventListener('dragleave', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
      });

      dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        
        if (e.dataTransfer.files.length > 0) {
          fileInput.files = e.dataTransfer.files;
          handleFiles(fileInput.files);
        }
      });

      // File input change
      fileInput.addEventListener('change', () => {
        handleFiles(fileInput.files);
      });

      function handleFiles(files) {
        uploadPreview.innerHTML = ''; // Clear previous previews
        
        Array.from(files).forEach((file, index) => {
          if (!file.type.match('image.*')) return;

          const reader = new FileReader();
          reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'upload-preview-item';
            
            let label = `Photo ${index + 1}`;
            if (index === 0) label = 'Cover Photo';
            
            div.innerHTML = `
              <img src="${e.target.result}" alt="Preview">
              <div class="upload-preview-label">${label}</div>
            `;
            uploadPreview.appendChild(div);
          };
          reader.readAsDataURL(file);
        });
      }
    }
  }

  // Setup Add Vehicle Upload
  setupUploadZone('dropZone', 'photoInput', 'uploadPreview');
  
  // Setup Edit Vehicle Upload
  setupUploadZone('editDropZone', 'editPhotoInput', 'editUploadPreview');
});


// ====== FORM SUBMISSIONS ======

// Add Vehicle to Live Inventory
async function submitAddVehicle(event) {
  event.preventDefault();
  const form = event.target;
  const submitBtn = document.getElementById('addVehicleSubmitBtn');
  const feedbackEl = document.getElementById('addVehicleFeedback');
  
  if (!form.checkValidity()) {
    const firstInvalid = form.querySelector(':invalid');
    if (firstInvalid) {
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstInvalid.focus();
      alert('Please fill out all required fields (marked with an asterisk *).');
    }
    return;
  }
  
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = 'Saving...';
  
  try {
    const formData = new FormData(form);
    
    // Add logic here to determine API endpoint based on action
    const response = await fetch('../api/admin.php?action=add_vehicle', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    feedbackEl.style.display = 'block';
    if (result.success) {
      feedbackEl.className = 'form-message success';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">check_circle</span> Vehicle added to inventory successfully.`;
      
      // Reload page after short delay to show new data
      setTimeout(() => window.location.reload(), 1500);
    } else {
      feedbackEl.className = 'form-message error';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> ${result.message}`;
    }
  } catch (error) {
    feedbackEl.style.display = 'block';
    feedbackEl.className = 'form-message error';
    feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> An error occurred while saving the vehicle.`;
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  }
}

// Add Stock Inward (Auction Win)
async function submitStockInward(event) {
  event.preventDefault();
  const form = event.target;
  const submitBtn = document.getElementById('stockInwardSubmitBtn');
  const feedbackEl = document.getElementById('stockInwardFeedback');
  
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = 'Saving...';
  
  try {
    const formData = new FormData(form);
    
    const response = await fetch('../api/admin.php?action=add_stock_inward', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    feedbackEl.style.display = 'block';
    if (result.success) {
      feedbackEl.className = 'form-message success';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">check_circle</span> Vehicle added to inward pipeline successfully.`;
      
      setTimeout(() => window.location.reload(), 1500);
    } else {
      feedbackEl.className = 'form-message error';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> ${result.message}`;
    }
  } catch (error) {
    feedbackEl.style.display = 'block';
    feedbackEl.className = 'form-message error';
    feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> An error occurred while saving the inward record.`;
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  }
}

// Edit Vehicle
async function openEditVehicleModal(id) {
  try {
    const response = await fetch(`../api/admin.php?action=get_vehicle&id=${id}`);
    const result = await response.json();
    if (result.success) {
      const v = result.data;
      document.getElementById('edit_vehicle_id').value = v.id;
      document.getElementById('edit_car_name').value = v.car_name;
      document.getElementById('edit_package_trim').value = v.package_trim || '';
      document.getElementById('edit_year_of_manufacture').value = v.year_of_manufacture;
      document.getElementById('edit_color_name').value = v.color_name || '';
      document.getElementById('edit_chassis_code').value = v.chassis_code;
      document.getElementById('edit_mileage_km').value = v.mileage_km;
      document.getElementById('edit_auction_grade').value = v.auction_grade || '';
      document.getElementById('edit_transmission').value = v.transmission || '';
      document.getElementById('edit_body_type').value = v.body_type || 'SUV';
      document.getElementById('edit_fuel_type').value = v.fuel_type || 'Petrol';
      document.getElementById('edit_status').value = v.status || 'available';
      document.getElementById('edit_price_bdt').value = v.price_bdt || '';
      document.getElementById('edit_is_featured').checked = (v.is_featured == 1);
      
      openModal('editVehicleModal');
    } else {
      alert('Error fetching vehicle: ' + result.message);
    }
  } catch (e) {
    alert('Error fetching vehicle details.');
  }
}

async function submitEditVehicle(event) {
  event.preventDefault();
  const form = event.target;
  const submitBtn = document.getElementById('editVehicleSubmitBtn');
  const feedbackEl = document.getElementById('editVehicleFeedback');
  
  if (!form.checkValidity()) {
    const firstInvalid = form.querySelector(':invalid');
    if (firstInvalid) {
      firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
      firstInvalid.focus();
      alert('Please fill out all required fields (marked with an asterisk *).');
    }
    return;
  }
  
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = 'Saving...';
  
  try {
    const formData = new FormData(form);
    
    const response = await fetch('../api/admin.php?action=edit_vehicle', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    feedbackEl.style.display = 'block';
    if (result.success) {
      feedbackEl.className = 'form-message success';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">check_circle</span> ${result.message}`;
      
      setTimeout(() => window.location.reload(), 1500);
    } else {
      feedbackEl.className = 'form-message error';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> ${result.message}`;
    }
  } catch (error) {
    feedbackEl.style.display = 'block';
    feedbackEl.className = 'form-message error';
    feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> An error occurred while updating the vehicle.`;
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  }
}

// Edit Stock Inward
async function openEditStockInwardModal(id) {
  try {
    const response = await fetch(`../api/admin.php?action=get_stock_inward&id=${id}`);
    const result = await response.json();
    if (result.success) {
      const v = result.data;
      document.getElementById('edit_inward_id').value = v.id;
      document.getElementById('edit_inward_stage').value = v.current_stage || 'auction_won';
      document.getElementById('edit_inward_chassis').value = v.chassis_code;
      document.getElementById('edit_inward_car_name').value = v.car_name;
      document.getElementById('edit_inward_trim').value = v.package_trim || '';
      document.getElementById('edit_inward_yom').value = v.year_of_manufacture;
      document.getElementById('edit_inward_color').value = v.color_name || '';
      document.getElementById('edit_inward_mileage').value = v.mileage_km;
      document.getElementById('edit_inward_grade').value = v.auction_grade || '';
      document.getElementById('edit_inward_house').value = v.auction_house || '';
      document.getElementById('edit_inward_lot').value = v.auction_lot || '';
      document.getElementById('edit_inward_jpy').value = v.won_price_jpy || '';
      document.getElementById('edit_inward_bdt').value = v.bdt_equivalent || '';
      
      openModal('editStockInwardModal');
    } else {
      alert('Error fetching record: ' + result.message);
    }
  } catch (e) {
    alert('Error fetching inward details.');
  }
}

async function submitEditStockInward(event) {
  event.preventDefault();
  const form = event.target;
  const submitBtn = document.getElementById('editStockInwardSubmitBtn');
  const feedbackEl = document.getElementById('editStockInwardFeedback');
  
  const originalText = submitBtn.textContent;
  submitBtn.disabled = true;
  submitBtn.textContent = 'Saving...';
  
  try {
    const formData = new FormData(form);
    
    const response = await fetch('../api/admin.php?action=edit_stock_inward', {
      method: 'POST',
      body: formData
    });
    
    const result = await response.json();
    
    feedbackEl.style.display = 'block';
    if (result.success) {
      feedbackEl.className = 'form-message success';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">check_circle</span> ${result.message}`;
      
      setTimeout(() => window.location.reload(), 1500);
    } else {
      feedbackEl.className = 'form-message error';
      feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> ${result.message}`;
    }
  } catch (error) {
    feedbackEl.style.display = 'block';
    feedbackEl.className = 'form-message error';
    feedbackEl.innerHTML = `<span class="material-symbols-outlined" style="vertical-align:middle;margin-right:6px;font-size:18px;">error</span> An error occurred while updating the record.`;
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = originalText;
  }
}

// ====== GLOBAL SEARCH ======
document.addEventListener('DOMContentLoaded', () => {
  const searchInput = document.getElementById('globalSearchInput');
  const searchResults = document.getElementById('globalSearchResults');
  let searchTimeout = null;

  if (searchInput && searchResults) {
    // Cmd+K / Ctrl+K shortcut
    document.addEventListener('keydown', (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        searchInput.focus();
      }
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
      if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.style.display = 'none';
      }
    });

    // Search input handler
    searchInput.addEventListener('input', (e) => {
      const q = e.target.value.trim();
      
      if (q.length < 2) {
        searchResults.style.display = 'none';
        return;
      }

      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(async () => {
        try {
          const res = await fetch(`../api/admin.php?action=global_search&q=${encodeURIComponent(q)}`);
          const json = await res.json();
          
          if (json.success && json.data.length > 0) {
            let html = '<div style="padding: 8px;">';
            json.data.forEach(item => {
              html += `
                <a href="${item.link}" style="display:flex; justify-content:space-between; align-items:center; padding:10px 12px; text-decoration:none; color:var(--on-surface); border-radius:var(--radius-sm); margin-bottom:2px;" onmouseover="this.style.background='var(--surface-container-high)'" onmouseout="this.style.background='transparent'">
                  <div>
                    <div style="font-weight:600; font-size:14px; margin-bottom:2px;">${item.title}</div>
                    <div style="font-size:12px; color:var(--secondary);">${item.subtitle} &bull; <span style="color:var(--primary);">${item.type}</span></div>
                  </div>
                  <div style="font-size:10px; font-weight:700; text-transform:uppercase; padding:4px 8px; background:var(--surface-container); border-radius:4px;">
                    ${item.badge}
                  </div>
                </a>
              `;
            });
            html += '</div>';
            searchResults.innerHTML = html;
            searchResults.style.display = 'block';
          } else {
            searchResults.innerHTML = '<div style="padding:16px; text-align:center; color:var(--secondary); font-size:13px;">No results found for "' + q + '"</div>';
            searchResults.style.display = 'block';
          }
        } catch (err) {
          console.error("Search error", err);
        }
      }, 300);
    });
  }
});

// ====== AUTO-OPEN MODALS ======
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  
  if (urlParams.has('edit_vehicle')) {
    const id = urlParams.get('edit_vehicle');
    if (typeof openEditVehicleModal === 'function') {
      openEditVehicleModal(id);
    }
  }
  
  if (urlParams.has('edit_inward')) {
    const id = urlParams.get('edit_inward');
    if (typeof openEditStockInwardModal === 'function') {
      openEditStockInwardModal(id);
    }
  }
});

// ====== NOTIFICATIONS ======
document.addEventListener('DOMContentLoaded', () => {
  const notifBtn = document.getElementById('notificationBtn');
  const notifDropdown = document.getElementById('notificationDropdown');
  const notifBadge = document.getElementById('notificationBadge');

  if (notifBtn && notifDropdown) {
    notifBtn.addEventListener('click', async (e) => {
      e.stopPropagation();
      const isVisible = notifDropdown.style.display === 'block';
      notifDropdown.style.display = isVisible ? 'none' : 'block';
      
      if (!isVisible && notifBadge) {
        notifBadge.style.display = 'none';
        try {
          await fetch('../api/admin.php?action=read_notifications', { method: 'POST' });
        } catch(err) {
          console.error("Failed to mark notifications read", err);
        }
      }
    });

    document.addEventListener('click', (e) => {
      if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
        notifDropdown.style.display = 'none';
      }
    });
  }
});

// ====== DELETE VEHICLE ======
async function deleteVehicle(id) {
  if (!confirm("Are you sure you want to permanently delete this vehicle? This will remove all associated photos and unlink it from any sales records. This action cannot be undone.")) {
    return;
  }
  
  try {
    const formData = new FormData();
    formData.append('vehicle_id', id);
    
    const res = await fetch('../api/admin.php?action=delete_vehicle', {
      method: 'POST',
      body: formData
    });
    const json = await res.json();
    
    if (json.success) {
      window.location.reload();
    } else {
      alert("Error: " + json.message);
    }
  } catch (err) {
    alert("An error occurred while deleting the vehicle.");
    console.error(err);
  }
}
