<?php
class Problem {
	private $con;

	public function __construct($con){
		$this->con = $con;
	}

    public function ratingCircle($rating,$message){
        $big="";
        if($rating==$message)
            $big="Big";
        if($rating>=3000){
            $image=floor(min(3600,$rating)/100)*100;
            return "<span style='color:#cbfdfd;''><img src='assets/images/rating_circle/{$image}.png' class='ratingCircleImg{$big}'> $message</span>";
        }
        else if($rating>=2700){
            $color="#cbfdfd";
            $height=($rating-2700)/300;
        }
        else if($rating>=2400){
            $color="red";
            $height=($rating-2400)/300;
        }
        else if($rating>=2100){
            $color="#ffb100";
            $height=($rating-2100)/300;
        }
        else if($rating>=1900){
            $color="#9b59b6";
            $height=($rating-1900)/200;
        }
        else if($rating>=1600){
            $color="#6f66ff";
            $height=($rating-1600)/300;
        }
        else if($rating>=1400){
            $color="#16e0e0";
            $height=($rating-1400)/200;
        }
        else if($rating>=1200){
            $color="#2ecc71";
            $height=($rating-1200)/200;
        }
        else if($rating>=0){
            $color="#95a5a6";
            $height=($rating-0)/1200;
        }
        else if($rating==-1){
            return "<span style='color:magenta;''><img src='assets/images/rating_circle/-1.png' class='ratingCircleImg{$big}'> $message</span>";
        }
        $height*=100;
        $ret="<span style='color:$color'> <span class='ratingCircle{$big}' style='border-color: {$color}; background: rgba(0, 0, 0, 0) linear-gradient(to top, $color {$height}%, rgba(0, 0, 0, 0) {$height}%) repeat scroll 0% 0% border-box;'></span> $message</span>";
        return $ret;
    }
    private function getQuery($data,$count=0) {
        $minDif=(int)$data['mindifficulty'];
        $maxDif=(int)$data['maxdifficulty'];
        $minQ=(int)$data['minquality'];
        $maxQ=(int)$data['maxquality'];
        $tag=(int)$data['tag'];

        if($tag!=-1){
            if($count==1)
                $query="SELECT COUNT(idx) count FROM problems INNER JOIN tagproblems ON problems.id=tagproblems.problem WHERE tag=$tag AND";
            else
                $query="SELECT idx,name,difficulty,quality,thumbnail,problems.id,tagproblems.id tagproblemid FROM problems INNER JOIN tagproblems ON problems.id=tagproblems.problem WHERE tag=$tag AND";
        }
        else{
            if($count==1)
                $query="SELECT COUNT(id) count FROM problems WHERE"; 
            else
                $query="SELECT * FROM problems WHERE"; 
        }
        $query.=" deleted=0 AND (difficulty BETWEEN $minDif AND $maxDif)
         AND (quality BETWEEN $minQ AND $maxQ)";
        if(isset($data['editorial'])&&$data['editorial']=='true')
            $query.=" AND has_editorial=1";
        if(isset($data['code'])&&$data['code']=='true')
            $query.=" AND has_solution=1";
        if(isset($data['data'])&&$data['data']=='true')
            $query.=" AND has_data=1";
        if($data['search']!== "")
            $query.=" AND name LIKE ?";
        if(isset($data['sort'])){
            if($data['sort']==0){
                if($tag==-1)
                    $query.=" ORDER BY id";
                else
                    $query.=" ORDER BY idx";
            }
            else if($data['sort']==1)
                $query.=" ORDER BY difficulty";
            else if($data['sort']==2)
                $query.=" ORDER BY quality";
            else if($data['sort']==3)
                $query.=" ORDER BY name";
            else if($data['sort']==4)
                $query.=" ORDER BY RAND()";
            if($data['order']==1)
                $query.=" DESC";
        }
        return $query;
    }
    public function loadProblems($data,$user,$type=0) {
        $start = (int)$data['amount']*($data['page']-1); 
        $amount=(int)$data['amount'];
        $tag=(int)$data['tag'];
		$ret = "";
        $query=$this->getQuery($data);

        $query.=" LIMIT $start, $amount";

		$sql=mysqli_prepare($this->con,$query);
        if($data['search']!== ""){
            $search="%{$data['search']}%";
		    mysqli_stmt_bind_param($sql,"s",$search);
        }
		mysqli_stmt_execute($sql);
		$data_query=mysqli_stmt_get_result($sql);
		//$data_query = mysqli_query($this->con, $query);
        if($type==0){
            $rows=mysqli_query($this->con,"SELECT tags.background,tagproblems.problem FROM tags INNER JOIN tagproblems ON tags.id=tagproblems.tag INNER JOIN tagowners ON tags.id=tagowners.tag WHERE tagowners.owner=$user AND tagowners.type=0");
            foreach($rows as $key=>$row){
                if($row['background']!='')
                    $background[$row['problem']]=$row['background'];
            }
        }
        
        $no_more_problems=true;
        $idx=$start;
        if($type==1)
            $thumbnails = scandir('assets/images/thumbnails/');
        while($row = mysqli_fetch_array($data_query)){
            $no_more_problems=false;
            $idx++;
            $circle=$this->ratingCircle($row['difficulty'],$row['difficulty']);
            $rowbg="";
            $color2="";
            if($type==0&&isset($background[$row['id']])&&$background[$row['id']]!=""){
                $color=$background[$row['id']];
                $a=Color::fromString($color);
                $L=$a->toHSL();
                $L=$L['L'];
                $a=$a->getAlpha();
                $rowbg="style='background-color: {$color}'";
                if((1-$L)*$a>0.5)
                    $color2="style='color: lightblue'";
                else if($L*$a>0.5)
                    $color2="style='color: #007bff'";
            }
            if($type==1){
                $thumbnail=$row['thumbnail'];
                if($thumbnail==""){
                    $rand = $row['id']%(count($thumbnails)-2)+2;
                    $thumbnail = "assets/images/thumbnails/".$thumbnails[$rand];
                }
                $ret.="
                <div class='col-3'><a href='{$row['id']}'><div class='problembox h-100'>
                    <img class='card-img-top' src='{$thumbnail}'>
                    <p>{$row['name']}</p>
                </div></a></div>";
            }
            else if($type==2){
                $ret.="
                <div class='col-4'><a href='{$row['id']}'><div class='problembox h-100'>
                    <p>{$row['name']}</p>
                </div></a></div>";
            }
            else if($tag==-1){
                $ret .= "
                        <tr>
                            <td>$idx</td>
                            <td $rowbg><a $color2 href=\"{$row['id']}\">{$row['name']} </a></th>
                            <td style='padding-bottom:0'>{$circle}</th>
                            <td>{$row['quality']}</th>
                        </tr>
                        ";
            }
            else{
                $ret .= "
                        <tr id=\"{$row['tagproblemid']}\">
                            <th>$idx</th>
                            <td $rowbg><a $color2 href=\"{$row['id']}\">{$row['name']}</a></td>
                            <td>{$circle}</th>
                            <td>{$row['quality']}</th>
                            <td>
                                <button class='square_button btn-danger' onclick=\"deleteTagProblem({$row['id']})\">x</button>
                            </td>
                        </tr>
                        ";
            }
        }
        if($type==1){
            echo $ret;
            return;
        }
        if($no_more_problems) 
            $ret .= "<input type='hidden' class='noMorePosts' value='true'>";
        else 
            $ret .= "<input type='hidden' class='nextPage' value='" . ($data['page'] + 1) . "'>
                        <input type='hidden' class='noMorePosts' value='false'>";

		echo $ret;
	}
    public function countProblems($data,$user,$type=0) {
        $query=$this->getQuery($data,1);

        $sql=mysqli_prepare($this->con,$query);
        if($data['search']!== ""){
            $search="%{$data['search']}%";
            mysqli_stmt_bind_param($sql,"s",$search);
        }
        mysqli_stmt_execute($sql);
        $data_query=mysqli_stmt_get_result($sql);
        $totalcount=mysqli_fetch_array($data_query)['count'];
        echo $totalcount;
    }

    public function getName($id){
        $id=(int)$id;
        if($id==0)
            return "Announcements";
        $data_query=mysqli_query($this->con, "SELECT * FROM problems WHERE id=$id");
        if($row = mysqli_fetch_array($data_query))
            return $row['name'];
        else
            return 'PROBLEM NOT FOUND';
    }
}

?>