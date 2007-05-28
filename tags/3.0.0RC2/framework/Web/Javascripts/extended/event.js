/**
 * @class Event extensions.
 */
Object.extend(Event, 
{
	/**
	 * Register a function to be executed when the page is loaded. 
	 * Note that the page is only loaded if all resources (e.g. images) 
	 * are loaded.
	 * 
	 * Example: Show an alert box with message "Page Loaded!" when the 
	 * page finished loading.
	 * <code>
	 * Event.OnLoad(function(){ alert("Page Loaded!"); });
	 * </code>
	 *
	 * @param {Function} function to execute when page is loaded.
	 */
	OnLoad : function (fn) 
	{
		// opera onload is in document, not window
		var w = document.addEventListener && 
					!window.addEventListener ? document : window;
		Event.__observe(w,'load',fn);
	},

	/**
	 * Adds the specified event listener function to the set of 
	 * listeners registered on given element to handle events of the 
	 * specified type. If <tt>useCapture</tt> is <tt>true</tt>, the 
	 * listener is registered as a capturing event listener. If 
	 * <tt>useCapture</tt> is <tt>false</tt>, it is registered as a 
	 * normal event listener. 
	 * 
	 * <tt>Event.observe</tt> may be called multiple times to register 
	 * multiple event handlers for the same type of event on the 
	 * same nodes. Note, however, that the DOM makes no guarantees 
	 * about the order in which multiple event handlers will be invoked.
	 *
	 * Example: Show an alert box with message "Clicked!" when a link
	 * with ID "link1" is clicked.
	 * <code>
	 * var link1_clicked = function()
	 * { 
	 *     alert("Clicked!"); 
	 * };
	 * Event.observe("link1", "click", link1_clicked);
	 * </code>
	 *
	 * @param {Object} element id string, DOM Element, or an Array 
	 * of element ids or elements.
	 * @param {String} The type of event for which the event listener 
	 * is to be invoked. For example, "load", "click", or "mousedown". 
	 * @param {Function} The event listener function that will be 
	 * invoked when an event of the specified type is dispatched to 
	 * this Document node. 
	 * @param {Boolean} If true, the specified listener is to be 
	 * invoked only during the capturing phase of event propagation. 
	 * The more common value of <tt>false</tt> means that the listener 
	 * will not be invoked during the capturing phase but instead will 
	 * be invoked when this node is the actual event target or when the 
	 * event bubbles up to this node from its original target. 
	 */
	observe: function(elements, name, observer, useCapture) 
	{
		if(!isList(elements))
			return this.__observe(elements, name, observer, useCapture);
		for(var i=0; i<elements.length; i++)
			this.__observe(elements[i], name, observer, useCapture);
	},
	
	/**
	 * Register event listeners.
	 * @private
	 */
	__observe: function(element, name, observer, useCapture) 
	{
		var element = $(element);
		useCapture = useCapture || false;
    
		if (name == 'keypress' && 
				((navigator.appVersion.indexOf('AppleWebKit') > 0) 
					|| element.attachEvent))
			name = 'keydown';
    
	    this._observeAndCache(element, name, observer, useCapture);
	},
	
	/**
	 * @param {Event} a keyboard event
	 * @return {Number} the Unicode character code generated by the key 
	 * that was struck. 
	 */
	keyCode : function(e)
	{
	   return e.keyCode != null ? e.keyCode : e.charCode
	},

	/**
	 * @param {String} event type or event name.
	 * @return {Boolean} true if event type is of HTMLEvent, false 
	 * otherwise
	 */
	isHTMLEvent : function(type)
	{
		var events = ['abort', 'blur', 'change', 'error', 'focus', 
					'load', 'reset', 'resize', 'scroll', 'select', 
					'submit', 'unload'];
		return events.include(type);
	},

	/**
	 * @param {String} event type or event name
	 * @return {Boolean} true if event type is of MouseEvent, 
	 * false otherwise
	 */
	isMouseEvent : function(type)
	{
		var events = ['click', 'mousedown', 'mousemove', 'mouseout', 
					'mouseover', 'mouseup'];
		return events.include(type);
	},

	/**
	 * Dispatch the DOM event of a given <tt>type</tt> on a DOM 
	 * <tt>element</tt>. Only HTMLEvent and MouseEvent can be 
	 * dispatched, keyboard events or UIEvent can not be dispatch 
	 * via javascript.
	 * @param {Object} element id string or a DOM element.
	 * @param {String} event type to dispatch.
	 */
	fireEvent : function(element,type)
	{
		element = $(element);
		if(document.createEvent)
        {            
			if(Event.isHTMLEvent(type))
			{
				var event = document.createEvent('HTMLEvents');
	            event.initEvent(type, true, true);
			}
			else if(Event.isMouseEvent(type))
			{
				var event = document.createEvent('MouseEvents');
				event.initMouseEvent(type,true,true,
					document.defaultView, 1, 0, 0, 0, 0, false, 
							false, false, false, 0, null);
			}
			else
			{
				if(Logger) 
					Logger.error("undefined event", type);
				return;
			}
            element.dispatchEvent(event);
        }
        else if(element.fireEvent)
        {
            element.fireEvent('on'+type);
            element[type]();
        }
        else
            element[type]();
	}
});