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
	var applicationversion = 'v1.2.0';
	
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
    	$.extend(jsonrequest.params, {timestamp : self.timestamp});
        var options = options || {};
        var requesturl = self.serverurl;
        if ($.isArray(jsonrequest)) {
            requesturl += 'multijsonrpc2.php';
        } else {
            requesturl += 'jsonrpc2.php';
        }
        $.ajax($.extend(self.options, options, {
            url : requesturl,
            type: 'POST',
            dataType : 'json',
            data : JSON.stringify(jsonrequest),
            success : function (json) {
        		if (json.result != undefined && typeof json.result.result != 'undefined') {
	                $.each(json.result, function(key, result) {
	                	switch (key) {
	                		case '_':
	                			break;
	                		case 'timestamp':
	                			self.timestamp = result;
	                			break;
	                		default:
	    	                	if (self.callbacks[key] != undefined) {
	    	                		self.callbacks[key]({
	    	                			result : result,
	    	                			id : json.id,
	    	                			jsonrpc : json.jsonrpc,
	    	                		});
	    	                	}
	                	}
	                });
	                json.result = json.result.result;
        		}
    			if (options.success != undefined) {
    				options.success(json);
    				return;
    			}
    			if (self.options.success != undefined) {
    				self.options.success(json);
    				return;
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
        $.ajax($.extend(self.options, options, {
            url : self.serverurl + 'jsonrpc2.php',
            dataType : 'json'
        }));
        return self;
    }
}
