<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Contract_history_model extends CI_Model {
  public function get_contract_history_json($search){
    $requestData = $_REQUEST;

    $columns = array(
      0 => 'emp_name',
      1 => 'dept',
      2 => 'subDept',
      3 => 'workSite',
      4 => 'position',
      5 => 'c_date'
    );

    $sql = "SELECT a.id,
            CONCAT(f.last_name,',', f.first_name,' ', f.middle_name) as emp_name,
            d.description as dept,
            e.description as subDept,
            c.description as workSite,
            b.description as position, a.contract_status,
            CONCAT(DATE_FORMAT(a.contract_start, '%M %D, %Y'), ' - ', DATE_FORMAT(a.contract_end, '%M %D, %Y')) as c_date
            FROM contract a
            LEFT JOIN position b ON a.position_id = b.positionid
            LEFT JOIN worksite c ON a.work_site_id = c.worksiteid
            LEFT JOIN department d ON b.deptId = d.departmentid
            LEFT JOIN subdept e ON b.subDeptId = e.subdeptid
            LEFT JOIN employee_record f ON a.contract_emp_id = f.id WHERE a.enabled = 1";

    $query = $this->db->query($sql);

    $totalData = $query->num_rows();
    $totalFiltered = $query->num_rows();

    if($search != ""){

      if(is_array($search)){
        // print_r($search);
        $sql = "SELECT a.id,
                CONCAT(f.last_name,',', f.first_name,' ', f.middle_name) as emp_name,
                d.description as dept,
                e.description as subDept,
                c.description as workSite,
                b.description as position, a.sal_cat, a.contract_status,
                CONCAT(DATE_FORMAT(a.contract_start, '%M %D, %Y'), ' - ', DATE_FORMAT(a.contract_end, '%M %D, %Y')) as c_date
                FROM contract a
                LEFT JOIN position b ON a.position_id = b.positionid
                LEFT JOIN worksite c ON a.work_site_id = c.worksiteid
                LEFT JOIN department d ON b.deptId = d.departmentid
                LEFT JOIN subdept e ON b.subDeptId = e.subdeptid
                LEFT JOIN employee_record f ON a.contract_emp_id = f.id WHERE a.enabled = 1 ".$search[2];
        $rows = $this->db->query($sql);
        $data = array();
        foreach($rows->result_array() as $row){
          $total = 0;
          $sal_cat = json_decode($row['sal_cat']);
          foreach($sal_cat as $sal){
            $total += $sal->amount;
          }
          // echo $total.'</br>';
          if($total >= (float)$search[0] && $total <= (float)$search[1]){


            $nestedData=array();

            if($row['contract_status'] == "active"){
              $status = '<span style = "width:50px" class=" text-center badge badge-pill badge-sm badge-success">Active</span>';
            }else{
              $status = '<span style = "width:50px" class=" text-center badge badge-pill badge-sm badge-danger">Inactive</span>';
            }

            $nestedData[] = $row['emp_name'];
            $nestedData[] = $row['dept'];
            $nestedData[] = $row['subDept'];
            $nestedData[] = $row['workSite'];
            $nestedData[] = $row['position'];
            $nestedData[] = $row['c_date'];
            $nestedData[] = $status;

            $nestedData[] =
            '
              <center>
                <button class="btn_view_contract btn btn-sm btn-primary" style = "width:100px;" data-cid = "'.$row['id'].'"><i class="fa fa-clone mr-2"></i>View</button>
              </center>
            ';

            $data[] = $nestedData;
          }
        }

        $totalData = count(array($data));
        $totalFiltered = count(array($data));

        $json_data = array(

          "recordsTotal"    => intval( $totalData ),
          "recordsFiltered" => intval( $totalFiltered ),
          "data"            => $data
        );
        return $json_data;
        exit();
      }else{
        $sql .= $search;
      }
    }

    $query = $this->db->query($sql);
    $totalData = $query->num_rows();
    $totalFiltered = $totalData;

    $sql .= " ORDER BY last_name ASC LIMIT ".$requestData['start']." ,".$requestData['length']."";

    $query = $this->db->query($sql);

    $data = array();

      foreach( $query->result_array() as $row )
      {
        $nestedData=array();

        if($row['contract_status'] == "active"){
          $status = '<span style = "width:50px" class=" text-center badge badge-pill badge-sm badge-success">Active</span>';
        }else{
          $status = '<span style = "width:50px" class=" text-center badge badge-pill badge-sm badge-danger">Inactive</span>';
        }

        $nestedData[] = $row['emp_name'];
        $nestedData[] = $row['dept'];
        $nestedData[] = $row['subDept'];
        $nestedData[] = $row['workSite'];
        $nestedData[] = $row['position'];
        $nestedData[] = $row['c_date'];
        $nestedData[] = $status;

        $nestedData[] =
        '
          <center>
            <button class="btn_view_contract btn btn-sm btn-primary" style = "width:100px;" data-cid = "'.$row['id'].'"><i class="fa fa-clone mr-2"></i>View</button>
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
      // return $this->db->last_query();
  }

  public function getContract($id){
    $sql = "SELECT a.*, b.*, c.work_sched, c.sched_type, c.sched_type2,
            e.positionid as posID, f.levelid as levelID, g.id as sssID,
            h.phID as phID, i.id as pagibigID, j.id as taxID, k.paytypeid as paytypeID
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
            WHERE a.id = ?";
    $data = array($id);
    return $this->db->query($sql,$data);
  }

  public function getPrevContractFull($cid){
    $sql = "SELECT a.*, b.*, c.work_sched, c.sched_type, c.sched_type2,
            CONCAT(a.last_name,', ',a.first_name,' ',a.middle_name) as fullname,
            e.positionid as posID, f.levelid as levelID, CONCAT(g.salRange_From,'-',g.salRange_to) as sss,
            d.description as workSite, e.description as position, f.description as emplvl,
            CONCAT(h.basic_mo_sal,'-',h.basic_mo_sal1) as philhealth, i.monthly_compensation as pagibig,
            CONCAT(j.aibLowerLimit,'-',j.aibUpperLimit) as tax, k.description as paytype,
            l.description as empstatus, m.description as p_medium
            FROM employee_record a
            LEFT JOIN contract b ON a.id = b.contract_emp_id
            LEFT JOIN work_schedule c ON b.work_sched_id = c.id
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
            WHERE b.id = ?";
    $data = array($cid);
    return $this->db->query($sql,$data);
  }

  public function getDept(){
		$sql = "SELECT * FROM department WHERE enabled = 1";
		return $this->db->query($sql);
	}

  public function getSubDept($deptId){
		$sql = "SELECT * FROM subdept WHERE departmentid = ? AND enabled = 1";
		$data = array($deptId);
		return $this->db->query($sql, $data);
	}

  public function getPos($id = false){
    if($id == false){
      $sql = "SELECT *,a.description as position, b.description as department FROM position a LEFT JOIN department b ON a.deptId = b.departmentid WHERE a.enabled = 1 AND deptId > 0";
      return $this->db->query($sql);
    }
  }
}
