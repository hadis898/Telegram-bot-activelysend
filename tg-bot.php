<!DOCTYPE html>
<html>
<head>
    <title>Telegram机器人主动发送信息</title>
     <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
</head>
<body>
    <div class="container">
        <h1>Telegram机器人</h1>
        <form method="post">
            <div class="form-group">
                <label for="token">机器人Token:</label>
                <input type="text" class="form-control" id="token" name="token">
            </div>    
            <div class="form-group">
                <label for="chat_id">发送对象(用户ID):</label>
                <input type="text" class="form-control" id="chat_id" name="chat_id">
            </div>
            <div class="form-group">
                <label>发送信息内容(支持Markdown排版):</label>
                <textarea name="text" id="editor" rows="10" class="form-control"></textarea>
            </div>
            <div class="form-group">
                <label for="interval">定时发送间隔(秒):</label>
                <input type="number" class="form-control" id="interval" name="interval" value="5"> 
            </div>
            <button type="submit" class="btn btn-primary">立即发送</button>
            <button type="button" class="btn btn-success" onclick="startInterval()">开始定时</button>
            <button type="button" class="btn btn-danger" onclick="stopInterval()">停止定时</button>    
        </form>
      <br>
        <h3>发送日志:</h3>
        <div id="log"></div>
    </div>
</body>
</html>

<?php 
$interval;
$logs = [];

function sendMessage() {
    global $token, $chat_id, $text;
    $url = "https://api.telegram.org/bot$token/sendMessage";
    $params = ['chat_id' => $chat_id, 'text' => $text];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    $response = curl_exec($ch);
    curl_close($ch);
    logMessage('sendMessage', $response); 
}

function startInterval() {
    global $interval, $logs;
    if ($interval > 0) {
        $interval = setInterval('sendMessage()', $interval); 
        logMessage('startInterval', "定时发送已开启,间隔{$interval}秒!");
    }   
}

function stopInterval() {
    global $interval, $logs; 
    if ($interval > 0) {
        clearInterval($interval);
        $interval = 0;
        logMessage('stopInterval', '定时发送已关闭!');
    }   
}

function logMessage($function, $message) {
    global $logs;
    $now = date('Y-m-d H:i:s');
    $logs[] = "[$now] $function() - $message"; 
}

if ($_POST) {
    $token = $_POST['token'];
    $chat_id = $_POST['chat_id'];  
    $text = $_POST['text']; 
    $interval = $_POST['interval'];
    sendMessage();  
}

?> 
<script>
let logs = <?php echo json_encode($logs); ?>;
logs.forEach(log => {
    document.querySelector('#log').innerHTML += log + '<br>'; 
});
</script>