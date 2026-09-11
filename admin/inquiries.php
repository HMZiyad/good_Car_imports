<?php
/**
 * Good Car Imports — Admin Inquiries & Pre-Orders
 */

$pageTitle = 'Inquiries & Pre-Orders';
$pageSubtitle = 'Manage customer leads and pre-order requests';
require_once __DIR__ . '/includes/admin-header.php';

// Fetch inquiries
$inquiries = dbFetchAll("SELECT * FROM inquiries ORDER BY created_at DESC");
// Fetch pre-orders
$preOrders = dbFetchAll("SELECT * FROM pre_orders ORDER BY created_at DESC");

// Determine active tab
$tab = $_GET['tab'] ?? 'inquiries';
?>

<div class="admin-content-card" style="margin-top: 24px;">
  <div class="card-header" style="display: flex; gap: 24px; border-bottom: 1px solid var(--surface-container-high); padding-bottom: 0;">
    <a href="?tab=inquiries" style="padding: 16px 8px; font-weight: 600; color: <?= $tab === 'inquiries' ? 'var(--primary)' : 'var(--secondary)' ?>; border-bottom: 2px solid <?= $tab === 'inquiries' ? 'var(--primary)' : 'transparent' ?>; text-decoration: none;">
      Contact Inquiries (<?= count($inquiries) ?>)
    </a>
    <a href="?tab=pre_orders" style="padding: 16px 8px; font-weight: 600; color: <?= $tab === 'pre_orders' ? 'var(--primary)' : 'var(--secondary)' ?>; border-bottom: 2px solid <?= $tab === 'pre_orders' ? 'var(--primary)' : 'transparent' ?>; text-decoration: none;">
      Pre-Orders (<?= count($preOrders) ?>)
    </a>
  </div>

  <div class="card-body" style="padding: 0;">
    <?php if ($tab === 'inquiries'): ?>
      <!-- INQUIRIES TABLE -->
      <table class="admin-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Interested In</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($inquiries as $inq): ?>
          <tr style="<?= $inq['status'] === 'new' ? 'background: rgba(255, 77, 94, 0.05); font-weight: 600;' : '' ?>">
            <td style="color:var(--secondary); font-size:13px;"><?= date('M j, Y g:i A', strtotime($inq['created_at'])) ?></td>
            <td><?= sanitize($inq['full_name']) ?></td>
            <td>
              <div><?= sanitize($inq['phone']) ?></div>
              <div style="font-size:12px; color:var(--secondary);"><?= sanitize($inq['email']) ?></div>
            </td>
            <td><?= sanitize($inq['interested_in'] ?? 'General') ?></td>
            <td>
              <?php if ($inq['status'] === 'new'): ?>
                <span class="badge" style="background:var(--error-container); color:var(--on-error-container);">New</span>
              <?php else: ?>
                <span class="badge" style="background:var(--surface-container-high); color:var(--on-surface);">Read</span>
              <?php endif; ?>
            </td>
            <td style="text-align:right;">
              <div style="display:flex; gap:8px; justify-content:flex-end;">
                <button class="btn btn-outline" style="padding:4px 8px; font-size:13px;" onclick="viewInquiry(<?= $inq['id'] ?>)">View</button>
                <button class="btn btn-outline" style="padding:4px 8px; font-size:13px; color:var(--error); border-color:var(--error);" onclick="deleteInquiry(<?= $inq['id'] ?>)">
                  <span class="material-symbols-outlined" style="font-size:16px;">delete</span>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($inquiries)): ?>
            <tr><td colspan="6" style="text-align:center; padding: 48px; color:var(--secondary);">No contact inquiries found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php else: ?>
      <!-- PRE-ORDERS TABLE -->
      <table class="admin-table">
        <thead>
          <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Car Requested</th>
            <th>Budget</th>
            <th>Status</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($preOrders as $po): ?>
          <tr style="<?= $po['status'] === 'new' ? 'background: rgba(255, 77, 94, 0.05); font-weight: 600;' : '' ?>">
            <td style="color:var(--secondary); font-size:13px;"><?= date('M j, Y g:i A', strtotime($po['created_at'])) ?></td>
            <td><?= sanitize($po['full_name']) ?></td>
            <td>
              <div><?= sanitize($po['phone']) ?></div>
              <div style="font-size:12px; color:var(--secondary);"><?= sanitize($po['email']) ?></div>
            </td>
            <td>
              <div style="font-weight:600;"><?= sanitize($po['make_model']) ?></div>
              <div style="font-size:12px; color:var(--secondary);"><?= sanitize($po['year_from']) ?>+ • <?= sanitize($po['body_style']) ?></div>
            </td>
            <td>
              <?php 
                if ($po['budget_min_bdt']) {
                  echo formatBDT($po['budget_min_bdt'], true) . ' - ' . formatBDT($po['budget_max_bdt'], true);
                } else {
                  echo 'Not specified';
                }
              ?>
            </td>
            <td>
              <?php if ($po['status'] === 'new'): ?>
                <span class="badge" style="background:var(--error-container); color:var(--on-error-container);">New</span>
              <?php else: ?>
                <span class="badge" style="background:var(--surface-container-high); color:var(--on-surface);">Read</span>
              <?php endif; ?>
            </td>
            <td style="text-align:right;">
              <div style="display:flex; gap:8px; justify-content:flex-end;">
                <button class="btn btn-outline" style="padding:4px 8px; font-size:13px;" onclick="viewPreOrder(<?= $po['id'] ?>)">View</button>
                <button class="btn btn-outline" style="padding:4px 8px; font-size:13px; color:var(--error); border-color:var(--error);" onclick="deletePreOrder(<?= $po['id'] ?>)">
                  <span class="material-symbols-outlined" style="font-size:16px;">delete</span>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($preOrders)): ?>
            <tr><td colspan="7" style="text-align:center; padding: 48px; color:var(--secondary);">No pre-orders found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</div>

