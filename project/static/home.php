<?php
include '../components/connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
};

include '../components/like_post.php';

// Truy vấn để lấy đường dẫn ảnh
$settings_query = $conn->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ('banner_slide_1', 'banner_slide_2', 'banner_slide_3', 'banner_slide_4')");
$settings_query->execute();
$settings = $settings_query->fetchAll(PDO::FETCH_KEY_PAIR);

// Đảm bảo rằng tất cả các banner có giá trị mặc định nếu không có trong database
$default_images = [
   'banner_slide_1' => '../uploaded_img/default_img.jpg',
   'banner_slide_2' => '../uploaded_img/default_img.jpg',
   'banner_slide_3' => '../uploaded_img/default_img.jpg',
   'banner_slide_4' => '../uploaded_img/default_img.jpg',
];

$banner_images = array_merge($default_images, $settings);

// Xử lý lưu bài viết
if (isset($_POST['save_post']) && isset($_POST['post_id']) && !empty($user_id)) {
   $post_id = $_POST['post_id'];

   // Kiểm tra xem bài viết đã được lưu chưa
   $stmt_check = $conn->prepare("SELECT * FROM favorite_posts WHERE user_id = ? AND post_id = ?");
   $stmt_check->execute([$user_id, $post_id]);

   if ($stmt_check->rowCount() > 0) {
      // Nếu đã lưu, thì xóa khỏi danh sách yêu thích
      $stmt_delete = $conn->prepare("DELETE FROM favorite_posts WHERE user_id = ? AND post_id = ?");
      $stmt_delete->execute([$user_id, $post_id]);
   } else {
      // Nếu chưa lưu, thêm vào danh sách yêu thích
      $stmt_insert = $conn->prepare("INSERT INTO favorite_posts (user_id, post_id) VALUES (?, ?)");
      $stmt_insert->execute([$user_id, $post_id]);
   }

   // Redirect hoặc xử lý tiếp theo sau khi lưu thay đổi
   header("Location: " . $_SERVER['PHP_SELF']);
   exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <!-- 
   <link rel="stylesheet" href="css/style.css"> -->
   <link rel="stylesheet" href="../css/style_edit.css">

</head>

<body>
   <?php include '../components/user_header.php'; ?>

   <div class="banner-container">
      <h1 class="heading"></h1>

      <swiper-container class="mySwiper" pagination="true" pagination-clickable="true" navigation="true" space-between="30" centered-slides="true" autoplay-delay="5000" autoplay-disable-on-interaction="false">
         <swiper-slide><img src="<?php echo htmlspecialchars($banner_images['banner_slide_1']); ?>" alt="">
            <div class="modal">
               <div class="caption">
                  <h3>BAO ANH</h3>
                  <button>Click me</button>
               </div>
               <div class="content">
                  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vel, sint.</p>
               </div>
            </div>
         </swiper-slide>
         <swiper-slide><img src="<?php echo htmlspecialchars($banner_images['banner_slide_2']); ?>" alt="">
            <div class="modal">
               <div class="caption">
                  <h3>TAXI PHAN RANG</h3>
                  <button>Click me</button>
               </div>
               <div class="content">
                  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vel, sint.</p>
               </div>
            </div>
         </swiper-slide>
         <swiper-slide><img src="<?php echo htmlspecialchars($banner_images['banner_slide_3']); ?>" alt="">
            <div class="modal">
               <div class="caption">
                  <h3>PHAN RANG - THAPS CHAMS</h3>
                  <button>Click me</button>
               </div>
               <div class="content">
                  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vel, sint.</p>
               </div>
            </div>
         </swiper-slide>
         <swiper-slide><img src="<?php echo htmlspecialchars($banner_images['banner_slide_4']); ?>" alt="">
            <div class="modal">
               <div class="caption">
                  <h3>NINH HAI NINH THUAN</h3>
                  <button>Click me</button>
               </div>
               <div class="content">
                  <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Vel, sint.</p>
               </div>
            </div>
         </swiper-slide>
      </swiper-container>
   </div>

   <section class="home-grid">

      <div class="box-container">

         <div class="box">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if ($select_profile->rowCount() > 0) {
               $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
               $count_user_comments = $conn->prepare("SELECT * FROM `comments` WHERE user_id = ?");
               $count_user_comments->execute([$user_id]);
               $total_user_comments = $count_user_comments->rowCount();
               $count_user_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ?");
               $count_user_likes->execute([$user_id]);
               $total_user_likes = $count_user_likes->rowCount();
            ?>
               <p> welcome <span><?= $fetch_profile['name']; ?></span></p>
               <p>total comments : <span><?= $total_user_comments; ?></span></p>
               <p>posts liked : <span><?= $total_user_likes; ?></span></p>
               <a href="update.php" class="btn">update profile</a>

               <div class="flex-btn">
                  <a href="user_likes.php" class="option-btn">likes</a>
                  <a href="user_comments.php" class="option-btn">comments</a>
               </div>
            <?php
            } else {
            ?>
               <p class="name">Đăng nhập hoặc Đăng Ký!</p>
               <div class="flex-btn">
                  <a href="login.php" class="option-btn">Đăng nhập</a>
                  <a href="register.php" class="option-btn">Đăng ký</a>
               </div>
            <?php
            }
            ?>
         </div>

         <div class="box">
            <p>Các thể loại</p>
            <div class="flex-box">
               <a href="category.php?category=nature" class="links">nature</a>
               <a href="category.php?category=travel" class="links">travel</a>
               <a href="category.php?category=news" class="links">news</a>
               <a href="category.php?category=gaming" class="links">gaming</a>
               <a href="category.php?category=sports" class="links">sports</a>

               <a href="all_category.php" class="btn">Xem tất cả</a>
            </div>
         </div>

         <div class="box">
            <p>Tác giả</p>
            <div class="flex-box">
               <?php
               $select_authors = $conn->prepare("SELECT DISTINCT name FROM `admin` LIMIT 10");
               $select_authors->execute();
               if ($select_authors->rowCount() > 0) {
                  while ($fetch_authors = $select_authors->fetch(PDO::FETCH_ASSOC)) {
               ?>
                     <a href="author_posts.php?author=<?= $fetch_authors['name']; ?>" class="links"><?= $fetch_authors['name']; ?></a>
               <?php
                  }
               } else {
                  echo '<p class="empty">no posts added yet!</p>';
               }
               ?>
               <a href="authors.php" class="btn">Xem tất cả</a>
            </div>
         </div>

      </div>

   </section>


   <?php
   include './introduce.php'
   ?>

   <?php
   // Lấy tất cả các ID từ bảng `posts`
   $select_ids = $conn->prepare("SELECT id FROM `posts`");
   $select_ids->execute();
   $ids = $select_ids->fetchAll(PDO::FETCH_COLUMN);

   // Kiểm tra xem có ID nào không
   if ($ids) {
      // Lấy một chỉ mục ngẫu nhiên từ mảng các ID
      $random_index = array_rand($ids);

      // Lấy ID tương ứng từ mảng
      $post_id = $ids[$random_index];
   } else {
      // Không có ID nào, đặt $post_id thành null hoặc giá trị mặc định nào đó
      $post_id = null;
   }


   $select_post = $conn->prepare("SELECT * FROM `posts` WHERE id = ?");
   $select_post->execute([$post_id]);
   $fetch_post = $select_post->fetch(PDO::FETCH_ASSOC);

   if ($fetch_post) {
   ?>
      <!-- main post -->
      <section class="main_post-container">
         <div class="box_container">
            <div class="box ">
               <div class="box_left">
                  <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                  <input type="hidden" name="admin_id" value="<?= $fetch_post['admin_id']; ?>">
                  <div class="post-admin">
                     <i class="fas fa-user"></i>
                     <div>
                        <a href="author_posts.php?author=<?= $fetch_post['name']; ?>"><?= $fetch_post['name']; ?></a>
                        <div><?= $fetch_post['date']; ?></div>
                     </div>
                  </div>

                  <?php if ($fetch_post['image'] != '') { ?>
                     <img src="../uploaded_img/<?= $fetch_post['image']; ?>" class="post_main-image" alt="">
                  <?php } ?>
               </div>

               <div class="box_right">
                  <a href="view_post.php?post_id=<?= $post_id; ?>" class="post-title"><?= $fetch_post['title']; ?></a>
                  <a href="view_post.php?post_id=<?= $post_id; ?>" class="post-content content-200"><?= $fetch_post['content']; ?></a>
                  <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">Đọc thêm</a>
               </div>

               <!-- Tính toán số lượt bình luận và lượt thích -->
               <?php
               $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
               $count_post_comments->execute([$post_id]);
               $total_post_comments = $count_post_comments->rowCount();

               $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
               $count_post_likes->execute([$post_id]);
               $total_post_likes = $count_post_likes->rowCount();

               $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
               $confirm_likes->execute([$user_id, $post_id]);
               ?>

               <!-- <div class="icons">
                <div><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></div>
                <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($confirm_likes->rowCount() > 0) {
                                                                                          echo 'color:var(--red);';
                                                                                       } ?>"></i><span>(<?= $total_post_likes; ?>)</span></button>
            </div> -->
            </div>
         </div>
      </section>

   <?php
   }
   ?>


   <!-- posts part -->
   <section class="posts-container" id="news">

      <h1 class="heading">Bài viết được phổ biến</h1>

      <?php
      $count_posts = $conn->prepare("SELECT COUNT(*) FROM `posts`");
      $count_posts->execute();
      $num_posts = $count_posts->fetchColumn();

      if ($num_posts >= 4) {
      ?>

         <div class="btn_post">
            <button class="prev" id="prevBtn"><i class="fa-solid fa-chevron-left"></i></button>
            <button class="next" id="nextBtn"><i class="fa-solid fa-chevron-right"></i></button>
         </div>

      <?php
      }
      ?>

      <div class="box-container">

         <?php
         $select_posts = $conn->prepare("SELECT * FROM `posts` WHERE status = ?");
         $select_posts->execute(['active']);
         if ($select_posts->rowCount() > 0) {
            while ($fetch_posts = $select_posts->fetch(PDO::FETCH_ASSOC)) {

               $post_id = $fetch_posts['id'];

               $count_post_comments = $conn->prepare("SELECT * FROM `comments` WHERE post_id = ?");
               $count_post_comments->execute([$post_id]);
               $total_post_comments = $count_post_comments->rowCount();

               $count_post_likes = $conn->prepare("SELECT * FROM `likes` WHERE post_id = ?");
               $count_post_likes->execute([$post_id]);
               $total_post_likes = $count_post_likes->rowCount();

               $confirm_likes = $conn->prepare("SELECT * FROM `likes` WHERE user_id = ? AND post_id = ?");
               $confirm_likes->execute([$user_id, $post_id]);

               $confirm_save = $conn->prepare("SELECT * FROM `favorite_posts` WHERE user_id = ? AND post_id = ?");
               $confirm_save->execute([$user_id, $post_id]);
         ?>
               <form class="box_byPost" method="post">
                  <input type="hidden" name="post_id" value="<?= $post_id; ?>">
                  <input type="hidden" name="admin_id" value="<?= $fetch_posts['admin_id']; ?>">
                  <div class="post-admin">
                     <div class="details_left">
                        <i class="fas fa-user"></i>
                        <div>
                           <a href="author_posts.php?author=<?= $fetch_posts['name']; ?>"><?= $fetch_posts['name']; ?></a>
                           <div><?= $fetch_posts['date']; ?></div>
                        </div>
                     </div>
                     <button type="submit" name="save_post" class="save_mark-btn"><i class="fa-solid fa-bookmark" style="<?php if ($confirm_save->rowCount() > 0) {
                                                                                                                              echo 'color:yellow;';
                                                                                                                           } ?>  "></i></button>
                  </div>

                  <?php
                  if ($fetch_posts['image'] != '') {
                  ?>
                     <img src="../uploaded_img/<?= $fetch_posts['image']; ?>" class="post-image" alt="">
                  <?php
                  }
                  ?>
                  <a href="view_post.php?post_id=<?= $post_id; ?>" class="post-title"><?= $fetch_posts['title']; ?></a>
                  <a href="view_post.php?post_id=<?= $post_id; ?>" class="post-content content-30"><?= $fetch_posts['content']; ?></a>
                  <a href="view_post.php?post_id=<?= $post_id; ?>" class="inline-btn">Đọc thêm</a>
                  <a href="category.php?category=<?= $fetch_posts['category']; ?>" class="post-cat"> <i class="fas fa-tag"></i> <span><?= $fetch_posts['category']; ?></span></a>
                  <div class="icons">
                     <a href="view_post.php?post_id=<?= $post_id; ?>"><i class="fas fa-comment"></i><span>(<?= $total_post_comments; ?>)</span></a>
                     <button type="submit" name="like_post"><i class="fas fa-heart" style="<?php if ($confirm_likes->rowCount() > 0) {
                                                                                                echo 'color:var(--red);';
                                                                                             } ?>  "></i><span>(<?= $total_post_likes; ?>)</span>
                     </button>
                     <button><i class="fa-solid fa-share-from-square"></i></button>
                  </div>

               </form>
         <?php
            }
         } else {
            echo '<p class="empty">Chưa bài viết nào được thêm!</p>';
         }
         ?>
      </div>


      <div class="more-btn" style="text-align: center; margin-top:1rem;">
         <a href="posts.php" class="inline-btn">Xem tất cả</a>
      </div>

   </section>

   <?php include './contact.php' ?>

   <?php include '../components/footer.php'; ?>


   <script src="../js/script_edit.js"></script>
   <script src="../js/script.js"></script>

</body>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-element-bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script>
   AOS.init();
</script>

</html>