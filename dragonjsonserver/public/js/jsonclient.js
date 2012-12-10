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
 * @param object options
 * @constructor
 */
function JsonRequest(id, method, params, options)
{
    this.id = id;
    this.method = method;
    this.params = params || {};
    this.jsonrpc = '2.0';
    if (typeof options == 'function') {
    	options = {success : options};
    }
    this.options = options || {};
}

/**
 * Erstellt einen neuen Json Client zum Absenden von Json Requests
 * @param string serverurl
 * @param object options
 * @param object callbacks
 * @param object defaultparams
 * @constructor
 */
function JsonClient(serverurl, options, callbacks, defaultparams)
{
	var libraryname = 'JsonClient';
	var libraryversion = 'v1.11.0';

    $('#libraryname').html(libraryname);
    $('#libraryversion').html(libraryversion);
    $('#librarycopyright').html('© DragonProjects 2012');
	
    this.serverurl = serverurl;
    this.options = options || {};
    this.callbacks = callbacks || {};
    this.defaultparams = defaultparams || {};
    this.authenticate = undefined;
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
     * Setzt einen Defaultparameter der bei jedem Request mitgesendet wird
     * @param string param
     * @param string value
     * @return JsonClient
     */
    this.setDefaultParam = function (param, value) {
    	self.defaultparams[param] = value;
        return self;
    }

    var self = this;
    /**
     * Setzt den Htaccess der bei jedem Request mitgesendet wird
     * @param string username
     * @param string password
     * @return JsonClient
     */
    this.setAuthenticate = function (username, password) {
    	self.authenticate = {username : username, password : password};
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
            	value.params = $.extend({}, self.defaultparams, value.params);
            	if (index < jsonrequest.length - 1) {
            		value.params = $.extend({timestamp : -1}, value.params);
            	} else {
            		value.params = $.extend({timestamp : self.timestamp}, value.params);
            	}
            });
        } else {
            requesturl += 'jsonrpc2.php';
            jsonrequest.params = $.extend({timestamp : self.timestamp}, self.defaultparams, jsonrequest.params);
        }
        $.ajax($.extend({
            url : requesturl,
            type: 'POST',
            dataType : 'json',
            data : JSON.stringify(jsonrequest),
            beforeSend: function (jqXHR) {
        		if (self.authenticate != undefined) {
        			jqXHR.setRequestHeader('Authorization', 'Basic ' + base64encode(self.authenticate.username + ':' + self.authenticate.password));
        		}
        	},
        }, self.options, options, {
            success : function (json, statusText, jqXHR) {
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
					options.success(json, statusText, jqXHR);
				} else if (self.options.success != undefined) {
					self.options.success(json, statusText, jqXHR);
				}
				if (!$.isArray(jsonrequest)) {
					jsonrequest = [jsonrequest];
				}
				if (!$.isArray(json)) {
					json = [json];
				}
    			var jsonrequests = {};
    			$.each(jsonrequest, function (key, jsonrequest) {
    				jsonrequests[jsonrequest.id] = jsonrequest;
    			});
    			$.each(json, function (key, json) {
    				var jsonrequest = jsonrequests[json.id];
	    			if (json.result != undefined && jsonrequest.options.success != undefined) {
	    				jsonrequest.options.success(json, statusText, jqXHR);
	    			}
	    			if (json.error != undefined && jsonrequest.options.exception != undefined) {
	    				jsonrequest.options.exception(json, statusText, jqXHR);
	    			}
    			});
            },
	        error : function(jqXHR, statusText, errorThrown)
	        {
	            var jsonrequest = jsonrequest;
	        	if (options.error != undefined) {
	        		options.error(jqXHR, statusText, errorThrown);
	        	} else if (self.options.error != undefined) {
	        		self.options.error(jqXHR, statusText, errorThrown);
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
            dataType : 'json',
            beforeSend: function (jqXHR) {
        		if (self.authenticate != undefined) {
        			jqXHR.setRequestHeader('Authorization', 'Basic ' + base64encode(self.authenticate.username + ':' + self.authenticate.password));
        		}
        	},
        }));
        return self;
    }
}
