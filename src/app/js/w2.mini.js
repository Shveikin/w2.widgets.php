// c.js

const c = new Proxy({}, {
	get:(_, _type) => {
        if (typeof widgetdom[_type] == 'function')
            return widgetdom[_type]
        else
            return (source) => {
                // if (_type in widgetdom.widgetStore)
                //     return widgetdom.widgetStore[_type](source)
                
                const result = widgetconvertor.toWidget(source, _type)
                return result
            }
    },
    // set:(_, _type, element) => widgetdom.widgetRegister(_type, element)
})
// helpers.js

const current_template_data = {}
function tval(key){
    if (key in current_template_data.data){
        return current_template_data.data[key]
    } else {
        return ''
    }
}
// widgetcallback.js

class widgetcallback {
    static group = {}
    
    static index(group, callback){
        callback = callback.toString()
        if (group in widgetcallback.group){
            const index = widgetcallback.group[group].indexOf(callback)
            if (index != -1){
                return index
            } else {
                widgetcallback.group[group].push(callback)
                return widgetcallback.group[group].length
            }
        } else {
            widgetcallback.group[group] = []
            widgetcallback.group[group].push(callback)
            return 0
        }
    }
}
// widgetwatcher__link.js

class widgetwatcher__link {
    constructor(widget, props, watcher){
        this._widget = widget
        this._props = props
        this._watcher = watcher
        this._key = [widget.id, props].join('_')

        if (watcher.onlink){
            watcher.onlink(widget, props)
        }

        this.view = false
    }

    key(){
        return this._key
    }

    update(){
        const value = this._watcher.get_value(this._widget)
        
/* 
        if (this._props=='innerText') // ## 1+
        if (widgetconvertor.getType(value)=='Array'){
            this._widget.assignProp('innerText', '')
            this._props = 'child'
        }
 */

        const changeProps = this._widget.assignProp(this._props, value)
        if (changeProps)
            this._props = changeProps
    }
}
// widgetwatcher__static.js

class widgetwatcher__static {

    static parsekeys(keys, callback = false){

        if (typeof keys == 'function'){
			callback = keys
			const [_, fprops] = /\(?(.{0,}?)[\)|=]/m.exec(keys.toString())
			keys = fprops.split(',').map(i => i.trim())
		} else if (typeof keys == 'string'){
			keys = keys.split(',').map(i => i.trim())
		}

        let strkeys = keys.join(',')
/* 
        if (callback)
            strkeys += '_cbk' + widgetcallback.index('watchers', callback)
*/
        return {
            keys,
            callback,
            strkeys,
        }
    }

    static by(path, keys, callback = false){
        const j = widgetwatcher__static.parsekeys(keys, callback)

        if (path in widgetwatcher.global){
/*             
            if (j.strkeys in widgetwatcher.global[path]) {
                return widgetwatcher.global[path][j.strkeys]
            }
*/
        } else {
            widgetwatcher.global[path] = {}
        }

        widgetwatcher.global[path][j.strkeys] = new widgetwatcher(path, j.keys, j.callback)

        return widgetwatcher.global[path][j.strkeys]
    }

    key_inside(key){
        return this._keys.includes(key)
    }

    static update_path(path, key){
        if (path in widgetwatcher.global){
            Object.values(widgetwatcher.global[path]).forEach(watcher => {
                if (watcher.key_inside(key)){
                    watcher.update()
                }
            })
        }
    }

}
// widgetwatcher.js

class widgetwatcher extends widgetwatcher__static {
    static global = {}

    constructor(path, keys, callback = false){
        super()

        this._path = path
        this._keys = keys
        this._callback = callback?callback:this.tempcallback 
        this._link = {}

        this.onlink = false
    }

    tempcallback(){
        if (arguments.length==1)
            return arguments[0]
        else 
            return arguments
    }

    update(){
        for (const [key, link] of Object.entries(this._link)){
            widgetdom.debug('widgetwatcher', 'update')('update watcher', key)

            link.update()
        }
    }

