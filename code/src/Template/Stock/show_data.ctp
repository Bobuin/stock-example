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

<div class="row">
    <div class="columns large-12">
        <table cellpadding="0" cellspacing="0">
            <thead>
            <tr>
                <th scope="col"><?= $this->Paginator->sort('id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('wallet_id') ?></th>
                <th scope="col"><?= $this->Paginator->sort('amount') ?></th>
                <th scope="col"><?= $this->Paginator->sort('base_amount') ?></th>
                <th scope="col"><?= $this->Paginator->sort('created') ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($stockData as $transaction): ?>
                <tr>
                    <td><?= $this->Number->format($stockData->id) ?></td>
                    <td><?= $this->Number->format($stockData->wallet_id) ?></td>
                    <td><?= $this->Number->format($stockData->amount) ?></td>
                    <td><?= $this->Number->format($stockData->base_amount) ?></td>
                    <td><?= h($stockData->created) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="paginator">
            <ul class="pagination">
                <?= $this->Paginator->first('<< ' . __('first')) ?>
                <?= $this->Paginator->prev('< ' . __('previous')) ?>
                <?= $this->Paginator->numbers() ?>
                <?= $this->Paginator->next(__('next') . ' >') ?>
                <?= $this->Paginator->last(__('last') . ' >>') ?>
            </ul>
            <p><?= $this->Paginator->counter(['format' => __('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')]) ?></p>
        </div>
    </div>
</div>
