<?php
class Tag {
	private $con;

	public function __construct($con){
		$this->con = $con;
	}
    
    public function getTags($problem,$owner,$type) {
        $problem=(int)$problem;
        $owner=(int)$owner;
		$ret = "";
        if($type==1){
            $tags=mysqli_query($this->con,"SELECT tags.id,name,background 
            FROM tagowners
            INNER JOIN tags ON tagowners.tag=tags.id
            WHERE tagowners.owner=$owner AND tags.id NOT IN
            (SELECT tagproblems.tag FROM tagproblems WHERE tagproblems.problem=$problem)") ;
            $ret .= "<option>Add tag</option>";
        }
        else if($type==4){
            $tags=mysqli_query($this->con,"SELECT tags.id,name,background,thumbnail 
            FROM tagowners a
            INNER JOIN tagowners b ON a.tag=b.tag
            INNER JOIN tags ON a.tag=tags.id
            WHERE a.owner=$owner AND b.owner=0");
        }
        else if($problem==-1&&$owner==-1){
            $tags=mysqli_query($this->con,"SELECT tags.id,name,background 
            FROM tags");
        }
        else if($problem==-1){
            $tags=mysqli_query($this->con,"SELECT tags.id,name,background
            FROM tagowners
            INNER JOIN tags ON tagowners.tag=tags.id
            WHERE tagowners.owner=$owner");
        }
        else if($owner==-1){
            $tags=mysqli_query($this->con,"SELECT tags.id,name,background 
            FROM tagproblems
            INNER JOIN tags ON tagproblems.tag=tags.id
            WHERE tagproblems.problem=$problem");
        }
        else{
            $tags=mysqli_query($this->con,"SELECT tags.id,name,background 
            FROM tagowners
            INNER JOIN tagproblems ON tagowners.tag=tagproblems.tag
            INNER JOIN tags ON tagowners.tag=tags.id
            WHERE tagowners.owner=$owner AND tagproblems.problem=$problem");
        }

        while($row = mysqli_fetch_array($tags)){
            $color=$row['background'];
            $optionVal=$row['id'];
            if($owner>0&&$row['name']=="Solved-$owner")
                $optionVal=-$row['id'];
            if($type==0)
                $ret .= "
                    <div class=\"tag_chip\" style='border-color: $color;'>
                        {$row['name']}
                        <div class=\"chip_x\" onclick=\"removeTag({$optionVal})\" style='background-color: $color;'>x</div>
                    </div>
                    ";
            else if($type==1)
                $ret .= "<option value=\"{$optionVal}\">{$row['name']}</option>";
            else if($type==2)
                $ret .= "
                    <span class='badge bg-primary' style='border-color: $color;'>
                        {$row['name']}
                    </span>
                    ";
            else if($type==3)
                $ret .= "<tr><td><a href=\"tag.php?id={$row['id']}\">{$row['name']}</a></td></tr>";
            else if($type==4){
                $thumbnail=$row['thumbnail'];
                if($thumbnail==""){
                    $thumbnails = scandir('assets/images/thumbnails/');
                    $rand = $row['id']%(count($thumbnails)-2)+2;
                    $thumbnail = "assets/images/thumbnails/".$thumbnails[$rand];
                }
                $ret.="
                <div class='col-3'><a href='tag.php?id={$row['id']}'><div class='text-center h-100'>
                    <img class='card-img-top rounded-0' src='{$thumbnail}'>
                    <p>{$row['name']}</p>
                </div></a></div>";
            }
        }
		echo $ret;
    }

    public function addProblemTag($problem,$tag){
        $problem=(int)$problem;
        $tag=(int)$tag;
        if(mysqli_num_rows(mysqli_query($this->con,"SELECT * FROM tagproblems WHERE tag=$tag AND problem=$problem"))==0){
            $idx=mysqli_num_rows(mysqli_query($this->con,"SELECT * FROM tagproblems WHERE tag=$tag"))+1;
            mysqli_query($this->con, "INSERT INTO tagproblems VALUES (NULL, $tag, $problem, $idx)");
        }
    }
    public function removeProblemTag($problem,$tag){
        $problem=(int)$problem;
        $tag=(int)$tag;
        mysqli_query($this->con, "DELETE FROM tagproblems WHERE tag=$tag AND problem=$problem");
        $idx=1;
        $data_query=mysqli_query($this->con,"SELECT * FROM tagproblems WHERE tag=$tag");
        while($row = mysqli_fetch_array($data_query)){
			mysqli_query($this->con, "UPDATE tagproblems SET idx=$idx WHERE id={$row['id']}");
            $idx++;
        }
    }
    public function createTag($name,$user,$background=''){
        $sql=mysqli_prepare($this->con,"INSERT INTO tags VALUES (NULL, ?,?,'','')");
		mysqli_stmt_bind_param($sql,"ss",$name,$background);
		mysqli_stmt_execute($sql);
		$query=mysqli_stmt_get_result($sql);
        echo $query;
        //mysqli_query($this->con, "INSERT INTO tags VALUES (NULL, '$name')");
        $tag = mysqli_insert_id($this->con);
        mysqli_query($this->con, "INSERT INTO tagowners VALUES (NULL, $tag,$user)");
        echo $name,$user,$tag;
        return $tag;
    }
    public function deleteTag($id){
        $id=(int)$id;
        mysqli_query($this->con, "DELETE FROM tags WHERE id=$id");
        mysqli_query($this->con, "DELETE FROM tagproblems WHERE tag=$id");
        mysqli_query($this->con, "DELETE FROM tagowners WHERE tag=$id");
        echo $id;
    }
    public function calcPoints($user){
        $user=(int)$user;
        $tags=mysqli_query($this->con,"SELECT tagproblems.problem,problems.difficulty 
        FROM tagowners
        INNER JOIN tagproblems ON tagowners.tag=tagproblems.tag
        INNER JOIN tags ON tagowners.tag=tags.id
        INNER JOIN problems ON tagproblems.problem=problems.id
        WHERE tagowners.owner=$user AND tags.name='Solved-$user'");
        $count=mysqli_num_rows($tags);
        $solved=array();
        $points=$points2=0;
        foreach($tags as $row){
            array_push($solved,$row['difficulty']);
        }
        sort($solved);
        foreach($solved as $pp){
            $points=($points*0.90)+$pp;
            $points2+=pow(2,$pp/300);
        }
        $points/=10;
        $points2*=1+$count/10;
        if($user==1)
            $points=-1;
        mysqli_query($this->con,"UPDATE users SET power=$points,experience=$points2,solved=$count WHERE id=$user");
        $points=round($points,4);
        $points2=round($points2,4);
        return array($count,$points,$points2);
    }
    public function shareTag($tag,$user){
        $tag=(int)$tag;
        $user=(int)$user;
        if(mysqli_num_rows(mysqli_query($this->con,"SELECT * FROM tagowners WHERE tag=$tag AND owner=$user"))==0)
            mysqli_query($this->con, "INSERT INTO tagowners VALUES (NULL, $tag,$user)");
    }
    public function unshareTag($tag,$user){
        $tag=(int)$tag;
        $user=(int)$user;
        mysqli_query($this->con, "DELETE FROM tagowners WHERE tag=$tag AND owner=$user");
    }
    public function copyTag($tag,$user){
        $tag=(int)$tag;
        $user=(int)$user;
        $name=mysqli_fetch_array(mysqli_query($this->con,"SELECT * FROM tags WHERE id=$tag"))['name'];
        $newTag=$this->createTag($name,$user);
        $problems=mysqli_query($this->con,"SELECT problem FROM tagproblems WHERE tag=$tag");
        while($row = mysqli_fetch_array($problems)){
            $this->addProblemTag($row['problem'],$newTag);
        }
    }
    public function getOwners($tag){
        $tag=(int)$tag;
        $ret="Shared with: ";
        $owners=mysqli_query($this->con,"SELECT users.id,users.username
        FROM tagowners
        INNER JOIN users ON users.id=tagowners.owner
        WHERE tag=$tag");
        while($row = mysqli_fetch_array($owners)){
            $ret.="<a href=\"profile.php?user={$row['id']}\">{$row['username']}</a>, ";
        }
        echo $ret;
    }
    public function editTag($tag, $field, $value){
        $sql=mysqli_prepare($this->con,"UPDATE tags SET {$field}=? WHERE id=?");
		mysqli_stmt_bind_param($sql,"si",$value,$tag);
		mysqli_stmt_execute($sql);-
		$query=mysqli_stmt_get_result($sql);
		//mysqli_query($this->con, "UPDATE tags SET name='$name' WHERE id=$tag");
    }
    public function reorderTag($tag, $order){
        $tag=(int)$tag;
        foreach($order as $idx => $entry){
            $idx=(int)$idx;
            $idx++;
            mysqli_query($this->con, "UPDATE tagproblems SET idx={$idx} WHERE id=$entry AND tag=$tag");
        }
    }
    public function isPublic($tag){
        $tag=(int)$tag;
        $owners=mysqli_query($this->con,"SELECT * FROM tagowners WHERE tag=$tag AND owner=0");
        if(mysqli_num_rows($owners))
            return "Public";
        else
            return "Not Public";
    }
    public function mergeTag($tag, $newTag){ //this function doesn't update everything, for admin use only
        $tag=(int)$tag;
        $newTag=(int)$newTag;
        $sql=mysqli_query($this->con,"UPDATE tagproblems SET tag=$newTag WHERE tag=$tag");
		//mysqli_query($this->con, "UPDATE tags SET name='$name' WHERE id=$tag");
    }
}

?>