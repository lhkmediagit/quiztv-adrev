<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="admin-section-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 class="admin-card-title" style="margin: 0;">Captured Leads & Player Details</h2>
        <?php if (!empty($players)): ?>
            <a href="<?= site_url('admin/players/download') ?>" class="btn btn-outline btn-sm">
                <span style="margin-right: 4px;">📥</span> Download
            </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($players)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email Address</th>
                        <th>Phone Number</th>
                        <th>Quiz Attempted</th>
                        <th>Date Submitted</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $p): ?>
                        <tr>
                            <td><strong><?= esc($p->lead_name) ?></strong></td>
                            <td><?= esc($p->lead_email) ?></td>
                            <td><span style="font-family: monospace; font-size: 0.95em;"><?= esc($p->lead_phone) ?></span></td>
                            <td><span class="badge-status status-active"><?= esc($p->quiz_title ?? 'Deleted Quiz') ?></span></td>
                            <td style="color: var(--text-muted); font-size: 0.9em;">
                                <?= date('M d, Y - h:i A', strtotime($p->created_at)) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Download button at the bottom as requested -->
        <div style="margin-top: 25px; display: flex; justify-content: flex-end;">
            <a href="<?= site_url('admin/players/download') ?>" class="btn btn-primary btn-lg" id="download-players-csv-btn">
                <span class="btn-icon">📥</span> Download
            </a>
        </div>
    <?php else: ?>
        <div class="admin-empty-state" style="padding: 40px; text-align: center; color: var(--text-muted);">
            <div style="font-size: 48px; margin-bottom: 15px;">🎮</div>
            <p style="font-size: 16px; font-weight: 500; margin-bottom: 10px;">No player information captured yet.</p>
            <p style="font-size: 14px; max-width: 400px; margin: 0 auto;">Player leads are saved automatically when guest or registered users complete a quiz and submit the lead capture form.</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
