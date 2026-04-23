<?php
/**
 * Stub: Fine model — валидация суммы штрафа
 *
 * Этот файл содержит ТОЛЬКО skeleton изменений для models/Fine.php.
 * НЕ является самостоятельным файлом — код должен быть добавлен в существующий Fine.php.
 *
 * @see docs/arch/feature-1-fine-partial-payment.md — ADR-2
 */

namespace app\models;

// === Изменить rules() в классе Fine ===

// Добавить в массив rules():
// ['sum', 'validateFineSum'],

// === Добавить метод в класс Fine ===

/**
 * Кастомный валидатор: сумма штрафа не должна превышать остаток долга по кредиту.
 *
 * @param string $attribute — имя валидируемого атрибута ('sum')
 * @param array $params — параметры валидатора
 */
// public function validateFineSum($attribute, $params)
// {
//     if ($this->hasErrors()) {
//         return;
//     }
//
//     // TODO: загрузить связанный кредит
//     // $credit = Credit::findOne($this->id_credit);
//     //
//     // if ($credit === null) {
//     //     $this->addError($attribute, 'Кредит не найден.');
//     //     return;
//     // }
//     //
//     // // Проверка: сумма штрафа не должна превышать остаток долга
//     // if ($this->$attribute > $credit->debt) {
//     //     $this->addError($attribute,
//     //         "Сумма штрафа ({$this->$attribute}) превышает остаток долга ({$credit->debt})."
//     //     );
//     // }
//     //
//     // // Проверка: сумма штрафа должна быть положительной
//     // if ($this->$attribute <= 0) {
//     //     $this->addError($attribute, 'Сумма штрафа должна быть больше нуля.');
//     // }
// }
