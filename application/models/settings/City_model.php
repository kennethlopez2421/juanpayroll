<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class City_model extends CI_Model {

	public function getCity($start,$length,$search) {

		if($start != null && $length != null) {

			if($search != null) {
				$sql = "SELECT c.cityid, c.description, c.countryid, cr.description as country_desc FROM city c LEFT JOIN country cr ON c.countryid = cr.countryid AND cr.enabled = 1 WHERE c.enabled = 1 AND c.description LIKE '".$this->db->escape_like_str($search)."%' ORDER BY c.description ASC LIMIT ".$start.",".$length;
			}else {
				$sql = "SELECT c.cityid, c.description, c.countryid, cr.description as country_desc FROM city c LEFT JOIN country cr ON c.countryid = cr.countryid AND cr.enabled = 1 WHERE c.enabled = 1 ORDER BY c.description ASC LIMIT ".$start.",".$length;
			}

		}else {
			$sql = "SELECT * FROM city WHERE enabled = 1";
		}
		return $this->db->query($sql);
	}

	// public function searchCity($city){
	// 	$sql = "SELECT *, b.description as country FROM city a LEFT JOIN country b ON a.countryid = b.countryid WHERE a.description LIKE ? AND a.enabled = 1";
  //   $city_data = array("%".$city."%");
  //   $query =  $this->db->query($sql,$city_data);
  //   $totalData = $query->num_rows();
	// 	$totalFiltered = $query->num_rows();
	//
  //   $data = array();
	//
  //   foreach( $query->result_array() as $row )
  //   {
  //     $nestedData=array();
	//
  //     $nestedData[] = $row['cityid'];
  //     $nestedData[] = $row['description'];
	//
  //     $nestedData[] =
  //     '
  //     	<button id="edit-btn'.$row['cityid'].'"
  //     					data-id = "'.$row['cityid'].'"
  //     					data-description = "'.$row['description'].'"
  //     					data-countryid = "'.$row['countryid'].'"
  //     					data-county = "'.$row['country'].'"
  //     					data-toggle = "modal" data-target="#editCityModal" class="btn btn-primary" style="width:40%;"
  //     					 ><i class="fa fa-edit mr-2"></i>Edit</button>
	//
	// 			<button id="delete-btn'.$row['cityid'].'"
	// 							data-id = "'.$row['cityid'].'"
	// 							data-description = "'.$row['description'].'"
	// 							data-toggle="modal" data-target="#delCityModal" class="btn btn-danger" style="width:40%;"
	// 			><i class="fa fa-trash mr-2"></i>Delete</button>
  //     ';
	//
  //     $data[] = $nestedData;
  //   }
  //   $json_data = array(
	//
  //     "recordsTotal"    => intval( $totalData ),
  //     "recordsFiltered" => intval( $totalFiltered ),
  //     "data"            => $data
  //   );
  //   return $json_data;
	// }

	public function getCityByDesc($description,$countryId) {
		$sql = "SELECT * FROM city WHERE description = ? AND countryid = ? AND enabled = 1";
		$data = array($description,$countryId);
		return $this->db->query($sql,$data);
	}

	public function create($data) {
		$sql = "INSERT INTO city(description,countryid,date_updated,date_created,user_id,enabled) VALUES (?,?,?,?,?,?)";
		$this->db->query($sql, $data);
	}

	public function update($data) {
		$sql = "UPDATE city SET description = ?, countryid = ? ,date_updated = ? WHERE cityid = ?";
		$this->db->query($sql, $data);
	}

	public function destroy($data) {
		$sql = "UPDATE city SET enabled = ? WHERE cityid = ?";
		$this->db->query($sql, $data);
	}

}
