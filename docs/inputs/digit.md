# Компонент для отображения чисел
**Название:** DigitComponent  
**Тэг:** <digit-component>  
**Расположение:** /resources/js/system/components/inputs/DigitComponent.vue

## Входящие параметры:    
* **name** - name поля
  * **type:** String
  * **default:** 'digit'
* **value** - value поля
  * **type:** [String, Number]
  * **default:** 0
* **decimalPlace** - количество знаков после запятой
  * **type:** Number
  * **default:** 2
* **id** - id поля
  * **type:** String
  * **default:** null
* **classes** - class поля, type: String, default: null
  * **type:** String
  * **default:** null
* **disabled** - disabled поля, type
  * **type:** Boolean
  * **default:** false
* **required** - required поля, type: Boolean, default: false
  * **type:** Boolean
  * **default:** false
* **limit** - value поля, type: [String, Number],  default: 99999999
  * **type:** [String, Number]
  * **default:** 99999999

## Исходящие параметры:    
* **focus** - клик в поле (фокус)
* **blur** - клик вне поля (потеря фокуса)
* **change** - изменение значения в поле, отдается значение
* **enter** - нажатие клавиши enter в поле, отдается значение


