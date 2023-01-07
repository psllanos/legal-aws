@extends('layouts.admin')

@section('title')
    {{ __('Dashboard')}}
@endsection

 @push('head')
    @if($calenderTasks)
      <link rel="stylesheet" href="{{asset('custom/libs/fullcalendar/dist/fullcalendar.min.css')}}">
     @endif
@endpush

@push('script')
    <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
    @if($calenderTasks)
         <script src="{{ asset('custom/libs/fullcalendar/dist/fullcalendar.min.js') }}"></script>
    @endif
    <script>

        @if($calenderTasks)
            (function () {
                var etitle;
                var etype;
                var etypeclass;
                var calendar = new FullCalendar.Calendar(document.getElementById('event_calendar'), {
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'timeGridDay,timeGridWeek,dayGridMonth'
                    },
                    buttonText: {
                    timeGridDay: "{{__('Day')}}",
                    timeGridWeek: "{{__('Week')}}",
                    dayGridMonth: "{{__('Month')}}"
                    },
                    themeSystem: 'bootstrap',
                    initialDate: '{{$transdate}}',
                    slotDuration: '00:10:00',
                    navLinks: true,
                    droppable: true,
                    selectable: true,
                    selectMirror: true,
                    editable: true,
                    dayMaxEvents: true,
                    handleWindowResize: true,
                    events: {!! json_encode($calenderTasks) !!},
                });
                calendar.render();
            })();

        @endif



        $(document).on('click', '.fc-daygrid-event', function (e) {
            if (!$(this).hasClass('deal')) {
                e.preventDefault();
                var event = $(this);
                var title = $(this).find('.fc-event-title-container .fc-event-title').html();
                var size = 'md';
                var url = $(this).attr('href');
                $("#commonModal .modal-title").html(title);
                $("#commonModal .modal-dialog").addClass('modal-' + size);

                $.ajax({
                    url: url,
                    success: function (data) {
                        $('#commonModal .body').html(data);
                        $("#commonModal").modal('show');
                    },
                    error: function (data) {
                        data = data.responseJSON;
                        show_toastr('Error', data.error, 'error')
                    }
                });
            }
        });
    </script>



<script>
    (function() {
          @if(!empty($chartData['date']))
          var options = {
              chart: {
                  height: 100,
                  type: 'area',
                  toolbar: {
                      show: false,
                  },
              },
              dataLabels: {
                  enabled: false
              },
              stroke: {
                  width: 2,
                  curve: 'smooth'
              },

              series: [{
                  name: "{{__('Invoice')}}",
                  data:{!! json_encode($chartData['invoice']) !!}
              }, {
                  name: "{{__('Payment')}}",
                  data:{!! json_encode($chartData['payment']) !!}
              }],

              xaxis: {
                  categories:{!! json_encode($chartData['date']) !!},
                  // title: {
                  //     text: 'Last 15 days'
                  // }
              },
              colors: ['#6fd943','#2633cb'],

              grid: {
                  strokeDashArray: 4,
              },
              legend: {
                  show: false,
              },
              yaxis: {
                  tickAmount: 3,
              }

          };
          @else
          var options = {
              chart: {
                  height: 250,
                  type: 'area',
                  toolbar: {
                      show: false,
                  },
              },
              dataLabels: {
                  enabled: false
              },
              stroke: {
                  width: 2,
                  curve: 'smooth'
              },

              series: [{
                  name: "{{__('Order')}}",
                  data:{!! json_encode($chartData['data']) !!}
              }],

              xaxis: {
                  categories:{!! json_encode($chartData['label']) !!},
                  // title: {
                  //     text: 'Last 15 days'
                  // }
              },
              colors: ['#6fd943','#2633cb'],

              grid: {
                  strokeDashArray: 4,
              },
              legend: {
                  show: false,
              },
              yaxis: {
                  tickAmount: 6,
              }

          };
          @endif
          var chart = new ApexCharts(document.querySelector("#myChart"), options);
          chart.render();
      })();
  </script>