<!-- Modal: View Inquiry -->
<div class="modal-container" id="viewInquiryModal" style="display:none; max-width: 500px;" onclick="event.stopPropagation()">
  <div class="modal-header">
    <h2 class="modal-title">Inquiry Details</h2>
    <button type="button" class="modal-close" onclick="closeModal('viewInquiryModal'); window.location.reload();">
      <span class="material-symbols-outlined">close</span>
    </button>
  </div>
  <div class="modal-body" id="inquiryModalBody" style="line-height: 1.6;">
    <!-- Content populated via JS -->
  </div>
</div>

<!-- Modal: View Pre-Order -->
<div class="modal-container" id="viewPreOrderModal" style="display:none; max-width: 600px;" onclick="event.stopPropagation()">
  <div class="modal-header">
    <h2 class="modal-title">Pre-Order Details</h2>
    <button type="button" class="modal-close" onclick="closeModal('viewPreOrderModal'); window.location.reload();">
      <span class="material-symbols-outlined">close</span>
    </button>
  </div>
  <div class="modal-body" id="preOrderModalBody" style="line-height: 1.6;">
    <!-- Content populated via JS -->
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const overlay = document.getElementById('modalOverlay');
  if (overlay) {
    overlay.appendChild(document.getElementById('viewInquiryModal'));
    overlay.appendChild(document.getElementById('viewPreOrderModal'));
  }
});

async function viewInquiry(id) {
  try {
    const res = await fetch(`../api/admin.php?action=get_inquiry&id=${id}`, { method: 'POST' });
    const json = await res.json();
    if (json.success) {
      const data = json.data;
      document.getElementById('inquiryModalBody').innerHTML = `
        <div style="display:grid; grid-template-columns: 100px 1fr; gap: 8px; margin-bottom: 16px;">
          <div style="color:var(--secondary); font-weight:600;">Name:</div><div>${data.full_name}</div>
          <div style="color:var(--secondary); font-weight:600;">Phone:</div><div>${data.phone}</div>
          <div style="color:var(--secondary); font-weight:600;">Email:</div><div>${data.email || '-'}</div>
          <div style="color:var(--secondary); font-weight:600;">Interested:</div><div>${data.interested_in || 'General'}</div>
          <div style="color:var(--secondary); font-weight:600;">Date:</div><div>${new Date(data.created_at).toLocaleString()}</div>
        </div>
        <hr style="border:0; border-top:1px solid var(--surface-container-high); margin: 16px 0;">
        <div style="color:var(--secondary); font-weight:600; margin-bottom: 8px;">Message:</div>
        <div style="background:var(--surface-container-low); padding:16px; border-radius:8px; white-space:pre-wrap; word-break:break-word; overflow-wrap:anywhere;">${data.message || 'No message provided.'}</div>
      `;
      openModal('viewInquiryModal');
    } else {
      alert(json.message);
    }
  } catch (err) {
    alert('Network error');
  }
}

