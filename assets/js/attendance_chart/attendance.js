$(function(){
  var base_url = $("body").data('base_url');
  var token = $('#token').val();
  var en_empid = $('#emp_idno').val();
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


  // console.log(lates);

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

  $(document).on('change', '#filter_by', function(){
    var filter_by = $(this).val();
    var total_whours = $('#total_whours').val();
    var total_bhours = $('#total_bhours').val();
    var sched_type = $('#sched_type').val();
    var worksched = $('#worksched').val();

    // console.log(worksched);
    // return;

    $.ajax({
      url: base_url+'attendance_chart/Attendance/get_attendance_json',
      type: 'post',
      data:{
        filter_by,
        total_whours,
        total_bhours,
        sched_type,
        worksched
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
                    text: 'Employee Attendance Report '+ data.month
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
                    text: 'Employee Attendance Report '+months[0]+"-"+months[2]
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
                              url: base_url+'attendance_chart/Attendance/get_attendance_breakdown_monthly',
                              type: 'post',
                              data:{
                                month: this.category,
                                total_whours,
                                total_bhours,
                                sched_type,
                                worksched
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
                                        text: 'Employee Attendance Report '+data.month
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
                    text: 'Employee Attendance Report '+months[0]+"-"+months[2]
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
                              url: base_url+'attendance_chart/Attendance/get_attendance_breakdown_monthly',
                              type: 'post',
                              data:{
                                month: this.category,
                                total_whours,
                                total_bhours,
                                sched_type,
                                worksched
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
                                        text: 'Employee Attendance Report '+data.month
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

  $(document).on('click', '#btn_back', function(){
    var filter_by = $('#filter_by').val();
    var total_whours = $('#total_whours').val();
    var total_bhours = $('#total_bhours').val();
    var sched_type = $('#sched_type').val();
    var worksched = $('#worksched').val();

    $.ajax({
      url: base_url+'attendance_chart/Attendance/get_attendance_json',
      type: 'post',
      data:{
        filter_by,
        total_whours,
        total_bhours,
        sched_type,
        worksched
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
                    text: 'Employee Attendance Report '+ data.month
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
                    text: 'Employee Attendance Report '+months[0]+"-"+months[2]
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
                              url: base_url+'attendance_chart/Attendance/get_attendance_breakdown_monthly',
                              type: 'post',
                              data:{
                                month: this.category,
                                total_whours,
                                total_bhours,
                                sched_type,
                                worksched
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
                                        text: 'Employee Attendance Report '+data.month
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
                    text: 'Employee Attendance Report '+months[0]+"-"+months[2]
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
                              url: base_url+'attendance_chart/Attendance/get_attendance_breakdown_monthly',
                              type: 'post',
                              data:{
                                month: this.category,
                                total_whours,
                                total_bhours,
                                sched_type,
                                worksched
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
                                        text: 'Employee Attendance Report '+data.month
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
