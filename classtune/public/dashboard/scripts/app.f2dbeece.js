'use strict';

/**
 * @ngdoc overview
 * @name minovateApp
 * @description
 * # minovateApp
 *
 * Main module of the application.
 */

  /*jshint -W079 */

var app = angular
  .module('minovateApp', [
    'ngAnimate',
    'ngCookies',
    'ngResource',
    'ngSanitize',
    'ngTouch',
    'ngMessages',
    'picardy.fontawesome',
    'ui.bootstrap',
    'ui.router',
    'ui.utils',
    'angular-loading-bar',
    'angular-momentjs',
    'FBAngular',
    'lazyModel',
    'toastr',
    'angularBootstrapNavTree',
    'oc.lazyLoad',
    'ui.select',
    'ui.tree',
    'textAngular',
    'colorpicker.module',
    'angularFileUpload',
    'ngImgCrop',
    'datatables',
    'datatables.bootstrap',
    'datatables.colreorder',
    'datatables.colvis',
    'datatables.tabletools',
    'datatables.scroller',
    'datatables.columnfilter',
    'ui.grid',
    'ui.grid.resizeColumns',
    'ui.grid.edit',
    'ui.grid.moveColumns',
    'ngTable',
    'smart-table',
    'angular-flot',
    'angular-rickshaw',
    'easypiechart',
    'uiGmapgoogle-maps',
    'ui.calendar',
    'ngTagsInput',
    'pascalprecht.translate',
    'ngMaterial',
    'localytics.directives',
    'leaflet-directive',
    'wu.masonry',
    'ipsum',
    'angular-intro',
    'dragularModule'
  ])
  .run(['$rootScope', '$state', '$stateParams', function($rootScope, $state, $stateParams) {
    $rootScope.$state = $state;
    $rootScope.$stateParams = $stateParams;
    $rootScope.$on('$stateChangeSuccess', function(event, toState) {

      event.targetScope.$watch('$viewContentLoaded', function () {

        angular.element('html, body, #content').animate({ scrollTop: 0 }, 200);

        setTimeout(function () {
          angular.element('#wrap').css('visibility','visible');

          if (!angular.element('.dropdown').hasClass('open')) {
            angular.element('.dropdown').find('>ul').slideUp();
          }
        }, 200);
      });
      $rootScope.containerClass = toState.containerClass;
    });
  }])

  .config(['uiSelectConfig', function (uiSelectConfig) {
        uiSelectConfig.theme = 'bootstrap';
        uiSelectConfig.resetSearchInput = true;
  }])

  //angular-language
  .config(['$translateProvider', function($translateProvider) {
    $translateProvider.useStaticFilesLoader({
      prefix: 'languages/',
      suffix: '.json'
    });
    $translateProvider.useLocalStorage();
    $translateProvider.preferredLanguage('en');
    $translateProvider.useSanitizeValueStrategy(null);
  }])

  .config(['$stateProvider', '$urlRouterProvider', function($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.otherwise('/app/' + school_domain);

    $stateProvider

    .state('app', {
      abstract: true,
      url: '/app',
      templateUrl: 'views/tmpl/app.html'
    })
    //dashboard
    .state('app.' + school_domain, {
      url: '/' + school_domain,
      controller: 'DashboardCtrl',
      templateUrl: 'views/tmpl/dashboard.html',
      resolve: {
        plugins: ['$ocLazyLoad', function($ocLazyLoad) {
          return $ocLazyLoad.load([
            'scripts/vendor/flot/jquery.flot.resize.js',
            'scripts/vendor/flot/jquery.flot.stack.js',
            'scripts/vendor/flot/jquery.flot.pie.js',
            'scripts/vendor/gaugejs/gauge.min.js',  
            'scripts/vendor/datatables/datatables.bootstrap.min.css'
          ]);
        }]
      }

    });
  }]);


'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:MainCtrl
 * @description
 * # MainCtrl
 * Controller of the minovateApp
 */
app
  .controller('MainCtrl', function ($scope, $http, $translate) {

    $scope.main = {
      title: school_name,
      settings: {
        navbarHeaderColor: 'scheme-lightred',
        sidebarColor: 'scheme-greensea',
        brandingColor: 'scheme-lightred',
        activeColor: 'greensea-scheme-color',
        headerFixed: true,
        asideFixed: true,
        rightbarShow: false
      }
    };

    $scope.ajaxFaker = function(){
//      $scope.data=[];
//      var url = 'http://www.filltext.com/?rows=10&fname={firstName}&lname={lastName}&delay=5&callback=JSON_CALLBACK';
//
//      $http.jsonp(url).success(function(data){
//        $scope.data=data;
//        angular.element('.tile.refreshing').removeClass('refreshing');
//      });
    };

    $scope.changeLanguage = function (langKey) {
      $translate.use(langKey);
      $scope.currentLanguage = langKey;
    };
    $scope.currentLanguage = $translate.proposedLanguage() || $translate.use();
  });

'use strict';
app.controller('CountController', function($scope, $http){
    var schoolSelectInterval = setInterval(function(){
           if ( school_id > 0 )
           {
                clearInterval(schoolSelectInterval);
                $scope.loadData(school_id);
           }
    }, 200);  
    
    $scope.loadData = function(school_id){
        $http({ 
            url: '/scripts/modules/school/counts.php',
            method: "PUT",
            data: { 'school_id' : school_id }
        })
        .then(function(response) {
            $scope.counts = response.data;
        },
        function(response) { // optional
                // failed
        }); 
    };
});

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:navCollapse
 * @description
 * # navCollapse
 * # sidebar navigation dropdown collapse
 */
