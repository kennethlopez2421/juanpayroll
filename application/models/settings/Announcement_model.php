<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Announcement_model extends CI_Model {
  public function get_announce_waiting_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'date',
      1 => 'title',
      2 => 'subject',
      3 => 'created_by'
    );


    $sql = "SELECT a.*,
            (CASE WHEN a.created_by < 3 THEN @name := 'Admin'
              ELSE @name := CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) END) as name
            FROM hris_announcement a
            LEFT JOIN employee_record b ON a.created_by = b.employee_idno
            WHERE a.enabled = 1 AND a.status = 'waiting'";


    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    // if( !empty($requestData['search']['value']) ){
    //   $sql.=" AND monthly_compensation LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employee_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employer_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%'";
    // }

    if($search != ""){
      // $search = $this->db->escape($search);
      // $sql .= " AND a.title LIKE '".$search."%'";
      $sql .= $search;
    }

    $totalFiltered = $query->num_rows();
    $totalFiltered = $totalData;
      // $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
      //012819
      $sql.=" ORDER BY a.created_at ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
      $data = array();

      foreach( $query->result_array() as $row )
      {
        $nestedData=array();

        $status = ($row['status'] == 'waiting')
        ? '<h5 class = "test-warning">Waiting for Approval</h5>'
        : '<h5 class = "text-success">Approved</h5>';

        $nestedData[] = $row['announce_start']." - ".$row['announce_end'];
        $nestedData[] = $row['announce_title'];
        $nestedData[] = $row['announce_subject'];
        $nestedData[] = $row['name'];
        $nestedData[] = $status;

        $nestedData[] =
        '
          <center>
            <button class="btn btn-sm btn-info btn_approved" style = "width:90px;"
              data-approved_id = "'.$row['announce_id'].'">
              <i class="fa fa-check mr-2"></i>Approve
            </button>
            <button class="btn btn-sm btn-primary btn_announce_edit" style = "width:90px;"
              data-edit_id = "'.$row['announce_id'].'">
              <i class="fa fa-pencil mr-2"></i>Edit
            </button>
            <button class="btn btn-sm btn-danger btn_announce_del" style = "width:90px;"
              data-delete_id = "'.$row['announce_id'].'">
              <i class="fa fa-trash mr-2"></i>Delete
            </button>
          </center>
        ';

        $data[] = $nestedData;
      }
      $json_data = array(

        "recordsTotal"    => intval( $totalData ),
        "recordsFiltered" => intval( $totalFiltered ),
        "data"            => $data
      );
      return $json_data;
  }

  public function get_announce_approved_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'date',
      1 => 'title',
      2 => 'subject',
      3 => 'created_by'
    );


    $sql = "SELECT a.*,
            (CASE WHEN a.created_by < 3 THEN @name := 'Admin'
              ELSE @name := CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) END) as name
            FROM hris_announcement a
            LEFT JOIN employee_record b ON a.created_by = b.employee_idno
            WHERE a.enabled = 1 AND a.status = 'approved'";


    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    // if( !empty($requestData['search']['value']) ){
    //   $sql.=" AND monthly_compensation LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employee_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%' OR employer_share LIKE '%".str_replace(' ', '', sanitize($requestData['search']['value']))."%'";
    // }

    if($search != ""){
      // $search = $this->db->escape($search);
      // $sql .= " AND a.title LIKE '".$search."%'";
      $sql .= $search;
    }

    $totalFiltered = $query->num_rows();
    $totalFiltered = $totalData;
      // $sql.=" ORDER BY ". $columns[0]."   ".$requestData['order'][0]['dir']."   LIMIT ".$requestData['start']." ,".$requestData['length']."";
      //012819
      $sql.=" ORDER BY a.created_at ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);
      $data = array();

      foreach( $query->result_array() as $row )
      {
        $nestedData=array();

        $status = ($row['status'] == 'waiting')
        ? '<h5 class = "test-warning">Waiting for Approval</h5>'
        : '<h5 class = "text-success">Approved</h5>';

        $nestedData[] = $row['announce_start']." - ".$row['announce_end'];
        $nestedData[] = $row['announce_title'];
        $nestedData[] = $row['announce_subject'];
        $nestedData[] = $row['name'];
        $nestedData[] = $status;

        $nestedData[] =
        '
          <center>
            <button class="btn btn-sm btn-primary btn_announce_edit" style = "width:90px;"
              data-edit_id = "'.$row['announce_id'].'">
              <i class="fa fa-pencil mr-2"></i>Edit
            </button>
            <button class="btn btn-sm btn-danger btn_announce_del" style = "width:90px;"
              data-delete_id = "'.$row['announce_id'].'">
              <i class="fa fa-trash mr-2"></i>Delete
            </button>
          </center>
        ';

        $data[] = $nestedData;
      }
      $json_data = array(

        "recordsTotal"    => intval( $totalData ),
        "recordsFiltered" => intval( $totalFiltered ),
        "data"            => $data
      );
      return $json_data;
  }

  public function get_announcement($id){
    // $sql = "SELECT * FROM hris_announcement WHERE announce_id = ? AND enabled = 1";
    $sql = "SELECT a.*,
            (CASE WHEN a.created_by < 3 THEN @name := 'Admin'
              ELSE @name := CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) END) as name
            FROM hris_announcement a
            LEFT JOIN employee_record b ON a.created_by = b.employee_idno
            WHERE a.enabled = 1 AND announce_id = ?";
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function get_all_announcement($limit = false, $offset = false){
    $today = $this->db->escape(today());
    if($limit != false && $offset == false){
      $sql = "SELECT a.*,
        (CASE WHEN a.created_by < 3 THEN @name := 'Admin'
        ELSE @name := CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) END) as name
        FROM hris_announcement a
        LEFT JOIN employee_record b ON a.created_by = b.employee_idno
        WHERE a.enabled = 1 AND a.status = 'approved' AND announce_end >= $today LIMIT ?";
      $data = array($limit);
      return $this->db->query($sql,$data);
    }

    if($limit != false && $offset != false){
      $sql = "SELECT a.*,
        (CASE WHEN a.created_by < 3 THEN @name := 'Admin'
        ELSE @name := CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) END) as name
        FROM hris_announcement a
        LEFT JOIN employee_record b ON a.created_by = b.employee_idno
        WHERE a.enabled = 1 AND a.status = 'approved' AND announce_end >= $today LIMIT ? OFFSET ?";
      $data = array($limit,$offset);
      return $this->db->query($sql,$data);
    }

    $sql = "SELECT a.*,
      (CASE WHEN a.created_by < 3 THEN @name := 'Admin'
      ELSE @name := CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) END) as name
      FROM hris_announcement a
      LEFT JOIN employee_record b ON a.created_by = b.employee_idno
      WHERE a.enabled = 1 AND a.status = 'approved' AND announce_end >= $today";
    return $this->db->query($sql);
  }

  public function set_announcement($data){
    $this->db->insert('hris_announcement',$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_announcement($data){
    $sql = "UPDATE hris_announcement
            SET announce_title = ?, announce_subject = ?, announce_start = ? , announce_end = ?, announce_body = ?
            WHERE announce_id = ?";
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function approved_announcement($id){
    $sql = "UPDATE hris_announcement SET status = 'approved' WHERE announce_id = ? AND enabled = 1";
    $data = array($id);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
