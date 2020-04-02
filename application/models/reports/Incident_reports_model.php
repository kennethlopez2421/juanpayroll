<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Incident_reports_model extends CI_Model {
  public function get_incident_reports_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'id',
      1 => 'fullname',
      2 => 'date_reported',
      3 => 'date_happened',
      4 => 'department',
      5 => 'position'
    );

    $sql = "SELECT b.*, CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, @reported_by := b.reported_by,
     c.description as position, d.description as dept, a.employee_idno, b.date_reported, b.date_happened,
     @rd_head_id := b.reporting_dept_head, @cd_head_id := b.concerned_dept_head, @hr_head_id := b.hr_dept_head, @ac_head_id := b.account_dept_head,
     @reporter := (SELECT CONCAT(last_name,', ',first_name,' ',middle_name) as reporter FROM employee_record WHERE employee_idno = @reported_by AND enabled = 1 LIMIT 1) as reporter,
     @reporting_dept_head := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as rd_head FROM hris_users WHERE employee_idno = @rd_head_id AND enabled = 1 LIMIT 1) as rd_head,
     @concerned_dept_head := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as cd_head FROM hris_users WHERE employee_idno = @cd_head_id AND enabled = 1 LIMIT 1) as cd_head,
     @hr_dept_head := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as hr_head FROM hris_users WHERE employee_idno = @hr_head_id AND enabled = 1 LIMIT 1) as hr_head,
     @account_dept_head := (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) as ac_head FROM hris_users WHERE employee_idno = @ac_head_id AND enabled = 1 LIMIT 1) as ac_head
     FROM employee_record a
     INNER JOIN hris_incident_reports b ON a.employee_idno = b.employee_idno
     INNER JOIN position c ON b.position_id = c.positionid
     INNER JOIN department d ON b.dept_id = d.departmentid
     WHERE a.enabled = 1 AND b.enabled = 1 AND c.enabled = 1 AND d.enabled = 1";

     ### sub filter ###
     switch ($search->filter) {
       case 'divName':
         $emp_name = $this->db->escape("%".$search->search."%");
         $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                   OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                   OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
         break;
       case 'divDate':
         $from = $this->db->escape($search->from);
         $to = $this->db->escape($search->to);
         $sql .= " AND b.date_reported >= $from AND b.date_reported <= $to";
         break;
       case 'divDept':
         $deptid = $this->db->escape($search->search);
         $sql .= " AND d.departmentid = $deptid";
         break;
       default:
         break;
     }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY b.date_reported ASC, b.date_happened ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['date_reported'];
      $nestedData[] = $row['dept'];
      $nestedData[] = ($row['reporting_dept_head'] != "") ? '<center><span class="badge badge-pill badge-success">Approve</span></center>' : '<center><span class="badge badge-pill badge-warning">Waiting</span></center>';
      $nestedData[] = ($row['concerned_dept_head'] != "") ? '<center><span class="badge badge-pill badge-success">Approve</span></center>' : '<center><span class="badge badge-pill badge-warning">Waiting</span></center>';
      $nestedData[] = ($row['hr_dept_head'] != "") ? '<center><span class="badge badge-pill badge-success">Approve</span></center>' : '<center><span class="badge badge-pill badge-warning">Waiting</span></center>';
      $nestedData[] = ($row['account_dept_head'] != "") ? '<center><span class="badge badge-pill badge-success">Approve</span></center>' : '<center><span class="badge badge-pill badge-warning">Waiting</sp;an></center>';

      $nestedData[] =
      '
        <center>
          <button class="btn btn-primary btn_edit" style = "width:90px;"
            data-user_id = "'.$this->session->emp_idno.'"
            data-user_dept = "'.$this->session->deptId.'"
            data-user_lvl = "'.$this->session->position_lvl.'"
            data-uid = "'.en_dec('en',$row['id']).'"
            data-emp_name = "'.$row['fullname'].'"
            data-emp_idno = "'.$row['employee_idno'].'"
            data-pos_id = "'.$row['position_id'].'"
            data-dept_id = "'.$row['dept_id'].'"
            data-date_reported = "'.$row['date_reported'].'"
            data-date_happened = "'.$row['date_happened'].'"
            data-time_of_incidence = "'.$row['time_of_incidence'].'"
            data-place_of_incidence = "'.$row['place_of_incidence'].'"
            data-resulting_damage = "'.$row['resulting_damage'].'"
            data-incident_brief = "'.$row['incident_brief'].'"
            data-reported_by = "'.$row['reported_by'].'"
            data-reporter_name = "'.$row['reporter'].'"
            data-reporting_head_id = "'.$row['reporting_head_id'].'"
            data-concerned_head_id = "'.$row['concerned_head_id'].'"
            data-rd_head = "'.$row['rd_head'].'"
            data-cd_head = "'.$row['cd_head'].'"
            data-hr_head = "'.$row['hr_head'].'"
            data-ac_head = "'.$row['ac_head'].'"
          >
            <i class="fa fa-eye mr-1"></i>View
          </button>
          <button class="btn btn-danger btn_delete" style = "width:90px;"
            data-delid = "'.en_dec('en',$row['id']).'"
          >
            <i class="fa fa-trash mr-1"></i>Delete
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

  public function set_incident_reports($data){
    $this->db->insert('hris_incident_reports', $data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_incident_reports_approve($id,$field,$update_id){
    $id = $this->db->escape($id);
    $update_id = $this->db->escape($update_id);
    $sql = "UPDATE hris_incident_reports SET $field = $id WHERE id = $update_id";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true: false;
    // return $this->db->last_query();
  }

  public function update_incident_reports($data,$id){
    $this->db->update('hris_incident_reports', $data, array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
    // return $this->db->last_query();
  }

  public function update_incident_reports_status($id,$status = 0){
    $sql = "UPDATE hris_incident_reports SET enabled = ? WHERE id = ?";
    $data = array($status,$id);
    $this->db->query($sql,$data);
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