<script>
    (function() {
        @if(!empty($chartcall['date']))
          var options = {
              chart: {
                  height: 260,
                  type: 'area',
                  toolbar: {
                      show: false,
                  },
              },
              dataLabels: {
                  enabled: false
              },
              stroke: {
                  width: 2,
                  curve: 'smooth'
              },

              series: [{
                  name: "{{__('Deal calls by day')}}",
                  data:{!! json_encode($chartcall['dealcall']) !!}
              }, ],

              xaxis: {
                  categories:{!! json_encode($chartcall['date']) !!},

              },
              colors: ['#6fd943','#2633cb'],

              grid: {
                  strokeDashArray: 4,
              },
              legend: {
                  show: false,
              },
              yaxis: {
                  tickAmount: 3,
              }

          };

          @endif
          var chart = new ApexCharts(document.querySelector("#callchart"), options);
          chart.render();
      })();
  </script>

<script>
    (function() {
        @if(!empty($dealdata['date']))
          var options = {
              chart: {
                  height: 140,
                  type: 'area',
                  toolbar: {
                      show: false,
                  },
              },
              dataLabels: {
                  enabled: false
              },
              stroke: {
                  width: 2,
                  curve: 'smooth'
              },

              series: [{
                  name: "{{__('Won Deal by day')}}",
                  data:{!! json_encode($dealdata['deal']) !!}
              }, ],

              xaxis: {
                  categories:{!! json_encode($dealdata['date']) !!},

              },
              colors: ['#6fd943','#2633cb'],

              grid: {
                  strokeDashArray: 4,
              },
              legend: {
                  show: false,
              },
              yaxis: {
                  tickAmount: 3,
              }

          };

          @endif
          var chart = new ApexCharts(document.querySelector("#deal_data"), options);
          chart.render();
      })();
  </script>

<script>
    var WorkedHoursChart = (function () {
        var $chart = $('#deal_stage');

        function init($this) {
            var options = {
                chart: {
                    height: 265,
                    type: 'bar',
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    },
                    shadow: {
                        enabled: false,
                    },

                },
                plotOptions: {
            bar: {
                columnWidth: '30%',
                borderRadius: 10,
                dataLabels: {
                    position: 'top',
                },
            }
        },
                stroke: {
            show: true,
            width: 1,
            colors: ['#fff']
        },
                series: [{
                    name: 'Platform',
                    data: {!! json_encode($dealStageData) !!},
                }],
                xaxis: {
                    labels: {
                        // format: 'MMM',
                        style: {
                            colors: '#293240',
                            fontSize: '12px',
                            fontFamily: "sans-serif",
                            cssClass: 'apexcharts-xaxis-label',
                        },
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: true,
                        borderType: 'solid',
                        color: '#f2f2f2',
                        height: 6,
                        offsetX: 0,
                        offsetY: 0
                    },
                    title: {
                        text: 'Platform'
                    },
                    categories: {!! json_encode($dealStageName) !!},
                },
                yaxis: {
                    labels: {
                        style: {
                            color: '#f2f2f2',
                            fontSize: '12px',
                            fontFamily: "Open Sans",
                        },
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: true,
                        borderType: 'solid',
                        color: '#f2f2f2',
                        height: 6,
                        offsetX: 0,
                        offsetY: 0
                    }
                },
                fill: {
                    type: 'solid',
                    opacity: 1

                },
                markers: {
                    size: 4,
                    opacity: 0.7,
                    strokeColor: "#000",
                    strokeWidth: 3,
                    hover: {
                        size: 7,
                    }
                },
                grid: {
                    borderColor: '#f2f2f2',
                    strokeDashArray: 5,
                },
                dataLabels: {
                    enabled: false
                }
            }
            // Get data from data attributes
            var dataset = $this.data().dataset,
                labels = $this.data().labels,
                color = $this.data().color,
                height = $this.data().height,
                type = $this.data().type;

            // Inject synamic properties
            // options.colors = [
            //     PurposeStyle.colors.theme[color]
            // ];
            // options.markers.colors = [
            //     PurposeStyle.colors.theme[color]
            // ];
            // Init chart
            var chart = new ApexCharts($this[0], options);
            // Draw chart
            setTimeout(function () {
                chart.render();
            }, 300);
        }

        // Events
        if ($chart.length) {
            $chart.each(function () {
                init($(this));
            });
        }
    })();
