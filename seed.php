<?php
// فایل seed.php
// دقت کن قبل اجرا:
// - باید فایل رو توی مسیر پروژه (مثلاً C:\xampp\htdocs\website\) بذاری
// - دستور اجرا در PowerShell: php seed.php

$host = 'localhost';
$dbname = 'students';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $users = [
        ['amirhosseinasadi', 'amirhosseinasadi162@gmail.com', '123654'],
        ['AbbasEsmaili', 'abbas@gmail.com', '123456'],
        ['ali babakordi', 'alibabakordi82@gmail.com', '08342056'],
        ['Fatemeh Binesh', 'fatemehbinesh17@gmail.com', '567834'],
        ['Ali Daneshmand', 'daneshmanda8@gmail.com', '123ali'],
        ['Mobina Amini', 'mobina.amini.web@gmail.com', '40405050w'],
        ['Mobina Fallah', 'flhmobinaa@gmail.com', '246810'],
        ['Mobina mahdavi', 'mobina.mahdavi.web@gmail.com', '45459898w'],
        ['Matineh Mousavi', 'matineh.mousavi8200@gmail.com', '684723'],
        ['Mostafa Montazery', 'Mostafamtz@gmail.com', '123654'],
        ['Danial Isaabadi', 'danial.isaabadi81@gmail.com', '5038@3910'],
        ['Amirhosein Tasharrofi', 'amirhosein.tasharrofi@gmail.com', '626864'],
        ['Masoud Taghipour', 'mtaghipourj@gmail.com', '682749'],
        ['Fatemeh khajeh', 'fatemeh.khajeh1404@gmail.com', '54578388F'],
        ['Sara Sarfi', 'sarasarfi79@gmail.com', '240530'],
        ['Sadjad Rezagholizadeh', 'sajjad.rz@gmail.com', 'Mirame.@loco33'],
        ['Ghazal Rezaei', 'rezaeighazal432@gmail.com', '546897'],
        ['Shakila Shaker', 'shakilashaker80@gmail.com', '9708404'],
        ['Shahed Shirazi', 'shirazishaheds926@gmail.com', '456ph327'],
        ['Aida Sadeghi', 'asv94974@gmail.com', '2412@8'],
        ['taha sadeghi', 'taha18319113@gmail.com', '18319113'],
        ['Parmida Mehrnikoo', 'Mehrnikoo.net@gmail.com', '051051'],
        ['Mahdieh panjei', 'mahdiehpanjei@gmail.com', '123456'],
    ];

    foreach ($users as $user) {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
        $hashedPassword = password_hash($user[2], PASSWORD_DEFAULT);
        $stmt->execute([$user[0], $user[1], $hashedPassword]);

        $userId = $pdo->lastInsertId();

        // ساخت پست‌های تصادفی
        $postCount = rand(5, 7);
        $postIds = [];
        for ($i = 0; $i < $postCount; $i++) {
            $title = "عنوان پست شماره " . ($i+1) . " از {$user[0]}";
            $body = "محتوای پست شماره " . ($i+1) . " از {$user[0]}";
            $stmtPost = $pdo->prepare("INSERT INTO posts (user_id, title, body, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
            $stmtPost->execute([$userId, $title, $body]);

            $postId = $pdo->lastInsertId();
            $postIds[] = $postId;

            // اضافه کردن view تصادفی
            $views = rand(100, 1000);
            $stmtViews = $pdo->prepare("INSERT INTO post_views (post_id, views) VALUES (?, ?)");
            $stmtViews->execute([$postId, $views]);
        }

        // مرتبط کردن هر پست با ۳ پست دیگر
        foreach ($postIds as $postId) {
            $relatedPosts = array_diff($postIds, [$postId]);
            shuffle($relatedPosts);
            $relatedPosts = array_slice($relatedPosts, 0, 3);

            foreach ($relatedPosts as $relatedId) {
                $stmtRelated = $pdo->prepare("INSERT INTO related_post (post_1_id, post_2_id) VALUES (?, ?)");
                $stmtRelated->execute([$postId, $relatedId]);
            }
        }
    }

    echo "✅ داده‌ها به‌خوبی درج شدند!\n";

} catch (PDOException $e) {
    die("❌ خطا: " . $e->getMessage());
}