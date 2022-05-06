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
// __h32.js
class __h32 {

    static map = {
        '+': true,
        '-': false,
        q: 'div',
        w: 'element',
        e: 'props',
        r: 'child',
        t: 'method',
        u: 'stateName',
        i: 'modelin',
        o: 'value',
        p: 'label',
        a: 'args',
        s: 'style',
        d: 'input',
        f: 'checked',
        g: 'h1',
        h: 'h2',
        j: 'h3',
        k: 'h4',
        l: 'h5',
        z: 'h6',
        x: 'textarea',
        c: 'span',
        y: 'watch',
        v: 'watchif',
        b: 'watchin',
        n: 'watchdefault',
        m: 'StateMethod',
        qq: 'type',
        qw: 'json',
        qe: 'button',
        qr: 'onclick',
        qt: 'innerText',
        qy: 'StateMapElement',
        tt: 'title',
        qu: 'key',
    };

    hashItm(itm){
        if (typeof itm == 'object'){
            return this.toElement(itm)
        } else
        if (itm in __h32.map){
            return __h32.map[itm]
        } else {
            return this.list[itm]
        }
    }

    constructor(list){
        this.list = widgetconvertor.toArray(list)
    }

    toElement(hash){
        if (Array.isArray(hash)){
            return hash.map(value => this.hashItm(value))
        } else {
            const result = {}
            for (const [hashkey, hashvalue] of Object.entries(hash)){
                const key = this.hashItm(hashkey)
                const value = this.hashItm(hashvalue)
            
                result[key] = value
            }
            return result;
        }
    }

    static unpack(pack64){
        const regex = /(\w+|[+|-])/g;
        const hash = pack64.replace(regex, '"$1"')
        return JSON.parse(hash)
    }
}
// requeststorage.js

class requeststorage {
    static storage = {};
    static active = {};
    static delay = false;

    static create(data){
        this.storage = data
    }

    static fetch(hash, bind){
        let then = false
        if ('then' in bind){
            then = bind.then
            delete bind.then
        }
        requeststorage.run(hash, bind, then, this)
    }

    static run(hash, bind = false, then = false, bindToHtmlElement = false){
        if (hash in requeststorage.storage){

            const request = hash in requeststorage.active
                ?requeststorage.active[hash]
                :new widgetrequest(hash, requeststorage.storage[hash], bind, bindToHtmlElement, then)

            request.run(requeststorage.delay)
        } else {
            showDialog({
                title: 'Ошибка 842',
                message: `Request "${hash}" отсутствует!`
            })
        }
    }

