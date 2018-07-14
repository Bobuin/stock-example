<?php
/**
 * @var \Cake\View\View                      $this
 * @var \Cake\Collection\CollectionInterface $stockData Array of Stock Data
 */

?>
<div class="row">
    <div class="columns large-12">
        <h2>Stock data</h2>
    </div>
</div>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {'packages': ['corechart']});
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = google.visualization.arrayToDataTable([
    <?php foreach ($stockData as $row): ?>
        ['<?= $row[0] . '\', ' . $row[3] . ', ' . $row[1] . ', ' . $row[4] . ', ' . $row[2]?>],
    <?php endforeach; ?>
        ], true);

        var options = {
            legend: 'none'
        };

        var chart = new google.visualization.CandlestickChart(document.getElementById('chart_div'));

        chart.draw(data, options);
    }
</script>
<div class="row">
    <div class="columns large-12">
        <div id="chart_div" style="width: 100%; height: 500px;"></div>
    </div>
</div>
<div class="row">
    <div class="columns large-12">
        <table cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th scope="col">Date</th>
                <th scope="col">Open</th>
                <th scope="col">High</th>
                <th scope="col">Low</th>
                <th scope="col">Close</th>
                <th scope="col">Volume</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stockData as $row): ?>
                <tr>
                    <td><?= h($row[0]) ?></td>
                    <td><?= $this->Number->format($row[1]) ?></td>
                    <td><?= $this->Number->format($row[2]) ?></td>
                    <td><?= $this->Number->format($row[3]) ?></td>
                    <td><?= $this->Number->format($row[4]) ?></td>
                    <td><?= $this->Number->format($row[5]) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
