// c.js

// v18.2

const c = new Proxy({}, {
	get:(_, _type) => {
        if (typeof widgetdom[_type] == 'function')
            return widgetdom[_type]
        else
            return (source) => {
                if (_type in widgetdom__api.widgetStore)
                    return widgetdom__api.widgetStore[_type](source)
                
                const result = widgetconvertor.toWidget(source, _type)
                return result
            }
    },
    set:(_, _type, element) => widgetdom__api.widgetRegister(_type, element)
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
// requeststorage.js

class requeststorage {
    static storage = {};
    static active = {};

    static create(data){
        this.storage = data
    }

    static run(hash, bind = false, then = false, bindToHtmlElement = false){
        let result = false;

        if (hash in requeststorage.storage){
            const request = requeststorage.storage[hash]

            if (hash in requeststorage.active){
                requeststorage.active[hash].stop()
            }

            requeststorage.active[hash] = new widgetrequest(
                hash, 
                request.url, 
                request.source, 
                request.method, 
                request.useState, 
                bind, 
                bindToHtmlElement
            )

            result = requeststorage.active[hash].run()
        } else {
            showDialog({
                title: 'Ошибка 842',
                message: `Request "${hash}" отсутствует!`
            })
        }

        return result;
    }
}
// widgetalias.js

class widgetalias {
    static global = {}
    static url = ''

    static set(url, value){
        widgetalias.global[url] = value
		widgetalias.updateHistory()
    }

	static remove(url){
		if (url in widgetalias.global) {
			delete widgetalias.global[url]
			widgetalias.updateHistory()
		}
	}

    static getUrl(){
        let result = '';
        
		Object.keys(widgetalias.global).forEach(key => {
			result += '&' + key
			if (Array.isArray(widgetalias.global[key])){
				result += `=${widgetalias.global[key].join(',')}`
			} else {
				result += `=${widgetalias.global[key]}`
			}
		})
        
		return result!=''?result.substring(1):''
    }

	static updateHistory(){
		const currentUrl = widgetalias.getUrl()
        if (currentUrl!=widgetalias.url){
			window.history.replaceState(0, "", location.origin + location.pathname + '?' + currentUrl);
			widgetalias.url = currentUrl
		}
	}
}
// widgetrequest.js

class widgetrequest {
    static active = {};

    constructor(hash, url, source, method, useState = [], bind = false, bindToHtmlElement = false){
        this.hash = hash
        this.url = url
        this.source = source
        this.method = method
        this.useState = useState
        this.bind = bind
        this.bindToHtmlElement = bindToHtmlElement
        this.signal = false
    }


    wait(check){
        if (this.bindToHtmlElement && this.bindToHtmlElement.tagName=='BUTTON')
            if (check) 
                this.bindToHtmlElement.classList.add('waiting')
            else
                this.bindToHtmlElement.classList.remove('waiting')
    }

    run(){
        const state = this.getStateData()
        const body = JSON.stringify({
            state,
            // this: widget.props
            executor: {
                source: this.source,
                method: this.method,
                props: this.props,
                bind: this.bind,
            },
            // request_id: widgetstate.current_request,
        })
        const signal = this.get_abort_signal()

        this.wait(true)
        fetch(this.url, {
            method: 'POST',
            body,
            signal
        })
        .then(res => res.json())
        .then(res => this.apply(res))
        .catch(error => this.error(error))
        .finally(() => this.wait(false));
    }

    stop(){
        this.signal.abort();
        this.signal = null
        delete requeststorage.active[this.hash]
    }


    get_abort_signal(){
		this.signal = new AbortController();
		return this.signal.signal
    }


    getStateData(){
        const result = {}
    
        this.useState.forEach(useState => {
            const stateName = Array.isArray(useState)?useState[1]:useState
            result[stateName] = {
                data: state[stateName].getRequestData(),
                source: useState
            }
        })

        return result
    }






    apply(res){
        if ('state' in res){
            Object.keys(res.state).forEach(stateName => {
                if ('data' in res.state[stateName])
                    Object.keys(res.state[stateName].data).forEach(propName => {
                        state[stateName].set(propName, res.state[stateName].data[propName])
                    })

                if ('runOnFrontend' in res.state[stateName]){
                    console.log('runOnFrontend', res.state[stateName].runOnFrontend)

                    res.state[stateName].runOnFrontend.forEach(func => {
                        const func2 = widgetconvertor.toFunction(func)
                        func2()
                    })
                }
            })
        }

        if ('then' in res){
            const func = window[props.then].bind({
                bind: props.bind
            })
            func(res.result)
        }

        this.catchControll(res)
    }

    error(error){
        this.catchControll(error)
    }


    setCatch(callback){
        this.catch = callback
        return this
    }

    catchControll(result){
        if (typeof this.catch == 'function'){
            this.catch(result)
            this.catch = false
        }
    }

}
// widgetelement.js



class widgetelement {
    static tools = [
        'group', 'func', 'requestmethod', 'list', 'requeststore_element', 
    ]

    static make(element){
        let result = false;

        if (widgetelement.tools.includes(element.element)){
            result = widgetelement[element.element](element.props)
        } else if (widgetdom__api.isReg(element.element)){
            result = widgetdom__api.get(element.element, element.props)
        } else {
            result = new widget(element.element, element.props) 
        }

        return result
    }





    static group({list}){
        return () => 
            list.forEach(itm => 
                widgetconvertor.toFunction(itm)()
            )
    }

    static func({body}){
        return new Function(body);
    }

/* 
    static requestmethod(props){
        return function() {
            return new widgetrequest(props, this).run()
        }
    }
*/

    static list({list}){
        return list;
    }

    static requeststore_element({hash, bind, then}){
        return function(){
            return requeststorage.run(hash, bind, then, this)
        }
    }

}
// widgetwatcher__link.js

class widgetwatcher__link {
    /**
     * 
     * @param {*} watcher 
     * @param {*} to == widget || callback 
     * @param {*} props - widget.props
     */
    constructor(watcher, to, props = false){
        this._watcher = watcher
        this._to = to
        this._props = props

        this._key = this.generate_key();

        if (watcher.onlink){
            watcher.onlink(widget, props)
        }

        this.view = false
    }

    generate_key(){
        if (this.is_func()){
            return Math.random()
        } else {
            return [this._to, this._props].join('_')
        }
    }

    is_func(){
        return typeof this._to == 'function';
    }

    key(){
        return this._key
    }

    update(){
        const is_func = this.is_func()
        const value = this._watcher.get_value(is_func?false:this._to)
        
        if (is_func){
            this.update_callback(value)
        } else {
            this.update_widget(value)
        }
        // this._watcher.updateAlias()
    }

    update_callback(value){
        this._to(value)
    }

    update_widget(value){
        const changeProps = this._to.assignProp(this._props, value)
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
/* 
    static by(path, keys, callback = false){
        const j = widgetwatcher__static.parsekeys(keys, callback)

        if (path in widgetwatcher.global){

        } else {
            widgetwatcher.global[path] = {}
        }

        widgetwatcher.global[path][j.strkeys] = new widgetwatcher(path, j.keys, j.callback)

        return widgetwatcher.global[path][j.strkeys]
    }
 */

    static by(path, keys, callback = false){
        const j = widgetwatcher__static.parsekeys(keys, callback)
        const watcher = new widgetwatcher(path, j.keys, j.callback)
        return watcher
    }

    static addToGlobal(path, watcherKey, link){
        if (!(path in widgetwatcher.global)) 
            widgetwatcher.global[path] = {}
        
        widgetwatcher.global[path][watcherKey] = link
    }

    key_inside(key){
        return this._keys.includes(key)
    }

    static update_path(path, key){
        if (path in widgetwatcher.global){
            Object.keys(widgetwatcher.global[path]).forEach(watcherKey => {
                const link = widgetwatcher.global[path][watcherKey]
                if (link.key_inside(key)){
                    link.update()
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
            link.update()
        }
    }

    link(to, props = false){
        const link = new widgetwatcher__link(this, to, props)

        const watcherKey = link.key()
        this._link[watcherKey] = link

        widgetwatcher.addToGlobal(this._path, watcherKey, this)

        this.update()
    }

    setonlink(callback){
        this.onlink = callback
        return this
    }


    get_value(widget = false){
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

    updateAlias(){
        
    }

}
// widgetstate__props.js

class widgetstate__props {

    static props = {}

    static setdefault(path, defaults){
        widgetstate__props.setStateProp(path, 'defaults', defaults);
    }

    static setalias(path, alias){
        widgetstate__props.setStateProp(path, 'alias', alias);
    }

    static setStateProp(path, prop, data){
        if (!(path in widgetstate__props.props))
            widgetstate__props.props[path] = {};

        widgetstate__props.props[path][prop] = data;
    }

    static by(path){
        if (path in widgetstate__props.props)
            return widgetstate__props.props[path]
        else
            return false
    }

    static getdefault(path, key){
        return widgetstate__props.props[path]?.defaults[key]
    }

/*
    static getAlias(path, key){
        return widgetstate__props.props[path]?.alias[key]
    }
*/

}
// widgetstate__methods.js

class widgetstate__methods {

    static getFromElement(element){
        let {method, args, stateName} = element.props;
        const functionWrap = method.substr(0, 1)=='_'

        if (functionWrap)
            method = method.substr(1)

        const result = function(){
            try {
                return state[stateName][method].apply(this, args)
            } catch (error) {
                console.error('widgetstate__tools - ', method, 'неопределен! или не занесен в widgetstate__tools.tools');
            }
        }


        return functionWrap?result:result()
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

    static path_from_args(){
        let result = ''
        if (Array.isArray(arguments[0])){
            result = widgetstate.path_from_args(...arguments[0])
        } else {
            result = Array.from(arguments).join(widgetstate.splitter)
        }

        if (result.startsWith(widgetstate.start)){
            return result;
        } else {
            return widgetstate.path_from_args(widgetstate.start, result)
        }
        /* let result = ''
        if (Array.isArray(args)){
            if (args.length==1){
                return widgetstate.path_from_args(args[0])
            } else {
                return ['#', ...args].join(widgetstate.splitter)
            }
        } else {
            return args.toString()
        } */
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
        'watchdefault', 
        'model', 
        'modelin', 


        'turn', 
        'map', 
        'inc',
        'dec',

        'proxy', 
        'getRequestData'
    ]

    
    to(key){
        return state.name(this._path, key)
    }

    proxy(){
        
    }

    set(key, value){
        if (typeof value == 'object' && !key.startsWith('_')) {
            let tempState = this.to(key)
            for (const [nkey, nvalue] of Object.entries(value)) {
                tempState.set(nkey, nvalue)
            }
        } else {
            if (this._data[key] != value){
                this._data[key] = value
                widgetwatcher.update_path(this._path, key)
                if (this.props){
                    this.updateAlias(key)
                }
                // this.request(key)
            }
        }
    }

    updateAlias(key){
        const url = this.props?.alias[key]

        if (url) 
        if (this.isdefault(key))
            widgetalias.remove(url)
        else
            widgetalias.set(url, this._data[key]) // обновил url без задержек!

    }


    get(key){
        return this._data[key]
    }

    getdefault(key){
        return widgetstate__props.getDefault(this._path, key)
    }

    isdefault(key){
        return this.getdefault(key) != this._data[key]
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

    watchdefault(key, __true, __false){
        const defaultValue = this.getdefault(key) 
        return this.watch(key, function(value){

            return value==defaultValue
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



    getRequestData(){
        const result = {}

        Object.keys(this._data).forEach(key => {
            if (!key.startsWith('__')){
                result[key] = this._data[key]
            }
        });

        // добавить source

        return result
    }
}
// widgetstate.js

class widgetstate extends widgetstate__tools {

    static global = {}
    static start = '#' 
    static splitter = '/'

    constructor(path){
        super()
        this._path = widgetstate.path_from_args(path)
        this._data = this.getdata()

        this.props = widgetstate__props.by(this._path)
        this.activerequest = false
    }

    static name(){
        return new widgetstate(Array.from(arguments))
    }

    static use(name, data, extra = false){
        state[name] = data
        const path = widgetstate.path_from_args(name)

        if (extra)
        for (const [key, value] of Object.entries(extra)) {
            try {
                widgetstate__props['set' + key](path, value);
            } catch (error) {
                console.error('Неизвестный метод widgetstate__props.set' + key)
            }
        } 
        
        /* 
            widgetstate__props.setDefault(path, defaults);
            widgetstate__props.setAlias(path, alias); 
        */
    }

}
// state.js

const state = new Proxy({path: [widgetstate.start]}, {
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
        if (to!='') path.push(to)

        return new Proxy({path}, this)
    },
    set:(sm, to, data) => {
        const stt = new widgetstate(sm.path)

        if (to=='' && typeof data == 'object'){
            for (const [keyTo, val] of Object.entries(data)) {
                // const element = array[index];
                stt.set(keyTo, val)
            }
        } else {
            stt.set(to, data)
        }

        return true;
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
        return widgetelement.make(element)
/*         
        const tag = widgetdom.getDefaultTag(element.element)
        const props = 'props' in element
                            ?element.props
                            :{}

        if (widgetdom__api.isReg(tag))
            return widgetdom__api.get(tag, props)
        else
            return new widget(tag, props) 
*/
    }

    static StateMethodToWidget(state_method){
        return widgetstate__methods.getFromElement(state_method);
    }

    static StateMethodToFunction(state_method){
        const result = widgetstate__methods.getFromElement(state_method);
        return widgetconvertor.getType(result)=='Function'?result:() => result;
    }

    static BoolToWidget(bool){
        return c.div('')
    }

    static ArrayToFunction(array){
        return function(){
            array.forEach(itm => {
                try {
                    widgetconvertor.toFunction(itm).apply(this)
                } catch (error) {
                    console.error('Не удалось выполнить функцию ', itm)
                }
            })
        }
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

    static toFunction(element){
        const type = widgetconvertor.getType(element)
        if (type == 'Function')
            return element

        switch(type){
            case 'Element':
                return widgetelement.make(element)
            break;
            default:
                const result = widgetconvertor.convert(element, type, 'Function')
                return result
            break;
        }
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

	static methods = ['StateMethod', 'WidgetRequest'];

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
				if (widgetconvertor.methods.includes(element.element)){
					return element.element
				}
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
				// widgetdom.debug('widgetconvertor', 'copy')('Не знаю как копировать этот тип', type)
				console.log('Не знаю как копировать этот тип', type, element)
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
        widgetdom__api.render('#app', element)
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


    static widgetStore = {};
    static widgetRegister(_type, element){
        widgetdom__api.widgetStore[_type] = element
    }

    static isReg(element){
        return element in widgetdom__api.widgetStore
    }

    static get(element, props){
        return widgetdom__api.widgetStore[element](props)
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
            this.domElement.setAttribute('key_id', this.id);
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
        console.log('Не помню зачем очищать watchlist', this)
        // widgetdom.debug('widget__tools', 'clear_watchlist')('clear watch')
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
            case 'StateMethod':
                value = widgetstate__methods.getFromElement(value) 
            break;
            case 'Element':
                value = widgetelement.make(value)
            break;
            case 'Array':
                if (prop.substr(0, 2)=='on'){
                    value = widgetconvertor.toFunction(value);
                }
            break;
        }

        if (widgetconvertor.getType(value)!=type){
            this.props[prop] = value;
            this.assignProp(prop, value);
            return result;
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
            }
            /* 
            else {
                this.props[prop] = value
            } 
            */
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

                    this.props['child'] = value
                    delete this.props['innerText']
                break;
            }
        }

        if (prop=='child'){
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
                if (!attrListBool.includes(prop)){
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
                // widgetdom.debug('widget', 'sequreAssign')('Обновляю dom root', prop)
                console.info('ARRAY', `${prop} (${type})`, value)
            break;
            default:
                console.info('Не применено - ', 'prop: ', prop, 'value: ', value, 'type: ', type)
                // widgetdom.debug('widget', 'sequreAssign')('Не применено', prop, value, type)
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
// widgetdialog.js
class widgetdialog {

    static props = {
        template: 'template',
        message: '__message',
        title: 'title',
        buttons: '__buttons', 

        hidetitle: 'hidetitle',
        width: 'width',
        height: 'height',
        
        active: '_active',
        active_arrow: 'active_arrow',

        // form
        enctype: 'enctype',
        action: 'action',
        method: 'method',
        onsubmit: 'onsubmit',
    }

    static styles = {
        black_h12nbsx9dk23m32ui4948382: "position:fixed;left:0;top:0;bottom:0;right:0;background:#0004;z-index:9999999999999999;overflow:auto;padding:10px;transition:all.1s;font-family:'Segoe UI', Roboto, Oxygen,Ubuntu,Cantarell,'Open Sans','Helvetica Neue','sans-serif';",
        black_habsolute: "position:absolute;background:#0000;",
        window_h12nbsx9dk23m32ui4948382: "max-width:650px;background:#fff;box-shadow: 5px 8px 20px 1px #00000057;border-radius:5px;color:#000;margin:12% auto;transition:all .3s;",
        dialogTitle_h12nbsx9dk23m32ui4948382: "padding:10px !important;font-weight:bold !important;font-family:Verdana, Geneva, Tahoma, sans-serif !important;color:inherit;font-size:11pt !important;",
        form_panel_h12nbsx9dk23m32ui4948382: "	display:flex !important; color:inherit;",
        _form_h12nbsx9dk23m32ui4948382: "width: 650px !important;padding: 10px !important;border-radius: 0;",
        close_panel_h12nbsx9dk23m32ui4948382: "border-bottom: 1px solid #0002;position: sticky !important;top: 0px !important;display: flex !important;background: #ffffff30;align-items: center !important;justify-content: space-between !important;color: inherit;",
        buttons_panel_h12nbsx9dk23m32ui4948382: "background: #ffffff30;position: sticky !important;bottom: 0px !important;display: flex !important;justify-content: flex-start !important;color: inherit;flex-direction: row-reverse;border-top: 1px solid #0002;",
        black_h12nbsx9dk23m32ui4948382__fieldset: "padding: 0 !important;margin: 0 !important;border: none !important;color: inherit;",
        btnh12n: "display: inline-block !important;padding: 2px 10px !important;background: #fff2;margin: 5px 0 !important;border: 1px solid #0003;border-radius: 3px !important;cursor: pointer !important;margin-right: 5px !important;margin-left: 5px !important;font-size: 14px !important;box-shadow: none;color: inherit;",
    }   

    static templates = []

    static __init__(){

        const $state = state.dialogstate
        const window = c.div({
            child: c.div({
                child: [
                    $state.watchif('hidetitle', false,
                        c.div({
                            child: [
                                '',
                                c.div({
                                    innerText: $state.watch('title'),
                                    style: widgetdialog.styles.dialogTitle_h12nbsx9dk23m32ui4948382,
                                }),
                                c.button({
                                    child: '✖',
                                    onclick(){
                                        $state.__message = false
                                    },
                                    style: widgetdialog.styles.btnh12n,
                                })
                            ],
                            style: widgetdialog.styles.close_panel_h12nbsx9dk23m32ui4948382,
                        }),
                        false
                    ),
                    c.div({
                        child: c.form({
                            child: c.fieldset({
                                child: $state.watch('__message'),
                                style: widgetdialog.styles.black_h12nbsx9dk23m32ui4948382__fieldset,
                            }),
                            style: $state.watch(height => `${widgetdialog.styles._form_h12nbsx9dk23m32ui4948382}; min-height: ${height?height:120}px;`),
                            enctype: $state.watch('enctype'),
                            action: $state.watch('action'),
                            method: $state.watch('method'),
                            onsubmit(){
                                return false;
                            },
                        }),
                        style: widgetdialog.styles.form_panel_h12nbsx9dk23m32ui4948382,
                    }),
                    c.div({
                        child: ['', $state.watch('__buttons')],
                        style: widgetdialog.styles.buttons_panel_h12nbsx9dk23m32ui4948382,
                    })
                ],
                style: widgetdialog.styles.window_h12nbsx9dk23m32ui4948382
            }),
            style: $state.watch(__style => `${widgetdialog.styles.black_h12nbsx9dk23m32ui4948382}; ${__style}`),
            _onmousedown(){
                $state.__message = false
            }
        })

        $state.watch('__message').link(message => { 
            const style = message==false?'opacity: 0; visibility: hidden;':''
            widgetdom.querySelector('body').then(body => {
                if ($state.get('__style'))
                    body.style.overflow = 'auto'
                else
                    body.style.overflow = 'hidden'
            })

            $state.__style = style
        })

        $state.watch(['width', 'height', '_active', 'hidetitle'])
        .link((width, height, active, hidetitle) => {
            let window_style = ''

            if (width) window_style += `width: ${width}px; `
            if (height) window_style += `min-height: ${hidetitle?height:height+39}px; `

            if (active){
                if ('element' in active){
                    widgetdom.querySelector(active.element).then(element => {
                        const rect = element.getBoundingClientRect()
                        window_style += `position: absolute; margin: 0;`


                        window_style += `bottom: calc(100% - ${rect.y-10}px); `
                        window_style += `left: ${rect.x}px; `
                        $state.active_arrow = 'bottom'
                        $state.__position = window_style
                    })
                }
            } else {
                $state.active_arrow = false
            }


            $state.__position = window_style
        })


        c.render('body', window, 'append')
    }


    static setup(template_name, props){
        widgetdialog.templates[template_name] = props
    }

    static template(template_name){
        if (template_name)
        if (template_name in widgetdialog.templates){

            const template = widgetdialog.templates[template_name];

            for (const [statekey, objectkey] of Object.entries(widgetdialog.props)){
                if (objectkey in template){
                    widgetdialog.setPorp(statekey, template[objectkey])
                } else {
                    widgetdialog.setPorp(statekey, false)
                }
            }
        } else {
            if (widgetdom.debug)
                console.info(template_name, ' отсутствует')
        }

        return widgetdialog
    }

    static show(props = true, title = false){
        const proptype = widgetconvertor.getType(props)

        switch (proptype) {
            case 'String':
            case 'Array':

                state.dialogstate.__message = props
                if (title)
                state.dialogstate.title = title
            break;
            case 'Object':


                for (const [key, value] of Object.entries(props)){
                    if (key in widgetdialog.props){
                        const statekey = widgetdialog.props[key]
                        widgetdialog.setPorp(statekey, value)
                    }
                }


            break;
            case 'Bool':
                if (!props)
                state.dialogstate.__message = false
            break;
        }
    }

    static setPorp(prop, value){
        switch (prop){
            case '__buttons':
                if (typeof value == 'object'){
                    const buttons = [];
                    for (const [buttontitle, func] of Object.entries(value)){
                        buttons.push(
                            c.button({
                                innerText: buttontitle,
                                style: widgetdialog.styles.btnh12n,
                                onclick: () => {
                                    widgetconvertor.toFunction(func).apply(this)
                                } 
                            })
                        )
                    }

                    state.dialogstate[prop] = buttons
                }
            break;
            case 'template':
                widgetdialog.template(value)
            break;
            default:
                state.dialogstate[prop] = value
            break;
        }
    }
}

widgetdialog.__init__();

function showDialog(props, title = false){
    widgetdialog.show(props, title)
}
