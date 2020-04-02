<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Evaluations_model extends CI_Model {
  public function get_pending_evaluations_json($search){
    $requestData = $_REQUEST;
    $evaluator = 0;

    $columns = array(
      0 => 'fullname',
      1 => 'evaluator',
      2 => 'department',
      3 => 'eval_date',
      4 => 'covered_period'
    );

    $sql = "SELECT
      a.employee_idno, @employee_idno := c.management_id, d.description as position, e.description as department,
      c.eval_type, c.department_id,
      c.eval_date, CONCAT(c.eval_from,' - ', c.eval_to) as covered_period, @emp_id := a.id as emp_id, c.status2,
      CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, @eval_date := c.eval_date, c.id as eval_id,
      @evaluator := (CASE WHEN c.eval_type = 'type_1' THEN (SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) FROM employee_record a WHERE employee_idno = c.management_id AND enabled = 1)
      ELSE (SELECT CONCAT(a.user_lname,', ',a.user_fname,' ',a.user_mname,' (',b.position,')') as fullname FROM hris_users a INNER JOIN hris_position b ON a.position_id = b.position_id WHERE a.enabled = 1 AND b.enabled = 1 AND a.employee_idno = @employee_idno) END) as evaluator,
      (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) FROM hris_users WHERE employee_idno = c.certify_by AND enabled = 1) as certify_by,
      @date_hired := (SELECT contract_start FROM contract WHERE contract_emp_id = @emp_id ORDER BY id DESC LIMIT 1) as date_hired,
      (SELECT CONCAT(
        TIMESTAMPDIFF(YEAR, @date_hired, @eval_date),' year(s) ',
        TIMESTAMPDIFF(MONTH, @date_hired + INTERVAL TIMESTAMPDIFF(YEAR, @date_hired, @eval_date) YEAR, NOW()),' month(s)')
      ) as date_diff
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN hris_evaluations c ON a.employee_idno = c.employee_idno
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON c.department_id = e.departmentid
      WHERE c.employee_idno = a.employee_idno AND a.enabled = 1 AND b.contract_status = 'active'
      AND b.enabled = 1 AND c.enabled = 1 AND c.status2 = 'ongoing'";

    switch ($search->filter_by) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape("%".$search->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divDate':
        $from = $this->db->escape($search->from);
        $to = $this->db->escape($search->to);
        $sql .= " AND c.eval_date BETWEEN $from AND $to";
      default:
        // code...
        break;
    }

    if($this->session->position_lvl > hr_or_above() && $this->session->deptId != hr_id()){
      $management_id = $this->db->escape($this->session->emp_idno);
      $sql .= " AND c.management_id = $management_id";
      $evaluator = 1;
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY eval_date ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $token = en_dec('en', $this->session->token_session);

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $encrypt_id = en_dec('en', $row['eval_id']);
      if($row['status2'] == 'ongoing'){
        $status = '<center><h4 class="text-warning">'.$row['status2'].'</h4></center>';
      }

      if($row['status2'] == 'evaluated'){
        $status = '<center><h4 class="text-success">'.$row['status2'].'</h4></center>';
      }

      if($row['status2'] == 'certified'){
        $status = '<center><h4 class="text-info">'.$row['status2'].'</h4></center>';
      }

      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['evaluator'];
      $nestedData[] = ($row['eval_type'] == 'type_1') ? $row['department'] : 'Higher Department';
      $nestedData[] = $row['eval_date'];
      $nestedData[] = $row['covered_period'];
      $nestedData[] = $status;

      // HR MANAGER OR ABOVE
      // if($this->session->userdata('position_lvl') <= hr_or_above()){
      //   if($row['status2'] === 'ongoing'){
      //   }else{
      //     $nestedData[] =
      //     '
      //       <center>
      //         <a href="'.base_url('evaluations/Evaluations/view/'.$token.'/'.$encrypt_id).'" class = "btn btn-sm btn-primary"><i class="fa fa-eye mr-1"></i>View</a>
      //       </center>
      //     ';
      //   }
      // }

      if($this->session->userdata('position_lvl') <= hr_or_above() && $evaluator == 0){
        $nestedData[] =
        '
          <center>
            <a href="'.base_url('evaluations/Evaluations/view/'.$token.'/'.$encrypt_id).'" class = "btn btn-primary"><i class="fa fa-eye mr-1"></i>View</a>
            <button class="btn btn-danger btn_del_eval" data-fullname = "'.$row['fullname'].'" data-id = "'.en_dec('en',$row['eval_id']).'"> <i class="fa fa-trash mr-1"></i>Delete</button>
          </center>
        ';
      }elseif($this->session->userdata('position_lvl') >= hr_or_above() && $evaluator == 1){
        $nestedData[] =
        '
          <center>
            <a href="'.base_url('evaluations/Evaluations/view/'.$token.'/'.$encrypt_id).'" class = "btn btn-sm btn-primary"><i class="fa fa-eye mr-1"></i>View</a>
            <button class="btn btn-primary btn_reassign"
              data-eval_id = "'.en_dec('en',$row['eval_id']).'"
            >
              Reassign
            </button>
          </center>
        ';
      }else{
        if($row['eval_type'] == 'type_2' && $row['department_id'] == 0){
          $nestedData[] =
          '
            <center>
              <a href="'.base_url('evaluations/Evaluations/view/'.$token.'/'.$encrypt_id).'" class = "btn btn-sm btn-primary"><i class="fa fa-eye mr-1"></i>View</a>
              <button class="btn btn-danger btn_del_eval" data-fullname = "'.$row['fullname'].'" data-id = "'.en_dec('en',$row['eval_id']).'"> <i class="fa fa-trash mr-1"></i>Delete</button>
            </center>
          ';
        }else{
          $nestedData[] = '<center>------</center>';
        }
      }

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_evaluated_evaluations_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'fullname',
      1 => 'evaluator',
      2 => 'department',
      3 => 'eval_date',
      4 => 'covered_period'
    );

    $sql = "SELECT
      a.employee_idno, @employee_idno := c.management_id, d.description as position, e.description as department,
      c.eval_type, c.department_id,
      c.eval_date, CONCAT(c.eval_from,' - ', c.eval_to) as covered_period, @emp_id := a.id as emp_id, c.status2,
      CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, @eval_date := c.eval_date, c.id as eval_id,
      @evaluator := (CASE WHEN c.eval_type = 'type_1' THEN (SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) FROM employee_record a WHERE employee_idno = c.management_id AND enabled = 1)
      ELSE (SELECT CONCAT(a.user_lname,', ',a.user_fname,' ',a.user_mname,' (',b.position,')') as fullname FROM hris_users a INNER JOIN hris_position b ON a.position_id = b.position_id WHERE a.enabled = 1 AND b.enabled = 1 AND a.employee_idno = @employee_idno) END) as evaluator,
      (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) FROM hris_users WHERE employee_idno = c.certify_by AND enabled = 1) as certify_by,
      @date_hired := (SELECT contract_start FROM contract WHERE contract_emp_id = @emp_id ORDER BY id DESC LIMIT 1) as date_hired,
      (SELECT CONCAT(
        TIMESTAMPDIFF(YEAR, @date_hired, @eval_date),' year(s) ',
        TIMESTAMPDIFF(MONTH, @date_hired + INTERVAL TIMESTAMPDIFF(YEAR, @date_hired, @eval_date) YEAR, NOW()),' month(s)')
      ) as date_diff
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN hris_evaluations c ON a.employee_idno = c.employee_idno
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON c.department_id = e.departmentid
      WHERE c.employee_idno = a.employee_idno AND a.enabled = 1 AND b.contract_status = 'active'
      AND b.enabled = 1 AND c.enabled = 1 AND c.status2 = 'evaluated'";

    switch ($search->filter_by) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape("%".$search->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divDate':
        $from = $this->db->escape($search->from);
        $to = $this->db->escape($search->to);
        $sql .= " AND c.eval_date BETWEEN $from AND $to";
      default:
        // code...
        break;
    }

    if($this->session->position_lvl > hr_or_above() && $this->session->deptId != hr_id() && $this->session->login_type == 'admin'){
      $management_id = $this->db->escape($this->session->emp_idno);
      $sql .= " AND c.management_id = $management_id";
    }

    // EMPLOYEE VIEW
    if($this->session->login_type != 'admin'){
      $emp = $this->db->escape($this->session->emp_idno);
      $sql .= " AND c.employee_idno = $emp";
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY eval_date ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $token = en_dec('en', $this->session->token_session);

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $encrypt_id = en_dec('en', $row['eval_id']);
      if($row['status2'] == 'ongoing'){
        $status = '<center><h4 class="text-warning">'.$row['status2'].'</h4></center>';
      }

      if($row['status2'] == 'evaluated'){
        $status = '<center><h4 class="text-success">'.$row['status2'].'</h4></center>';
      }

      if($row['status2'] == 'certified'){
        $status = '<center><h4 class="text-info">'.$row['status2'].'</h4></center>';
      }

      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['evaluator'];
      $nestedData[] = ($row['eval_type'] == 'type_1') ? $row['department'] : 'Higher Department';
      $nestedData[] = $row['eval_date'];
      $nestedData[] = $row['covered_period'];
      $nestedData[] = $status;
      $nestedData[] =
      '
        <center>
          <a href="'.base_url('evaluations/Evaluations/view/'.$token.'/'.$encrypt_id).'" class = "btn btn-sm btn-primary"><i class="fa fa-eye mr-1"></i>View</a>
        </center>
      ';

      // HR MANAGER OR ABOVE
      // if($this->session->userdata('position_lvl') <= hr_or_above()){
      //   if($row['status2'] === 'ongoing'){
      //   }else{
      //     $nestedData[] =
      //     '
      //       <center>
      //         <a href="'.base_url('evaluations/Evaluations/view/'.$token.'/'.$encrypt_id).'" class = "btn btn-sm btn-primary"><i class="fa fa-eye mr-1"></i>View</a>
      //       </center>
      //     ';
      //   }
      // }

      // if($this->session->userdata('position_lvl') >= hr_or_above()){
      //   $nestedData[] =
      //   '
      //     <center>
      //       <a href="'.base_url('evaluations/Evaluations/view/'.$token.'/'.$encrypt_id).'" class = "btn btn-sm btn-primary"><i class="fa fa-eye mr-1"></i>View</a>
      //     </center>
      //   ';
      // }

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_certified_evaluations_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'fullname',
      1 => 'evaluator',
      2 => 'department',
      3 => 'eval_date',
      4 => 'covered_period'
    );

    $sql = "SELECT
      a.employee_idno, @employee_idno := c.management_id, d.description as position, e.description as department,
      c.eval_type, c.department_id,
      c.eval_date, CONCAT(c.eval_from,' - ', c.eval_to) as covered_period, @emp_id := a.id as emp_id, c.status2,
      CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, @eval_date := c.eval_date, c.id as eval_id,
      @evaluator := (CASE WHEN c.eval_type = 'type_1' THEN (SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) FROM employee_record a WHERE employee_idno = c.management_id AND enabled = 1)
      ELSE (SELECT CONCAT(a.user_lname,', ',a.user_fname,' ',a.user_mname,' (',b.position,')') as fullname FROM hris_users a INNER JOIN hris_position b ON a.position_id = b.position_id WHERE a.enabled = 1 AND b.enabled = 1 AND a.employee_idno = @employee_idno) END) as evaluator,
      (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) FROM hris_users WHERE employee_idno = c.certify_by AND enabled = 1 LIMIT 1) as certify_by,
      @date_hired := (SELECT contract_start FROM contract WHERE contract_emp_id = @emp_id ORDER BY id DESC LIMIT 1) as date_hired,
      (SELECT CONCAT(
        TIMESTAMPDIFF(YEAR, @date_hired, @eval_date),' year(s) ',
        TIMESTAMPDIFF(MONTH, @date_hired + INTERVAL TIMESTAMPDIFF(YEAR, @date_hired, @eval_date) YEAR, NOW()),' month(s)')
      ) as date_diff
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN hris_evaluations c ON a.employee_idno = c.employee_idno
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON c.department_id = e.departmentid
      WHERE c.employee_idno = a.employee_idno AND a.enabled = 1 AND b.contract_status = 'active'
      AND b.enabled = 1 AND c.enabled = 1 AND c.status2 = 'certified'";

    switch ($search->filter_by) {
      case 'divID':
        $emp_id = $this->db->escape($search->search);
        $sql .= " AND a.employee_idno = $emp_id";
        break;
      case 'divName':
        $emp_name = $this->db->escape("%".$search->search."%");
        $sql .= " AND (CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) LIKE $emp_name
                  OR CONCAT(a.last_name,', ',a.first_name) LIKE $emp_name
                  OR a.last_name LIKE $emp_name OR a.first_name LIKE $emp_name)";
        break;
      case 'divDept':
        $deptId = $this->db->escape((int)$search->search);
        $sql .= " AND e.departmentid = $deptId";
        break;
      case 'divDate':
        $from = $this->db->escape($search->from);
        $to = $this->db->escape($search->to);
        $sql .= " AND c.eval_date BETWEEN $from AND $to";
      default:
        // code...
        break;
    }

    if($this->session->position_lvl > hr_or_above() && $this->session->deptId != hr_id() && $this->session->login_type == 'admin'){
      $management_id = $this->db->escape($this->session->emp_idno);
      $sql .= " AND c.management_id = $management_id";
    }

    // EMPLOYEE VIEW
    if($this->session->login_type != 'admin'){
      $emp = $this->db->escape($this->session->emp_idno);
      $sql .= " AND c.employee_idno = $emp";
    }


    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" ORDER BY eval_date ASC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();
    $token = en_dec('en', $this->session->token_session);

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();
      $encrypt_id = en_dec('en', $row['eval_id']);
      if($row['status2'] == 'ongoing'){
        $status = '<center><h4 class="text-warning">'.$row['status2'].'</h4></center>';
      }

      if($row['status2'] == 'evaluated'){
        $status = '<center><h4 class="text-success">'.$row['status2'].'</h4></center>';
      }

      if($row['status2'] == 'certified'){
        $status = '<center><h4 class="text-info">'.$row['status2'].'</h4></center>';
      }

      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['evaluator'];
      $nestedData[] = ($row['eval_type'] == 'type_1') ? $row['department'] : 'Higher Department';
      $nestedData[] = $row['eval_date'];
      $nestedData[] = $row['covered_period'];
      $nestedData[] = $status;
      $nestedData[] =
      '
        <center>
          <a href="'.base_url('evaluations/Evaluations/view/'.$token.'/'.$encrypt_id).'" class = "btn btn-sm btn-primary"><i class="fa fa-eye mr-1"></i>View</a>
        </center>
      ';

      // HR MANAGER OR ABOVE
      // if($this->session->userdata('position_lvl') <= hr_or_above()){
      //   if($row['status2'] === 'ongoing'){
      //   }else{
      //     $nestedData[] =
      //     '
      //       <center>
      //         <a href="'.base_url('evaluations/Evaluations/view/'.$token.'/'.$encrypt_id).'" class = "btn btn-sm btn-primary"><i class="fa fa-eye mr-1"></i>View</a>
      //       </center>
      //     ';
      //   }
      // }

      // if($this->session->userdata('position_lvl') >= hr_or_above()){
      //   $nestedData[] =
      //   '
      //     <center>
      //       <a href="'.base_url('evaluations/Evaluations/view/'.$token.'/'.$encrypt_id).'" class = "btn btn-sm btn-primary"><i class="fa fa-eye mr-1"></i>View</a>
      //     </center>
      //   ';
      // }

      $data[] = $nestedData;
    }

    $json_data = array(

      "recordsTotal"    => intval( $totalData ),
      "recordsFiltered" => intval( $totalFiltered ),
      "data"            => $data
    );

    return $json_data;
  }

  public function get_evaluation($id){
    $sql = "SELECT
      a.employee_idno, @employee_idno := c.management_id as management_id, d.description as position, e.description as department,
      c.eval_date, CONCAT(c.eval_from,' - ', c.eval_to) as covered_period, @emp_id := a.id as emp_id, c.status2,
      CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname, @eval_date := c.eval_date,
      c.id as eval_id, c.department_id, c.eval_remarks, c.eval_recommendations, c.eval_purpose, c.eval_comments,
      c.eval_project, c.eval_score, c.eval_score_percent, c.eval_equivalent_rate, c.eval_purpose_type,
      c.status2, a.id as emp_id, c.ref_no, c.eval_type, c.eval_assessment, c.eval_action_hr,
      @evaluator := (CASE WHEN c.eval_type = 'type_1' THEN (SELECT CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) FROM employee_record a WHERE employee_idno = c.management_id AND enabled = 1)
      ELSE (SELECT CONCAT(a.user_lname,', ',a.user_fname,' ',a.user_mname,' (',b.position,')') as fullname FROM hris_users a INNER JOIN hris_position b ON a.position_id = b.position_id WHERE a.enabled = 1 AND b.enabled = 1 AND a.employee_idno = @employee_idno) END) as evaluator,
      (SELECT CONCAT(user_lname,', ',user_fname,' ',user_mname) FROM hris_users WHERE employee_idno = c.certify_by AND enabled = 1 LIMIT 1) as certify_by,
      @date_hired := (SELECT contract_start FROM contract WHERE contract_emp_id = @emp_id ORDER BY id ASC LIMIT 1) as date_hired,
      (SELECT CONCAT(
        TIMESTAMPDIFF(YEAR, @date_hired, @eval_date),' year(s) ',
        TIMESTAMPDIFF(MONTH, @date_hired + INTERVAL TIMESTAMPDIFF(YEAR, @date_hired, @eval_date) YEAR, NOW()),' month(s)')
      ) as date_diff
      FROM employee_record a
      INNER JOIN contract b ON a.id = b.contract_emp_id
      INNER JOIN hris_evaluations c ON a.employee_idno = c.employee_idno
      LEFT JOIN position d ON b.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      WHERE c.employee_idno = a.employee_idno AND a.enabled = 1 AND b.contract_status = 'active'
      AND b.enabled = 1 AND c.enabled = 1 AND c.id = ?";
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function get_ratings(){
    $sql = "SELECT equivalent_rating, score FROM hris_eval_ratings WHERE enabled = 1";
    return $this->db->query($sql);
  }

  public function get_all_contract($emp_id){
    $sql = "SELECT a.position_id, b.description, a.contract_start FROM contract a
      INNER JOIN position b ON a.position_id = b.positionid
      WHERE b.enabled = 1 AND a.contract_emp_id = ? ORDER BY a.id ASC";
    $data = array($emp_id);
    return $this->db->query($sql,$data);
  }

  public function update_hris_eval($data,$id){
    $this->db->update('hris_evaluations',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function certify($data,$id){
    $this->db->update('hris_evaluations',$data,array('id' => $id));
    return ($this->db->affected_rows() > 0) ? true: false;
  }

  public function reassign_eval($id,$user){
    $id = $this->db->escape($id);
    $user = $this->db->escape($user);

    $sql = "UPDATE hris_evaluations SET management_id = $user WHERE id = $id AND enabled = 1";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }

  public function update_eval_status($id,$status = 0){
    $status = $this->db->escape($status);
    $id = $this->db->escape($id);
    $sql = "UPDATE hris_evaluations SET enabled = $status WHERE id = $id";
    $this->db->query($sql);
    return ($this->db->affected_rows() > 0) ? true : false;
  }
}
