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
 * Klasse zur Initialisierung und Steuerung des Dragon Json Clients
 * @param JsonClient Json Client zum Abwenden der Json Requests
 * @constructor
 */
function DragonJsonClient(jsonclient)
{
	var applicationname = 'DragonJsonClient';
	var applicationversion = 'v1.8.0';

    $('#applicationname').html(applicationname);
    $('#applicationversion').html(applicationversion);
    $('#applicationcopyright').html('© DragonProjects 2012');

    this.jsonclient = jsonclient;
    this.namespaces = {};
    this.data = {};

    var self = this;
    /**
     * Setzt die Daten zu einem Eingabefeld auf einen Defaultwert
     * @param string key
     * @param string value
     * @return DragonJsonClient
     */
    this.setData = function (key, value)
    {
        self.data[key] = value;
        return self;
    };

    var self = this;
    /**
     * Gibt die eingegebenen Daten der Eingabefelder zurück
     * @return object
     */
    this.getData = function ()
    {
        var data = {};
        $("#arguments input[type='text']").each(function (index, element) {
        	element = $(element);
        	var value = element.val();
            if (value != '') {
            	var parametername = element.attr('data-parametername');
            	if (parametername != undefined) {
            		var keyname = element.attr('data-keyname');
            		if (keyname != undefined) {
                		if (data[parametername] == undefined) {
                			data[parametername] = {};
                		}
                		data[parametername][keyname] = value;
            		} else {
                		if (data[parametername] == undefined) {
                			data[parametername] = [];
                		}
                		data[parametername].push(value);
            		}
            	} else {
            		data[element.attr('name')] = value;
            	}
            }
        });
        $("#arguments input[type='checkbox']").each(function (index, element) {
			element = $(element);
        	data[element.attr('name')] = element.attr('checked') == 'checked';
        });
        return data;
    };

    var self = this;
    /**
     * Sendet und verarbeitet einen Json Request mit den eingegebenen Parametern
     * @return DragonJsonClient
     */
    this.sendRequest = function ()
    {
    	var namespace = $('#namespace').val();
    	var method = $('#method').val();
    	var data = self.getData();
    	$('#uri').val(
    		new URI()
		    	.query({
		    		namespace : namespace,
		    		method : method,
		    		data : JSON.stringify(data),
		    	})
		    + ''
		);
        jsonclient.send(
            new JsonRequest(
                applicationname + ' ' + applicationversion,
                namespace + '.' + method,
                data
            ),
            {
                async : false,
                success : function (json) {
            	$('#response').html('<pre>' + $('<div/>').text(JSON.stringify(json, null, 4)).html() + '</pre>');
                    if (json.result != undefined) {
                        self.data = $.extend(json.result, self.data);
                    }
                },
                error : function(jqXHR, textStatus, errorThrown) {
                	$('#response').html('<pre>' + $('<div/>').text(errorThrown + "\n" + jqXHR.responseText + "\n" + textStatus).html() + '</pre>');
                }
            }
        );
        return self;
    };

    var self = this;
    /**
     * Selektiert einen anderen Namespace und baut die GUI entsprechend um
     * @param object query
     * @return DragonJsonClient
     */
    this.selectNamespace = function (query)
    {
    	if (query && query.namespace != undefined) {
	        $('#namespace').val(query.namespace);
    	}
        var namespace = $('#namespace').val();
        var select = $('#method').html('');
        $.each(this.namespaces[namespace], function(method, parameters) {
            $('<option></option>')
                .html(method)
                .appendTo(select);
        });
        self.selectMethod(true, query);
        return self;
    };

    var self = this;
    /**
     * Selektiert eine andere Methode und baut die GUI entsprechend um
     * @param boolean clearresponse
     * @param object query
     * @return DragonJsonClient
     */
    this.selectMethod = function (clearresponse, query)
    {
    	if (clearresponse) {
    		$('#response').html('<pre>Antwort</pre>');
    	}
    	if (query && query.method != undefined) {
	        $('#method').val(query.method);
    	}
        $.extend(self.data, self.getData());
        var namespace = $('#namespace').val();
        var method = $('#method').val();
        var div = $('#arguments');
        if (this.namespaces[namespace][method].length) {
        	div.html('');
            $.each(this.namespaces[namespace][method], function(index, parameter) {
                var controlgroup = 
                	$('<div class="control-group"></div>')
                    	.appendTo(div)
                    	.append($('<label class="control-label" for="' + parameter.name + '"></label>')
                            .html(parameter.name + ':'));
                var controls = $('<div class="controls"></div>')
                    .appendTo(controlgroup);
                
                var value = undefined;
                if (parameter.optional) {
                	value = parameter.default;
                }
                if (self.data[parameter.name] != undefined) {
                	value = self.data[parameter.name];
                }
                
                switch (parameter.type) {
	            	case 'array':
	                	if (value == undefined) {
	                		value = [''];
	                	}
                    	$.each(value, function(subindex, subvalue) {
                    		var subindex = controls.children().length;
                            controls
                            	.attr('id', 'controls_' + parameter.name)
                            	.append($('<input>')
					                .attr({'type' : 'text', 'data-parametername' : parameter.name})
					                .val(subvalue));
                    	});
                    	var a = $('<a class="btn"></a>');
                    	controls
                    		.append(a
	                    		.append($('<i class="icon-plus-sign"></i>'))
	                    		.click(function(element) {
	                    			var subindex = controls.children().length - 2;
	                    			a.before($('<input>')
						                .attr({'type' : 'text', 'data-parametername' : parameter.name}));
	                    		}))
	                    	.append($('<a class="btn"><i class="icon-refresh"></i></a>')
                    			.click(function(element) {
                    				self.selectMethod();
	                    		}));
                    	break;
	            	case 'boolean':
                    	if (value == undefined) {
                    		value = false;
                    	}
                    	controls
    	            		.append($('<input>')
    			                .attr({'type' : 'checkbox', 'id' : parameter.name, 'name' : parameter.name, 'checked' : value}));
                    	break;
                	case 'object':
                    	if (value == undefined) {
                    		value = {'':''};
                    	}
                    	$.each(value, function(subindex, subvalue) {
                            var subcontrolgroup = 
                            	$('<div class="control-group"></div>')
                                	.appendTo(controls)
                                	.append($('<label class="control-label" for="' + parameter.name + '_' + subindex + '"></label>')
                                        .html(subindex + ':'));
                            $('<div class="controls"></div>')
                            	.appendTo(subcontrolgroup)
    	                		.append($('<input>')
					                .attr({'type' : 'text', 'id' : parameter.name + '_' + subindex, 'name' : parameter.name + '_' + subindex, 'data-parametername' : parameter.name, 'data-keyname' : subindex})
					                .val(subvalue));
                    	});
                		break;
                	default:
                    	if (value == undefined) {
                    		value = '';
                    	}
                    	controls
    	            		.append($('<input>')
    			                .attr({'type' : 'text', 'id' : parameter.name, 'name' : parameter.name})
    			                .val(value));
                    	break;
                }
            });
        } else {
        	div.html('Keine Argumente benötigt');
        }
        return self;
    };

    var self = this;
    jsonclient.smd({
        async : false,
        error : function(jqXHR, textStatus, errorThrown) {
            $('#dragonjsonclient').html('<p>Fehler beim Laden der SMD</p><pre>' + errorThrown + jqXHR.responseText + textStatus + '</pre>');
        },
        success : function(json) {
            self.namespaces = {};
            $.each(json.services, function(servicename, service) {
                var namespace = servicename.substr(0, servicename.lastIndexOf('.'));
                if (self.namespaces[namespace] == undefined) {
                    self.namespaces[namespace] = {};
                }
                var method = servicename.substr(servicename.lastIndexOf('.') + 1);
                self.namespaces[namespace][method] = service.parameters;
            });
        }
    });

    var select = $('#namespace');
    $.each(this.namespaces, function(namespace, methods) {
        $('<option></option>')
            .html(namespace)
            .appendTo(select);
    });
    var query = new URI().query(true);
    if (query.data) {
    	$.extend(this.data, $.parseJSON(query.data));
    }
    this.selectNamespace(query);
}