    link(widget, props){
        const link = new widgetwatcher__link(widget, props, this)
        this._link[link.key()] = link

        link.update()
    }

    setonlink(callback){
        this.onlink = callback
        return this
    }

    get_value(widget){
        const state = widgetstate.by(this._path)
        const args = []
        
        this._keys.forEach(key => {
            if (key in state)
                args.push(state[key])
            else
                args.push(false)
        })

        const result = this._callback.apply(widget, args)
        return result
    }

}
// widgetstate__static.js

class widgetstate__static {
    static smart_methods = ['val']

    static val(path){
        const to = path.pop()
        const stateobject = widgetstate.by(path)

        if (to in stateobject)
            return stateobject[to]
        else 
            return false
    }

    static set(path, data){
        const to = path.pop()
        const state = widgetstate.by(path)

        state[to] = data
    }

    static path_from_args(args){
        if (Array.isArray(args)){
            if (args.length==1){
                return widgetstate.path_from_args(args[0])
            } else {
                return args.join(widgetstate.splitter)
            }
        } else {
            return args.toString()
        }
    }

    run_method(method){
        return function(){
            return this[method].apply(this, arguments)
        }.bind(this)
    }

    static by(path){
        const cpath = Array.isArray(path)?path.join(widgetstate.splitter):path

        if (cpath in widgetstate.global){
            return widgetstate.global[cpath]
        } else {
            return {}
        }
    }
    
    path(){
        return this._path
    }

    getdata(){
        const path = this.path()
        if (!(path in widgetstate.global)){
            widgetstate.global[path] = {
                // data: {},
                // keys: [],
            }
        }
        return widgetstate.global[path]
    }

}
// widgetstate__tools.js

class widgetstate__tools extends widgetstate__static {
    static tools = [
        'to',

        'get',
        'getdefault',
        'set', 
        'pushto',
        'lpushto',
        'pullfrom',


        'watch', 
        'watchif', 
        'watchin', 
        'model', 
        'modelin',


        'turn',
        'map',

        'proxy',
    ]

    
    to(key){
        return state.name(this._path, key)
    }

    proxy(){
        
    }

    set(key, value){
        if (typeof value == 'object' && !key.startsWith('_')) {
            let tempState = this.to(key)
            for (const [key, value] of Object.entries(value)) {
                tempState.set(key, value)
            }
        } else {
            this._data[key] = value
            widgetwatcher.update_path(this._path, key)
        }
    }

    get(key){
        return this._data[key]
    }

    getdefault(key){
        return this._data[key]
    }


    turn(key){
        this.set(key, !this._data[key])
    }



    inc(key, stap = 1){
        this.set(key, this._data[key] + stap)
    }

    dec(key, stap = 1){
        this.set(key, this._data[key] - stap)
    }


    pushto(to, value){
        if (Array.isArray(this._data[to])) {
            const temp = this._data[to]
            temp.push(value)
            this.set(to, temp)
        } else {
            console.error('#pushto', to, 'is not Array')
        }
    }

    lpushto(to, value){
        if (Array.isArray(this._data[to])) {
            const temp = [value ,...this._data[to]]
            this.set(to, temp)
        } else {
            console.error('#lpushto', to, 'is not Array')
        }
    }

    pullfrom(from, value){
        if (Array.isArray(this._data[from])) {
            const temp = this._data[from].filter(val => value!=val)
            this.set(from, temp)
        } else {
            console.error('#pullfrom', from, 'is not Array')
        }
    }


    watch(keys, callback = false){
        return widgetwatcher.by(this._path, keys, callback)
    }

    watchif(key, equality, __true, __false = false){
        return this.watch(key, function(value){

            return value==equality
                ?__true
                :__false

        })
    }

    watchin(key, equality, __true, __false = false){
        return this.watch(key, function(value){

            return Array.isArray(value) && value.includes(equality)
                ?__true
                :__false

        })
    }



    model(key){
        return this.watch(key).setonlink(
            (widget) => {
                const stt = this
                widget.assignProp('oninput', function(){
                    stt.set(key, this.value)
                })
            }
        )
    }

