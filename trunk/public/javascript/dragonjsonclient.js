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
 * Klasse zur Initialisierung und Steuerung des JsonClients
 * @param string url Die URL zum JsonServer
 */
function DragonJsonClient(url)
{
	$('#applicationname').html('DragonJsonClient');
	$('#applicationversion').html('v1.0.0');
	$('#applicationcopyright').html('© DragonProjects 2012');
	
	this.url = url;
	this.namespaces = {};
	this.data = {};

    var self = this;
    this.getData = function ()
    {
    	var data = {};
		$("input[type='text']").each(function (index, element) {
			if (element.value != '') {
				data[element.name] = element.value;
			}
		});
		return data;
    };
    
	var self = this;
    this.sendRequest = function () 
    {
        var request = {};
        request.method = $('#namespace').val() + '.' + $('#method').val();
        request.params = self.getData();
        request.id = 1;
        request.jsonrpc = '2.0';
        $.ajax({
            url : self.url,
        	type: 'POST',
            async : false,
            dataType : 'json',
            data : JSON.stringify(request),
			error : function(jqXHR, textStatus, errorThrown) {
				$('#response').html('<pre>' + errorThrown + jqXHR.responseText + textStatus + '</pre>');
		  	},
            success : function(json) {
        		$('#response').html('<pre>' + JSON.stringify(json, null, 4) + '</pre>');
            }
        });
    };
	
	var self = this;
    this.selectNamespace = function () 
    {
    	var namespace = $('#namespace').val();
        var select = $('#method').html('');
        $.each(this.namespaces[namespace], function(method, parameters) {
    	    $('<option></option>')
    		    .html(method)
    		    .appendTo(select);
        });
        self.selectMethod();
    };
	
	var self = this;
    this.selectMethod = function () 
    {
    	$('#response').html('<pre>Antwort</pre>');
    	$.extend(self.data, self.getData());
    	var namespace = $('#namespace').val();
    	var method = $('#method').val();
    	var table = $('#arguments');
    	if (this.namespaces[namespace][method].length) {
    		table.html('');
	        $.each(this.namespaces[namespace][method], function(key, parameter) {
	        	var tr = $('<tr></tr>')
	                         .appendTo(table);
	        	$('<td></td>')
	                .appendTo(tr)
	                .append($('<label></label>')
	             			    .attr({'for' : parameter.name})
	            				.html(parameter.name + ': '));
	        	$('<td></td>')
	                .appendTo(tr)
	                .append($('<input />')
				                .attr({'type' : 'text', 'name' : parameter.name})
				                .val(self.data[parameter.name]));
	        });
    	} else {
    		table.html('<tr><td>Keine Argumente benötigt</td></tr>');
    	}
    };
    
	var self = this;
    $.ajax({
        url : url,
        async : false,
        dataType : 'json',
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
    			self.namespaces[namespace][method] = [];
    			$.each(service.parameters, function(key, parameter) {
    				self.namespaces[namespace][method].push({
    					name : parameter.name,
    					type : parameter.type
    				});
    			});
    		});
        }
    });
    
    var select = $('#namespace');
    $.each(this.namespaces, function(namespace, methods) {
	    $('<option></option>')
		    .html(namespace)
		    .appendTo(select);
    });
    this.selectNamespace();
}
