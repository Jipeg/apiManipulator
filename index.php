<?php
ini_set("allow_url_fopen", 1);

class ApiManipulator
{
    private $addr = __DIR__ . '/'; //'https://jsonplaceholder.typicode.com/';

    private function findUserId($userName) {
        $json = file_get_contents($this->addr . 'users');
        $obj = json_decode($json);

        foreach($obj as $key=>$value){
            if ($value->name == $userName) {
                return $value->id;
            }
        }
        return -1;
    }
    private function getUserStuff($userName, $whatStuff) {
        // look for user id
        $id = $this->findUserId($userName);
        if ($id == -1) {
            echo "$userName - No such user!";
            return -1;
        }
        $json = file_get_contents($this->addr . $whatStuff);
        $obj = json_decode($json);
        $flag = 0;
        foreach($obj as $key=>$value){
            if ($value->userId == $id) {
                if ($flag < 1) {
                    $tmp = ucfirst($whatStuff);
                    echo "<h1>$tmp from $userName</h1>";
                }
                $flag++;
                
                echo "<h2>$flag) $value->title</h2>";
                if (isset($value->body)) {
                    echo "<p>$value->body</p><br>"; 
                }
            }
        }
        if (!$flag) {
            echo "No $whatStuff from this user";
        }
        return 0;
    }
    function getUserList() {
        //echo ($this->addr);
        $json = file_get_contents($this->addr . 'users');
        $obj = json_decode($json);

        foreach($obj as $key=>$value){
            echo $value->name . '<br>';
        }
    }
    function getUserPosts($userName) {
        $this->getUserStuff($userName, 'posts');
    }
    function getUserToDos($userName) {
        $this->getUserStuff($userName, 'todos');
    }
    function addPost($userName, $title, $body) {
        $json = file_get_contents($this->addr . 'posts');
        $obj = json_decode($json);

        $newId = end($obj)->id + 1;
        $userId = $this->findUserId($userName);
        //echo $newId . $userId;

        array_push($obj, (object)[
            'userId' => $userId,
            'id' => $newId,
            'title' => $title,
            'body' => $body
        ]);
        $newJsonString = json_encode($obj);
        return file_put_contents($this->addr . 'posts', $newJsonString);
    }
    function changePost($postId, $userName, $title, $body) {
        $json = file_get_contents($this->addr . 'posts');
        $obj = json_decode($json);
        $userId = $this->findUserId($userName);
        if ($userId == -1) {
            echo "$userName - No such user!";
            return -1;
        }
        foreach($obj as $key=>&$value){
            if ($value->id == $postId) {
                $value = (object)[
                    'userId' => $userId,
                    'id' => $postId,
                    'title' => $title,
                    'body' => $body
                ];
                break;
            }
        }
        $newJsonString = json_encode($obj);
        file_put_contents($this->addr . 'posts', $newJsonString);
    }

    function deletePost($postId) {
        $json = file_get_contents($this->addr . 'posts');
        $obj = json_decode($json);
        foreach($obj as $key=>$value){
            if ($value->id == $postId) {
                unset($obj[$key]);
                break;
            }
        }
        $newJsonString = json_encode($obj);
        return file_put_contents($this->addr . 'posts', $newJsonString);
    }
}
$s = new ApiManipulator;
$s->getUserList();
echo '<br><br>';

if ($s->addPost('Clementina DuBuque', 'qwe', 'qwe qwe qwe')) {
    echo 'post added';
}
$s->changePost(101, 'Clementina DuBuque', 'QKRQ', '123456');
$s->deletePost(50);
$s->getUserPosts('Clementina DuBuque');
$s->getUserToDos('Clementina DuBuque');
?>