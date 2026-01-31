<?php
// 配置信息
$images_file = 'images.json';
$links_file = 'links.json';

// 确保JSON文件存在
if (!file_exists($images_file)) {
    file_put_contents($images_file, json_encode(array()));
}

if (!file_exists($links_file)) {
    file_put_contents($links_file, json_encode(array()));
}

// 获取用户的查询ID
$user_query_id = $_COOKIE['query_id'] ?? '';
$user_images = array();
$user_redirect_url = '';

if ($user_query_id) {
    // 读取图片数据
    $images = json_decode(file_get_contents($images_file), true);
    
    // 如果该查询ID有图片记录
    if (isset($images[$user_query_id])) {
        $user_images = $images[$user_query_id];
    }
    
    // 读取跳转URL
    $links = json_decode(file_get_contents($links_file), true);
    if (isset($links[$user_query_id])) {
        $user_redirect_url = $links[$user_query_id];
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网恋照妖镜 - 我的生成记录</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .info-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 4px;
        }
        .info-section h3 {
            margin-top: 0;
            color: #555;
        }
        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .image-item {
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .image-item img {
            width: 100%;
            height: auto;
            display: block;
        }
        .image-info {
            padding: 10px;
            background-color: #f8f9fa;
            font-size: 14px;
            color: #666;
        }
        .no-images {
            text-align: center;
            padding: 50px;
            color: #999;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>网恋照妖镜</h1>
        <h2>我的生成记录</h2>
        
        <?php if ($user_query_id): ?>
            <div class="info-section">
                <h3>查询ID</h3>
                <p><?php echo $user_query_id; ?></p>
                
                <?php if ($user_redirect_url): ?>
                    <h3>跳转网址</h3>
                    <p><a href="<?php echo $user_redirect_url; ?>" target="_blank"><?php echo $user_redirect_url; ?></a></p>
                <?php endif; ?>
            </div>
            
            <h3>拍摄的照片</h3>
            
            <?php if (count($user_images) > 0): ?>
                <div class="images-grid">
                    <?php foreach ($user_images as $index => $image_path): ?>
                        <div class="image-item">
                            <img src="<?php echo $image_path; ?>" alt="拍摄照片 <?php echo $index + 1; ?>">
                            <div class="image-info">
                                照片 <?php echo $index + 1; ?><br>
                                <small><?php echo basename($image_path); ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-images">
                    <p>暂无拍摄的照片记录</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="no-images">
                <p>未找到您的查询ID，请先在首页生成拍摄链接</p>
            </div>
        <?php endif; ?>
        
        <a href="index.php" class="back-link">返回首页</a>
    </div>
</body>
</html>