    modelin(key, equality){
        return this.watch(key, function(value){
            return Array.isArray(value) && value.includes(equality)
        }).setonlink(
            (widget) => {
                const stt = this
                widget.assignProp('onchange', function(){
                    if (this.checked)
                        stt.pushto(key, equality)
                    else
                        stt.pullfrom(key, equality)
                })
            }
        )
    }


    map(_key, reference = false){
        reference = widgetconvertor.toWidget(reference)
        return this.watch(_key, array => 
            Array.isArray(array)
                ?array.map(data => {
                    const copy = widgetconvertor.copy(
                        reference.setTemplateData(data)
                    )
                    return copy
                })
                :false
        )
    }
}
// widgetstate.js

class widgetstate extends widgetstate__tools {
    
    static global = {}
    static splitter = '/'
    constructor(path){
        super()
        this._path = widgetstate.path_from_args(path)
        this._data = this.getdata()
    }

    static name(){
        return new widgetstate(Array.from(arguments))
    }

}
// state.js

const state = new Proxy({path: ['#']}, {
	get(sm, to){
        if (widgetstate__static.smart_methods.includes(to)){
            return widgetstate__static[to](sm.path)
        } else
        if (widgetstate__tools.tools.includes(to)){
            return new widgetstate(sm.path).run_method(to)
        } else 
        if (typeof widgetstate[to] == 'function') {
            return widgetstate[to]
        }
        
        const path = [...sm.path]
        path.push(to)
        return new Proxy({path}, this)
    },
    set:(sm, to, data) => {
        const stt = new widgetstate(sm.path)
        stt.set(to, data)
        // widgetstate.by(sm.path).applyTo(to, data)
    }
})
// widgetconvertor__fromToFunc.js


class widgetconvertor__fromToFunc {

    static IntToWidget(int, tag = false){
        tag = widgetdom.getDefaultTag(tag)
        const val = widgetdom.getPropForDefaultValue(int, tag)
        return new widget(
            tag,
            val
        )
    }

    static StringToWidget(str, tag = false){
        tag = widgetdom.getDefaultTag(tag)
        const val = widgetdom.getPropForDefaultValue(str, tag)
        return new widget(
            tag,
            val
        )
    }

    static ArrayToWidget(array, tag = false){
        tag = widgetdom.getDefaultTag(tag)
        return new widget(
            tag,
            {child: array}
        )
    }

    static WatcherToWidget(watcher, tag = false){
        tag = widgetdom.getDefaultTag(tag)
        const val = widgetdom.getPropForDefaultValue(watcher, tag)
        return new widget(
            tag,
            val
        )
    }

    static ObjectToWidget(object, tag = false){
        tag = widgetdom.getDefaultTag(tag)
        return new widget(
            tag,
            typeof object == 'object'
                ?object
                :{}
        )
    }

    static FunctionToWidget(func, tag = false){
        tag = widgetdom.getDefaultTag(tag)
        return widgetconvertor.toWidget(func(), tag)
    }

    static ElementToWidget(element){
        return new widget(
            'element' in element
                ?element.element
                :widgetdom.defaulttag,
            'props' in element
                ?element.props
                :{}
        )
    }

}
// widgetconvertor__tools.js

class widgetconvertor__tools extends widgetconvertor__fromToFunc {

    static toArray(element){
        if (Array.isArray(element))
            return element
        else 
            return [element]
    }

    static toWidget(element, tag = false){
        const type = widgetconvertor.getType(element)
        if (type == 'Widget')
            return element

        const result = widgetconvertor.convert(element, type, 'Widget', tag)
        return result
    }

    static toElement(element, tag = false){
        const type = widgetconvertor.getType(element)
        if (type == 'Element')
            return element

        const result = widgetconvertor.convert(newelement, newtype, 'Element', tag)
        return result
    }
}
// widgetconvertor.js

class widgetconvertor extends widgetconvertor__tools {

	static map(value, [from, to], [from2, to2]) {
		return (((to2 - from2) / 100) * ((value - from) / ((to - from) / 100))) + from2
	}

