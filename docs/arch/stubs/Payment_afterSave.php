<?php
/**
 * Stub: Payment model — afterSave() и afterDelete() для автообновления Credit.debt
 *
 * Этот файл содержит ТОЛЬКО skeleton изменений для models/Payment.php.
 * НЕ является самостоятельным файлом — код должен быть добавлен в существующий Payment.php.
 *
 * @see docs/arch/feature-1-fine-partial-payment.md — ADR-1
 */

namespace app\models;

// === Добавить в класс Payment ===

/**
 * После сохранения платежа — пересчитать остаток долга в связанном кредите.
 *
 * @param bool $insert — true если INSERT, false если UPDATE
 * @param array $changedAttributes — изменённые атрибуты (только при UPDATE)
 */
// public function afterSave($insert, $changedAttributes)
// {
//     parent::afterSave($insert, $changedAttributes);
//
//     // Загрузить связанный кредит
//     $credit = $this->getCredit()->one();
//     if ($credit !== null) {
//         // TODO: пересчитать debt через Credit::recalculateDebt()
//         // $credit->recalculateDebt();
//     }
// }

/**
 * После удаления платежа — пересчитать остаток долга в связанном кредите.
 */
// public function afterDelete()
// {
//     parent::afterDelete();
//
//     // Загрузить связанный кредит
//     $credit = Credit::findOne($this->id_credit);
//     if ($credit !== null) {
//         // TODO: пересчитать debt через Credit::recalculateDebt()
//         // $credit->recalculateDebt();
//     }
// }

// === Добавить в класс Credit (models/Credit.php) ===

/**
 * Пересчитать остаток долга на основе всех платежей.
 * debt = sum (тело кредита) - SUM(payments.sum)
 */
// public function recalculateDebt()
// {
//     // TODO: вычислить сумму всех платежей по этому кредиту
//     // $totalPaid = (float) Payment::find()
//     //     ->where(['id_credit' => $this->id])
//     //     ->sum('sum');
//     //
//     // // Обновить debt
//     // $this->debt = round($this->sum - $totalPaid, 2);
//     //
//     // // Защита от отрицательного остатка
//     // if ($this->debt < 0) {
//     //     $this->debt = 0;
//     // }
//     //
//     // // Сохранить без валидации (меняем только debt)
//     // $this->save(false);
// }
