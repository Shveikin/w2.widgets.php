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



-----
### example


create - SliderState::create('rashod', default() )
php    - SliderState::rashod()->set('min', 11)
js     - state.SliderState.rashod.set('min', 11)



---


SliderState::create('Rashod', default())

php    - SliderState::Rashod::set('min', 11)
js     - state.SliderStateRashod.set('min', 11)




-----



state__method (class)
   + toHTML (+?)
   + toElement (+)


##widget (class)
   + url
   + toHTML (str wath tags) (+)
   + toElement (obj) (+?)


##widgetrequest (?)
