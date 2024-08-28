<?php
include("conekt.php");
if(isset($_POST['insert'])){
    $letter_id=$_POST['letter'];
    $user_id=$_POST['id'];
    $title=$_POST['title'];
    $content=$_POST['content'];
    $created_at=date('Y-m-d H:i:s');
    if(empty($letter_id)||empty($user_id)||empty($title)||empty($content)){

        
    }
}