<?php
include_once "./base/header.php";
include_once "./database/db.php";

session_start();

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

$user_name = $_SESSION['first_name'] . " " . $_SESSION['last_name'];
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];
$avatar_url = $_SESSION['avatar_url'];
$group_name = $_SESSION['group_name'];
$direction_code = $_SESSION['direction_code'] ?? null;
$direction_name = $_SESSION['direction_name'] ?? null;
$profile = $_SESSION['profile'] ?? null;
$moderator_status = $_SESSION['moderator_status'] ?? null;
$moderator_comment = $_SESSION['moderator_comment'] ?? null;
$phone_number = $_SESSION['phone_number'] ?? null;

// Проверка количества участий
$sql = "SELECT COUNT(*) as participation_count FROM event_participation WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$participation_count = $row['participation_count'];

?>

<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">
      <div class="profile text-center">
        <div class="profile-picture-container">
          <img src="<?php echo htmlspecialchars($avatar_url); ?>" alt="Profile Picture" class="profile-picture mb-3">
        </div>
        <div class="profile-info">
          <h3><?php echo htmlspecialchars($user_name); ?></h3>
          <p><?php echo htmlspecialchars($group_name); ?> · <?php echo htmlspecialchars($user_email); ?></p>
        </div>

        <button class="btn btn-outline-secondary mb-4" data-toggle="modal" data-target="#editProfileModal">Изменить данные</button>

        <?php if ($direction_code && $direction_name && $profile) : ?>
          <div class="profile-details">
            <p><strong>Код направления:</strong> <?php echo htmlspecialchars($direction_code); ?></p>
            <p><strong>Название направления:</strong> <?php echo htmlspecialchars($direction_name); ?></p>
            <p><strong>Профиль:</strong> <?php echo htmlspecialchars($profile); ?></p>
          </div>
        <?php else : ?>
          <?php if ($moderator_status == 2) : ?>
            <div class="alert alert-danger">
              Причина, по которой нельзя приобретать сертификаты: <?php echo htmlspecialchars($moderator_comment); ?>
            </div>
          <?php elseif ($moderator_status == 0) : ?>
            <div class="alert alert-warning">
              Профиль на проверке.
            </div>
          <?php endif; ?>
        <?php endif; ?>


        <h4>Мои достижения</h4>
        <div class="showcase-container">
          <div class="award" data-title="Первый игрок на готове">
            <img src="<?php echo ($moderator_status == 1) ? '../images/awards/1a.png' : '../images/awards/1.png'; ?>" alt="Первый игрок на готове">
          </div>

          <div class="award" data-title="Участник трех мероприятий">
            <img src="<?php echo ($participation_count >= 3) ? '../images/awards/2a.png' : '../images/awards/2.png'; ?>" alt="Участник трех мероприятий">
          </div>

          <!-- Добавьте другие награды здесь -->
        </div>


        <h4 class="mt-3">Статистика моих участий</h4>
        <div class="chart-container">
          <canvas id="participationChart"></canvas>
        </div>


      </div>
    </div>
  </div>
</div>
<!-- Модальное окно для редактирования профиля -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="./database/update_profile.php" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfileModalLabel">Редактировать профиль</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="firstName">Имя</label>
            <input type="text" class="form-control" id="firstName" name="firstName" value="<?php echo htmlspecialchars($_SESSION['first_name']); ?>" required>
          </div>
          <div class="form-group">
            <label for="lastName">Фамилия</label>
            <input type="text" class="form-control" id="lastName" name="lastName" value="<?php echo htmlspecialchars($_SESSION['last_name']); ?>" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user_email); ?>" required>
          </div>
          <div class="form-group">
            <label for="phoneNumber">Номер телефона</label>
            <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" value="<?php echo htmlspecialchars($phone_number); ?>" required>
          </div>
          <div class="form-group">
            <label for="groupName">Группа</label>
            <input type="text" class="form-control" id="groupName" name="groupName" value="<?php echo htmlspecialchars($group_name); ?>" required>
          </div>
          <div class="form-group">
            <label for="avatar">Фотография</label>
            <input type="file" class="form-control-file" id="avatar" name="avatar">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Отмена</button>
          <button type="submit" class="btn btn-primary">Сохранить изменения</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    fetch('./database/get_participation_data.php')
      .then(response => response.json())
      .then(data => {
        const months = ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'];
        const chartData = new Array(12).fill(0);

        data.forEach(item => {
          chartData[item.month - 1] = item.participation_count;
        });

        const ctx = document.getElementById('participationChart').getContext('2d');
        const participationChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: months,
            datasets: [{
              label: 'Участия',
              data: chartData,
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 2,
              fill: false,
              tension: 0.1
            }]
          },
          options: {
            scales: {
              y: {
                beginAtZero: true,
                max: 20
              }
            },
            plugins: {
              legend: {
                display: false
              }
            }
          }
        });
      })
      .catch(error => console.error('Error fetching data:', error));
  });
</script>
<?php
include_once "./base/footer.php";
?>