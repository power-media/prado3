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
		Event.observe(w,'load',fn);
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
	 * via javascript consistently.
	 * @param {Object} element id string or a DOM element.
	 * @param {String} event type to dispatch.
	 */
	fireEvent : function(element,type)
	{
		element = $(element);
		if(type == "submit")
			return element.submit();
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
				if(typeof(Logger) != "undefined") 
					Logger.error("undefined event", type);
				return;
			}
            element.dispatchEvent(event);
        }
        else if(document.createEventObject)
        {
        	var evObj = document.createEventObject();
            element.fireEvent('on'+type, evObj);
        }
        else if(typeof(element['on'+type]) == "function")
            element['on'+type]();
	}
});