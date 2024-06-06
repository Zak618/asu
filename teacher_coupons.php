<?php
include_once "./base/header.php";
include_once "./database/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 2) {
    header("Location: login.php");
    exit();
}

$teacher_id = $_SESSION['user_id'];

$sql = "SELECT h.*, m.item_name, u.first_name, u.last_name FROM history_market h
        JOIN market m ON h.item_id = m.id
        JOIN users u ON h.user_id = u.id
        WHERE h.teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$result = $stmt->get_result();

$coupons = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $coupons[] = $row;
    }
}

$stmt->close();
$conn->close();
?>
<style>
    .coupon-card {
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        background-color: #fff;
    }

    .coupon-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }

    .badge-custom {
        background-color: #ffcc00;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
        font-size: 0.9em;
    }

    .icon {
        margin-right: 10px;
    }

    .date {
        font-size: 0.9em;
        color: #888;
    }
</style>
<h2 class="text-center mb-4">Активированные купоны</h2>
<div class="container mt-4">
    <div class="row">
        <?php if (empty($coupons)) : ?>
            <div class="col-12">
                <p class="text-center">Нет активированных купонов</p>
            </div>
        <?php else : ?>
            <?php foreach ($coupons as $coupon) : ?>
                <div class="col-12 col-md-6 col-lg-4 mb-4">
                    <div class="coupon-card">
                        <h5><i class="icon fas fa-ticket-alt"></i> <?php echo htmlspecialchars($coupon['item_name']); ?></h5>
                        <p class="date">Дата и время: <?php echo date('d.m.Y H:i', strtotime($coupon['purchase_date'] . ' +3 hours')); ?></p>
                        <p><strong>Студент:</strong> <?php echo htmlspecialchars($coupon['first_name'] . ' ' . $coupon['last_name']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php
include_once "./base/footer.php";
?>