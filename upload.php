<?php
// 配置信息
$images_file = 'images.json';
$images_dir = 'images/';

// 确保images.json存在
if (!file_exists($images_file)) {
    file_put_contents($images_file, json_encode(array()));
}

// 获取POST数据
$image_data = $_POST['image'] ?? '';
$query_id = $_POST['query_id'] ?? '';

if ($image_data && $query_id) {
    // 处理base64图片数据
    $image_parts = explode(';base64,', $image_data);
    $image_type_aux = explode('image/', $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    
    // 生成唯一文件名
    $file_name = $query_id . '_' . time() . '.' . $image_type;
    $file_path = $images_dir . $file_name;
    
    // 保存图片
    if (file_put_contents($file_path, $image_base64)) {
        // 读取现有图片数据
        $images = json_decode(file_get_contents($images_file), true);
        
        // 如果该查询ID还没有图片记录，创建一个数组
        if (!isset($images[$query_id])) {
            $images[$query_id] = array();
        }
        
        // 添加新图片路径
        $images[$query_id][] = $file_path;
        
        // 保存更新后的图片数据
        file_put_contents($images_file, json_encode($images, JSON_PRETTY_PRINT));
        
        echo '图片上传成功！';
    } else {
        echo '图片保存失败！';
    }
} else {
    echo '缺少必要的参数！';
}
?>