@extends('apm::app')

@section('content')

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Web Requests</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
            <form action="">
                <input type="hidden" name="type" value="{{ request('type') }}">
                <select name="group">
                    <option value="total-time" @if (request('group') === 'total-time') selected @endif>Total time</option>
                    <option value="sql-time" @if (request('group') === 'sql-time') selected @endif>SQL time</option>
                    <option value="sql-count" @if (request('group') === 'sql-count') selected @endif>SQL count</option>
                    <option value="longest" @if (request('group') === 'longest') selected @endif>Longest requests</option>
                    <option value="request-count" @if (request('group') === 'request-count') selected @endif>Request count</option>
                    <option value="p99-max" @if (request('group') === 'p99-max') selected @endif>Longest P99</option>
                </select>
                <input type="text" name="search" placeholder="Search" value="{{ request('search') }}">
                <button>Filter</button>
            </form>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <?php
            //            foreach ($data['count_by_hour'] as &$v) {
            //                $v = round($v, 2);
            //            }
            ?>
            @include('apm::partial.chart', [
                'title' => $group === 'request-count' ? 'Requests (count)' : 'Time (seconds)',
                'data' => $data['count_by_hour'],
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">

            <?php
            $block_data = [];
            $total_time = $data['top_total_count'];
            $title = '';
            foreach ($data['top_requests'] as $name => $value) {
                if (count($block_data) > 50) {
                    break;
                }
                if($group === 'sql-time'){
                    $value = explode('|', $value);
                    $percent = round($value[0] / $total_time * 100);
                    $avg = $value[0]/$value[1];
                    $left = sprintf('%s (%s)  (calls %s times) <br>  <i>avg : %s </i>',
                        $name,
                        \Done\LaravelAPM\Helpers\Helper::timeForHumans($value[0]),
                        $value[1],
                        \Done\LaravelAPM\Helpers\Helper::timeForHumans($avg));
                    $title = 'Time';
                }else if ($group === 'sql-count') {
                    $value = explode('|', $value);
                    $percent = round($value[0] / $total_time * 100);
                    $unique = round($value[0]/$value[1], 2);
                    $left = sprintf('%s : %s queries  (calls %s times) <br>  <i>%s / call </i>',
                        $name,
                        $value[0],
                        $value[1],
                        $unique);
                    $title = 'Count';

                } else {
                    $percent = round($value / $total_time * 100);
                    $left = $group === 'request-count' ? "$name ($value requests)" : $name . ' (' . \Done\LaravelAPM\Helpers\Helper::timeForHumans($value) . ')';
                    $title = $group === 'request-count' ? 'Requests' : 'Time';

                }
                $block_data[] = [
                    'left' => $left,
                    'right' => $percent . '%',
                    'percent' => $percent,
                ];
            }

            ?>

            @include('apm::partial.list', [
                'title' => $title,
                'data' => $block_data,
            ])

        </div>
        <div class="col-lg-6 mb-4">

            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Explanation</h6>
                </div>
                <div class="card-body">
                    @if ($group === 'total-time')
                        <p><b>Shows:</b> sum of page load times.</p>
                        <p><b>Purpose:</b> optimizing those pages will reduce server load the most.</p>
                        <p><b>Example:</b><br>
                            If page A received 1000 requests and each executed in 0.1 s, then in total it would take 100 * 0.1 = 100 seconds. <br>
                            If page B had 2 requests and each took 1 s, then 2 * 1 = 2 seconds.</p>
                        <p>In this case, to reduce the server load, optimizing page A would be the most advantageous. Optimizing page B will not bring a noticeable reduction in server load (max 2 seconds to win).</p>
                    @elseif ($group === 'sql-time')
                        <p><b>Shows:</b> pages that spent the longest time in SQL queries.</p>
                        <p><b>Purpose:</b> pinpoint pages which would benefit the most from optimizing SQL queries.</p>
                    @elseif ($group === 'sql-count')
                        <p><b>Shows:</b> Pages that received the most SQL queries.</p>
                        <p><b>Purpose:</b> pinpoint pages which would benefit the most from optimizing amount of SQL queries.</p>
                    @elseif ($group === 'longest')
                        <p><b>Shows:</b> single page that took the longest to load.</p>
                        <p><b>Purpose:</b> to find problematic pages</p>
                        <p>Some pages with low amount of data will load quickly. After some time when some tables gather a lot of rows, those pages might start to load slower. All the data is shown for a single page load.</p>
                    @elseif ($group === 'request-count')
                        Pages that received the most requests.
                    @elseif ($group === 'p99-max')
                        <p><b>Shows:</b> single page that took the longest to load from the first 99% fastest.</p>
                        <p><b>Purpose:</b> to find problematic pages</p>
                        <p>Some pages with low amount of data will load quickly. After some time when some tables gather a lot of rows, those pages might start to load slower. All the data is shown for a single page load.</p>
                    @else
                            <?php throw new \Exception('unknown group'); ?>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
