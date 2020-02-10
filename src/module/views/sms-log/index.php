<?php
/**
 * @var \yii\data\ActiveDataProvider $dataProvider
 * @var \glsv\smssender\forms\SmsLogSearch $filterModel
 */

use yii\helpers\Html;
use common\widgets\Alert;
use glsv\smssender\widgets\SmsLogGridView;

$this->title = 'Отправка SMS';
$this->params['headerTitle'] = 'SMS';
$this->params['breadcrumbs'][] = 'Список SMS';
?>

<?= Alert::widget() ?>

    <p>
        <?= Html::a('Отправить SMS', ['form'], ['class' => 'btn btn-primary2 btn-sm']) ?>
        <?= Html::a('Проверить баланс', ['check-balance'], ['class' => 'btn btn-primary2 btn-sm']) ?>
    </p>

<?=
SmsLogGridView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{summary} {items} {pager}',
    'filterModel' => $filterModel,
    // Ваша реализация RecipientUrlBuilder, при необходимости
    // 'recipientUrlBuilder' => new RecipientUrlBuilder()
]);
?>

