<?php
class User {
	private $user;
	private $con;

	public function __construct($con, $user=NULL){
		$this->con = $con;
		if($user){
			$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE id='$user'");
			$this->user = mysqli_fetch_array($user_details_query);
		}
	}
    public function loadUsers($data) {

		$start = $data['amount']*($data['page']-1); 
        $amount=$data['amount'];
		$option=$data['option'];
		$sort=$data['sort'];
		$ret = "";
		$queryString="SELECT * FROM users";
        if($data['search']!== "")
			$queryString.=" WHERE username LIKE ?";
		switch($data['sort']){
			case 0:
				$queryString.=" ORDER BY id";
				break;
			case 1:
				$queryString.=" ORDER BY username";
				break;
			case 2:
				$queryString.=" ORDER BY power DESC";
				break;
			case 3:
				$queryString.=" ORDER BY experience DESC";
				break;
			case 4:
				$queryString.=" ORDER BY solved DESC";
				break;
		}
        $queryString.=" LIMIT $start, $amount";
		$sql=mysqli_prepare($this->con,$queryString);
		if($data['search']!== ""){
			$search="{$data['search']}%";
		    mysqli_stmt_bind_param($sql,"s",$search);
        }
		mysqli_stmt_execute($sql);
		$data_query=mysqli_stmt_get_result($sql);
		//$data_query = mysqli_query($this->con, $queryString);

        $no_more_users=true;
		$count=$start;
		if($option==1)
			$ret=array();
        while($row = mysqli_fetch_array($data_query)){
            $no_more_users=false;
			$count++;
			if($option==1){
				$ret[]=array("id"=>$row['id'],"text"=>$row['username']);
				continue;
			}
			$level=floor($row['experience']**(1/2));
            $ret .= "
                    <tr>
						<td>$count</td>
                        <td><a href=\"profile.php?user={$row['id']}\">{$row['username']} </a></th>
						<td>{$row['power']}</td>
						<td>{$level}</td>
						<td>{$row['solved']}</td>
                    </tr>
                    ";
        }
		if($option==1){
			echo json_encode($ret);
			return;
		}
        if($no_more_users) 
            $ret .= "<input type='hidden' class='noMorePosts' value='true'>";
        else 
            $ret .= "<input type='hidden' class='nextPage' value='" . ($data['page'] + 1) . "'>
                        <input type='hidden' class='noMorePosts' value='false'>";

		echo $ret;
	}

}

?>