app
  .directive('navCollapse', function ($timeout) {
    return {
      restrict: 'A',
      link: function($scope, $el) {

        $timeout(function(){

          var $dropdowns = $el.find('ul').parent('li'),
            $a = $dropdowns.children('a'),
            $notDropdowns = $el.children('li').not($dropdowns),
            $notDropdownsLinks = $notDropdowns.children('a'),
            app = angular.element('.appWrapper'),
            sidebar = angular.element('#sidebar'),
            controls = angular.element('#controls');

          $dropdowns.addClass('dropdown');

          var $submenus = $dropdowns.find('ul >.dropdown');
          $submenus.addClass('submenu');

          $a.append('<i class="fa fa-plus"></i>');

          $a.on('click', function(event) {
            if (app.hasClass('sidebar-sm') || app.hasClass('sidebar-xs') || app.hasClass('hz-menu')) {
              return false;
            }

            var $this = angular.element(this),
              $parent = $this.parent('li'),
              $openSubmenu = angular.element('.submenu.open');

            if (!$parent.hasClass('submenu')) {
              $dropdowns.not($parent).removeClass('open').find('ul').slideUp();
            }

            $openSubmenu.not($this.parents('.submenu')).removeClass('open').find('ul').slideUp();
            $parent.toggleClass('open').find('>ul').stop().slideToggle();
            event.preventDefault();
          });

          $dropdowns.on('mouseenter', function() {
            sidebar.addClass('dropdown-open');
            controls.addClass('dropdown-open');
          });

          $dropdowns.on('mouseleave', function() {
            sidebar.removeClass('dropdown-open');
            controls.removeClass('dropdown-open');
          });

          $notDropdownsLinks.on('click', function() {
            $dropdowns.removeClass('open').find('ul').slideUp();
          });

          var $activeDropdown = angular.element('.dropdown>ul>.active').parent();

          $activeDropdown.css('display', 'block');
        });

      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:slimScroll
 * @description
 * # slimScroll
 */
app
  .directive('slimscroll', function () {
    return {
      restrict: 'A',
      link: function ($scope, $elem, $attr) {
        var off = [];
        var option = {};

        var refresh = function () {
          if ($attr.slimscroll) {
            option = $scope.$eval($attr.slimscroll);
          } else if ($attr.slimscrollOption) {
            option = $scope.$eval($attr.slimscrollOption);
          }

          angular.element($elem).slimScroll({ destroy: true });

          angular.element($elem).slimScroll(option);
        };

        var registerWatch = function () {
          if ($attr.slimscroll && !option.noWatch) {
            off.push($scope.$watchCollection($attr.slimscroll, refresh));
          }

          if ($attr.slimscrollWatch) {
            off.push($scope.$watchCollection($attr.slimscrollWatch, refresh));
          }

          if ($attr.slimscrolllistento) {
            off.push($scope.$on($attr.slimscrolllistento, refresh));
          }
        };

        var destructor = function () {
          angular.element($elem).slimScroll({ destroy: true });
          off.forEach(function (unbind) {
            unbind();
          });
          off = null;
        };

        off.push($scope.$on('$destroy', destructor));

        registerWatch();
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:sparkline
 * @description
 * # sparkline
 */
app
  .directive('sparkline', [
  function() {
    return {
      restrict: 'A',
      scope: {
        data: '=',
        options: '='
      },
      link: function($scope, $el) {
        var data = $scope.data,
            options = $scope.options,
            chartResize,
            chartRedraw = function() {
              return $el.sparkline(data, options);
            };
        angular.element(window).resize(function() {
          clearTimeout(chartResize);
          chartResize = setTimeout(chartRedraw, 200);
        });
        return chartRedraw();
      }
    };
  }
]);

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:DashboardCtrl
 * @description
 * # DashboardCtrl
 * Controller of the minovateApp
 */
app
.controller('DashboardCtrl', function($scope,$http){
    var schoolSelectInterval = setInterval(function(){
        if ( school_id > 0 )
        {
            clearInterval(schoolSelectInterval);
            $scope.scroll = 0;  
            $scope.page = {
              title: school_name,
              subtitle: 'Dashboard'
            };

            $scope.displayQuickLink = false;
        }
     }, 200);  
})

.controller('UserCtrl', function($scope,$http){
    var schoolSelectInterval = setInterval(function(){
        if ( admin_username.length > 0 )
        {
            clearInterval(schoolSelectInterval);
            $scope.admin_profile_image = classtune_server + "/images/icons/user_new.png";
            $scope.admin_profile_link = classtune_server + "/user/profile/" + admin_username;
            $scope.admin_logout_link = classtune_server + "/user/logout/";
            $scope.admin_name = admin_name;  
        }
     }, 200);  
})

.controller('NotificationCtrl', function($scope,$http){
    var schoolSelectInterval = setInterval(function(){
        if ( school_id.length > 0 && classtune_server.length > 0 && username.length > 0 && token.length > 0 )
        {
            clearInterval(schoolSelectInterval);
            try
            {
                var xhr = new XMLHttpRequest();
                
                xhr.onreadystatechange = function(evt)
                {
                   if (xhr.readyState==4)
                    {
                        var xml = evt.target.responseText;
                        var parser = new DOMParser();
                        var xmlDoc = parser.parseFromString(xml,"text/xml");
                        $scope.count = xmlDoc.getElementsByTagName("count")[0].childNodes[0].nodeValue;
                        $scope.reminders = [];
                        $scope.reminders_link_all = classtune_server + "/reminder";
                        $scope.reminders_link = classtune_server + "/reminder/view_reminder?id2=";
                        if ( $scope.count > 0 )
                        {
                            try
                            {
                                var xhr_new = new XMLHttpRequest();

                                xhr_new.onreadystatechange = function(evt)
                                {
                                   if (xhr_new.readyState==4)
                                    {
                                        var xml = evt.target.responseText;
                                        var parser = new DOMParser();
                                        var xmlDoc = parser.parseFromString(xml,"text/xml");
                                        var reminders = [];
                                        var xml_reminder_length = xmlDoc.getElementsByTagName("reminder").length;
                                        var j = 0;
                                        for( var i = 0; i<xml_reminder_length; i++ )
                                        {
                                            var tmp = {
                                                id          : xmlDoc.getElementsByTagName("reminder_id")[i].childNodes[0].nodeValue,
                                                subject     : xmlDoc.getElementsByTagName("subject")[i].childNodes[0].nodeValue,
                                                created_at  : xmlDoc.getElementsByTagName("created_at")[i].childNodes[0].nodeValue
                                            };
                                            reminders[j] = tmp;
                                            j++;
                                        }
                                        $scope.reminders = reminders;
                                    }
                                }
                                xhr_new.open('GET', classtune_server+"/api/reminders/collections/"+username);
                                xhr_new.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                                xhr_new.setRequestHeader('Authorization', 'Token token="'+token+'"');
                                xhr_new.send();
                            }
                            catch(err)
                            {
                                alert(err.message);
                            }
                        }
                    }
                }
                xhr.open('GET', classtune_server+"/api/reminders/count/"+username);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('Authorization', 'Token token="'+token+'"');
                xhr.send();
            }
            catch(err)
            {
                alert(err.message);
            }
        }
     }, 200);  
})

.controller('EmployeeStatisticsCtrl', function ($scope, $templateRequest, $sce, $compile, $http) {
    
    var schoolSelectInterval = setInterval(function(){
           if ( school_id > 0 )
           {
                clearInterval(schoolSelectInterval);
                $scope.loadData(school_id);
           }
    }, 200);          
              
    $scope.oneAtATime = true;
    
    $scope.status = {
        isOpen: []
    };
    
    var openArr = $scope.status.isOpen;
    openArr[0] = true;
    
    $scope.donutData = [];
    
    $scope.loadData = function(school_id){
        $http({ 
            url: '/scripts/modules/school/courses.php',
            method: "PUT",
            data: { 'school_id' : school_id, 'school_domain' : school_domain }
        })
        .then(function(response) {
            $scope.courses = response.data;
            console.log($scope.courses);
            var templateUrl = $sce.getTrustedResourceUrl('/views/ajax/courses.html');
            $templateRequest(templateUrl).then(function(template) {
                // template is the HTML template as a string

                // Let's put it into an HTML element and parse any directives and expressions
                // in the code. (Note: This is just an example, modifying the DOM from within
                // a controller is considered bad style.)
                $compile(angular.element("#statistics").html(template).contents())($scope);
            }, function() {
                // An error has occurred
            });
        },
        function(response) { // optional
                // failed
        }); 
    };
  })
  
.controller('NoticesCtrl', function($scope, $sce, $http){
        var schoolSelectInterval = setInterval(function(){
           if ( school_id > 0 )
           {
                clearInterval(schoolSelectInterval);
                $http({ 
                    url: '/scripts/modules/school/notices.php',
                    method: "PUT",
                    data: { 'school_id' : school_id, 'school_domain' : school_domain }
                })
                .then(function(response){
                    $scope.noticeData = response.data;
                    $scope.server = classtune_server;
                });
           }
        }, 200);    
})
  
.controller("AttendanceTypeCtrl", function($scope, $templateRequest, $sce, $compile, DTOptionsBuilder, DTColumnBuilder, DTAjaxRenderer, $http) {    
        $scope.dataset = [{
          data: [],
          label: 'Present',
          points: {
            show: true,
            radius: 4
          },
          splines: {
            show: true,
            tension: 0.45,
            lineWidth: 4,
            fill: 0
          }
        }, {
          data: [],
          label: 'Student Count',
          bars: {
            show: true,
            barWidth: 0.5,
            lineWidth: 0,
            fillColor: { colors: [{ opacity: 0.3 }, { opacity: 0.8}] }
          }
        }];

        $scope.options = {
          colors: ['#e05d6f','#61c8b8'],
          series: {
            shadowSize: 0
          },
          legend: {
            backgroundOpacity: 0,
            margin: -7,
            position: 'ne',
            noColumns: 2
          },
          xaxis: {
            tickLength: 0,
            font: {
              color: '#fff'
            },
            position: 'bottom',
            margin: 10,
            ticks: []
          },
          yaxis: {
            tickLength: 0,
            font: {
              color: '#fff'
            }
          },
          grid: {
            borderWidth: {
              top: 0,
              right: 0,
              bottom: 1,
              left: 1
            },
            borderColor: 'rgba(255,255,255,.3)',
            margin:0,
            minBorderMargin:0,
            labelMargin:20,
            hoverable: true,
            clickable: true,
            mouseActiveRadius:6
          },
          tooltip: true,
          tooltipOpts: {
            content: '%s: %y',
            defaultTheme: false,
            shifts: {
              x: 0,
              y: 20
            }
          }
        };
    
        $scope.datasetAttendance = [{
          data: [],
          label: 'Present'
        }, {
          data: [],
          label: 'Absent'
        }, {
          data: [],
          label: 'Late'
        }];

        $scope.optionsAttendance = {
          series: {
            shadowSize: 0
          },
          legend: {
            backgroundOpacity: 0,
            margin: -15,
            position: 'ne',
            noColumns: 3
          },
          bars: {
            show: true,
            fill: true,
            lineWidth: 0,
            fillColor: {
              colors: [{ opacity:0.6 }, { opacity:0.8}]
            },
            order: 1, // order bars
            colors: ['#428bca','#d9534f','#A40778']
          },
          xaxis: {
            tickLength: 0,
            font: {
              color: '#fff'
            },
            position: 'bottom',
            ticks: []
          },
          yaxis: {
            font: {
              color: '#ccc'
            }
          },
          grid: {
            hoverable: true,
            clickable: true,
            borderWidth: 0,
            color: '#ccc'
          },
          tooltip: true
        };  

        var valuesJson = [{
                'key': 1,
                'value': 'Student'
            }, {
                'key': 2,
                'value': 'Employee'
            }
        ];
    
        $scope.values = valuesJson;
        $scope.values.selected = $scope.values[0];
        $scope.typeAttendance = $scope.values.selected.value;

        $scope.loadAttendaceGraph = function(item, school_id){
            $scope.attendace_type = item.value;

            var templateUrl = $sce.getTrustedResourceUrl('/views/ajax/attendance.html');
            $templateRequest(templateUrl).then(function(template) {
                //angular.element("#attendance_table").html(template);
                $scope.dtOptions = DTOptionsBuilder.newOptions()
                                    .withOption('ajax', {
                                             url: '/scripts/modules/school/attendance.php',
                                             type: 'GET',
                                             data: {school_id: school_id, type_attendance: item.key }
                                     })
                                     .withOption('responsive', true)
                                     .withOption('autoWidth', true)
                                     .withOption('processing', true)
                                     .withOption('bLengthChange', false)
                                     .withOption('bFilter', false)
                                     .withDataProp('data')
                                     .withOption('scrollY', 650)
                                     .withScroller()
                                     .withOption('serverSide', true)
                                     .withBootstrap()
                                     .withOption('fnDrawCallback', function () { 
                                         //alert(angular.element( document.getElementsByClassName("attendance_chart"));
                                        angular.forEach(angular.element( document.getElementsByClassName("attendance_chart") ), function(obj, index) {
                                            var data = JSON.parse(obj.attributes['data'].value);//;
                                            var options = JSON.parse(obj.attributes['options'].value);
                                            angular.element(obj).sparkline(data, options);
                                        });

                                        $http({
                                            url: '/scripts/modules/school/attendace_details.php',
                                            method: "PUT",
                                            data: { 'school_id' : school_id, 'school_domain' : school_domain }
                                        })
                                        .then(function(response) {
                                            $scope.dataset[0].data = response.data.student_attendace;
                                            $scope.dataset[1].data = response.data.student_counts;
                                            if ( $scope.dataset[0].data == undefined )
                                            {
                                                $scope.dataset[0].data = [];
                                                $scope.dataset[1].data = [];
                                                $scope.options.xaxis.ticks = [];
                                            }
                                            else if ( $scope.dataset[0].data == undefined )
                                            {
                                                $scope.dataset[0].data = [];
                                                $scope.dataset[1].data = [];
                                                $scope.options.xaxis.ticks = [];
                                            }
                                            else
                                            {
                                                $scope.options.xaxis.ticks = response.data.courses;
                                            }
                                            
                                            $scope.datasetAttendance[0].data = response.data.student_present;
                                            $scope.datasetAttendance[1].data = response.data.student_absent;
                                            $scope.datasetAttendance[2].data = response.data.student_late;
                                            if ( $scope.datasetAttendance[0].data == undefined )
                                            {
                                                $scope.datasetAttendance[0].data = [];
                                                $scope.datasetAttendance[1].data = [];
                                                $scope.datasetAttendance[2].data = [];
                                                $scope.optionsAttendance.xaxis.ticks = [];
                                            }
                                            else if ( $scope.datasetAttendance[1].data == undefined )
                                            {
                                                $scope.datasetAttendance[0].data = [];
                                                $scope.datasetAttendance[1].data = [];
                                                $scope.datasetAttendance[2].data = [];
                                                $scope.optionsAttendance.xaxis.ticks = [];
                                            }
                                            else if ( $scope.datasetAttendance[2].data == undefined )
                                            {
                                                $scope.datasetAttendance[0].data = [];
                                                $scope.datasetAttendance[1].data = [];
                                                $scope.datasetAttendance[2].data = [];
                                                $scope.optionsAttendance.xaxis.ticks = [];
                                            }
                                            else
                                            {
                                                $scope.optionsAttendance.xaxis.ticks = response.data.courses_attendance;
                                            }
                                            

                                            $scope.attendance_statistics = response.data.attendance_statistics;
                                            $scope.attendance_statistics_emp = response.data.attendance_statistics_emp;
                                            $scope.students_top = response.data.students_top;
                                            $scope.employees_top = response.data.employees_top;
                                            $scope.students_absent_today = response.data.students_absent_today;
                                            $scope.employees_absent_today = response.data.employees_absent_today;
                                            $scope.students_absent_month = response.data.students_absent_month;
                                            $scope.employees_absent_month = response.data.employees_absent_month;

                                            var templateUrl = $sce.getTrustedResourceUrl('/views/ajax/attendance_statistics.html');
                                            $templateRequest(templateUrl).then(function(template) {
                                                $compile(angular.element("#attendance_statistics").html(template).contents())($scope);
                                            }, function() {
                                                // An error has occurred
                                            });

                                            var templateUrl = $sce.getTrustedResourceUrl('/views/ajax/attendance_statistics_employee.html');
                                            $templateRequest(templateUrl).then(function(template) {
                                                $compile(angular.element("#attendance_statistics_employee").html(template).contents())($scope);
                                            }, function() {
                                                // An error has occurred
                                            });

                                            var templateUrl = $sce.getTrustedResourceUrl('/views/ajax/absent_students.html');
                                            $templateRequest(templateUrl).then(function(template) {
                                                $compile(angular.element("#absent_students").html(template).contents())($scope);
                                            }, function() {
                                                // An error has occurred
                                            });

                                            var templateUrl = $sce.getTrustedResourceUrl('/views/ajax/absent_employees.html');
                                            $templateRequest(templateUrl).then(function(template) {
                                                $compile(angular.element("#absent_employees").html(template).contents())($scope);
                                            }, function() {
                                                // An error has occurred
                                            });

                                            var templateUrl = $sce.getTrustedResourceUrl('/views/ajax/absent_students_month.html');
                                            $templateRequest(templateUrl).then(function(template) {
                                                $compile(angular.element("#absent_students_month").html(template).contents())($scope);
                                            }, function() {
                                                // An error has occurred
                                            });

                                            var templateUrl = $sce.getTrustedResourceUrl('/views/ajax/absent_employees_month.html');
                                            $templateRequest(templateUrl).then(function(template) {
                                                $compile(angular.element("#absent_employees_month").html(template).contents())($scope);
                                            }, function() {
                                                // An error has occurred
                                            });

                                            var templateUrl = $sce.getTrustedResourceUrl('/views/ajax/top_students.html');
                                            $templateRequest(templateUrl).then(function(template) {
                                                $compile(angular.element("#top_students").html(template).contents())($scope);
                                            }, function() {
                                                // An error has occurred
                                            });

                                            var templateUrl = $sce.getTrustedResourceUrl('/views/ajax/top_employees.html');
                                            $templateRequest(templateUrl).then(function(template) {
                                                $compile(angular.element("#top_employees").html(template).contents())($scope);
                                            }, function() {
                                                // An error has occurred
                                            });
                                        }, 
                                        function(response) { // optional
                                                // failed
                                        });
                                      })
                                     .withPaginationType('full_numbers');

               $scope.dtColumns = [
                        DTColumnBuilder.newColumn('serial').withTitle('Serial').notVisible(),
                        DTColumnBuilder.newColumn('name').withTitle('Class').withOption('width', '35%'),
                        DTColumnBuilder.newColumn('num_student').withTitle('Student Count').withOption('width', '20%'),
                        DTColumnBuilder.newColumn('percent').withTitle('% of Present').withOption('width', '25%').notSortable(),
                        DTColumnBuilder.newColumn('graph_attendance').withTitle('Attendance Graph').withOption('width', '35%').notSortable()
                    ];
                $compile(angular.element("#attendance_table").html(template).contents())($scope);
            });

            $scope.sizeOf = function(obj) {
                if ( obj == undefined || typeof(obj) == undefined || obj == null )
                {
                    return false;
                }
                else
                {
                    return Object.keys(obj).length;
                }
            };
        };


        var schoolSelectInterval = setInterval(function(){
               if ( school_id > 0 )
               {
                    clearInterval(schoolSelectInterval);
                    $scope.loadAttendaceGraph($scope.values.selected, school_id);
               }
        }, 200);
    
    })
  
.controller('HomeworkController', function($scope, DTOptionsBuilder, DTColumnBuilder, DTAjaxRenderer, $http){
        var valuesJson = [{
                'key': 1,
                'value': 'Homework',
                'table': 'assignments'
            }, {
                'key': 2,
                'value': 'ClassWork',
                'table': 'classworks'
            }
        ];
        $scope.dtOptions = [];
        $scope.dtColumns = [];

        var schoolSelectInterval = setInterval(function(){
               if ( school_id > 0 )
               {
                    clearInterval(schoolSelectInterval);
                    $scope.generateDatatable(school_id, valuesJson);
               }
        }, 200);     

        $scope.generateDatatable = function(school_id, valuesJson){
            $scope.options = valuesJson;
            angular.forEach(valuesJson, function(options, index){
                $scope.dtOptions[options.key] = DTOptionsBuilder.newOptions()
                                    .withOption('ajax', {
                                             url: '/scripts/modules/school/list.php',
                                             type: 'GET',
                                             data: {table_info: options.table, school_id : school_id, 'school_domain' : school_domain}
                                     })
                                     .withOption('responsive', true)
                                     .withOption('autoWidth', true)
                                     .withOption('processing', true)
                                     .withOption('bLengthChange', false)
                                     .withOption('bInfo', false)
                                     .withOption('bPaginate', false)
                                     .withOption('bFilter', false)
                                     .withDataProp('data')
                                     .withOption('scrollY', 400)
                                     .withScroller()
                                     .withOption('serverSide', true)
                                     .withBootstrap();

                $scope.dtColumns[options.key] = [
                    DTColumnBuilder.newColumn('id').withTitle('ID').notVisible(),
                    DTColumnBuilder.newColumn('name').withTitle('Subject Info').withOption('width', '65%'),
                    DTColumnBuilder.newColumn('info').withTitle(options.value).withOption('width', '35%').notSortable()
                ];
            });
        };

        $scope.dtInstance = {};

        $scope.callTabAjax = function(id){
            //$scope.dtInstance.rerender();
    };
})

.controller('CalendarWidgetCtrl', function ($scope) {

        $scope.today = function() {
          $scope.dt = new Date();
        };

        $scope.today();

        $scope.clear = function () {
          $scope.dt = null;
        };

        // Disable weekend selection
        $scope.disabled = function(date, mode) {
          return ( mode === 'day' && ( date.getDay() === 0 || date.getDay() === 6 ) );
        };

        $scope.toggleMin = function() {
          $scope.minDate = $scope.minDate ? null : new Date();
        };
        $scope.toggleMin();

        $scope.open = function($event) {
          $event.preventDefault();
          $event.stopPropagation();

          $scope.opened = true;
        };

        $scope.dateOptions = {
          formatYear: 'yy',
          startingDay: 1,
          'class': 'datepicker'
        };

        $scope.formats = ['dd-MMMM-yyyy', 'yyyy/MM/dd', 'dd.MM.yyyy', 'shortDate'];
        $scope.format = $scope.formats[0];
    })

.controller('NavCtrl', function ($scope, $http) {
    var schoolSelectInterval = setInterval(function(){
        $scope.oneAtATime = false;

        $scope.status = {
          isFirstOpen: true,
          isSecondOpen: true,
          isThirdOpen: true
        };
        if ( school_id > 0 )
        {
             clearInterval(schoolSelectInterval);
             $scope.server = classtune_server;
             $http({ 
                 url: '/scripts/modules/school/menus.php',
                 method: "PUT",
                 data: { 'school_id' : school_id, 'server' : classtune_server, 'dashboard_link' : dashboard_link, 'school_domain' : school_domain, 'username' : username, 'token': token }
             })
             .then(function(response){
                 $scope.menus = response.data;
             });
        }
     }, 200);
});

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:collapseSidebarSm
 * @description
 * # collapseSidebarSm
 */
app
  .directive('collapseSidebar', function ($rootScope) {
    return {
      restrict: 'A',
      link: function postLink(scope, element) {

        var app = angular.element('.appWrapper'),
            $window = angular.element(window),
            width = $window.width();

        var removeRipple = function() {
          angular.element('#sidebar').find('.ink').remove();
        };

        var collapse = function() {

          width = $window.width();

          if (width < 992) {
            app.addClass('sidebar-sm');
          } else {
            app.removeClass('sidebar-sm sidebar-xs');
          }

          if (width < 768) {
            app.removeClass('sidebar-sm').addClass('sidebar-xs');
          } else if (width > 992){
            app.removeClass('sidebar-sm sidebar-xs');
          } else {
            app.removeClass('sidebar-xs').addClass('sidebar-sm');
          }

          if (app.hasClass('sidebar-sm-forced')) {
            app.addClass('sidebar-sm');
          }

          if (app.hasClass('sidebar-xs-forced')) {
            app.addClass('sidebar-xs');
          }

        };

        collapse();

        $window.resize(function() {
          if(width !== $window.width()) {
            var t;
            clearTimeout(t);
            t = setTimeout(collapse, 300);
            removeRipple();
          }
        });

        element.on('click', function(e) {
          if (app.hasClass('sidebar-sm')) {
            app.removeClass('sidebar-sm').addClass('sidebar-xs');
          }
          else if (app.hasClass('sidebar-xs')) {
            app.removeClass('sidebar-xs');
          }
          else {
            app.addClass('sidebar-sm');
          }

          app.removeClass('sidebar-sm-forced sidebar-xs-forced');
          app.parent().removeClass('sidebar-sm sidebar-xs');
          removeRipple();
          e.preventDefault();
        });

      }
    };
  })
  
  .directive('scrollPosition', function($window) {
    return {
      scope: {
        scroll: '=scrollPosition'
      },
      link: function(scope, element, attrs) {
        var windowEl = angular.element($window);
        var handler = function() {
          scope.scroll = windowEl.scrollTop();
        }
        windowEl.on('scroll', scope.$apply.bind(scope, handler));
        handler();
      }
    };
})

  .directive('ripple', function () {
    return {
      restrict: 'A',
      link: function(scope, element) {
        var parent, ink, d, x, y;

        angular.element(element).find('>li>a').click(function(e){
          parent = angular.element(this).parent();

          if(parent.find('.ink').length === 0) {
            parent.prepend('<span class="ink"></span>');
          }

          ink = parent.find('.ink');
          //incase of quick double clicks stop the previous animation
          ink.removeClass('animate');

          //set size of .ink
          if(!ink.height() && !ink.width())
          {
            //use parent's width or height whichever is larger for the diameter to make a circle which can cover the entire element.
            d = Math.max(parent.outerWidth(), parent.outerHeight());
            ink.css({height: d, width: d});
          }

          //get click coordinates
          //logic = click coordinates relative to page - parent's position relative to page - half of self height/width to make it controllable from the center;
          x = e.pageX - parent.offset().left - ink.width()/2;
          y = e.pageY - parent.offset().top - ink.height()/2;

          //set the position and add class .animate
          ink.css({top: y+'px', left: x+'px'}).addClass('animate');

          setTimeout(function(){
            angular.element('.ink').remove();
          }, 600);
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:NavCtrl
 * @description
 * # NavCtrl
 * Controller of the minovateApp
 */
app
  

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:pageLoader
 * @description
 * # pageLoader
 */
app
  .directive('pageLoader', [
    '$timeout',
    function ($timeout) {
      return {
        restrict: 'AE',
        template: '<div class="dot1"></div><div class="dot2"></div>',
        link: function (scope, element) {
          element.addClass('hide');
          scope.$on('$stateChangeStart', function() {
            element.toggleClass('hide animate');
          });
          scope.$on('$stateChangeSuccess', function (event) {
            event.targetScope.$watch('$viewContentLoaded', function () {
              $timeout(function () {
                element.toggleClass('hide animate');
              }, 600);
            });
          });
        }
      };
    }
  ]);

'use strict';

/**
 * @ngdoc function
 * @name minovateApp.controller:DaterangepickerCtrl
 * @description
 * # DaterangepickerCtrl
 * Controller of the minovateApp
 */
app
  .controller('DaterangepickerCtrl', function ($scope, $moment) {
    $scope.startDate = $moment().subtract(10, 'days').format('MMMM D, YYYY');
    $scope.endDate = $moment().format('MMMM D, YYYY');
    //$scope.endDate = $moment().add(31, 'days').format('MMMM D, YYYY');
    $scope.rangeOptions = {
      ranges: {
        Today: [$moment(), $moment()],
        Yesterday: [$moment().subtract(1, 'days'), $moment().subtract(1, 'days')],
        'Last 7 Days': [$moment().subtract(6, 'days'), $moment()],
        'Last 30 Days': [$moment().subtract(29, 'days'), $moment()],
        'This Month': [$moment().startOf('month'), $moment().endOf('month')],
        'Last Month': [$moment().subtract(1, 'month').startOf('month'), $moment().subtract(1, 'month').endOf('month')]
      },
      opens: 'left',
      startDate: $moment().subtract(29, 'days'),
      endDate: $moment(),
      parentEl: '#content'
    };
  });
 

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:daterangepicker
 * @description
 * # daterangepicker
 */
app
  .directive('daterangepicker', function() {
    return {
      restrict: 'A',
      scope: {
        options: '=daterangepicker',
        start: '=dateBegin',
        end: '=dateEnd'
      },
      link: function(scope, element) {
        element.daterangepicker(scope.options, function(start, end) {
          scope.start = start.format('MMMM D, YYYY');
          scope.end = end.format('MMMM D, YYYY');
          scope.$apply();
        });
      }
    };
  });

/**
 * lazy-model directive
 *
 * AngularJS directive that works like `ng-model` but saves changes
 * only when form is submitted (otherwise changes are canceled)
 */

angular.module('lazyModel', [])

// lazy-model
.directive('lazyModel', ['$compile', '$timeout',
  function($compile, $timeout) {
    'use strict';
    return {
      restrict: 'A',
      priority: 500,
      terminal: true,
      require: ['lazyModel', '^form', '?^lazySubmit'],
      scope: true,
      controller: ['$scope', '$element', '$attrs', '$parse',
        function($scope, $element, $attrs, $parse) {
          if ($attrs.lazyModel === '') {
            throw '`lazy-model` should have a value.';
          }

          // getter and setter for original model
          var ngModelGet = $parse($attrs.lazyModel);
          var ngModelSet = ngModelGet.assign;

          // accept changes
          this.accept = function() {
            ngModelSet($scope.$parent, $scope.buffer);
          };

          // reset changes
          this.reset = function() {
            $scope.buffer = ngModelGet($scope.$parent);
          };

          // watch for original model change (and initialization also)
          $scope.$watch($attrs.lazyModel, angular.bind(this, function () {
            this.reset();
          }));
        }],
      compile: function compile(elem) {
        // set ng-model to buffer in directive scope (nested)
        elem.attr('ng-model', 'buffer');
        // remove lazy-model attribute to exclude recursion
        elem.removeAttr('lazy-model');
        // store compiled fn
        var compiled = $compile(elem);
        return {
          pre: function(scope) {
            // compile element with ng-model directive poining to `scope.buffer`   
            compiled(scope);
          },
          post: function postLink(scope, elem, attr, ctrls) {
            var lazyModelCtrl = ctrls[0];
            var formCtrl = ctrls[1];
            var lazySubmitCtrl = ctrls[2];
            // parentCtrl may be formCtrl or lazySubmitCtrl
            var parentCtrl = lazySubmitCtrl || formCtrl;

            // for the first time attach hooks
            if (parentCtrl.$lazyControls === undefined) {
              parentCtrl.$lazyControls = [];

              // find form element
              var form = elem.parent();
              while (form[0].tagName !== 'FORM') {
                form = form.parent();
              }

              // bind submit
              form.bind('submit', function() {
                // this submit handler must be called LAST after all other `submit` handlers
                // to get final value of formCtrl.$valid. The only way - is to call it in
                // the next tick via $timeout
                $timeout(function() {
                  if (formCtrl.$valid) {
                    // form valid - accept new values
                    for (var i = 0; i < parentCtrl.$lazyControls.length; i++) {
                      parentCtrl.$lazyControls[i].accept();
                    }

                    // call final hook `lazy-submit`
                    if (lazySubmitCtrl) {
                      lazySubmitCtrl.finalSubmit();
                    }
                  }
                });
              });

              // bind reset
              form.bind('reset', function(e) {
                e.preventDefault();
                $timeout(function() {
                  // reset changes
                  for (var i = 0; i < parentCtrl.$lazyControls.length; i++) {
                    parentCtrl.$lazyControls[i].reset();
                  }
                });
              });

            }

            // add to collection
            parentCtrl.$lazyControls.push(lazyModelCtrl);

            // remove from collection on destroy
            scope.$on('$destroy', function() {
              for (var i = parentCtrl.$lazyControls.length; i--;) {
                if (parentCtrl.$lazyControls[i] === lazyModelCtrl) {
                  parentCtrl.$lazyControls.splice(i, 1);
                }
              }
            });

          }
        };
      }
    };
  }
])

// lazy-submit
.directive('lazySubmit', function() {
    'use strict';
    return {
      restrict: 'A',
      require: ['lazySubmit', 'form'],
      controller: ['$element', '$attrs', '$scope', '$parse',
        function($element, $attrs, $scope, $parse) {
          var finalHook = $attrs.lazySubmit ? $parse($attrs.lazySubmit) : angular.noop;
          this.finalSubmit = function() {
            finalHook($scope);
          };
        }
      ]
    };
});

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:chartMorris
 * @description
 * # chartMorris
 * https://github.com/jasonshark/ng-morris/blob/master/src/ngMorris.js
 */
app
  .directive('morrisDonutChart', function(){
    return {
      restrict: 'A',
      scope: {
        donutData: '=',
        donutColors: '@'
      },
      link: function(scope, elem, attrs){
        var colors,
            morris;
        if (scope.donutColors === void 0 || scope.donutColors === '') {
          colors = null;
        } else {
          colors = JSON.parse(scope.donutColors);
        }

        scope.$watch('donutData', function(){
          if(scope.donutData){
            if(!morris) {
              morris = new Morris.Donut({
                element: elem,
                data: scope.donutData,
                colors: colors || ['#0B62A4', '#3980B5', '#679DC6', '#95BBD7', '#B0CCE1', '#095791', '#095085', '#083E67', '#052C48', '#042135'],
                resize: true
              });
            } else {
              morris.setData(scope.donutData);
            }
          }
        });
      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:wrapOwlcarousel
 * @description
 * # wrapOwlcarousel
 */
app
    .directive('wrapOwlcarousel', function () {
    return {
        restrict: 'E',
        link: function postLink(scope, element) {
          var options = scope.$eval(angular.element(element).attr('data-options'));
          angular.element(element).owlCarousel(options);
        }
    };
  })
  
    .directive("owlCarousel", function() {
        return {
            restrict: 'E',
            transclude: false,
            link: function (scope) {
                scope.initCarousel = function(element) {
                  // provide any default options you want
                    var defaultOptions = {
                    };
                    var customOptions = scope.$eval($(element).attr('data-options'));
                    // combine the two options objects
                    for(var key in customOptions) {
                        defaultOptions[key] = customOptions[key];
                    }
                    // init carousel
                    $(element).owlCarousel(defaultOptions);
                };
            }
        };
    })
    
    .directive('owlCarouselItem', [function() {
        return {
            restrict: 'A',
            transclude: false,
            link: function(scope, element) {
              // wait for the last item in the ng-repeat then call init
                if(scope.$last) {
                    scope.initCarousel(element.parent());
                }
            }
        };
    }]);

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:activeToggle
 * @description
 * # activeToggle
 */
app
  .directive('activeToggle', function () {
    return {
      restrict: 'A',
      link: function postLink(scope, element, attr) {

        element.on('click', function(){

          var target = angular.element(attr.target) || Array(element);

          if (element.hasClass('active')) {
            element.removeClass('active');
            target.removeClass('show');
          } else {
            element.addClass('active');
            target.addClass('show');
          }

        });

      }
    };
  });

'use strict';

/**
 * @ngdoc directive
 * @name minovateApp.directive:offcanvasSidebar
 * @description
 * # offcanvasSidebar
 */
app
  .directive('offcanvasSidebar', function () {
    return {
      restrict: 'A',
      link: function postLink(scope, element) {

        var app = angular.element('.appWrapper'),
            $window = angular.element(window),
            width = $window.width();

        element.on('click', function(e) {
          if (app.hasClass('offcanvas-opened')) {
            app.removeClass('offcanvas-opened');
          } else {
            app.addClass('offcanvas-opened');
          }
          e.preventDefault();
        });

      }
    };
  });

'use strict';

app
  .directive('scrollOnClick', function() {
  return {
    restrict: 'A',
    link: function(scope, $elm, attrs) {
      var idToScroll = attrs.href;
      
      $elm.on('click', function() {
        
        if ( attrs.email != undefined )  
        {
            var email = attrs.email;  
            if ( email.length > 0 || email != 'undefined' )
            {
                scope.callToAddMailAddress(email);  
            }
        }
        var $target;
        if (idToScroll) {
          $target = $(idToScroll);
        } else {
          $target = $elm;
        }
        
        if ( attrs.scrolltype == undefined )  
        {
            $("html,body").animate({scrollTop: $target.offset().top}, "slow", function(){
                if ( attrs.email != undefined ) 
                {
                    document.querySelector('#subject').focus();
                }
                //angular.element('text-angular[name=message-content]').find('*[id*=taText]').focus();
            });
        }
        else
        {
            if ( attrs.scrolltype == "tab_scroll" )  
            {
                angular.element(document.getElementsByClassName('custom-tab')).removeClass('active_tab_custom');
                angular.element(document.getElementsByClassName('custom-tab')).addClass('deactive_tab_custom');
                $elm.removeClass('deactive_tab_custom');
                $elm.addClass('active_tab_custom');
                if ( attrs.scrollattendance != undefined )
                {
                    if ( attrs.scrollattendance == "Student" )
                    {
                        var item = {
                                'key': 1,
                                'value': 'Student'
                        };
                    }
                    else if ( attrs.scrollattendance == "Employee" )
                    {
                        var item = {
                                'key': 2,
                                'value': 'Employee'
                        };
                    }
                    var uiScope = angular.element(document.getElementById(idToScroll.replace("#", ""))).scope();
                    uiScope.values.selected = item;
                    uiScope.loadAttendaceGraph(item);
                    if ( attrs.idtochange != undefined )
                    {
                        var idtochange = attrs.idtochange.split(",");
                        if ( attrs.targetheadclass != undefined )
                        {
                            var className = "bg-" + attrs.targetheadclass;
                            angular.forEach(idtochange, function(ids, index){
                                angular.element("#" + ids).removeClass('bg-drank');
                                angular.element("#" + ids).removeClass('bg-primary');
                                angular.element("#" + ids).addClass(className);
                            });
                        }
                    }
                }
                if ( attrs.targetheadclass != undefined )
                {
                    var parentclassName = "tab_border_" + attrs.targetheadclass;
                    $elm.parent().removeClass('tab_border_drank');
                    $elm.parent().removeClass('tab_border_primary');
                    $elm.parent().removeClass('tab_border_greensea');
                    $elm.parent().removeClass('tab_border_red');
                    $elm.parent().addClass(parentclassName);
                }
                $("html,body").animate({scrollTop: $target.offset().top - 200}, "slow", function(){
                    if ( attrs.email != undefined ) 
                    {
                        document.querySelector('#subject').focus();
                    }
                    //angular.element('text-angular[name=message-content]').find('*[id*=taText]').focus();
                });
            }
        }
      });
    }
  }
});




