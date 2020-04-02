<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Employment_history_model extends CI_Model {
  public function get_employment_history_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'employee_idno',
      1 => 'fullname',
      3 => 'reason',
      4 => 'termination_date',
      5 => 'department'
    );

    $sql = "SELECT a.*, b.id as emp_id,
      CONCAT(b.last_name,', ',b.first_name,' ',b.middle_name) as fullname,
      @contract_id := (SELECT id FROM contract WHERE contract_emp_id = emp_id ORDER BY id DESC LIMIT 1) as contract_id ,
      e.description as department
      FROM hris_employment_history a
      INNER JOIN employee_record b ON a.employee_idno = b.employee_idno
      INNER JOIN contract c ON b.id = c.contract_emp_id
      LEFT JOIN position d ON c.position_id = d.positionid
      LEFT JOIN department e ON d.deptId = e.departmentid
      WHERE a.enabled = 1";

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql.=" GROUP BY a.employee_idno ORDER BY termination_date DESC, fullname ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

    foreach( $query->result_array() as $row )
    {
      $nestedData=array();

      $nestedData[] = $row['employee_idno'];
      $nestedData[] = $row['fullname'];
      $nestedData[] = $row['reason'];
      $nestedData[] = $row['termination_date'];
      $nestedData[] = $row['department'];

      $nestedData[] =
      '
        <center>
          <button class="btn btn-sm btn-primary btn_contract" style = "width:90px;font-size:8px !important;"
            data-c_id = "'.$row['contract_id'].'"
          >
            <i class="fa fa-clone mr-1"></i>Contract
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

  public function getPrevContractFull($cid){
    $sql = "SELECT a.*, b.*, c.work_sched, c.sched_type, c.sched_type2,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.positionid as posID, f.levelid as levelID, CONCAT(g.salRange_From,'-',g.salRange_to) as sss,
            d.description as workSite, e.description as position, f.description as emplvl,
            CONCAT(h.basic_mo_sal,'-',h.basic_mo_sal1) as philhealth,
            i.monthly_compensation as pagibig, CONCAT(j.aibLowerLimit,'-',j.aibUpperLimit) as tax,
            k.description as paytype, l.description as empstatus, m.description as p_medium
            FROM employee_record a
            LEFT JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN work_schedule c ON a.id = c.emp_id
            LEFT JOIN worksite d ON b.work_site_id = d.worksiteid
            LEFT JOIN position e ON b.position_id = e.positionid
            LEFT JOIN level f ON b.emp_lvl = f.levelid
            LEFT JOIN sss g ON b.sss = g.id
            LEFT JOIN philhealth h ON b.philhealth = h.phID
            LEFT JOIN pagibig i ON b.pagibig = i.id
            LEFT JOIN tax j ON b.tax = j.id
            LEFT JOIN paytype k ON b.paytype = k.paytypeid
            LEFT JOIN empstatus l ON b.emp_status = l.empstatusid
            LEFT JOIN payoutmedium m ON b.payout_medium = m.payoutmediumid
            WHERE b.contract_status = 'inactive' AND b.id = ?";
    $data = array($cid);
    return $this->db->query($sql,$data);
  }
}