	static roundValue(value, type){
        switch (type) {
            case 'int': return parseInt(value)
            case 'float': return Math.round(value * 10) / 10
            case 'float2': return Math.round(value * 100) / 100
            case 'float3': return Math.round(value * 1000) / 1000
        }
    }

    static convert(element, from, to, tag = false){
		if (from == to){
			return element
		}
        const func = `${from}To${to}`
		
        if (func in widgetconvertor){
			const result = widgetconvertor[func](element, tag)
			const newType = widgetconvertor.getType(result)
			if (newType==to){
				return result;
			} else {
				return widgetconvertor.convert(result, newType, to)
			}
        } else {
            throw new Error(`${func} отсутствует!`);
        }
    }

    static getType(element){
		let type = 'Unknown'
        if (Array.isArray(element))
			type = 'Array'
		else
		if (element instanceof widget)
			return 'Widget'
		else
		if (element instanceof widgetwatcher)
			return 'Watcher'
		else
        if (element && typeof element == 'object'){
			type = 'Object'
            
			if (element instanceof HTMLElement || element instanceof Text)
				type = 'HTML'
			else
			if ('element' in element)
				type = 'Element'
/* 
				if (element.element == 'WidgetTools' || typeof widgettools[element.element] === 'function'){
					type = 'WidgetTools'
				}
				 */
		} else
		if (typeof element=='string')
			type = 'String'
		else
		if (typeof element=='number')
			type = 'Int'
		else
		if (typeof element == 'function')
			type = 'Function'
		else
		if (typeof element == 'boolean')
			type = 'Bool'
		
		return type;
	}


	static copy(element){
		const type = widgetconvertor.getType(element)
		switch (type) {
			case 'Widget':
				const wcopy = new widget(element.element)

				wcopy.props = widgetconvertor.copy(element.props)
				wcopy.templateData = widgetconvertor.copy(element.templateData)

			return wcopy	
			case 'Array': 
				return element.map(itm => widgetconvertor.copy(itm))
			break;
			case 'Object': 
				const copy = {}
				for (const [key, value] of Object.entries(element)){
					copy[key] = widgetconvertor.copy(value)
				}
				return copy
			break;
			case 'String':
			case 'Int':
			case 'Bool':
				return element
			break;
			default:
				widgetdom.debug('widgetconvertor', 'copy')('Не знаю как копировать этот тип', type)
				// return element;
			break;
		}
	}


}
// widgetdom__api.js

/** 
 * metods для работы с js - dom_api
*/
class widgetdom__api {
    static app(element){
        const widget = widgetconvertor.toWidget(element)
        widgetdom__api.render('#app', widget)
    }

    /**
     * RENDER
     */
    static active = {}
    
    static render(querySelector, widget, mode = 'rebuild'){
        widget = widgetconvertor.toWidget(widget)

        if (querySelector in widgetdom__api.active){
            const currNode = widgetdom__api.active[querySelector]
            widgetdom.update(currNode, widget)
        } else {
            widgetdom.querySelector(querySelector, mode).then(rootElement => {
                widgetdom.firstRender(rootElement, querySelector, widget)
            }).catch(message => {
                console.error('widget render ', message)
            })
        }
    }

    static querySelector(querySelector, mode = 'rebuild'){
        return new Promise(function(resolve, reject){
            const rootElement = window.document.querySelector(querySelector);
            if (rootElement){
                switch (mode) {
                    case 'rebuild':
                        resolve(rootElement);
                    break;
                    case 'append':
                        const wrapper = document.createElement('div')
                        rootElement.appendChild(wrapper)
                        resolve(wrapper);
                    break;
                }
            } else {
                window.addEventListener('load', () => {
                    const rootElement = widgetdom.querySelector(querySelector, mode);
                    if (rootElement){
                        resolve(rootElement)
                    } else {
                        reject('Элемента нет ' + querySelector)
                    }
                })
            }
        })
    }

