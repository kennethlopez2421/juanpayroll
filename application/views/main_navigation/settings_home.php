<!-- 
data-num = for numbering of navigation
data-namecollapse = is for collapsible navigation
data-labelname = label name of this file in navigation
-->

<div class="content-inner" id="pageActive" data-num="8" data-namecollapse="" data-labelname="Settings">
    <div class="bc-icons-2 card mb-4">

        <ol class="breadcrumb mb-0 primary-bg px-4 py-3">
            <li class="breadcrumb-item"><a class="white-text" href="<?=base_url('Main_page/display_page/home/'.$token); ?>">Home</a><i class="fa fa-chevron-right mx-2 white-text" aria-hidden="true"></i></li>
            <li class="breadcrumb-item active">Settings</li>
        </ol>

    </div>
    <!-- Page Header-->
    <div class="row">
        <!-- <div class="col-sm-6">
            <a href="<?=base_url('Main_settings/area/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Area</div>
                    <small class="card-text text-black-50">New Area and option to manage each record.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/credit_term/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Credit Term</div>
                    <small class="card-text text-black-50">New supplier credit term and option to manage each record.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/delivery_vehicle/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Delivery Vehicle</div>
                    <small class="card-text text-black-50">New Delivery Vechicle and option to manage each record.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/employee/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Employee</div>
                    <small class="card-text text-black-50">New Employee and option to manage each record.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/employee_type/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Employee Type</div>
                    <small class="card-text text-black-50">New Employee type and option to manage each record.</small>
                </div>
            </a>

            <a href="" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Export Data</div>
                    <small class="card-text text-black-50">Export data in a table.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/franchise/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Franchise</div>
                    <small class="card-text text-black-50">New franchise package and option to manage each record.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/gl_accounts/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">GL Accounts</div>
                    <small class="card-text text-black-50">New GL Accounts and option to manage each record.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/inventory_category/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Inventory Category</div>
                    <small class="card-text text-black-50">New inventory category and option to manage each record.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/payment_option/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Payment Option</div>
                    <small class="card-text text-black-50">New payment type and option to manage each record.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/price_category/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Price Category</div>
                    <small class="card-text text-black-50">New inventory price category and option to manage each record.</small>
                </div>
            </a>
        </div>

        <div class="col-sm-6"> 
            <a href="<?=base_url('Main_settings/sales_area/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Sales Area</div>
                    <small class="card-text text-black-50">New Sales Area and option to manage each record.</small>
                </div>
            </a>

            <a href="" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Setup</div>
                    <small class="card-text text-black-50">System Setup.</small>
                </div>
            </a>

            <a href="" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">System User</div>
                    <small class="card-text text-black-50">System User Setup.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/ticket_status/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Ticket Status</div>
                    <small class="card-text text-black-50">New ticket status and option to manage each record.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/uom/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Unit of Measurement</div>
                    <small class="card-text text-black-50">New unit of measurement and option to manage each record.</small>
                </div>
            </a>

            <a href="" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Upload Data</div>
                    <small class="card-text text-black-50">Upload data (.sql).</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/user_role/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">User Role</div>
                    <small class="card-text text-black-50">New user roles and option to add/edit/delete each record.</small>
                </div>
            </a>

            <a href="" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Void Record</div>
                    <small class="card-text text-black-50">New void record.</small>
                </div>
            </a>

            <a href="<?=base_url('Main_settings/warehouse_location/'.$token);?>" class="w-100">
                <div class="card card-option card-hover white p-3 mb-3 w-100">
                    <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                    <div class="card-header-title font-weight-bold">Warehouse Location</div>
                    <small class="card-text text-black-50">New warehouse and option to manage each record.</small>
                </div>
            </a>
        </div> -->

        <?php 
        //071318
        $position_access = $this->session->userdata('get_position_access');
        if (!empty($position_access->access_content_nav)) {
            $access_content_nav = $position_access->access_content_nav;
        }else{
            $access_content_nav = "";
        }
        
        $arr_ = explode(', ', $access_content_nav);
        $labelname = "Settings"; //check the labelname in the top div
        $main_nav = $this->model->get_main_nav_id($labelname)->row();
        $cn = $this->model->get_content_navigation($main_nav->main_nav_id)->result();
        $cn2 = $cn;
        $cn3 = $cn;
        //071318
        ?>
        <!-- //071318 -->
        <?php
            $main_counter = 0;
            foreach ($cn3 as $cn3) {
                if (in_array($cn3->id, $arr_)){
                    $main_counter++;
                }
            }
            $total = $main_counter; 
            $total_devided =  ceil($total / 2);
            $counter = 0;
            $counter2 = 0;
        ?>
        <div class="col-sm-6"> 

            <?php foreach($cn as $cn){ ?>
                <?php if (in_array($cn->id, $arr_)){ ?>
                    <a href="<?=base_url($cn->cn_url.$token);?>" class="w-100">
                        <div class="card card-option card-hover white p-3 mb-3 w-100">
                            <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                            <div class="card-header-title font-weight-bold"><?=$cn->cn_name;?></div>
                            <small class="card-text text-black-50"><?=$cn->cn_description;?></small>
                        </div>
                    </a>
                    <?php
                    $counter++;
                    if ($total_devided == $counter) {
                        break;
                    }
                    ?>
                <?php } ?>
            <?php } ?>
        </div>
        <div class="col-sm-6">

            <?php foreach($cn2 as $cn2){ ?>

                <?php if (in_array($cn2->id, $arr_)){ ?>
                    <?php $counter2++; ?>
                        
                    <?php if ($counter < $counter2){ ?>
                        <a href="<?=base_url($cn2->cn_url.$token);?>" class="w-100">
                            <div class="card card-option card-hover white p-3 mb-3 w-100">
                                <div class="option-check"><i class="fa fa-hand-o-right fa-lg"></i></div>
                                <div class="card-header-title font-weight-bold"><?=$cn2->cn_name;?></div>
                                <small class="card-text text-black-50"><?=$cn2->cn_description;?></small>
                            </div>
                        </a>
                    <?php } ?>
                
                <?php } ?>
            <?php } ?>
        </div>
            
    </div>
    <?php $this->load->view('includes/footer'); ?>
