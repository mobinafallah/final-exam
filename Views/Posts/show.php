<?php
use App\Model\Post;
use App\Model\User;

$id = $_GET['id'] ?? null;
$post = Post::find($id);
$user = $post ? User::find($post->user_id) : null;

// خواندن تعداد بازدیدها از دیتابیس
$views = 0;
try {
    if ($id) {
        $pdo = new PDO("mysql:host=localhost;dbname=students", "root", "");
        $stmt = $pdo->prepare("SELECT views FROM post_views WHERE post_id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $views = $result['views'];
        }
    }
} catch (PDOException $e) {
    // در صورت خطا، فقط مقدار پیش‌فرض صفر نمایش داده می‌شود
}

include_once __DIR__ . '/../header.php';
?>

<main style="flex: 1;">
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-7">
        <div class="card shadow-sm border-0 rounded-4">
          <div class="card-header bg-primary text-white rounded-top-4">
            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i> جزئیات پست</h5>
          </div>
          <div class="card-body">

            <?php if ($post && $user): ?>
              <div class="mb-4 pb-3 border-bottom">
                <h6 class="text-muted">نویسنده</h6>
                <p class="fs-5 mb-0"><i class="fas fa-user-circle me-2 text-secondary"></i><?= htmlspecialchars($user->name) ?></p>
              </div>

              <div class="mb-4 pb-3 border-bottom">
                <h6 class="text-muted">عنوان</h6>
                <p class="fs-4 text-dark"><?= htmlspecialchars($post->title) ?></p>
              </div>

              <div class="mb-4 pb-3 border-bottom">
                <h6 class="text-muted">تعداد بازدید</h6>
                <p class="fs-5"><i class="fas fa-eye me-2 text-secondary"></i><?= number_format($views) ?> بازدید</p>
              </div>

              <div class="mb-4">
                <h6 class="text-muted">محتوا</h6>
                <div class="border rounded-3 p-3 bg-light">
                  <p class="mb-0 fs-5"><?= nl2br(htmlspecialchars($post->body)) ?></p>
                </div>
              </div>
            <?php else: ?>
              <p class="text-danger">پست مورد نظر یافت نشد.</p>
            <?php endif; ?>

          </div>
        </div>

        <div class="text-center mt-4">
          <a href="/website/post" class="btn btn-outline-secondary px-4">
            <i class="fas fa-arrow-left me-1"></i> بازگشت به لیست پست‌ها
          </a>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include_once __DIR__ . '/../footer.php'; ?>

<script>
// اضافه کردن hover effect برای دکمه
document.querySelector('.btn').addEventListener('mouseover', function() {
    this.style.backgroundColor = '#F4845A !important';
    this.style.color = '#133853 !important';
});

document.querySelector('.btn').addEventListener('mouseout', function() {
    this.style.backgroundColor = 'transparent !important';
    this.style.color = '#F4845A !important';
});
</script>
