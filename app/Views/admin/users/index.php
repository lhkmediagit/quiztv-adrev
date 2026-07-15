<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="admin-section-card">
    <h2 class="admin-card-title">Register User Base</h2>
    <?php if (!empty($users)): ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 70px;">Avatar</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Quizzes Taken</th>
                        <th>Status</th>
                        <th style="width: 250px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>
                                <div class="admin-avatar-small">
                                    <?php if ($u->avatar): ?>
                                        <img src="<?= esc($u->avatar) ?>" alt="Avatar" class="avatar-img">
                                    <?php else: ?>
                                        <span class="avatar-char"><?= strtoupper(substr($u->name, 0, 1)) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td><strong><?= esc($u->name) ?></strong></td>
                            <td><?= esc($u->email) ?></td>
                            <td><?= esc($u->total_quizzes_taken) ?></td>
                            <td>
                                <?php if ((int)$u->is_banned === 1): ?>
                                    <span class="badge-status status-inactive">Banned</span>
                                <?php else: ?>
                                    <span class="badge-status status-active">Active</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-actions-cell">
                                    <a href="<?= site_url('admin/users/view/' . esc($u->id)) ?>" class="btn btn-outline btn-sm">History</a>
                                    
                                    <!-- Toggle Ban -->
                                    <form action="<?= site_url('admin/users/toggle-ban/' . esc($u->id)) ?>" method="POST" class="inline-form">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn <?= (int)$u->is_banned === 1 ? 'btn-success' : 'btn-danger' ?> btn-sm">
                                            <?= (int)$u->is_banned === 1 ? 'Unban' : 'Ban' ?>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="admin-empty-state">
            <p>No registered users found.</p>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