</script>


@endpush


@section('content')
    <div class="row">
        @if(!empty($arrErr))
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                @if(!empty($arrErr['system']))
                    <div class="alert alert-danger text-xs">
                        {{ $arrErr['system'] }} {{ __('are required in') }} <a href="{{ route('settings') }}" class=""><u> {{ __('System Setting') }}</u></a>
                    </div>
                @endif
                @if(!empty($arrErr['user']))
                    <div class="alert alert-danger text-xs">
                        {{ $arrErr['user'] }} <a href="{{ route('users') }}" class=""><u>{{ __('here') }}</u></a>
                    </div>
                @endif
                @if(!empty($arrErr['role']))
                    <div class="alert alert-danger text-xs">
                        {{ $arrErr['role'] }} <a href="{{ route('roles.index') }}" class=""><u>{{ __('here') }}</u></a>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <div class="row">

        @if(\Auth::user()->type=="Super Admin")
            <div class="col-xxl-7">
                <div class="row">
                    @if(isset($arrCount['owner']))
                    <div class="col-4">
                        <div class="card dash-card">
                            <div class="card-body">
                                {{-- <div class="row align-items-center justify-content-between"> --}}
                                    {{-- <div class="col-auto mb-3 mb-sm-0"> --}}
                                        {{-- <div class="d-flex align-items-center"> --}}
                                            <div class="theme-avtar bg-primary">
                                                <i class="ti ti-school"></i>
                                            </div>
                                            @if(\Auth::user()->type == 'Super Admin')
                                            <p class="text-muted text-sm mt-4">{{__('Paid Users')}} : <span class="text-primary">  {{ number_format($arrCount['owner']['total']) }}</span></p>
                                            @endif
                                            {{-- <div class="ms-3"> --}}
                                                <p class="text-primary text-m mb-3">{{ __('Total Owner') }}</p>

                                            {{-- </div> --}}
                                        {{-- </div> --}}
                                    {{-- </div> --}}
                                    {{-- <div class="col-auto text-end"> --}}
                                        <h3 class="m-0 text-primary">{{ $arrCount['owner']['owner'] }}</h3>

                                    {{-- </div> --}}
                                {{-- </div> --}}
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(isset($arrCount['order']))
                    <div class="col-4">
                        <div class="card dash-card">
                            <div class="card-body">
                                {{-- <div class="row align-items-center justify-content-between"> --}}
                                    {{-- <div class="col-auto mb-3 mb-sm-0"> --}}
                                        {{-- <div class="d-flex align-items-center"> --}}
                                            <div class="theme-avtar bg-warning">
                                                <i class="ti ti-shopping-cart"></i>
                                            </div>
                                            @if(\Auth::user()->type == 'Super Admin')
                                            <p class="text-muted text-sm mt-4 ">{{__('Total Order Amount')}} :<span class="text-primary">  {{ number_format($arrCount['order']['total']) }}</span></p>
                                            @endif
                                            {{-- <div class="ms-3"> --}}
                                                <p class="text-primary text-m mb-3">{{ __('Total Order') }}</p>

                                            {{-- </div> --}}
                                        {{-- </div> --}}
                                    {{-- </div> --}}
                                    {{-- <div class="col-auto text-end"> --}}
                                        <h3 class="m-0 text-primary">{{ $arrCount['order']['order'] }}</h3>

                                    {{-- </div> --}}
                                {{-- </div> --}}
                            </div>
                        </div>
                    </div>
                @endif

                @if(isset($arrCount['plan']))
                <div class="col-4">
                    <div class="card dash-card">
                        <div class="card-body">
                            {{-- <div class="row align-items-center justify-content-between"> --}}
                                {{-- <div class="col-auto mb-3 mb-sm-0"> --}}
                                    {{-- <div class="d-flex align-items-center"> --}}
                                        <div class="theme-avtar bg-danger">
                                            <i class="ti ti-award"></i>
                                        </div>
                                        @if(\Auth::user()->type == 'Super Admin')
                                        <p class="text-muted text-sm mt-3">{{__('Most purchase plan')}} :<span class="text-success"> {{ ($arrCount['plan']['total']) ? $arrCount['plan']['total']->name : '-' }} </span></p>
                                        @endif
                                        {{-- <div class="ms-3"> --}}
                                            <p class="text-primary text-m mb-3">{{ __('Total Plan') }}</p>

                                        {{-- </div> --}}
                                    {{-- </div> --}}
                                {{-- </div> --}}
                                {{-- <div class="col-auto text-end"> --}}
                                    <h3 class="m-0 text-primary">{{ $arrCount['plan']['plan'] }}</h3>

                                {{-- </div> --}}
                            {{-- </div> --}}
                        </div>
                    </div>
                </div>
            @endif
                </div>
            </div>
        @else
            <div class="col-xxl-7">
                <div class="row">
                    @php
                    $class = '';
                    if(count($arrCount) < 4)
                    {
                        $class = 'col-lg-4 col-md-4';
                    }
                    else
                    {
                        $class = 'col-lg-3 col-md-3';
                    }
                    @endphp

                    @if(isset($arrCount['client']))
                         <div class="{{ $class }} col-6">
                            <div class="card dash1-card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-success">
                                        <i class="ti ti-user"></i>
                                    </div>
                                    <p class="text-muted text-m mt-4 mb-4">{{ __('Total Client') }}</p>
                                    <h3 class="mb-0">{{ $arrCount['client'] }}</h3>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($arrCount['user']))
                        <div class="{{ $class }} col-6">
                            <div class="card dash1-card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-info">
                                        <i class="ti ti-users"></i>
                                    </div>
                                    <p class="text-muted text-m mt-4 mb-4">{{ __('Total User') }}</p>
                                    <h3 class="mb-0">{{ $arrCount['user'] }}</h3>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($arrCount['deal']))
                        <div class="{{ $class }} col-6">
                            <div class="card dash1-card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-warning">
                                        <i class="ti ti-rocket"></i>
                                    </div>
                                    <p class="text-muted text-m mt-4 mb-4">{{ __('Total Deal') }}</p>
                                    <h3 class="mb-0">{{ $arrCount['deal'] }}</h3>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($arrCount['invoice']))
                        <div class="{{ $class }} col-6">
                            <div class="card dash1-card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-danger">
                                        <i class="ti ti-file-invoice"></i>
                                    </div>
                                    <p class="text-muted text-m mt-4 mb-4">{{ __('Total Invoice') }}</p>
                                    <h3 class="mb-0">{{ $arrCount['invoice'] }}</h3>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($arrCount['task']))
                        <div class="{{ $class }} col-6">
                            <div class="card dash1-card">
                                <div class="card-body">
                                    <div class="theme-avtar bg-danger">
                                        <i class="ti ti-subtask"></i>
                                    </div>
                                    <p class="text-muted text-m mt-4 mb-4">{{ __('Total Task') }}</p>
                                    <h3 class="mb-0">{{ $arrCount['task'] }}</h3>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>



                <div class="card top-card">
                    <div class="card-header">
                        <h5>{{ __('Recently created deals') }}</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{__('Deal Name')}}</th>
                                        <th>{{__('Status')}}</th>
                                        <th>{{__('Created At')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($deals))
                                        @foreach ($deals as $deal)
                                            <tr>
                                                <td>{{$deal->name}}</td>
                                                <td>{{$deal->stage->name}}</td>
                                                <td>{{$deal->created_at}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center">{{__('No data available in table')}}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>



                <div class="card top-card">
                    <div class="card-header">
                        <h5>{{ __('Recently modified deals') }}</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{__('Deal Name')}}</th>
                                        <th>{{__('Status')}}</th>
                                        <th>{{__('Updated At')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($deals))
                                        @foreach ($deals as $deal)
                                            <tr>
                                                <td>{{$deal->name}}</td>
                                                <td>{{$deal->stage->name}}</td>
                                                <td>{{$deal->updated_at}}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center">{{__('No data available in table')}}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>





                 @if($calenderTasks)
                <div class="card top-card">
                        <div class="card-header">
                            <h5>Calendar</h5>
                        </div>
                        <div class="card-body">
                            <div class="w-100" id='event_calendar'></div>
                        </div>
                    </div>

                @endif


        </div>

        @endif


        @if(!empty($chartData))

            <div class="col-xxl-5">

                <div class="card">
                    <div class="card-header ">
                        @if(\Auth::user()->type != 'Super Admin')
                        <h5>{{__('Invoice & Payment')}}</h5>
                        @else
                        <h5>{{__('Recent Order')}}</h5>
                        @endif
                    </div>
                    <div class="card-body p-2">
                        <div id="myChart" data-color="primary"  data-height="230"></div>
                    </div>
                </div>



                @if(!empty($chartcall))

                    <div class="card">
                        <div class="card-header ">
                            @if(\Auth::user()->type != 'Super Admin')
                            <h5>{{__('Deal calls by day')}}</h5>
                            @endif
                        </div>
                        <div class="card-body p-2">
                            <div id="callchart" data-color="primary"  data-height="230"></div>
                        </div>
                    </div>
                @endif

                @if(!empty($dealStageData))

                <div class="card">
                    <div class="card-header ">
                        @if(\Auth::user()->type != 'Super Admin')
                        <h5>{{__('Deals by stage')}}</h5>
                        @endif
                    </div>
                    <div class="card-body p-2">
                        <div id="deal_stage" data-color="primary"  data-height="230"></div>
                    </div>
                </div>
                @endif

                @if(!empty($dealdata))
                @if(\Auth::user()->type == 'Client')
                <div class="card">
                    <div class="card-header ">
                        @if(\Auth::user()->type != 'Super Admin')
                        <h5>{{__('Won Deals by day')}}</h5>
                        @endif
                    </div>
                    <div class="card-body p-2">
                        <div id="deal_data" data-color="primary"  data-height="230"></div>
                    </div>
                </div>
                @endif
                @endif



                @if(\Auth::user()->type != 'Super Admin')
                <div class="card top-card">
                    <div class="card-header">
                        <h5>{{ __('Top Due Payment') }}</h5>
                    </div>
                    <div class="card-body table-border-style">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>{{__('Title')}}</th>
                                        <th>{{__('Date')}}</th>
                                        @if(Auth::user()->type != 'Client')
                                            <th>@can('View Invoice'){{__('Action')}}@endcan</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(!empty($invoices))
                                        @foreach ($invoices as $invoice)
                                            <tr>
                                                <td>
                                                    <span class="number-id">{{ Auth::user()->invoiceNumberFormat($invoice->invoice_id) }}</span><br> {{__('Due Amount :')}} {{ Auth::user()->priceFormat($invoice->getDue()) }}
                                                </td>
                                                <td>{{ Auth::user()->dateFormat($invoice->issue_date) }}</td>
                                                @if(Auth::user()->type != 'Client')
                                                    <td>
                                                        @can('View Invoice')
                                                        <div class="action-btn bg-warning ms-2">
                                                            <a href="{{route('invoices.show',$invoice->id)}}" class="mx-3 btn btn-sm d-inline-flex align-items-center" data-toggle="tooltip" data-original-title="{{__('View')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('View')}}"><i class="ti ti-eye text-white"></i></a>
                                                        </div>
                                                        @endcan
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="3" class="text-center">{{__('No data available in table')}}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            </div>
        @endif

    </div>

@endsection
