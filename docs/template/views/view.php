<?php
/**
 * @var \glsv\smssender\SmsLog $model
 */

use yii\bootstrap\Html;
use yii\bootstrap\ActiveForm;
use common\widgets\Alert;
use yii\widgets\DetailView;

$this->title = $model;
$this->params['headerTitle'] = 'SMS';
$this->params['breadcrumbs'][] = ['label' => 'SMS', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model;

$cssStyleOperation = $model->isSuccessOperation() ? 'bg-success' : ($model->isErrorOperation() ? 'bg-danger' : '');
$cssStyleMessage = $model->isDelivered() ? 'bg-success' : ($model->isFailed() ? 'bg-danger' : '');

?>

    <p>
        <?=
        Html::a('Все SMS', ['index'], ['class' => 'btn btn-primary2 btn-sm']);
        ?>
    </p>

<?= Alert::widget() ?>

<?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-6">
            <?php
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'phone',
                        'label' => 'Телефон',
                    ],
                    [
                        'attribute' => 'message',
                        'label' => 'Сообщение',
                    ],
                    [
                        'label' => 'Статус операции',
                        'value' => $model->getOperationStatusModel()->getLabel(),
                        'contentOptions' => ['class' => $cssStyleOperation]
                    ],
                    [
                        'label' => 'Статус сообщения',
                        'value' => $model->getMessageStatusModel()->getLabel(),
                        'contentOptions' => ['class' => $cssStyleMessage]
                    ],
                    [
                        'attribute' => 'created_at',
                        'label' => 'Создано',
                        'format' => 'datetime'
                    ],
                ],
            ]);
            ?>
        </div>
        <div class="col-md-6">
            <?php
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'provider_key',
                        'label' => 'Провайдер',
                    ],
                    [
                        'attribute' => 'message_id',
                        'label' => 'ID сообщения',
                    ],
                    [
                        'attribute' => 'updated_at',
                        'label' => 'Обновлено',
                        'format' => 'datetime'
                    ],
                ],
            ]);
            ?>

            <div class="form-group">
                <label>Последний ответ</label>
                <pre><?= $model->last_response ?></pre>
            </div>
        </div>
    </div>

<?php
echo Html::a('Обновить статус', ['update-status', 'id' => $model->id], ['class' => 'btn btn-primary']);
?>

<?php ActiveForm::end(); ?>