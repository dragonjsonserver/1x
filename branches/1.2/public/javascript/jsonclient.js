/**
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled with this
 * package in the file LICENSE.txt. It is also available through the
 * world-wide-web at this URL:
 * http://dragonjsonserver.de/homepage/index/license
 * If you did not receive a copy of the license and are unable to obtain it
 * through the world-wide-web, please send an email to
 * license@dragonjsonserver.de so we can send you a copy immediately.
 *
 * @copyright Copyright (c) 2012 DragonProjects (http://dragonprojects.de)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @author Christoph Herrmann <developer@dragonjsonserver.de>
 */

/**
 * Erstellt einen neuen Json Request mit den übergebenen Parametern
 * @param string id
 * @param string method
 * @param object params
 * @constructor
 */
function JsonRequest(id, method, params)
{
    this.id = id;
    this.method = method;
    this.params = params || {};
    this.jsonrpc = '2.0';
}

/**
 * Erstellt einen neuen Json Client zum Absenden von Json Requests
 * @param string serverurl
 * @param object options
 * @param object callbacks
 * @constructor
 */
function JsonClient(serverurl, options, callbacks)
{
	var applicationname = 'JsonClient';
	var applicationversion = 'v1.2.6';
	
    this.serverurl = serverurl;
    this.options = options || {};
    this.callbacks = callbacks || {};
    this.timestamp = undefined;

    var self = this;
    /**
     * Setzt eine Callback Funktion für den Key
     * @param string key
     * @param function callback
     * @return JsonClient
     */
    this.setCallback = function (key, callback) {
    	self.callbacks[key] = callback;
        return self;
    }

    var self = this;
    /**
     * Sendet einen oder mehrere Json Requests zum Json Server
     * @param JsonRequest jsonrequest
     * @param object options
     * @return JsonClient
     */
    this.send = function (jsonrequest, options) {
        var options = options || {};
        var requesturl = self.serverurl;
        if ($.isArray(jsonrequest)) {
            requesturl += 'multijsonrpc2.php';
            $.each(jsonrequest, function (index, value) {
            	value.params = $.extend({}, value.params);
            	if (index < jsonrequest.length - 1) {
            		value.params = $.extend({timestamp : -1}, value.params);
            	} else {
            		value.params = $.extend({timestamp : self.timestamp}, value.params);
            	}
            });
        } else {
            requesturl += 'jsonrpc2.php';
            jsonrequest.params = $.extend({timestamp : self.timestamp}, jsonrequest.params);
        }
        $.ajax($.extend({
            url : requesturl,
            type: 'POST',
            dataType : 'json',
            data : JSON.stringify(jsonrequest),
        }, self.options, options, {
            success : function (json) {
        		var clientmessageResponse = json;
        		if ($.isArray(json)) {
        			clientmessageResponse = json[json.length - 1];
        		}
	    		if (clientmessageResponse.result != undefined && typeof clientmessageResponse.result.result != 'undefined') {
	                $.each(clientmessageResponse.result, function(key, results) {
	                	switch (key) {
	                		case 'result':
	                			break;
	                		case 'timestamp':
	                			self.timestamp = results;
	                			break;
	                		default:
	                			$.each(results, function(subkey, result) {
		    	                	if (self.callbacks[key] != undefined) {
		    	                		self.callbacks[key]({
		    	                			result : result.result,
		    	                			id : clientmessageResponse.id,
		    	                			jsonrpc : clientmessageResponse.jsonrpc,
		    	                		}, result.timestamp);
		    	                	}
	                			});
	                			break;
	                	}
	                });
	                clientmessageResponse.result = clientmessageResponse.result.result;
        		}
				if (options.success != undefined) {
					options.success(json);
				} else if (self.options.success != undefined) {
					self.options.success(json);
				}
            }
        }));
        return self;
    }

    var self = this;
    /**
     * Sendet einen Request zur Abfrage der SMD des Json Servers
     * @param object options
     * @return JsonClient
     */
    this.smd = function (options) {
        var options = options || {};
        $.ajax($.extend({}, self.options, options, {
            url : self.serverurl + 'jsonrpc2.php',
            dataType : 'json'
        }));
        return self;
    }
}
