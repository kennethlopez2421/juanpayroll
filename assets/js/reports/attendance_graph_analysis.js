$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var month = $('#month').val();
  var days = $('#days').val().toString().split(',');
  var lates = $('#lates').val().toString().split(',');
  var undertimes = $('#undertimes').val().toString().split(',');
  var overbreaks = $('#overbreaks').val().toString().split(',');
  var total_mins = $('#total_mins').val().toString().split(',');
  var worksched = "";
  var total_whours = "";
  var total_bhours = "";
  var sched_type = "";

  var this_month = Highcharts.chart('container', {
      chart: {
          type: 'column'
      },
      title: {
          text: 'Employee Attendance Report '+ month
      },
      xAxis: {
          categories: days
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Total Man Hours (minutes)'
          }
      },
      tooltip: {
          pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y}</b> (mins)<br/>',
          shared: true
      },
      plotOptions: {
          column: {
              stacking: 'normal'
          }
      },
      series: [{
          name: 'Undertime',
          data: undertimes.map(Number)
      }, {
          name: 'Overbreak',
          data: overbreaks.map(Number)
      }, {
          name: 'Man Hours (minutes)',
          data: total_mins.map(Number)
      }, {
          name: 'Late',
          data: lates.map(Number)
      }]
  });

  $(document).on('change', '#dept', function(){
    var dept_id = $(this).val();
    // CHECK FOR AVAILABLE EMPLOYEE FOR DEPT
    if(dept_id != ""){
      $('#emp_id').removeAttr('disabled');
    }else{
      $('#emp_id').prop('disabled', true);
    }

    $.ajax({
      url: base_url+'Main/get_employee_by_dept',
      type: 'post',
      data:{dept_id: dept_id},
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');
        if(data.success == 1){
          $('#emp_id').html('<option value = "">------</option>');
          $.each(data.emp, function(i, val){
            $('#emp_id').append(`<option value = '${val['employee_idno']}' data-work_sched = '${val['work_sched']}' data-total_whours = '${val['total_whours']}' data-total_bhours = '${val['total_bhours']}' data-sched_type = '${val['sched_type']}'>${val['fullname']} (${val['employee_idno']})</option>`);
          });
        }else{
          notificationError('Error', data.message);
          $('#emp_id').html('<option value = "">------</option>');
        }
      }
    });
  });

  $(document).on('change', '#emp_id', function(){
    var selected = $(this).find('option:selected');
    worksched = selected.data('work_sched');
    total_whours = selected.data('total_whours');
    total_bhours = selected.data('total_bhours');
    sched_type = selected.data('sched_type');
  });

  $(document).on('click', '#btnSearchButton', function(){
    var error = 0;
    var errorMsg = "";
    var emp_id = $('#emp_id').val();
    var filter_by = $('#filter_by').val();

    // console.log(worksched);
    // return false;
    // console.log(filter_by);
    // return false;

    $('.rq').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.rq').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'reports/Attendance_graph_analysis/get_graph_analysis_json',
        type: 'post',
        data:{
          emp_idno: emp_id,
          filter_by: filter_by,
          worksched,
          total_whours,
          total_bhours,
          sched_type
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#filter_by').removeAttr('disabled');
            switch (filter_by) {
              case 'this_month':
                $('#btn_back').hide();
                days = data.days.toString().split(',');
                undertimes = data.undertimes.toString().split(',');
                overbreaks = data.overbreaks.toString().split(',');
                total_mins = data.total_mins.toString().split(',');
                lates = data.lates.toString().split(',');

                this_month.update({
                  title: {
                      text: data.fullname +' Attendance Report '+ data.month
                  },
                  xAxis:{
                    categories: days
                  },
                  series: [{
                      name: 'Undertime',
                      data: undertimes.map(Number)
                  }, {
                      name: 'Overbreak',
                      data: overbreaks.map(Number)
                  }, {
                      name: 'Man Hours (minutes)',
                      data: total_mins.map(Number)
                  }, {
                      name: 'Late',
                      data: lates.map(Number)
                  }]
                });
                break;
              case 'last_3months':
                var months = data.months.toString().split(',');
                var monthly_late = data.monthly_late.toString().split(',');
                var monthly_undertime = data.monthly_undertime.toString().split(',');
                var monthly_overbreak = data.monthly_overbreak.toString().split(',');
                var monthly_total_min = data.monthly_total_min.toString().split(',');
                var btn = $('#btn_back');

                this_month.update({
                  title: {
                      text: data.fullname +' Attendance Report '+months[0]+"-"+months[2]
                  },
                  xAxis: {
                    categories: months
                  },
                  plotOptions: {
                      column: {
                          stacking: 'normal'
                      },
                      series: {
                        cursor: 'pointer',
                        point: {
                          events: {
                            click: function() {

                              btn.show();
                              $.ajax({
                                url: base_url+'reports/Attendance_graph_analysis/get_attendance_breakdown_monthly',
                                type: 'post',
                                data:{
                                  month: this.category,
                                  emp_idno: emp_id,
                                  worksched,
                                  total_whours,
                                  total_bhours,
                                  sched_type
                                },
                                beforeSend: function(){
                                  $.LoadingOverlay('show');
                                },
                                success: function(data){
                                  $.LoadingOverlay('hide');
                                  if(data.success == 1){
                                    days = data.days.toString().split(',');
                                    undertimes = data.undertimes.toString().split(',');
                                    overbreaks = data.overbreaks.toString().split(',');
                                    total_mins = data.total_mins.toString().split(',');
                                    lates = data.lates.toString().split(',');

                                    this_month.update({
                                      title: {
                                          text: data.fullname +' Attendance Report '+data.month
                                      },
                                      xAxis:{
                                        categories: days
                                      },
                                      series: [{
                                          name: 'Undertime',
                                          data: undertimes.map(Number)
                                      }, {
                                          name: 'Overbreak',
                                          data: overbreaks.map(Number)
                                      }, {
                                          name: 'Man Hours (minutes)',
                                          data: total_mins.map(Number)
                                      }, {
                                          name: 'Late',
                                          data: lates.map(Number)
                                      }]
                                    });
                                  }else{

                                  }
                                }
                              });
                            }
                          }
                        }
                      }
                  },
                  series: [{
                      name: 'Undertime',
                      data: monthly_undertime.map(Number)
                  }, {
                      name: 'Overbreak',
                      data: monthly_overbreak.map(Number)
                  }, {
                      name: 'Man Hours (minutes)',
                      data: monthly_total_min.map(Number)
                  }, {
                      name: 'Late',
                      data: monthly_late.map(Number)
                  }]
                });
                break;
              case 'last_6months':
                var months = data.months.toString().split(',');
                var monthly_late = data.monthly_late.toString().split(',');
                var monthly_undertime = data.monthly_undertime.toString().split(',');
                var monthly_overbreak = data.monthly_overbreak.toString().split(',');
                var monthly_total_min = data.monthly_total_min.toString().split(',');
                var btn = $('#btn_back');
                this_month.update({
                  title: {
                      text: data.fullname +' Attendance Report '+months[0]+"-"+months[2]
                  },
                  xAxis: {
                    categories: months
                  },
                  plotOptions: {
                      column: {
                          stacking: 'normal'
                      },
                      series: {
                        cursor: 'pointer',
                        point: {
                          events: {
                            click: function() {
                              btn.show();
                              $.ajax({
                                url: base_url+'reports/Attendance_graph_analysis/get_attendance_breakdown_monthly',
                                type: 'post',
                                data:{
                                  month: this.category,
                                  emp_idno: emp_id,
                                  worksched,
                                  total_whours,
                                  total_bhours,
                                  sched_type
                                },
                                beforeSend: function(){
                                  $.LoadingOverlay('show');
                                },
                                success: function(data){
                                  $.LoadingOverlay('hide');
                                  if(data.success == 1){
                                    days = data.days.toString().split(',');
                                    undertimes = data.undertimes.toString().split(',');
                                    overbreaks = data.overbreaks.toString().split(',');
                                    total_mins = data.total_mins.toString().split(',');
                                    lates = data.lates.toString().split(',');

                                    this_month.update({
                                      title: {
                                          text: data.fullname +' Attendance Report '+data.month
                                      },
                                      xAxis:{
                                        categories: days
                                      },
                                      series: [{
                                          name: 'Undertime',
                                          data: undertimes.map(Number)
                                      }, {
                                          name: 'Overbreak',
                                          data: overbreaks.map(Number)
                                      }, {
                                          name: 'Man Hours (minutes)',
                                          data: total_mins.map(Number)
                                      }, {
                                          name: 'Late',
                                          data: lates.map(Number)
                                      }]
                                    });
                                  }else{

                                  }
                                }
                              });
                            }
                          }
                        }
                      }
                  },
                  series: [{
                      name: 'Undertime',
                      data: monthly_undertime.map(Number)
                  }, {
                      name: 'Overbreak',
                      data: monthly_overbreak.map(Number)
                  }, {
                      name: 'Man Hours (minutes)',
                      data: monthly_total_min.map(Number)
                  }, {
                      name: 'Late',
                      data: monthly_late.map(Number)
                  }]
                });
                break;
              default:

            }
          }else{
            $('#filter_by').prop('disabled', true);
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('change', '#filter_by', function(){
    var error = 0;
    var errorMsg = "";
    var emp_id = $('#emp_id').val();
    var filter_by = $('#filter_by').val();

    // console.log(emp_id);
    // console.log(filter_by);
    // return false;

    $('.rq').each(function(){
      if($(this).val() == ""){
        $(this).css("border", "1px solid #ef4131");
      }else{
        $(this).css("border", "1px solid gainsboro");
      }
    });

    $('.rq').each(function(){
      if($(this).val() == ""){
        $(this).focus();
        error = 1;
        errorMsg = "Please fill up all required fields.";
        return false;
      }
    });

    if(error == 0){
      $.ajax({
        url: base_url+'reports/Attendance_graph_analysis/get_graph_analysis_json',
        type: 'post',
        data:{
          emp_idno: emp_id,
          filter_by: filter_by,
          worksched,
          total_whours,
          total_bhours,
          sched_type
        },
        beforeSend: function(){
          $.LoadingOverlay('show');
        },
        success: function(data){
          $.LoadingOverlay('hide');
          if(data.success == 1){
            $('#filter_by').removeAttr('disabled');
            switch (filter_by) {
              case 'this_month':
                $('#btn_back').hide();
                days = data.days.toString().split(',');
                undertimes = data.undertimes.toString().split(',');
                overbreaks = data.overbreaks.toString().split(',');
                total_mins = data.total_mins.toString().split(',');
                lates = data.lates.toString().split(',');

                this_month.update({
                  title: {
                      text: data.fullname +' Attendance Report '+ data.month
                  },
                  xAxis:{
                    categories: days
                  },
                  series: [{
                      name: 'Undertime',
                      data: undertimes.map(Number)
                  }, {
                      name: 'Overbreak',
                      data: overbreaks.map(Number)
                  }, {
                      name: 'Man Hours (minutes)',
                      data: total_mins.map(Number)
                  }, {
                      name: 'Late',
                      data: lates.map(Number)
                  }]
                });
                break;
              case 'last_3months':
                var months = data.months.toString().split(',');
                var monthly_late = data.monthly_late.toString().split(',');
                var monthly_undertime = data.monthly_undertime.toString().split(',');
                var monthly_overbreak = data.monthly_overbreak.toString().split(',');
                var monthly_total_min = data.monthly_total_min.toString().split(',');
                var btn = $('#btn_back');

                this_month.update({
                  title: {
                      text: data.fullname +' Attendance Report '+months[0]+"-"+months[2]
                  },
                  xAxis: {
                    categories: months
                  },
                  plotOptions: {
                      column: {
                          stacking: 'normal'
                      },
                      series: {
                        cursor: 'pointer',
                        point: {
                          events: {
                            click: function() {

                              btn.show();
                              $.ajax({
                                url: base_url+'reports/Attendance_graph_analysis/get_attendance_breakdown_monthly',
                                type: 'post',
                                data:{
                                  month: this.category,
                                  emp_idno: emp_id,
                                  worksched,
                                  total_whours,
                                  total_bhours,
                                  sched_type
                                },
                                beforeSend: function(){
                                  $.LoadingOverlay('show');
                                },
                                success: function(data){
                                  $.LoadingOverlay('hide');
                                  if(data.success == 1){
                                    days = data.days.toString().split(',');
                                    undertimes = data.undertimes.toString().split(',');
                                    overbreaks = data.overbreaks.toString().split(',');
                                    total_mins = data.total_mins.toString().split(',');
                                    lates = data.lates.toString().split(',');

                                    this_month.update({
                                      title: {
                                          text: data.fullname +' Attendance Report '+data.month
                                      },
                                      xAxis:{
                                        categories: days
                                      },
                                      series: [{
                                          name: 'Undertime',
                                          data: undertimes.map(Number)
                                      }, {
                                          name: 'Overbreak',
                                          data: overbreaks.map(Number)
                                      }, {
                                          name: 'Man Hours (minutes)',
                                          data: total_mins.map(Number)
                                      }, {
                                          name: 'Late',
                                          data: lates.map(Number)
                                      }]
                                    });
                                  }else{

                                  }
                                }
                              });
                            }
                          }
                        }
                      }
                  },
                  series: [{
                      name: 'Undertime',
                      data: monthly_undertime.map(Number)
                  }, {
                      name: 'Overbreak',
                      data: monthly_overbreak.map(Number)
                  }, {
                      name: 'Man Hours (minutes)',
                      data: monthly_total_min.map(Number)
                  }, {
                      name: 'Late',
                      data: monthly_late.map(Number)
                  }]
                });
                break;
              case 'last_6months':
                var months = data.months.toString().split(',');
                var monthly_late = data.monthly_late.toString().split(',');
                var monthly_undertime = data.monthly_undertime.toString().split(',');
                var monthly_overbreak = data.monthly_overbreak.toString().split(',');
                var monthly_total_min = data.monthly_total_min.toString().split(',');
                var btn = $('#btn_back');
                this_month.update({
                  title: {
                      text: data.fullname +' Attendance Report '+months[0]+"-"+months[2]
                  },
                  xAxis: {
                    categories: months
                  },
                  plotOptions: {
                      column: {
                          stacking: 'normal'
                      },
                      series: {
                        cursor: 'pointer',
                        point: {
                          events: {
                            click: function() {
                              btn.show();
                              $.ajax({
                                url: base_url+'reports/Attendance_graph_analysis/get_attendance_breakdown_monthly',
                                type: 'post',
                                data:{
                                  month: this.category,
                                  emp_idno: emp_id,
                                  worksched,
                                  total_whours,
                                  total_bhours,
                                  sched_type
                                },
                                beforeSend: function(){
                                  $.LoadingOverlay('show');
                                },
                                success: function(data){
                                  $.LoadingOverlay('hide');
                                  if(data.success == 1){
                                    days = data.days.toString().split(',');
                                    undertimes = data.undertimes.toString().split(',');
                                    overbreaks = data.overbreaks.toString().split(',');
                                    total_mins = data.total_mins.toString().split(',');
                                    lates = data.lates.toString().split(',');

                                    this_month.update({
                                      title: {
                                          text: data.fullname +' Attendance Report '+data.month
                                      },
                                      xAxis:{
                                        categories: days
                                      },
                                      series: [{
                                          name: 'Undertime',
                                          data: undertimes.map(Number)
                                      }, {
                                          name: 'Overbreak',
                                          data: overbreaks.map(Number)
                                      }, {
                                          name: 'Man Hours (minutes)',
                                          data: total_mins.map(Number)
                                      }, {
                                          name: 'Late',
                                          data: lates.map(Number)
                                      }]
                                    });
                                  }else{

                                  }
                                }
                              });
                            }
                          }
                        }
                      }
                  },
                  series: [{
                      name: 'Undertime',
                      data: monthly_undertime.map(Number)
                  }, {
                      name: 'Overbreak',
                      data: monthly_overbreak.map(Number)
                  }, {
                      name: 'Man Hours (minutes)',
                      data: monthly_total_min.map(Number)
                  }, {
                      name: 'Late',
                      data: monthly_late.map(Number)
                  }]
                });
                break;
              default:

            }
          }else{
            $('#filter_by').prop('disabled', true);
            notificationError('Error', data.message);
          }
        }
      });
    }else{
      notificationError('Error', errorMsg);
    }
  });

  $(document).on('click', '#btn_back', function(){
    var filter_by = $('#filter_by').val();
    var emp_id = $('#emp_id').val();
    $.ajax({
      url: base_url+'reports/Attendance_graph_analysis/get_graph_analysis_json',
      type: 'post',
      data:{
        filter_by,
        emp_idno: emp_id,
        worksched,
        total_whours,
        total_bhours,
        sched_type
      },
      beforeSend: function(){
        $.LoadingOverlay('show');
      },
      success: function(data){
        $.LoadingOverlay('hide');

        if(data.success == 1){
          switch (filter_by) {
            case 'this_month':
              $('#btn_back').hide();
              days = data.days.toString().split(',');
              undertimes = data.undertimes.toString().split(',');
              overbreaks = data.overbreaks.toString().split(',');
              total_mins = data.total_mins.toString().split(',');
              lates = data.lates.toString().split(',');

              this_month.update({
                title: {
                    text: data.fullname +' Attendance Report '+ data.month
                },
                xAxis:{
                  categories: days
                },
                series: [{
                    name: 'Undertime',
                    data: undertimes.map(Number)
                }, {
                    name: 'Overbreak',
                    data: overbreaks.map(Number)
                }, {
                    name: 'Man Hours (minutes)',
                    data: total_mins.map(Number)
                }, {
                    name: 'Late',
                    data: lates.map(Number)
                }]
              });
              break;
            case 'last_3months':
              var months = data.months.toString().split(',');
              var monthly_late = data.monthly_late.toString().split(',');
              var monthly_undertime = data.monthly_undertime.toString().split(',');
              var monthly_overbreak = data.monthly_overbreak.toString().split(',');
              var monthly_total_min = data.monthly_total_min.toString().split(',');
              var btn = $('#btn_back');

              this_month.update({
                title: {
                    text: data.fullname +' Attendance Report '+months[0]+"-"+months[2]
                },
                xAxis: {
                  categories: months
                },
                plotOptions: {
                    column: {
                        stacking: 'normal'
                    },
                    series: {
                      cursor: 'pointer',
                      point: {
                        events: {
                          click: function() {

                            btn.show();
                            $.ajax({
                              url: base_url+'reports/Attendance_graph_analysis/get_attendance_breakdown_monthly',
                              type: 'post',
                              data:{
                                month: this.category,
                                emp_idno: emp_id,
                                worksched,
                                total_whours,
                                total_bhours,
                                sched_type
                              },
                              beforeSend: function(){
                                $.LoadingOverlay('show');
                              },
                              success: function(data){
                                $.LoadingOverlay('hide');
                                if(data.success == 1){
                                  days = data.days.toString().split(',');
                                  undertimes = data.undertimes.toString().split(',');
                                  overbreaks = data.overbreaks.toString().split(',');
                                  total_mins = data.total_mins.toString().split(',');
                                  lates = data.lates.toString().split(',');

                                  this_month.update({
                                    title: {
                                        text: data.fullname +' Attendance Report '+data.month
                                    },
                                    xAxis:{
                                      categories: days
                                    },
                                    series: [{
                                        name: 'Undertime',
                                        data: undertimes.map(Number)
                                    }, {
                                        name: 'Overbreak',
                                        data: overbreaks.map(Number)
                                    }, {
                                        name: 'Man Hours (minutes)',
                                        data: total_mins.map(Number)
                                    }, {
                                        name: 'Late',
                                        data: lates.map(Number)
                                    }]
                                  });
                                }else{

                                }
                              }
                            });
                          }
                        }
                      }
                    }
                },
                series: [{
                    name: 'Undertime',
                    data: monthly_undertime.map(Number)
                }, {
                    name: 'Overbreak',
                    data: monthly_overbreak.map(Number)
                }, {
                    name: 'Man Hours (minutes)',
                    data: monthly_total_min.map(Number)
                }, {
                    name: 'Late',
                    data: monthly_late.map(Number)
                }]
              });
              break;
            case 'last_6months':
              var months = data.months.toString().split(',');
              var monthly_late = data.monthly_late.toString().split(',');
              var monthly_undertime = data.monthly_undertime.toString().split(',');
              var monthly_overbreak = data.monthly_overbreak.toString().split(',');
              var monthly_total_min = data.monthly_total_min.toString().split(',');
              var btn = $('#btn_back');
              this_month.update({
                title: {
                    text: data.fullname +' Attendance Report '+months[0]+"-"+months[2]
                },
                xAxis: {
                  categories: months
                },
                plotOptions: {
                    column: {
                        stacking: 'normal'
                    },
                    series: {
                      cursor: 'pointer',
                      point: {
                        events: {
                          click: function() {
                            btn.show();
                            $.ajax({
                              url: base_url+'reports/Attendance_graph_analysis/get_attendance_breakdown_monthly',
                              type: 'post',
                              data:{
                                month: this.category,
                                emp_idno: emp_id,
                                worksched,
                                total_whours,
                                total_bhours,
                                sched_type
                              },
                              beforeSend: function(){
                                $.LoadingOverlay('show');
                              },
                              success: function(data){
                                $.LoadingOverlay('hide');
                                if(data.success == 1){
                                  days = data.days.toString().split(',');
                                  undertimes = data.undertimes.toString().split(',');
                                  overbreaks = data.overbreaks.toString().split(',');
                                  total_mins = data.total_mins.toString().split(',');
                                  lates = data.lates.toString().split(',');

                                  this_month.update({
                                    title: {
                                        text: data.fullname +' Attendance Report '+data.month
                                    },
                                    xAxis:{
                                      categories: days
                                    },
                                    series: [{
                                        name: 'Undertime',
                                        data: undertimes.map(Number)
                                    }, {
                                        name: 'Overbreak',
                                        data: overbreaks.map(Number)
                                    }, {
                                        name: 'Man Hours (minutes)',
                                        data: total_mins.map(Number)
                                    }, {
                                        name: 'Late',
                                        data: lates.map(Number)
                                    }]
                                  });
                                }else{

                                }
                              }
                            });
                          }
                        }
                      }
                    }
                },
                series: [{
                    name: 'Undertime',
                    data: monthly_undertime.map(Number)
                }, {
                    name: 'Overbreak',
                    data: monthly_overbreak.map(Number)
                }, {
                    name: 'Man Hours (minutes)',
                    data: monthly_total_min.map(Number)
                }, {
                    name: 'Late',
                    data: monthly_late.map(Number)
                }]
              });
              break;
            default:

          }

        }else{

        }
      }
    });
  });

});
