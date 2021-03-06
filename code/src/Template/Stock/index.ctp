<?php
/**
 * @var View $this
 */

use Cake\View\View;

?>
<div class="row">
    <div class="columns large-6">
        <h2>Input params:</h2>
    </div>
</div>

<div class="row">
    <div class="columns large-6">
        <?php
        echo $this->Form->create(null, ['method' => 'post', 'valueSources' => ['data']]);


        if (!empty($errors['symbol'])) {
            foreach ($errors['symbol'] as $error) {
                echo '<div class="error"><label>Company Symbol: ' . $error . '</label></div>';
            }
        }
        echo $this->Form->control(
            'Company Symbol',
            [
                'required' => true,
                'type' => 'text',
                'name' => 'symbol',
                'value' => $this->request->getData('symbol'),
            ]
        );

        if (!empty($errors['start_date'])) {
            foreach ($errors['start_date'] as $error) {
                echo '<div class="error"><label>Start Date: ' . $error . '</label></div>';
            }
        }
        echo $this->Form->control(
            'Start Date',
            [
                'required' => true,
                'type' => 'text',
                'name' => 'start_date',
                'value' => $this->request->getData('start_date'),
            ]
        );


        if (!empty($errors['end_date'])) {
            foreach ($errors['end_date'] as $error) {
                echo '<div class="error"><label>End Date: ' . $error . '</label></div>';
            }
        }
        echo $this->Form->control('End Date',
            [
                'required' => true,
                'type' => 'text',
                'name' => 'end_date',
                'value' => $this->request->getData('end_date'),
            ]);

        if (!empty($errors['email'])) {
            foreach ($errors['email'] as $error) {
                echo '<div class="error"><label>Email: ' . $error . '</label></div>';
            }
        }
        echo $this->Form->control('email', [
            'required' => true,
            'type' => 'email',
            'name' => 'email',
            'value' => $this->request->getData('email'),
        ]);

        echo $this->Form->submit();

        echo $this->Form->end()
        ?>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        var maxValue = new Date();
        maxValue.setDate(maxValue.getDate() - 1);
        $("#start-date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        }).datepicker(
            "option", 'maxDate', maxValue
        ).bind("change", function () {
            var minValue = $(this).val();
            minValue = $.datepicker.parseDate("yy-mm-dd", minValue);
            minValue.setDate(minValue.getDate());
            $("#end-date").datepicker("option", "minDate", minValue);
        });
        $("#end-date").datepicker({
            dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        }).datepicker(
            "option", 'maxDate', maxValue
        );
    });
</script>
