# 照妖镜
无礁网页照妖镜无数据库版

## 伪静态配置：
    location / {
        try_files $uri $uri/ @rewrite;
    }
    
    location @rewrite {
        if ($uri ~ "^/([^/]+)$") {
             return 301 /capture.php?id=$1;
        }
    }
