<?php
class Vote {
	private $con;

	public function __construct($con){
		$this->con = $con;
	}
    public function getQuality($parent){
        $parent=(int)$parent;
        $votes=mysqli_query($this->con,"SELECT value FROM votes WHERE parent=$parent AND type=0");
        $num=5;
        $sum=15;
        while($row = mysqli_fetch_array($votes)){
            $sum+=$row['value'];
            $num++;
        }
        return $sum/$num;
    }
    public function getDifficulty($parent){
        $parent=(int)$parent;
        $votes=mysqli_query($this->con,"SELECT value FROM votes WHERE parent=$parent AND type=1");
        $values=array();
        while($row = mysqli_fetch_array($votes)){
            array_push($values,$row['value']);
        }
        sort($values);
        $count=count($values);
        if($count==0)
            return 0;
        $a=intdiv($count-1,2);
        $b=intdiv($count,2);
        return ($values[$a]+$values[$b])/2;
    }
    public function getLikes($parent){
        $parent=(int)$parent;
        $votes=mysqli_query($this->con,"SELECT value FROM votes WHERE parent=$parent AND type=2");
        $sum=0;
        while($row = mysqli_fetch_array($votes)){
            $sum+=$row['value'];
        }
        return $sum;
    }
    public function getVote($parent,$type,$user){
        $parent=(int)$parent;
        $type=(int)$type;
        $user=(int)$user;
        $votes=mysqli_query($this->con,"SELECT value FROM votes WHERE parent=$parent AND type=$type AND user=$user");
        if($row = mysqli_fetch_array($votes)){
            return $row['value'];
        }
        return "none";
    }
    public function deleteVote($parent,$type,$user){
        $parent=(int)$parent;
        $type=(int)$type;
        $user=(int)$user;
        mysqli_query($this->con, "DELETE FROM votes WHERE parent=$parent AND type=$type AND user=$user");
    }
    public function addVote($parent,$type,$user,$value){
        $parent=(int)$parent;
        $type=(int)$type;
        $user=(int)$user;
        $this->deleteVote($parent,$type,$user);
        mysqli_query($this->con, "INSERT INTO votes VALUES (NULL, $parent, $user, $type, $value)");
    }
}

?>