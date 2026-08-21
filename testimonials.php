<?php
require_once 'config/config.php';
require_once 'config/function.php';
requireLogin();

// Verify token
if (!verifyToken($pdo)) {
    header("Location: " . APP_URL . "login.php");
    exit();
}

$currentUser = getCurrentUser($pdo);
$pageTitle = "Testimonials";

// Fetch all testimonials
$stmt = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC");
$testimonials = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once 'includes/head_links.php'; ?>
    <title>Testimonials · Tour Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .page-wrapper {
            padding: 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h4 {
            font-weight: 600;
            color: #123b4f;
            margin-bottom: 0;
            font-size: 1.2rem;
        }

        .page-header p {
            color: #5f7d92;
            margin-bottom: 0;
            font-size: 0.8rem;
        }

        .btn-add {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.4rem 1.2rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            font-size: 0.8rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .btn-add-empty {
            background: linear-gradient(145deg, #0b2a3e 0%, #123b4f 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            margin-top: 0.8rem;
            font-size: 0.85rem;
        }

        .btn-add-empty:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(11, 42, 62, 0.2);
            color: #ffd966;
        }

        .table-container {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.6);
            box-shadow: 0 4px 16px rgba(0, 20, 30, 0.04);
            overflow-x: auto;
        }

        .table-custom {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
        }

        .table-custom thead th {
            background: rgba(18, 59, 79, 0.04);
            color: #123b4f;
            font-weight: 600;
            padding: 0.8rem 0.8rem;
            text-align: left;
            border-bottom: 2px solid #ffd966;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .table-custom tbody td {
            padding: 0.7rem 0.8rem;
            border-bottom: 1px solid #f0f3f7;
            color: #123b4f;
            vertical-align: middle;
        }

        .table-custom tbody tr:hover {
            background: rgba(255, 215, 100, 0.04);
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }

        .logo-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e8edf3;
        }

        .logo-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #f0f3f7;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9bb2c5;
            font-size: 1.2rem;
            border: 2px solid #e8edf3;
        }

        .testimonial-text {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .testimonial-text:hover {
            white-space: normal;
            overflow: visible;
        }

        .badge-status {
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
            font-size: 0.65rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-status.publish {
            background: rgba(40, 167, 69, 0.15);
            color: #28a745;
        }

        .badge-status.unpublish {
            background: rgba(220, 53, 69, 0.15);
            color: #dc3545;
        }

        .btn-action {
            padding: 0.25rem 0.6rem;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 500;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
        }

        .btn-edit {
            background: rgba(18, 59, 79, 0.1);
            color: #123b4f;
        }

        .btn-edit:hover {
            background: #123b4f;
            color: #fff;
        }

        .btn-delete {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .btn-delete:hover {
            background: #dc3545;
            color: #fff;
        }

        .btn-toggle {
            background: rgba(255, 215, 100, 0.15);
            color: #b8860b;
        }

        .btn-toggle:hover {
            background: #ffd966;
            color: #123b4f;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
        }

        .empty-state i {
            font-size: 3rem;
            color: #e8edf3;
        }

        .empty-state h5 {
            color: #123b4f;
            margin-top: 0.8rem;
            font-size: 1.1rem;
        }

        .empty-state p {
            color: #5f7d92;
            font-size: 0.85rem;
        }

        .col-logo {
            width: 60px;
        }

        .col-name {
            min-width: 120px;
        }

        .col-testimonial {
            min-width: 200px;
        }

        .col-status {
            width: 100px;
        }

        .col-actions {
            width: 160px;
        }

        @media (max-width: 768px) {
            .page-wrapper {
                padding: 10px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-header .btn-add {
                width: 100%;
                justify-content: center;
            }

            .table-container {
                padding: 0.8rem;
                border-radius: 12px;
            }

            .table-custom {
                font-size: 0.75rem;
            }

            .table-custom thead th,
            .table-custom tbody td {
                padding: 0.5rem 0.5rem;
            }

            .col-logo {
                width: 40px;
            }

            .col-actions {
                width: 120px;
            }

            .btn-action {
                font-size: 0.6rem;
                padding: 0.2rem 0.4rem;
            }

            .logo-img,
            .logo-placeholder {
                width: 30px;
                height: 30px;
            }

            .testimonial-text {
                max-width: 120px;
            }
        }
    </style>
</head>

<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <?php include_once 'includes/nav.php'; ?>

    <div class="main-wrapper">
        <div class="top-bar">
            <button class="burger-btn" id="burgerBtn" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <div class="greeting-center">
                Welcome back, <strong><?= htmlspecialchars($currentUser['name'] ?? 'Admin') ?></strong>
                <small>Testimonials</small>
            </div>
        </div>

        <div class="page-wrapper">
            <div class="page-header">
                <div>
                    <h4><i class="bi bi-chat-quote me-2" style="color:#f5b342;"></i>Testimonials</h4>
                    <p>Manage client testimonials</p>
                </div>
                <a href="add-testimonial.php" class="btn-add">
                    <i class="bi bi-plus-circle"></i> Add New
                </a>
            </div>

            <div class="table-container">
                <?php if (empty($testimonials)): ?>
                    <div class="empty-state">
                        <i class="bi bi-chat-quote"></i>
                        <h5>No Testimonials Yet</h5>
                        <p>Add your first client testimonial</p>
                    </div>
                <?php else: ?>
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th class="col-logo">Logo</th>
                                <th class="col-name">Name</th>
                                <th class="col-testimonial">Testimonial</th>
                                <th class="col-status">Status</th>
                                <th class="col-actions">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($testimonials as $testimonial): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($testimonial['logo'])): ?>
                                            <img src="<?= APP_URL . $testimonial['logo'] ?>" alt="<?= htmlspecialchars($testimonial['name']) ?>" class="logo-img">
                                        <?php else: ?>
                                            <div class="logo-placeholder">
                                                <i class="bi bi-person"></i>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($testimonial['name']) ?></strong></td>
                                    <td>
                                        <div class="testimonial-text" title="<?= htmlspecialchars($testimonial['testimonial']) ?>">
                                            <?= htmlspecialchars($testimonial['testimonial']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-status <?= $testimonial['status'] ?>">
                                            <?= ucfirst($testimonial['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display:flex;gap:4px;flex-wrap:wrap;">
                                            <a href="edit-testimonial.php?id=<?= $testimonial['id'] ?>" class="btn-action btn-edit">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <button class="btn-action btn-toggle" onclick="toggleStatus(<?= $testimonial['id'] ?>, '<?= $testimonial['status'] ?>')">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            <button class="btn-action btn-delete" onclick="deleteTestimonial(<?= $testimonial['id'] ?>, '<?= htmlspecialchars($testimonial['name']) ?>')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // =============================================
        // TOGGLE STATUS
        // =============================================

        function toggleStatus(id, currentStatus) {
            const newStatus = currentStatus === 'publish' ? 'unpublish' : 'publish';
            const action = newStatus === 'publish' ? 'publish' : 'unpublish';

            Swal.fire({
                title: action === 'publish' ? 'Publish Testimonial?' : 'Unpublish Testimonial?',
                text: action === 'publish' ? 'This testimonial will be visible to users.' : 'This testimonial will be hidden from users.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#123b4f',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, ' + action + ' it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Updating...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('status', newStatus);

                    fetch('ajax/toggle-testimonial-status.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message,
                                    confirmButtonColor: '#123b4f'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An error occurred. Please try again.',
                                confirmButtonColor: '#123b4f'
                            });
                        });
                }
            });
        }

        // =============================================
        // DELETE TESTIMONIAL
        // =============================================

        function deleteTestimonial(id, name) {
            Swal.fire({
                title: 'Delete Testimonial?',
                text: 'Are you sure you want to delete testimonial from "' + name + '"? This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    const formData = new FormData();
                    formData.append('id', id);

                    fetch('ajax/delete-testimonial.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: data.message,
                                    confirmButtonColor: '#123b4f'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An error occurred. Please try again.',
                                confirmButtonColor: '#123b4f'
                            });
                        });
                }
            });
        }
    </script>
    <script src="<?= APP_URL ?>javascript/main.js"></script>
</body>

</html>