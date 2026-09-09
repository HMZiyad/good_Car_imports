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
  const dropZone = document.getElementById('dropZone');
  const fileInput = document.getElementById('photoInput');
  const uploadPreview = document.getElementById('uploadPreview');

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
});


// ====== FORM SUBMISSIONS ======

// Add Vehicle to Live Inventory
async function submitAddVehicle(event) {
  event.preventDefault();
  const form = event.target;
  const submitBtn = document.getElementById('addVehicleSubmitBtn');
  const feedbackEl = document.getElementById('addVehicleFeedback');
  
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
