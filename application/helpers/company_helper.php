<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function select_company_helper(){
	$ci=& get_instance();
    $ci->load->database(); 

    $sql = "SELECT * FROM pb_company_helper"; 
    $query = $ci->db->query($sql);
    return $query->row();
}

function company_name(){
    return select_company_helper()->company_name;
}

function company_initial(){
    return select_company_helper()->company_initial;
}

function company_logo(){
    return select_company_helper()->company_logo;
}

function company_logo_small(){
    return select_company_helper()->company_logo_small;
}

function company_address(){
	return select_company_helper()->company_address;
}

function company_website(){
	return select_company_helper()->company_website;
}

function company_phone(){
	return select_company_helper()->company_phone;
}

function company_email(){
	return select_company_helper()->company_email;
}	

function powered_by(){
	return select_company_helper()->powered_by;
}

function paypanda(){
	return select_company_helper()->paypanda_link;
}

//////////////////////////////
function url_folder(){
	return 'pandabooks';
}

function logo_image(){
	return 'pandabooks';
}