async function viewPreOrder(id) {
  try {
    const res = await fetch(`../api/admin.php?action=get_pre_order&id=${id}`, { method: 'POST' });
    const json = await res.json();
    if (json.success) {
      const data = json.data;
      document.getElementById('preOrderModalBody').innerHTML = `
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 24px;">
          <div>
            <h3 style="margin-bottom: 12px; font-size:14px; color:var(--primary);">Customer Info</h3>
            <div style="display:grid; grid-template-columns: 80px 1fr; gap: 8px;">
              <div style="color:var(--secondary); font-weight:600;">Name:</div><div>${data.full_name}</div>
              <div style="color:var(--secondary); font-weight:600;">Phone:</div><div>${data.phone}</div>
              <div style="color:var(--secondary); font-weight:600;">Email:</div><div>${data.email || '-'}</div>
              <div style="color:var(--secondary); font-weight:600;">WhatsApp:</div><div>${data.whatsapp || 'No'}</div>
            </div>
          </div>
          <div>
            <h3 style="margin-bottom: 12px; font-size:14px; color:var(--primary);">Vehicle Request</h3>
            <div style="display:grid; grid-template-columns: 80px 1fr; gap: 8px;">
              <div style="color:var(--secondary); font-weight:600;">Car:</div><div style="font-weight:600;">${data.make_model}</div>
              <div style="color:var(--secondary); font-weight:600;">Body:</div><div>${data.body_style || '-'}</div>
              <div style="color:var(--secondary); font-weight:600;">Year:</div><div>${data.year_from ? data.year_from + '+' : '-'}</div>
              <div style="color:var(--secondary); font-weight:600;">Color:</div><div>${data.color_preference || 'Any'}</div>
              <div style="color:var(--secondary); font-weight:600;">Region:</div><div>${data.region_of_origin || 'Any'}</div>
            </div>
          </div>
        </div>
        <hr style="border:0; border-top:1px solid var(--surface-container-high); margin: 16px 0;">
        <div style="display:grid; grid-template-columns: 80px 1fr; gap: 8px; margin-bottom: 16px;">
          <div style="color:var(--secondary); font-weight:600;">Budget:</div><div style="font-weight:600; color:var(--tertiary);">${data.budget_min_bdt ? 'BDT ' + (data.budget_min_bdt/10000000) + 'L - ' + (data.budget_max_bdt/10000000) + 'L' : 'Not specified'}</div>
          <div style="color:var(--secondary); font-weight:600;">Timeline:</div><div>${data.timeline || '-'}</div>
        </div>
        <div style="color:var(--secondary); font-weight:600; margin-bottom: 8px;">Additional Notes:</div>
        <div style="background:var(--surface-container-low); padding:16px; border-radius:8px; white-space:pre-wrap; word-break:break-word; overflow-wrap:anywhere;">${data.additional_notes || 'None'}</div>
      `;
      openModal('viewPreOrderModal');
    } else {
      alert(json.message);
    }
  } catch (err) {
    alert('Network error');
  }
}

async function deleteInquiry(id) {
  if (!confirm('Are you sure you want to delete this inquiry?')) return;
  const fd = new FormData();
  fd.append('id', id);
  try {
    const res = await fetch('../api/admin.php?action=delete_inquiry', { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) {
      window.location.reload();
    } else {
      alert(json.message);
    }
  } catch(e) { alert('Error deleting'); }
}

async function deletePreOrder(id) {
  if (!confirm('Are you sure you want to delete this pre-order?')) return;
  const fd = new FormData();
  fd.append('id', id);
  try {
    const res = await fetch('../api/admin.php?action=delete_pre_order', { method: 'POST', body: fd });
    const json = await res.json();
    if (json.success) {
      window.location.reload();
    } else {
      alert(json.message);
    }
  } catch(e) { alert('Error deleting'); }
}
</script>

<?php require_once __DIR__ . '/includes/admin-footer.php'; ?>
