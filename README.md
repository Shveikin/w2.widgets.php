
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
state.SpiderState.Rashod.get('unit')

```


### TODOS

##element -> наименшая единица в которой есть 2 свойства - elemenent, props\
   element: *element_name* (watch, map, div)\
   props: [props] ('temp', ['array', func], 'hello world')


##state (container)
 + state\
   widgetstate::name\
   widgetstate::create(stateName, array): state
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
