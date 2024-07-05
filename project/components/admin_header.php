<?php
if (isset($message)) {
   foreach ($message as $message) {
      echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">

   <a href="dashboard.php" class="logo">Admin<span>Panel</span></a>

   <div class="profile">
      <?php
      $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
      $select_profile->execute([$admin_id]);
      $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
      ?>
      <p><?= $fetch_profile['name']; ?></p>
      <a href="update_profile.php" class="btn">Thay đổi thông tin</a>
   </div>

   <nav class="navbar">
      <a href="dashboard.php" class="active"><i class="fas fa-home"></i> <span>Trang chủ</span></a>
      <a href="add_posts.php"><i class="fas fa-pen"></i> <span>Thêm bài viết</span></a>
      <a href="view_posts.php"><i class="fas fa-eye"></i> <span>Xem bài viết</span></a>
      <a href="admin_accounts.php"><i class="fas fa-user"></i> <span>Tài khoản</span></a>
      <a href="setting.php"><i class="fa-solid fa-gear"></i> <span>Cài đặt</span></a>
      <a href="../components/admin_logout.php" style="color:var(--red);" onclick="return confirm('logout from the website?');"><i class="fas fa-right-from-bracket"></i><span>Thoát</span></a>
   </nav>

   <div class="flex-btn">
      <a href="admin_login.php" class="option-btn ">Đăng nhập</a>
      <a href="register_admin.php" class="option-btn ">Đăng ký</a>
   </div>

</header>

<div id="menu-btn" class="fas fa-bars"></div>

<script>
   document.addEventListener('DOMContentLoaded', () => {
      const currentPath = window.location.pathname;
      const navbarLinks = document.querySelectorAll('.navbar a');

      // Loại bỏ class 'active' khỏi tất cả các liên kết trước khi gán lại
      navbarLinks.forEach(link => {
         link.classList.remove('active');
      });

      // Gán class 'active' cho liên kết hiện tại dựa trên đường dẫn
      navbarLinks.forEach(link => {
         if (link.getAttribute('href') === currentPath) {
            link.classList.add('active');
         }

         // Ghi nhớ liên kết đã nhấp vào
         link.addEventListener('click', () => {
            sessionStorage.setItem('activeNavbarLink', link.getAttribute('href'));
         });
      });

      // Lấy liên kết đã được lưu trong sessionStorage và gán class 'active' cho nó
      const activeLink = sessionStorage.getItem('activeNavbarLink');
      if (activeLink) {
         document.querySelector(`.navbar a[href="${activeLink}"]`)?.classList.add('active');
      }
   });
</script>