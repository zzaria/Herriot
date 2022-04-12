<?php

class Tag {
	private $con;

	public function __construct($con){
		$this->con = $con;
	}
    
    public function getTags($problem,$owner,$type,$spoiler=0) {
        $problem=(int)$problem;
        $owner=(int)$owner;
		$ret = "";
        if($type==1){
            $tags=mysqli_query($this->con,"SELECT tags.id,name,background 
            FROM tagowners
            INNER JOIN tags ON tagowners.tag=tags.id
            WHERE tagowners.owner=$owner AND tagowners.type=0 AND tags.id NOT IN
            (SELECT tagproblems.tag FROM tagproblems WHERE tagproblems.problem=$problem)") ;
            $ret .= "<option>Add tag</option>";
        }
        else if($type==4){
            $tags=mysqli_query($this->con,"SELECT tags.id,name,background,thumbnail 
            FROM tagowners a
            INNER JOIN tagowners b ON a.tag=b.tag
            INNER JOIN tags ON a.tag=tags.id
            WHERE a.owner=$owner AND a.type=0 AND b.owner=0");
        }
        else if($type==3){
            $tags=mysqli_query($this->con,"SELECT tags.id,name,background
            FROM tagowners
            INNER JOIN tags ON tagowners.tag=tags.id
            WHERE tagowners.owner=$owner AND tagowners.type=0");
        }
        else if($type==5){
            $tags=mysqli_query($this->con,"SELECT tags.id,name,background
            FROM tagowners
            INNER JOIN tags ON tagowners.tag=tags.id
            WHERE tagowners.owner=$owner AND tagowners.type=1");
        }
        else{
            if($spoiler==1){
                $tags=mysqli_query($this->con,"SELECT tags.id,name,background 
                FROM tagowners
                INNER JOIN tagproblems ON tagowners.tag=tagproblems.tag
                INNER JOIN tags ON tagowners.tag=tags.id
                WHERE tagowners.owner=$owner AND tagowners.type=0 AND tagproblems.problem=$problem AND spoiler=0");
            }
            else{
                $tags=mysqli_query($this->con,"SELECT tags.id,name,background 
                FROM tagowners
                INNER JOIN tagproblems ON tagowners.tag=tagproblems.tag
                INNER JOIN tags ON tagowners.tag=tags.id
                WHERE tagowners.owner=$owner AND tagproblems.problem=$problem");
            }
        }

        $colors=Array("primary","secondary","success","danger","warning text-dark","info","light text-dark","dark");
        if($type==4)
            $thumbnails = scandir('assets/images/thumbnails/');
        while($row = mysqli_fetch_array($tags)){
            if($type==0||$type==2){
                $color=$row['background'];
                $color2="";
                if($color==""){
                    $color2="bg-".$colors[$row['id']%count($colors)];
                }
                else{
                    $a=Color::fromString($color);
                    $L=$a->toHSL();
                    $L=$L['L'];
                    $a=$a->getAlpha();
                    if((1-$L)*$a>0.7)
                        $color2="text-light";
                    else if($L*$a>0.7)
                        $color2="text-dark";
                }
            }
            $optionVal=$row['id'];
            if($owner>0&&$row['name']=="Solved-$owner")
                $optionVal=-$row['id'];
            if($type==0)
                $ret .= "
                    <span class='badge $color2' style='background-color: $color;'>
                        {$row['name']}
                        <span class='cursor-pointer' onclick=\"removeTag({$optionVal})\">x</span>
                    </span>
                    ";
            else if($type==1)
                $ret .= "<option value=\"{$optionVal}\">{$row['name']}</option>";
            else if($type==2)
                $ret .= "
                    <span class='badge $color2' style='background-color: $color;'>
                        {$row['name']}
                    </span>
                    ";
            else if($type==3||$type==5)
                $ret .= "<tr><td><a href=\"tag.php?id={$row['id']}\">{$row['name']}</a></td></tr>";
            else if($type==4){
                $thumbnail=$row['thumbnail'];
                if($thumbnail==""){
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
            $idx=mysqli_num_rows(mysqli_query($this->con,"SELECT * FROM tagproblems WHERE tag=$tag"))+1;//retodo place with count
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
        $sql=mysqli_prepare($this->con,"INSERT INTO tags VALUES (NULL, ?,?,'','',0)");
		mysqli_stmt_bind_param($sql,"ss",$name,$background);
		mysqli_stmt_execute($sql);
		$query=mysqli_stmt_get_result($sql);
        echo $query;
        //mysqli_query($this->con, "INSERT INTO tags VALUES (NULL, '$name')");
        $tag = mysqli_insert_id($this->con);
        mysqli_query($this->con, "INSERT INTO tagowners VALUES (NULL, $tag,$user,0)");
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
        WHERE tagowners.owner=$user AND tagowners.type=0 AND tags.name='Solved-$user'");
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
    public function shareTag($tag,$user,$value){
        $tag=(int)$tag;
        $user=(int)$user;
        $value=(int)$value;
        $query=mysqli_query($this->con,"SELECT * FROM tagowners WHERE tag=$tag AND owner=$user");
        if($row=mysqli_fetch_array($query)){
            if($row['type']==1)
                mysqli_query($this->con, "UPDATE tagowners SET type=$value WHERE id={$row['id']}");
        }
        else
            mysqli_query($this->con, "INSERT INTO tagowners VALUES (NULL, $tag,$user,$value)");
    }
    public function unshareTag($tag,$user,$viewerOnly=0){
        $tag=(int)$tag;
        $user=(int)$user;
        if($viewerOnly)
            mysqli_query($this->con, "DELETE FROM tagowners WHERE tag=$tag AND owner=$user AND type=1");
        else
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
        $ret="Owners: ";
        $owners=mysqli_query($this->con,"SELECT users.id,users.username
        FROM tagowners
        INNER JOIN users ON users.id=tagowners.owner
        WHERE tag=$tag AND type=0");
        while($row = mysqli_fetch_array($owners)){
            $ret.="<a href=\"profile.php?user={$row['id']}\">{$row['username']}</a>, ";
        }
        echo $ret;
    }
    public function getViewers($tag){
        $tag=(int)$tag;
        $ret="";
        $viewers=mysqli_query($this->con,"SELECT users.id,users.username
        FROM tagowners
        INNER JOIN users ON users.id=tagowners.owner
        WHERE tag=$tag AND type=1");
        $colors=Array("primary","secondary","success","danger","warning","info","light text-dark","dark");
        while($row = mysqli_fetch_array($viewers)){
            $color=$colors[$row['id']%count($colors)];
            $ret.="<span class='badge rounded-pill bg-$color'>{$row['username']} <span class='cursor-pointer' onclick=shareTag({$row['id']},-1)>x</span></span>";
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