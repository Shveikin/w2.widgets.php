### stateconfig

При создании стейта проверки осуществяются вледующим образом:
 - выполняется default (or Name) метод
 - выполняется alias метод
 - widgetrequest::fillState
   - обновляем get данные (from widgetrequest)
   - обновляем post данные


### alias - url параметры
   при создании стейта проводим проверку есть ли стейт.alias(key) в get параметрах
   [ 'url' => 'statekey' ]



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

state__method (class)
   + toHTML (+?)
   + toElement (+)


## widget (class)
   + url
   + toHTML (str wath tags) (+)
   + toElement (obj) (+?)


## widgetrequest (?)
