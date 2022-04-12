<?php  
$postId=-1;
if(isset($_REQUEST['postId']))
    $postId=$_REQUEST['postId'];
if(isset($_POST['post'])){
    $post = new Post($con);
    if($_REQUEST['problem']<1&&$user['perms']<Constants::ADMIN_PERMS&&$_REQUEST['parent']==0){
        exit;
    }
    $postId=$post->submitPost($_REQUEST['post_text'], $_REQUEST['problem'],$curUID,$_REQUEST['parent']);
    if(!$postId)
        $postId=0;
}
?>