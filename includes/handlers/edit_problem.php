<?php  
include("../../config/config.php");
include("../current_user.php");

if($user['perms']<1) {
    echo "No perms";
    exit;
}
if($_REQUEST['action']=="createNew"){
    mysqli_query($con, "INSERT INTO problems VALUES (NULL,'new problem','','','',0,0,'','','','','',0,0,0,0,0,0)");
    echo mysqli_insert_id($con);
}
else if($_REQUEST['action']=="changeField"){
    if(!in_array($_REQUEST['field'],array("name","thumbnail","banner","problem_link","difficulty","quality","judge_links","statement_links","editorial_links","solution_links","data_links","has_editorial","has_solution","has_data","difficulty_lock","quality_lock","deleted")))
        return;
    $value=$_REQUEST['value'];
    $value = strip_tags($value, "<img><video><h1><h2><h3><h4><h5><h6><b><a>"); //removes html tags 
    //$body = mysqli_real_escape_string($this->con, $body);
    $value = str_replace('\r\n', "\n", $value);
    $value = nl2br($value);
    echo $value;
    
    $sql=mysqli_prepare($con, "UPDATE problems SET {$_REQUEST['field']}=? WHERE id=?");
    mysqli_stmt_bind_param($sql,"si",$value,$_REQUEST['id']);
    mysqli_stmt_execute($sql);
    $access_query=mysqli_stmt_get_result($sql);
    
    $notempty=(int)!empty($value);
    if($_REQUEST['field']=="editorial_links"){
        mysqli_query($con, "UPDATE problems set has_editorial={$notempty} WHERE id={$_REQUEST['id']}");
    }
    if($_REQUEST['field']=="solution_links"){
        mysqli_query($con, "UPDATE problems set has_solution={$notempty} WHERE id={$_REQUEST['id']}");
    }
    if($_REQUEST['field']=="data_links"){
        mysqli_query($con, "UPDATE problems set has_data={$notempty} WHERE id={$_REQUEST['id']}");
    }
    //mysqli_query($con, "UPDATE problems SET {$_REQUEST['field']}='{$value}' WHERE id={$_REQUEST['id']}");
}
?>