    static firstRender(rootElement, querySelector, widget){
        rootElement.innerHTML = ''
        widget.setRootElement(rootElement)
        // widgetdom.createElement(widget, rootElement)
        widgetdom.active[querySelector] = widget
    }

}
// widgetdom__tools.js

class widgetdom__tools extends widgetdom__api {
    
    static defaulttag = 'div'

    static getDefaultTag(tag = false){
        return tag?tag:widgetdom.defaulttag
    }





	static singleElement = {
		area: false,
		base: false,
		br: false,
		col: false,
		embed: false,
		hr: false,
		img: 'src',
		input: 'value',
		textarea: 'value',
		link: 'href',
		menuitem: false,
		meta: false,
		param: false,
		source: false,
		track: false,
		wbr: false,
	}

    static getPropForDefaultValue(value, tag){

        const prop = tag in widgetdom.singleElement
            ?widgetdom.singleElement[tag]
            :false

        if (prop)
            return {[prop]: value}
        else 
            return {innerText: value}
    }

}
// widgetdom.js

class widgetdom extends widgetdom__tools {
    static _debug = true
    static _trace = true


    static debug(clss, method){
        return function(){
            if (widgetdom._debug){

                console.group(arguments)
                    console.info(`class: %c${clss} %cmethod:%c ${method} `, 'color: #c00', 'color: #fff', 'color: #cc0')
                    if (widgetdom._trace)
                        console.trace()
                console.groupEnd()
            }
        }
    }
    
    /**
     * UPDATE 
     */
    static update(currWidget, nextWidget){
        if (!nextWidget) {

                // удалить текущий widget и все 
                // watcher
                currWidget.delete()

            return ['delete']
        }
        nextWidget = widgetconvertor.toWidget(nextWidget)
        if (widgetdom.changedType(currWidget, nextWidget)) {


            // заменить на next
            currWidget.replace_on(nextWidget)

            return ['replace', nextWidget]
        } else {

            // поменять свойства
            currWidget.set_props_from_widget(nextWidget)

            return ['change']
        }
    }

    static changedType(currWidget, nextWidget){
        const result = currWidget.element!=nextWidget.element
        return result
    }

}

// widget__tools.js


class widget__tools {
    static current_id = 0

    static next_id(){
        return ++widget__tools.current_id
    }

    /**
     * проверка на существование элемента
    */
    checkDomElement(){
        const type = widgetconvertor.getType(this.domElement)
        if (type!='HTML'){
            this.domElement = window.document.createElement(this.element)
        }
        return this
    }


    mix_props(next){
        const props = Object.keys(this.props)
        Object.keys(next.props).forEach(prop => {
            if (!(prop in this.props))
                props.push(prop)
        })
        return props
    }


    delete(){
        if (this.domElement && this.domElement.parentElement)
            this.domElement.parentElement.removeChild(this.domElement)
        this.domElement = null

        this.clear_watchlist()
    }

    clear_watchlist(){
        widgetdom.debug('widget__tools', 'clear_watchlist')('clear watch')
    }

    // replace on next
    replace_on(next){
        next.checkDomElement()

        this.domElement.parentElement.replaceChild(
            next.domElement, 
            this.domElement
        );

        this.delete()
        next.render()
    }


    set_props_from_widget(next){
        const props = this.mix_props(next)
        // this.checkDomElement() ??

        for (const prop of props){
            const currvalue = prop in this.props?this.props[prop]:''

            let nextvalue = ''
            if (prop in next.props)
                nextvalue = next.props[prop]

            if (currvalue!=nextvalue){
                this.assignProp(prop, nextvalue)
            }
        }
    }

}
// widget.js

class widget extends widget__tools {
    constructor(element, props = {}, root = false){
        super()

        this.element = element
        this.childs = []
        this.watchlist = []
        // this.child = widgetconvertor.toArray(child)
        this.props = props
        this.id = widget__tools.next_id()
        this.templateData = false
    }

    setRootElement(rootElement){
        this.rootElement = rootElement
        this.checkDomElement()
        this.rootElement.appendChild(this.domElement)
        this.render()
        return this
    }

