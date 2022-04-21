#### composer require pw2/widgets

### stateconfig

При создании стейта проверки осуществяются вледующим образом:
- выполняется default (or Name) метод
- выполняется alias метод
- widgetrequest::fillState
   - обновляем get данные (from widgetrequest)
   - обновляем post данные


### alias - url параметры
   при создании стейта проводим проверку есть ли стейт.alias(key) в get параметрах
   [ 'statekey' => 'url' ]



PHP
```php

class SliderState extends state {
   function default(){
      return ['unit' => 'def'];
   }

   function Rashod(){
      return ['unit' => 'next'];
   }
}



SliderState::get('unit'); // def
SliderState::name('Rashod')->get('unit'); // next

```

JS

```js

state.SliderState.get('unit')
state.Rashod.get('unit')

```





### widget
PHP
```php

class iButton extends widget {
    public $element = 'button';
    function __construct($title){
        $this->innerHTML = $title;
        $this->style = "
            padding: 20px;
        ";
    }
}


echo iButton:c('hello'); // <button style='padding: 20px;'>Hello</button>

```

---
## структура element
 - Смотреть в [widgets.js](widgets.js)

----








### widgetconventor
```php

   class widgetconventor {
      getType($element) // type
      canConvertToElement($element):bool // Могу конвентировать в Element
   }

```
### request
```php
   class request {
      apply(...$props):Element
   }
```










### TODOS

## element -> 
наименшая единица в которой есть 2 свойства - elemenent, props\
   element: *element_name* (watch, map, div)\
   props: [props] ('temp', ['array', func], 'hello world')


## state (container)
 + state\
   widgetstate::name
 + get
 + set
 + pushto
 + lpushto
 + _ * any * - function wrap

state__method (class)
   + toHTML (+?)
   + toElement (+)


## widget (class)
   + url
   + toHTML (str wath tags) (+)
   + toElement (obj) (+?)


## widgetrequest (?)




# widget
## <a name="useState"> public $useState = []</a>
> Список стейтов которые использует компонент
```php

   public $useState = [
      [myState::class, 'myStateName'], // myState::name('myStateName')->set('unit', 'C')
      tempState::class, // tempState::set('unit', 'C')
   ];

```












# <a name="stateinterface"> STATE INTERFACE</a>
> стейт на php может реальзовать следующие методы

```php

   function default(array $preload): array;
   function alias(): array|bool;
   function revice($key, $value);
   function updates(storage $storage)

```

> updates - список обновлений которые 
```php
   class MyState ex...
   // реализация
   function updates(storage $storage){
      $storage->updateMyColor = [
         state::_set('color', '#ff0000')
      ];
   }

   ...

   MyState::update('updateMyColor', [])

```
