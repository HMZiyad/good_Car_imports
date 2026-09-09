<?php
/**
 * Good Car Imports — Admin Footer Partial
 */
?>
  </main> <!-- /.admin-main -->
</div> <!-- /.admin-layout -->

<!-- ===== ADMIN MODALS (Shared across all pages) ===== -->

<!-- Overlay -->
<div class="modal-overlay" id="modalOverlay" onclick="closeAllModals(event)">
  
  <!-- Add New Vehicle Modal -->
  <div class="modal-container" id="addVehicleModal" style="display:none;" onclick="event.stopPropagation()">
    <form id="addVehicleForm" onsubmit="submitAddVehicle(event)">
      <div class="modal-header">
        <div class="modal-title">
          <h2>Add New Vehicle to Inventory</h2>
          <span class="modal-badge">8 Mandatory Spec Columns</span>
        </div>
        <button type="button" class="icon-btn" onclick="closeModal('addVehicleModal')">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>
      
      <div class="modal-body">
        <div id="addVehicleFeedback" class="form-message" style="display:none;"></div>
        
        <div class="form-section-title">Core Specifications</div>
        <div class="grid-2-col">
          <div class="form-group mandatory-field">
            <label class="form-label">Car Name</label>
            <input type="text" name="car_name" class="form-input" required placeholder="e.g. Toyota Land Cruiser 300">
          </div>
          <div class="form-group">
            <label class="form-label">Package/Trim</label>
            <input type="text" name="package_trim" class="form-input" placeholder="e.g. ZX Modellista">
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Year of Manufacture (YOM)</label>
            <input type="number" name="year_of_manufacture" class="form-input" required min="2000" max="<?= date('Y') + 1 ?>">
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Color</label>
            <input type="text" name="color_name" class="form-input" required placeholder="e.g. Pearl White">
          </div>
          <div class="form-group mandatory-field highlight-field">
            <label class="form-label">Chassis Code (KEY IDENTIFIER)</label>
            <input type="text" name="chassis_code" class="form-input" required placeholder="e.g. VJA300-0019482">
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Mileage (KM)</label>
            <input type="number" name="mileage_km" class="form-input" required min="0">
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Auction Grade</label>
            <select name="auction_grade" class="form-select" required>
              <option value="">Select Grade</option>
              <option value="6.0">6.0 (New/Unregistered)</option>
              <option value="S">S (Like New)</option>
              <option value="5.0">5.0 (Excellent)</option>
              <option value="4.5">4.5 (Very Good)</option>
              <option value="4.0">4.0 (Good)</option>
              <option value="3.5">3.5 (Fair)</option>
              <option value="R">R (Repaired/Accident)</option>
            </select>
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Transmission</label>
            <input type="text" name="transmission" class="form-input" required placeholder="e.g. AT, CVT">
          </div>
        </div>

        <div class="form-section-title">Additional Details & Pricing</div>
        <div class="grid-2-col">
          <div class="form-group mandatory-field">
            <label class="form-label">Body Type</label>
            <select name="body_type" class="form-select" required>
              <option value="SUV">SUV</option>
              <option value="Sedan">Sedan</option>
              <option value="Hatchback">Hatchback</option>
              <option value="MPV">MPV</option>
              <option value="Crossover">Crossover</option>
            </select>
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Fuel Type</label>
            <select name="fuel_type" class="form-select" required>
              <option value="Petrol">Petrol</option>
              <option value="Hybrid">Hybrid</option>
              <option value="PHEV">PHEV</option>
              <option value="Diesel">Diesel</option>
              <option value="Electric">Electric</option>
            </select>
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Target Stock Status</label>
            <select name="status" class="form-select" required>
              <option value="available">Available — Showroom</option>
              <option value="port_clearance">Port Clearance (CTG)</option>
              <option value="vessel_transit">On Vessel Transit</option>
              <option value="reserved">Reserved</option>
              <option value="sold">Sold</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Asking Price (BDT)</label>
            <input type="number" name="price_bdt" class="form-input" placeholder="e.g. 85000000 (8.5 Cr)">
            <small style="color:var(--secondary); font-size:11px;">Enter full amount, no commas.</small>
          </div>
        </div>

        <div class="form-section-title">Vehicle Photos & Media</div>
        <div class="upload-zone" id="dropZone">
          <span class="material-symbols-outlined">cloud_upload</span>
          <div class="upload-text">Drag & drop photos here, or click to browse</div>
          <div class="upload-subtext">JPEG, PNG, WEBP up to 10MB. First image will be cover.</div>
          <input type="file" id="photoInput" name="photos[]" multiple accept="image/jpeg, image/png, image/webp" style="display:none;">
        </div>
        <div class="upload-preview-grid" id="uploadPreview">
          <!-- Previews will be injected here via JS -->
        </div>
      </div>
      
      <div class="modal-footer">
        <div class="modal-footer-note">
          <span class="material-symbols-outlined" style="color:var(--tertiary);font-size:16px;">verified_user</span>
          Form validated against handwritten ledger
        </div>
        <div class="modal-actions">
          <button type="button" class="btn btn-outline" onclick="closeModal('addVehicleModal')">Cancel</button>
          <button type="submit" class="btn btn-primary" id="addVehicleSubmitBtn">Save & Inward Vehicle</button>
        </div>
      </div>
    </form>
  </div>

  <!-- Edit Vehicle Modal -->
  <div class="modal-container" id="editVehicleModal" style="display:none;" onclick="event.stopPropagation()">
    <form id="editVehicleForm" onsubmit="submitEditVehicle(event)">
      <input type="hidden" name="vehicle_id" id="edit_vehicle_id">
      <div class="modal-header">
        <div class="modal-title">
          <h2>Edit Vehicle Details</h2>
        </div>
        <button type="button" class="icon-btn" onclick="closeModal('editVehicleModal')">
          <span class="material-symbols-outlined">close</span>
        </button>
      </div>
      
      <div class="modal-body">
        <div id="editVehicleFeedback" class="form-message" style="display:none;"></div>
        
        <div class="form-section-title">Core Specifications</div>
        <div class="grid-2-col">
          <div class="form-group mandatory-field">
            <label class="form-label">Car Name</label>
            <input type="text" name="car_name" id="edit_car_name" class="form-input" required placeholder="e.g. Toyota Land Cruiser 300">
          </div>
          <div class="form-group">
            <label class="form-label">Package/Trim</label>
            <input type="text" name="package_trim" id="edit_package_trim" class="form-input" placeholder="e.g. ZX Modellista">
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Year of Manufacture (YOM)</label>
            <input type="number" name="year_of_manufacture" id="edit_year_of_manufacture" class="form-input" required min="2000" max="<?= date('Y') + 1 ?>">
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Color</label>
            <input type="text" name="color_name" id="edit_color_name" class="form-input" required placeholder="e.g. Pearl White">
          </div>
          <div class="form-group mandatory-field highlight-field">
            <label class="form-label">Chassis Code (KEY IDENTIFIER)</label>
            <input type="text" name="chassis_code" id="edit_chassis_code" class="form-input" required placeholder="e.g. VJA300-0019482">
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Mileage (KM)</label>
            <input type="number" name="mileage_km" id="edit_mileage_km" class="form-input" required min="0">
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Auction Grade</label>
            <select name="auction_grade" id="edit_auction_grade" class="form-select" required>
              <option value="">Select Grade</option>
              <option value="6.0">6.0 (New/Unregistered)</option>
              <option value="S">S (Like New)</option>
              <option value="5.0">5.0 (Excellent)</option>
              <option value="4.5">4.5 (Very Good)</option>
              <option value="4.0">4.0 (Good)</option>
              <option value="3.5">3.5 (Fair)</option>
              <option value="R">R (Repaired/Accident)</option>
            </select>
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Transmission</label>
            <input type="text" name="transmission" id="edit_transmission" class="form-input" required placeholder="e.g. AT, CVT">
          </div>
        </div>

        <div class="form-section-title">Additional Details & Pricing</div>
        <div class="grid-2-col">
          <div class="form-group mandatory-field">
            <label class="form-label">Body Type</label>
            <select name="body_type" id="edit_body_type" class="form-select" required>
              <option value="SUV">SUV</option>
              <option value="Sedan">Sedan</option>
              <option value="Hatchback">Hatchback</option>
              <option value="MPV">MPV</option>
              <option value="Crossover">Crossover</option>
            </select>
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Fuel Type</label>
            <select name="fuel_type" id="edit_fuel_type" class="form-select" required>
              <option value="Petrol">Petrol</option>
              <option value="Hybrid">Hybrid</option>
              <option value="PHEV">PHEV</option>
              <option value="Diesel">Diesel</option>
              <option value="Electric">Electric</option>
            </select>
          </div>
          <div class="form-group mandatory-field">
            <label class="form-label">Target Stock Status</label>
            <select name="status" id="edit_status" class="form-select" required>
              <option value="available">Available — Showroom</option>
              <option value="port_clearance">Port Clearance (CTG)</option>
              <option value="vessel_transit">On Vessel Transit</option>
              <option value="reserved">Reserved</option>
              <option value="sold">Sold</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Asking Price (BDT)</label>
            <input type="number" name="price_bdt" id="edit_price_bdt" class="form-input" placeholder="e.g. 85000000">
          </div>
        </div>
      </div>
      
      <div class="modal-footer">
        <div class="modal-actions">
          <button type="button" class="btn btn-outline" onclick="closeModal('editVehicleModal')">Cancel</button>
          <button type="submit" class="btn btn-primary" id="editVehicleSubmitBtn">Save Changes</button>
        </div>
      </div>
    </form>
  </div>

</div>

<!-- Admin Scripts -->
<script src="<?= ASSETS_URL ?>/js/admin.js"></script>
</body>
</html>