    render(){
        for (const [prop, value] of Object.entries(this.props)){
            this.assignProp(prop, value)
        }
        return this
    }

    

    updateChilds(next){
        next = widgetconvertor.toArray(next)
        
        const deleteIndexs = []
        const max = Math.max(this.childs.length, next.length) 
        
        for (let i = 0; i < max; i++) {
            if (this.childs[i]) {
                const updateresult = widgetdom.update(
                    this.childs[i],
                    next[i]
                )

                switch (updateresult[0]) {
                    case 'delete':
                        deleteIndexs.push(i)
                    break;
                    case 'replace':
                        this.childs[i] = updateresult[1]
                    break;
                    case 'change':
                        
                    break;
                }


            } else {
                const child = widgetconvertor.toWidget(next[i])
                child.setRootElement(this.domElement)
                this.childs[i] = child
            }
        }

        if (deleteIndexs.length>0){
            const temp = this.childs.filter((child, index) => {
                return !deleteIndexs.includes(index)
            })
            this.childs = temp
        }

        return this
    }


    templateFillContents(template){
        const regex = /\$\{(\w+)\}/gm;
        template = template.replace(regex, '${tval("$1")}')

        current_template_data['data'] = this.templateData
        let val = '';
        eval(`val = \`${template}\``)
        return val
    }

    
    assignProp(prop, value) {
        let type = widgetconvertor.getType(value)
        let result = false;
        switch(type){
            case 'Watcher':
                value.link(this, prop)
            return result
            case 'String':
                if (this.templateData){
                    value = this.templateFillContents(value)
                }
            break;
        }
        
        if (prop!='child'){
            const currentPropType = widgetconvertor.getType(this.props[prop])
            if (currentPropType=='Watcher'){
                const watcher = this.props[prop]
                if (watcher.view != value){
                    watcher.view = value
                } else {
                    return result
                }
            } else {
                this.props[prop] = value
            }
        }

        if (prop=='innerText'){
            switch (type) {
                case 'Array':
                case 'Widget':
                case 'Element':
                    this.sequreAssign('innerText', '')
                    this.childs = []
                    prop = 'child'
                    result = 'child'
                break;
            }
        }

        if (prop=='child'){
            // if (value!=false)
            this.updateChilds(value)
            return result
        }

        this.sequreAssign(prop, value)
        return result
    }
    
    /**
     * ASSIGN PROP to dom element
     */
    
    sequreAssign(prop, value){
        const type = widgetconvertor.getType(value)
        
        const targetOnly = prop.substr(0, 1)=='_';
        if (targetOnly)
            prop = prop.substr(1)


        switch(type){
            case 'Bool':
                const attrListBool = ['innerHTML'];
                if (attrListBool.includes(prop)){
                    this.domElement[prop] = ''
                } else {
                    this.domElement[prop] = value
                }
            break;
            case 'String':
            case 'Int':
                const attrList = ['for'];

                if (attrList.includes(prop)){
                    this.domElement.setAttribute(prop, value)
                } else {
                    this.domElement[prop] = value
                }
            break;
            case 'Function':
                if (prop.substr(0, 2)=='on'){
                    const func = function(event){
                        if (targetOnly) 
                            if (event.target!=this) 
                                return false

                        value.apply(this)
/* 
                        if (this.element in widgetconvertor.singleElement){
                            const defaultProp = widgetconvertor.singleElement[this.element]
                            if (defaultProp){
                                this.props[defaultProp] = this[defaultProp]
                            }
                        }
*/
                    }

                    this.on(prop.substr(2), func)
                } else {
                    this.domElement[prop] = value()
                }
            break;
            // case 'Watcher':
            //     value.link(this, prop)
            // break;
            case 'Array':
                widgetdom.debug('widget', 'sequreAssign')('Обновляю dom root', prop)
            break;
            default:
                widgetdom.debug('widget', 'sequreAssign')('Не применено', prop, value, type)
            break;
        }
    }

    on(prop, callback){
        this.domElement.addEventListener(prop, callback)
    }

    setTemplateData(data){
        this.templateData = data
        return this
    }
}
