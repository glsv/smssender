<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\widgets\Alert;

$this->title = 'Отправка SMS';
$this->params['breadcrumbs'][] = 'Отправка SMS';
?>

<?= Alert::widget() ?>

<p><?= Html::a('Список SMS', ['index'], ['class' => 'btn btn-primary btn-sm']) ?></p>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'number')->textInput() ?>
<?= $form->field($model, 'message')->textarea(['rows' => 3]) ?>
<?= $form->field($model, 'recipient_name')->textInput() ?>

<?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']); ?>

<?php ActiveForm::end(); ?>