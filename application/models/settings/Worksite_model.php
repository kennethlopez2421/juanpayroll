<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Worksite_model extends CI_Model {

	public function get_worksite_json($search){
	  $requestData = $_REQUEST;

	  $columns = array(
	    0 => 'id',
	    1 => 'description',
			2 => 'location',
			3 => 'loc_latitude',
			4 => 'loc_longitude',
			5 => 'distance'
	  );


	  $sql = "SELECT * FROM worksite WHERE enabled = 1";


	  if($search != ""){
			$search = $this->db->escape('%'.$search.'%');
	    $sql .= " AND description LIKE $search";
	  }

	  $query = $this->db->query($sql);
	  $totalData = $query->num_rows();
	  $totalFiltered = $totalData;

	  $sql.=" ORDER BY description ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

	  $query = $this->db->query($sql);

	  $data = array();
		$x = 1;
	  foreach( $query->result_array() as $row )
	  {
	    $nestedData=array();

	    $nestedData[] = $x;
	    $nestedData[] = $row['description'];
	    $nestedData[] = $row['location'];
	    $nestedData[] = $row['loc_latitude'];
	    $nestedData[] = $row['loc_longitude'];
	    $nestedData[] = $row['distance'];
	    $nestedData[] = '<textarea name="" id="" cols="30" rows="5" class="form control">'.base_url('timelog/'.$this->session->company_code.'/'.$row['description'].'/'.en_dec('en',$row['worksiteid'])).'</textarea>';


	    $nestedData[] =
	    '
	      <center>
	      	<button class="btn btn-primary btn_edit" style = "width:80px;"
						data-uid = "'.$row['worksiteid'].'"
						data-desc = "'.$row['description'].'"
						data-loc = "'.$row['location'].'"
						data-lat = "'.$row['loc_latitude'].'"
						data-lng = "'.$row['loc_longitude'].'"
						data-dist = "'.$row['distance'].'"
					>
						<i class="fa fa-pencil mr-1"></i>Edit
					</button>
					<button class="btn btn-danger btn_delete" style = "width:80px;"
						data-desc = "'.$row['description'].'"
						data-delid = "'.$row['worksiteid'].'"
					>
						<i class="fa fa-trash mr-1"></i>Delete
					</button>
	      </center>
	    ';
			$x++;
	    $data[] = $nestedData;
	  }
	  $json_data = array(

	    "recordsTotal"    => intval( $totalData ),
	    "recordsFiltered" => intval( $totalFiltered ),
	    "data"            => $data
	  );

	  return $json_data;
	}

	public function getWorkSite($start, $length) {

		if($start != null && $length != null) {
			$sql = "SELECT * FROM worksite WHERE enabled = 1 ORDER BY description ASC LIMIT ".$start.",".$length." ";
		}else {
			$sql = "SELECT * FROM worksite WHERE enabled = 1";
		}
		return $this->db->query($sql);
	}

	public function getWorkSiteByDesc($data) {
		$sql = "SELECT * FROM worksite WHERE description = ? AND enabled = 1";
		return $this->db->query($sql,$data);
	}

	public function getCity(){
		$sql = "SELECT * FROM city ORDER BY description ASC";
		return $this->db->query($sql);
	}

	public function create($data) {
		$sql = "INSERT INTO worksite (description, location, loc_latitude, loc_longitude, distance, date_updated, date_created, user_id, enabled) VALUES (?,?,?,?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE worksite SET description = ?, city = ?, location = ?, loc_latitude = ?, loc_longitude = ?, distance = ?, date_updated = ? WHERE worksiteid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE worksite SET enabled = ? WHERE worksiteid = ?";
		$this->db->query($sql, $data);
	}

}
