
### stateconfig

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
state.SliderState.Rashod.get('unit')

```





###widget
PHP
```php

class ibutton extends widget {
.   function __construct($title) {
.       this->style = "padding: 20px";
.       this->child = title
.   }
}

```





### TODOS

##element -> наименшая единица в которой есть 2 свойства - elemenent, props\
   element: *element_name* (watch, map, div)\
   props: [props] ('temp', ['array', func], 'hello world')


##state (container)
 + state\
   widgetstate::name
 + get
 + set
 + pushto
 + lpushto





state__method (class)
   + toHTML (+?)
   + toElement (+)


##widget (class)
   + url
   + toHTML (str wath tags) (+)
   + toElement (obj) (+?)


##widgetrequest (?)
