<?php
namespace App\Model;

class RelatedPosts {
    private $pdo;

    public function __construct() {
        try {
            $this->pdo = new \PDO("mysql:host=localhost;dbname=students", "root", "");
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            // Create related_posts table if not exists
            $this->pdo->exec("CREATE TABLE IF NOT EXISTS related_posts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                post_id INT NOT NULL,
                related_post_id INT NOT NULL,
                UNIQUE KEY unique_relation (post_id, related_post_id),
                FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
                FOREIGN KEY (related_post_id) REFERENCES posts(id) ON DELETE CASCADE
            )");
        } catch(\PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getMatrix() {
        try {
            // Get all posts ordered by ID
            $posts = $this->pdo->query("SELECT id FROM posts ORDER BY id")->fetchAll(\PDO::FETCH_ASSOC);
            $n = count($posts);

            if ($n == 0) {
                return [];
            }

            // Create a map of post IDs to array indices
            $postIdToIndex = [];
            foreach ($posts as $index => $post) {
                $postIdToIndex[$post['id']] = $index;
            }

            // Initialize matrix A with zeros
            $A = array_fill(0, $n, array_fill(0, $n, 0.0));

            // Get all related posts and their views
            $query = "
                SELECT 
                    rp.post_id,
                    rp.related_post_id,
                    COALESCE(pv.views, 1) as views
                FROM posts p
                LEFT JOIN related_posts rp ON p.id = rp.post_id
                LEFT JOIN post_views pv ON rp.related_post_id = pv.post_id
                WHERE rp.post_id IS NOT NULL
                ORDER BY rp.post_id, rp.related_post_id
            ";
            
            $relations = $this->pdo->query($query)->fetchAll(\PDO::FETCH_ASSOC);
            
            // اگر هیچ ارتباطی نیست، از تعداد بازدیدها استفاده کن
            if (empty($relations)) {
                $query = "
                    SELECT 
                        p.id as post_id,
                        COALESCE(pv.views, 1) as views
                    FROM posts p
                    LEFT JOIN post_views pv ON p.id = pv.post_id
                    ORDER BY p.id
                ";
                
                $views = $this->pdo->query($query)->fetchAll(\PDO::FETCH_ASSOC);
                $total_views = max(array_sum(array_column($views, 'views')), count($views)); // حداقل 1 برای هر پست
                
                foreach ($views as $view) {
                    if (isset($postIdToIndex[$view['post_id']])) {
                        $i = $postIdToIndex[$view['post_id']];
                        $view_count = max((int)$view['views'], 1);
                        for ($j = 0; $j < $n; $j++) {
                            if ($i != $j) {  // پست به خودش لینک نداشته باشد
                                $A[$i][$j] = $view_count / ($total_views * ($n - 1));
                            }
                        }
                    }
                }
                
                return $A;
            }

            // Calculate denominators (sum of views for each post's related posts)
            $denominators = [];
            foreach ($relations as $relation) {
                if (isset($postIdToIndex[$relation['post_id']])) {
                    $post_id = $relation['post_id'];
                    if (!isset($denominators[$post_id])) {
                        $denominators[$post_id] = 0;
                    }
                    $denominators[$post_id] += max((int)$relation['views'], 1);
                }
            }

            // Fill matrix A
            foreach ($relations as $relation) {
                if (isset($postIdToIndex[$relation['post_id']]) && isset($postIdToIndex[$relation['related_post_id']])) {
                    $i = $postIdToIndex[$relation['post_id']];
                    $j = $postIdToIndex[$relation['related_post_id']];
                    if ($denominators[$relation['post_id']] > 0) {
                        $A[$i][$j] = max((int)$relation['views'], 1) / $denominators[$relation['post_id']];
                    }
                }
            }

            // اطمینان از اینکه هر سطر جمعش 1 است
            for ($i = 0; $i < $n; $i++) {
                $row_sum = array_sum($A[$i]);
                if ($row_sum == 0) {
                    // اگر سطری هیچ ارتباطی ندارد، احتمال یکنواخت بده
                    for ($j = 0; $j < $n; $j++) {
                        if ($i != $j) {  // به خودش لینک نداشته باشد
                            $A[$i][$j] = 1.0 / ($n - 1);
                        }
                    }
                } elseif (abs($row_sum - 1.0) > 1e-10) {
                    // نرمال‌سازی سطر
                    for ($j = 0; $j < $n; $j++) {
                        $A[$i][$j] /= $row_sum;
                    }
                }
            }

            return $A;
        } catch(\PDOException $e) {
            error_log("Error calculating matrix: " . $e->getMessage());
            return [];
        } catch(\Exception $e) {
            error_log("Unexpected error in getMatrix: " . $e->getMessage());
            return [];
        }
    }

    public function powerIteration($matrix, $iterations = 100, $tolerance = 1e-10) {
        $n = count($matrix);
        
        if ($n == 0) {
            return [];
        }
        
        // Initialize eigenvector with equal probabilities
        $vector = array_fill(0, $n, 1.0 / $n);
        
        for ($iter = 0; $iter < $iterations; $iter++) {
            // Store previous vector for convergence check
            $previous = $vector;
            
            // Matrix multiplication
            $new = array_fill(0, $n, 0.0);
            for ($i = 0; $i < $n; $i++) {
                for ($j = 0; $j < $n; $j++) {
                    $new[$i] += $matrix[$j][$i] * $vector[$j];
                }
            }
            
            // Normalize
            $norm = sqrt(array_sum(array_map(function($x) { return $x * $x; }, $new)));
            if ($norm > 0) {
                $vector = array_map(function($x) use ($norm) { return $x / $norm; }, $new);
            } else {
                // اگر نرم صفر است، بردار یکنواخت برگردان
                $vector = array_fill(0, $n, 1.0 / $n);
            }
            
            // Check convergence
            $diff = 0;
            for ($i = 0; $i < $n; $i++) {
                $diff += abs($vector[$i] - $previous[$i]);
            }
            if ($diff < $tolerance) {
                break;
            }
        }
        
        return $vector;
    }

    public function getRankedPosts() {
        try {
            // Get all posts first with their IDs
            $posts = $this->pdo->query("SELECT * FROM posts ORDER BY id")->fetchAll(\PDO::FETCH_ASSOC);
            
            if (empty($posts)) {
                return [];
            }

            // Create a map of post IDs to array indices
            $postIdToIndex = [];
            foreach ($posts as $index => $post) {
                $postIdToIndex[$post['id']] = $index;
            }

            $matrix = $this->getMatrix();
            if (empty($matrix)) {
                return [];
            }

            // Ensure matrix size matches number of posts
            if (count($matrix) !== count($posts)) {
                // Recreate matrix with correct size
                $matrix = array_fill(0, count($posts), array_fill(0, count($posts), 1.0 / count($posts)));
            }

            $eigenVector = $this->powerIteration($matrix);
            
            // Combine posts with their importance scores and views
            $rankedPosts = [];
            foreach ($posts as $index => $post) {
                // Get views for this post
                $stmt = $this->pdo->prepare("SELECT COALESCE(views, 0) as views FROM post_views WHERE post_id = ?");
                $stmt->execute([$post['id']]);
                $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                $views = isset($result['views']) ? (int)$result['views'] : 0;
                
                // Get importance score for this post
                $importance = 0.0;
                if (isset($eigenVector[$index])) {
                    $importance = (float)$eigenVector[$index];
                    // Adjust importance based on views
                    if ($views > 0) {
                        $importance *= (1 + log(1 + $views));
                    }
                }
                
                $rankedPosts[] = [
                    'post' => $post,
                    'importance' => $importance,
                    'views' => $views
                ];
            }
            
            // Sort by importance score
            usort($rankedPosts, function($a, $b) {
                if ($a['importance'] == $b['importance']) {
                    return $b['views'] - $a['views']; // اگر امتیاز یکسان بود، بر اساس تعداد بازدید مرتب کن
                }
                return $b['importance'] - $a['importance'];
            });
            
            return $rankedPosts;
        } catch(\PDOException $e) {
            error_log("Error in getRankedPosts: " . $e->getMessage());
            return [];
        } catch(\Exception $e) {
            error_log("Unexpected error in getRankedPosts: " . $e->getMessage());
            return [];
        }
    }
} 