    static setDelay(delay){
        requeststorage.delay = delay
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

    constructor(hash, request, bind = false, bindToHtmlElement = false, then = false){
        this.hash = hash
        requeststorage.active[this.hash] = this

        this.url = request.url
        this.source = request.source
        this.method = request.method
        this.useState = request.useState
        this.bind = bind
        this.bindToHtmlElement = bindToHtmlElement
        this.then = then

        this.status = false
        this.current_request = false

        this.waiting = false
        this.waiting2 = false
        this.loading = false;
        this.block = false;

        this.elementWaiting = false
        this.penaltytime = 0
    }

    stopLoading(){
        if (this.loading){
            this.loading.abort();
            this.loading = false;
            this.addpenalty(500)
        }
    }

    addpenalty(penalty){
        if (this.penaltytime<1000){
            this.penaltytime += penalty
        }
    }

    run(delay = false){
        if (this.block) return false

        if (!this.elementWaiting) this.wait(true)
        this.stopLoading()

        if (delay && this.penaltytime!=0) {
            if (this.waiting2) {
                clearTimeout(this.waiting2)
                this.addpenalty(150)
            }
            if (this.waiting) {
                clearTimeout(this.waiting)
                this.addpenalty(100)

            }
            this.waiting = setTimeout(() => this.run(), delay + this.penaltytime);
        } else {
            this.waiting = false
            // console.log('penaltytime', this.penaltytime)
            this.createRequest(150)
            // this.fetch()
        }
    }

    createRequest(delay = false){
        this.stopLoading()

        if (delay) {
            if (this.waiting2) {
                clearTimeout(this.waiting2)
                this.addpenalty(150)
            }
            this.waiting2 = setTimeout(() => this.createRequest(), delay + this.penaltytime);
        } else {
            this.waiting2 = false
            this.fetch()
        }
    }




    fetch(){
        const stateData = this.getStateData()

        this.current_request = Math.random()

        const body = JSON.stringify({
            state: stateData,
            executor: {
                source: this.source,
                method: this.method,
                props: this.props,
                bind: this.bind,
            },
            request_id: this.current_request,
        })

        fetch(this.url, {
            method: 'POST',
            body,
            signal: this.getSignal()
        })
        .then(res => res.json())
        .then(res => {
            if (this.current_request==res.current_request){
                this.loading = false
                this.apply(res)

                this.wait(false)
                delete requeststorage.active[this.hash]
            }
        })
        // .finally(() => {
        //     this.loading = false
        // });
    }

    getSignal(){
        this.loading = new AbortController()
		return this.loading.signal
    }


    getStateData(){
        const result = {}
    
        this.useState.forEach(useState => {
            const stateName = Array.isArray(useState)?useState[1]:useState
            const data = state[stateName].getRequestData()
            if (Object.keys(data).length!=0)
                result[stateName] = {
                    data,
                    source: useState
                }
        })

        return result
    }



    apply(res){
        this.block = true
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

        if (this.then){
            const func = window[this.then].bind(this)
            func(res.result)
        }

        this.catchControll(res)
        this.block = false
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

    wait(check){
        this.elementWaiting = check
        if (this.bindToHtmlElement && this.bindToHtmlElement.tagName=='BUTTON')
            if (check){
                this.bindToHtmlElement.classList.add('waiting')
                this.bindToHtmlElement.disabled = true;
            } else {
                this.bindToHtmlElement.classList.remove('waiting')
                this.bindToHtmlElement.disabled = false;
            }
    }

}
// widgetelement.js



class widgetelement {
    static tools = [
        'group', 'func', 'requestmethod', 'list', 'requeststore_element', '__h32', 'StateMapElement', 
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

    static list({list}){
        return list;
    }

    static requeststore_element({hash, bind, then}){
        return function(){
            return requeststorage.run(hash, bind, then, this)
        }
    }

    static __h32({base, list, method = 32}){
        const result = (new __h32(list)).toElement(method==64?__h32.unpack(base):base);
        return result
    }


    static StateMapElement({stateName, key, widget, props}){

        return state[stateName].watch(key, function(obj){
            
            return Array.isArray(obj)
                ?obj.map((itm, elementKey) => {
                    let json = widget

                    Object.keys(props.keys).map(propkey => {
                        const hash = props.keys[propkey]
                        const value = itm[propkey]

                        json = json.replaceAll(hash, value)
                    })


                    Object.keys(props.math).map(hash => {
                        const calc = props.math[hash]
                            .replaceAll('@key', elementKey)
                            .replaceAll('@length', obj.length)
                        
                        let result = ''
                        eval(`result = ${calc}`)
                        json = json.replaceAll(hash, result)
                    })

                    return widgetconvertor.toWidget(JSON.parse(json))
                })
                :false
        })

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
            watcher.onlink(this._to, props)
        }

        this.view = false
    }

    generate_key(){
        if (this.is_func()){
            return Math.random()
        } else {
            return [this._to.id, this._props].join('_')
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

    static removeFromGlobal(path, watcherKey){
        if (path in widgetwatcher.global)
        if (watcherKey in widgetwatcher.global[path])
            delete widgetwatcher.global[path][watcherKey]
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


        return [this._path, watcherKey]
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

        
        const result = widgetconvertor.toFunction(this._callback).apply(widget, args)
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

    static setonchange(path, onchange){
        widgetstate__props.setStateProp(path, 'onchange', onchange);
    }

    static setdelay(path, delay){
        widgetstate__props.setStateProp(path, 'delay', delay);
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
        if (widgetstate__props.issetPropName('defaults', path, key))
            return widgetstate__props.props[path]?.defaults[key]
        else 
            return key.startsWith('_')?[]:false
    }

    static issetPropName(propName, path, key){
        if (path in widgetstate__props.props)
        if (propName in widgetstate__props.props[path])
        if (key in widgetstate__props.props[path][propName])
            return true
        
        return false
    }

}
// widgetstate__methods.js

class widgetstate__methods {

    static getFromElement(element){
        let {method, args, stateName} = element.props;
        const functionWrap = method.substr(0, 1)=='_'

        if (functionWrap)
            method = method.substr(1)

        const result = function(){
            return state[stateName].method(method).apply(this, args)
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
        'method',
        'to', 

        'get', 
        'getdefault',
        'set', 
        'setdefault', 
        'onepushto',
        'pushto',
        'lpushto',
        'pullfrom',


        'watch', 
        'watchif', 
        'watchin', 
        'watchdefault', 
        'model', 
        'modelin', 


        'toggle', 
        'map', 
        'inc',
        'dec',

        'proxy', 
        'getRequestData'
    ]

    method(method){
        return {
            apply: (bind, args) => {
                this.bind = bind
                args = widgetconvertor.toArray(args)

                requeststorage.setDelay(this.props?.delay)

                return this[method].apply(this, args)
            }
        }
    }
    
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
            let changed = false
            if (Array.isArray(value)){
                changed = !widgetconvertor.arraysEqual(this._data[key], value)
            } else {
                changed = this._data[key] != value
            }

            if (changed){
                this._data[key] = value
                widgetwatcher.update_path(this._path, key)

                // if (!key.startsWith('__'))
                if (this.props){
                    this.updateAlias(key)
                    this.change(key)
                }
                // this.request(key)
            }
        }
    }

    updateAlias(key){
        const url = 'alias' in this.props && this.props.alias!=false && key in this.props.alias
            ?this.props.alias[key]
            :false

        if (url) 
        if (this.isdefault(key))
            widgetalias.remove(url)
        else
            widgetalias.set(url, this._data[key]) // обновил url без задержек!

    }

    change(key){
        const onchange = this.props?.onchange
        if (onchange){
            requeststorage.setDelay(this.props?.delay)
            widgetconvertor.toFunction(onchange).apply(this.bind, [])
        }
    }


    get(key){
        if (key in this._data)
            return this._data[key]
        else
            return false
    }

    getdefault(key){
        return widgetstate__props.getdefault(this._path, key)
    }

    setdefault(key){
        const def = this.getdefault(key)
        this.set(key, def)
    }

    isdefault(key){
        const def = this.getdefault(key)
        const current = this.get(key)

        const result = Array.isArray(def) || Array.isArray(current)?widgetconvertor__tools.arraysEqual(def, current):def == current

        return result
    }


    toggle(key){
        this.set(key, !this.get(key))
    }



    inc(key, stap = 1){
        this.set(key, this._data[key] + stap)
    }

    dec(key, stap = 1){
        this.set(key, this._data[key] - stap)
    }

    onepushto(to, value){
        let current = this.get(to)

        if (!Array.isArray(current)) {
            if (to.startsWith('_'))
                current = []
            else
                console.error('#pushto', to, 'is not Array')
        }

        

        if (!current.includes(value)){
            const temp = [...current]
            temp.push(value)
            this.set(to, temp)
        }
    }

    pushto(to, value){
        let current = this.get(to)

        if (!Array.isArray(current)) {
            if (to.startsWith('_'))
                current = []
            else
                console.error('#pushto', to, 'is not Array')
        }

        const temp = [...current]
        temp.push(value)
        this.set(to, temp)
    }

    lpushto(to, value){
        let current = this.get(to)

        if (!Array.isArray(current)) {
            if (to.startsWith('_'))
                current = []
            else
                console.error('#lpushto', to, 'is not Array')
        }

        const temp = [value ,...current]
        this.set(to, temp)
    }

    pullfrom(from, value){
        let current = this.get(from)

        if (!Array.isArray(current)) {
            if (to.startsWith('_'))
                current = []
            else
                console.error('#pullfrom', to, 'is not Array')
        }

        const temp = current.filter(val => value!=val)
        this.set(from, temp)
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
        return this.watch(key, (value) => {
            const defaultValue = this.getdefault(key) 
            
            return value==defaultValue
                ?__true
                :__false
        }

            

        )
    }



    model(key, method = 'oninput'){

        const linkCallback = (widget, prop) => {
            const stt = this
            widget.assignProp(method, function(){
                stt.set(key, this[prop])
            })
        }

        return this.watch(key).setonlink(linkCallback)
    }

    modelin(key, equality){
        if (!key.startsWith('_')){
            console.error(key, ' (modelin) должен иметь тип array (_)')
            return false
        }

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
        this.bind = false
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

    static UnknownToWidget(unc){
        return c.div('')
    }

    static HTMLToWidget(HTML){
        return c.div({innerHTML: HTML})
    }

    static StringToFunction(str){
        if (str in window)
            return window[str]
        return () => str
    }

    static BoolToFunction(bool){
        return () => bool
    }

    static ObjectToFunction(Obj){
        return () => Obj
    }

}
// widgetconvertor__tools.js

class widgetconvertor__tools extends widgetconvertor__fromToFunc {

    static toArray(element){
        const type = widgetconvertor.getType(element);
        if (type=='Element' && element.element == 'list'){
            return element.props.list;
        }

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

    static arraysEqual(a, b) {
        if (a === b) return true;
        if (a == null || b == null) return false;
        if (a.length !== b.length) return false;
        
        for (var i = 0; i < a.length; ++i) {
            if (Array.isArray(a[i])){
                if (Array.isArray(b[i])){
                    if (!widgetconvertor__tools.arraysEqual(a[i], b[i])){
                        return false;
                    }
                } else {
                    return false;
                }
            } else
            if (a[i] !== b[i]) {
                return false;
            }
        }

        return true;
    }

}
// widgetconvertor.js

class widgetconvertor extends widgetconvertor__tools {

	static map(value, first, last) {
		try {
			const [from, to] = first; const [from2, to2] = last;
			return (((to2 - from2) / 100) * ((value - from) / ((to - from) / 100))) + from2
		} catch (error) {
			console.error(value, first, last, error)
		}
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
            })
            // .catch(message => {
            //     console.error('widget render ', message)
            // })
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

// widget_smartprops.js


class widget_smartprops {

    static dragboard(dragboard, props) {
        let mouseDown = false
        let mouseDownPosition = []
        const elements = []
        const boxsizing = {
            x: props.boxsizing?(props.boxsizing.x?props.boxsizing.x:props.boxsizing):0,
            y: props.boxsizing?(props.boxsizing.y?props.boxsizing.y:props.boxsizing):0,
        }

        const width = props.width
        const height = props.height


        dragboard.domElement.style.position = 'relative'
        dragboard.domElement.style.userSelect = 'none'
        dragboard.domElement.style.width = width + boxsizing.x + 'px'
        dragboard.domElement.style.height = props.height + 'px'

        dragboard.domElement.innerHTML = '';

        if ('childs' in props) {
            widgetconvertor.toArray(props.childs).forEach(child => {
                widgetconvertor.toWidget(child).setRootElement(dragboard.domElement, dragboard)
            })
            // const widgets = widgetconvertor.toArrayOfWidgets(props.childs);
            // widgets.forEach(widget => {
            //     dragboard.domElement.appendChild(
            //         widgetdom.createElement(widget)
            //     )
            // })
        }

        function rangeArray(){
            return [props.state.get('range_min'), props.state.get('range_max')]
        }

        let shift = false;

        let sliderMoveRange = {
            x: {min: 0, max: props.width },
            y: {min: 0, max: props.height},
        }

        if (props.useSlide){
            props.state.watch(['slide_min_start', 'slide_min_finish', 'slide_max_start', 'slide_max_finish']).link(
                function(slide_min_start, slide_min_finish, slide_max_start, slide_max_finish){

                    if (slide_min_start=="rangeMin")
                        slide_min_start = props.state.get('range_min')

                    if (slide_max_finish=="rangeMax")
                        slide_max_finish = props.state.get('range_max')


                    sliderMoveRange = [
                        {
                            x: {
                                min: widgetconvertor.map(slide_min_start, rangeArray(), [0, props.width]),
                                max: widgetconvertor.map(slide_min_finish, rangeArray(), [0, props.width]),
                            }
                        },
                        {
                            x: {
                                min: widgetconvertor.map(slide_max_start, rangeArray(), [0, props.width]),
                                max: widgetconvertor.map(slide_max_finish, rangeArray(), [0, props.width]),
                            }
                        }
                    ]
                    
                })
        }


        function mousemove(x, y){
            let posx, posy = 0

            const range = Array.isArray(sliderMoveRange)?sliderMoveRange[mouseDown]:sliderMoveRange

            if (props?.axis != 'y'){
                posx = ((parseInt(elements[mouseDown].domElement.style.left) || 0) + x)
                if (posx>range.x.max)
                    posx = range.x.max
                if (posx < range.x.min)
                    posx = range.x.min
                    
                elements[mouseDown].style('left', posx + 'px')
            }
            if (props?.axis != 'x'){
                posy = ((parseInt(elements[mouseDown].domElement.style.top) || 0) + y)
                if (posy>range.y.max)
                    posy = range.y.max
                if (posy < range.y.min)
                    posy = range.y.min

                elements[mouseDown].style('top', posy + 'px')
            }

            if (typeof props.ondrag == 'function'){
                let valposx = posx
                let valposy = posy

                valposx = widgetconvertor.map(posx, [0, width],  [0, 100])
                valposy = widgetconvertor.map(posy, [0, height], [0, 100])
                props.ondrag(mouseDown, valposx, valposy, posx, posy)
            }
        }

        const dragType = widgetconvertor.getType(props.drag)
        let widgets = []
        switch (dragType){
            case 'Object': 
                widgets = Object.values(props.drag)
                shift = Object.keys(props.drag)
            break;
            default: 
                widgets = props.drag
            break;  
        }

        function shiftXY(shiftVal){
            let left = 0
            let top = 0
            if (props?.axis != 'y')
                left = shiftVal

            if (props?.axis != 'x')
                top = shiftVal

            left = widgetconvertor.map(left, rangeArray(), [0, width])
            top =  widgetconvertor.map(top, rangeArray(), [0, height])

            return [left, top]
        }

        widgets = widgetconvertor.toArray(widgets)
        widgets.forEach((point, key) => {
            point = widgetconvertor.toWidget(point)
            point.setRootElement(dragboard.domElement, dragboard)

            point.style('position', 'absolute')
            
            if (shift){
                if ('state' in props) {
                    props.state.watch([shift[key], 'range_' + shift[key]]).link(function(newValue){
                        if (mouseDown===false){
                            const left = widgetconvertor.map(newValue[0], rangeArray(), [0, props.width])
                            point.style('left', left + 'px')
                        }
                    })
                } else {
                    const [left, top] = shiftXY(shift[key])
                    point.assignProp('style', `position: absolute; left: ${left}px; top: ${top}px`)
                }
            } else {
                point.assignProp('style', 'position: absolute; left: 0px; top: 0px')
            }

            point.assignProp('onmousedown', (event) => {
                mouseDownPosition = [event.screenX, event.screenY]; 
                mouseDown = key
            })

            elements.push(point)
        })




        dragboard.domElement.onmousemove = (event) => {
            if (mouseDown!==false){
                let x = event.screenX - mouseDownPosition[0]
                let y = event.screenY - mouseDownPosition[1]

                mousemove(x, y);
                mouseDownPosition = [event.screenX, event.screenY];
            }
        }

        dragboard.domElement.onmouseup = () => { mouseDown = false }
        dragboard.domElement.onmouseleave = () => { mouseDown = false }

            // dragboard.domElement.appendChild(dragElement)
        
        
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

        this.watchlist_clear()
    }

    watchlist_push(watcher_props){
        this.watchlist.push(watcher_props)
        if (this.parent)
            this.parent.watchlist_push(watcher_props)
    }

    watchlist_clear(){
        this.watchlist.forEach(watcher_props => {
            widgetwatcher.removeFromGlobal(watcher_props[0], watcher_props[1])
        })
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

        this.parent = false

        this.eventControllList = {}
    }

    setRootElement(rootElement, parent = false){
        this.rootElement = rootElement
        this.checkDomElement()
        this.rootElement.appendChild(this.domElement)
        this.parent = parent

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
                child.setRootElement(this.domElement, this)
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
        if (prop in widget_smartprops)
            return widget_smartprops[prop](this, value)

        let type = widgetconvertor.getType(value)
        let result = false;
        switch(type){
            case 'Watcher':
                const active = value.link(this, prop)
                this.watchlist_push(active)
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
            case 'Function':
                if (['innerText', 'innerHTML', 'child'].includes(prop)){
                    return this.assignProp(prop, value());
                }
            break;
        }

        if (widgetconvertor.getType(value)!=type){
            this.props[prop] = value;
            return this.assignProp(prop, value);
        }
        


        if (prop=='innerText' || prop=='innerHTML'){
            switch (type) {
                case 'Array':
                case 'Widget':
                case 'Element':
                    this.sequreAssign('innerText', '')
                    this.childs = []
                    const lastprop = prop
                    prop = 'child'
                    result = 'child'

                    this.props['child'] = value
                    delete this.props[lastprop]
                break;
                default: 
                    this.props[prop] = value;
                    this.childs = [];
                break;
            }
        }

        if (prop=='child'){
            switch (type) {
                case 'String':
                case 'Int':
                case 'Bool':
                    prop = 'innerText'
                    this.props['innerText'] = value
                    this.childs = []
                    result = 'innerText'
                    delete this.props['child']
                break;
                default:
                    this.updateChilds(value)
                    return result
                break;
            }
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
                const attrListBool = ['innerHTML', 'innerText', 'value'];
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
            case 'HTML':
                if (prop=='innerHTML' || prop=='innerText'){
                    this.domElement.appendChild(value)
                } else {
                    console.info('не знаю как применить в', prop, ' ->', value)
                }
            break;
            case 'Function':
                if (prop.substr(0, 2)=='on'){
                    const func = function(event){
                        if (targetOnly) 
                            if (event.target!=this) 
                                return false

                        return  value.apply(this, [event])
                    }

                    this.on(prop.substr(2), func)
                } else {
                    this.domElement[prop] = value()
                }
            break;
            case 'Array':
                console.info('ARRAY', `${prop} (${type})`, value)
            break;
            default:
                console.info('Не применено - ', 'prop: ', prop, 'value: ', value, 'type: ', type)
            break;
        }
    }

    on(prop, callback, type = ''){
        const event = prop + type
        if (!(event in this.eventControllList)){
            this.eventControllList[event] = callback
            this.domElement.addEventListener(prop, callback)
        }
    }

    style(prop, value){
        if (this.domElement)
            this.domElement.style[prop] = value
    }

    styles(styles){
        for (const [prop, val] of Object.entries(styles)) {
            this.style(prop, val)
        }
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


function showDialog(props, title = false){
    widgetdialog.show(props, title)
}



widgetdialog.__init__();

