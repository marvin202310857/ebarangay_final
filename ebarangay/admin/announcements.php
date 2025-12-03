<?php
// admin/announcements.php
require_once '../config/config.php';

if (!isLoggedIn() || !isAdmin()) {
    redirect('../login.php');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $conn->real_escape_string($_POST['title']);
    $content = $conn->real_escape_string($_POST['content']);
    $category = $conn->real_escape_string($_POST['category']);
    $status = $conn->real_escape_string($_POST['status']);
    $posted_by = (int) $_SESSION['user_id'];

    if (!empty($_POST['announcement_id'])) {
        $id = (int) $_POST['announcement_id'];
        $sql = "UPDATE announcements
                SET title = '$title',
                    content = '$content',
                    category = '$category',
                    status = '$status'
                WHERE announcement_id = $id";
    } else {
        $sql = "INSERT INTO announcements (title, content, category, status, posted_by)
                VALUES ('$title', '$content', '$category', '$status', $posted_by)";
    }

    if ($conn->query($sql)) {
        $message = 'Announcement saved.';
    } else {
        $message = 'Error saving announcement.';
    }
}

if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM announcements WHERE announcement_id = $id");
    redirect('announcements.php');
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $res = $conn->query("SELECT * FROM announcements WHERE announcement_id = $id");
    $edit_data = $res->fetch_assoc();
}

$announcements = $conn->query("SELECT * FROM announcements ORDER BY posted_date DESC");

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="col-md-10 ms-sm-auto px-md-4">
    <div class="pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h3"><i class="bi bi-megaphone"></i> Announcements</h1>
    </div>

    <?php if ($message): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header"><strong><?php echo $edit_data ? 'Edit' : 'New'; ?> announcement</strong></div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="announcement_id" value="<?php echo $edit_data['announcement_id'] ?? ''; ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" required
                               value="<?php echo htmlspecialchars($edit_data['title'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Category</label>
                        <input type="text" name="category" class="form-control"
                               value="<?php echo htmlspecialchars($edit_data['category'] ?? ''); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <?php $status = $edit_data['status'] ?? 'published'; ?>
                            <option value="published" <?php if ($status === 'published') echo 'selected'; ?>>Published</option>
                            <option value="draft" <?php if ($status === 'draft') echo 'selected'; ?>>Draft</option>
                            <option value="archived" <?php if ($status === 'archived') echo 'selected'; ?>>Archived</option>
                        </select>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Content *</label>
                        <textarea name="content" class="form-control" rows="4" required><?php
                            echo htmlspecialchars($edit_data['content'] ?? '');
                        ?></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Save
                </button>
                <?php if ($edit_data): ?>
                    <a href="announcements.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <h5 class="mb-3">All announcements</h5>
    <div class="card">
        <div class="card-body">
            <?php if ($announcements->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Posted date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; while ($row = $announcements->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo ucfirst($row['status']); ?></span></td>
                                <td><?php echo date('M d, Y', strtotime($row['posted_date'])); ?></td>
                                <td>
                                    <a href="announcements.php?edit=<?php echo $row['announcement_id']; ?>"
                                       class="btn btn-sm btn-primary">Edit</a>
                                    <a href="announcements.php?delete=<?php echo $row['announcement_id']; ?>"
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this announcement?');">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">No announcements found.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
