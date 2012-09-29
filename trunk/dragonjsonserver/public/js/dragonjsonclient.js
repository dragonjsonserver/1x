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
	var applicationversion = 'v1.7.0';

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
        $("input[type='text']").each(function (index, element) {
            if (element.value != '') {
                data[element.name] = element.value;
            }
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
        jsonclient.send(
            new JsonRequest(
                applicationname + ' ' + applicationversion,
                $('#namespace').val() + '.' + $('#method').val(),
                self.getData()
            ),
            {
                async : false,
                success : function (json) {
                    $('#response').html('<pre>' + JSON.stringify(json, null, 4) + '</pre>');
                    if (json.result != undefined) {
                        self.data = $.extend(json.result, self.data);
                    }
                },
                error : function(jqXHR, textStatus, errorThrown) {
                    $('#response').html('<pre>' + errorThrown + jqXHR.responseText + textStatus + '</pre>');
                }
            }
        );
        return self;
    };

    var self = this;
    /**
     * Selektiert einen anderen Namespace und baut die GUI entsprechend um
     * @return DragonJsonClient
     */
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
        return self;
    };

    var self = this;
    /**
     * Selektiert eine andere Methode und baut die GUI entsprechend um
     * @return DragonJsonClient
     */
    this.selectMethod = function ()
    {
        $('#response').html('<pre>Antwort</pre>');
        $.extend(self.data, self.getData());
        var namespace = $('#namespace').val();
        var method = $('#method').val();
        var div = $('#arguments');
        if (this.namespaces[namespace][method].length) {
        	div.html('');
            $.each(this.namespaces[namespace][method], function(index, parameter) {
                var controlgroup = 
                	$('<div class="control-group"></div>')
                    	.appendTo(div);
                
                $('<label class="control-label" for="newcredential"></label>')
                    .html(parameter.name + ':')
                    .appendTo(controlgroup);
                
                var value = '';
                if (parameter.optional) {
                	value = parameter.default;
                }
                if (self.data[parameter.name] != undefined) {
                	value = self.data[parameter.name];
                }
                
                $('<div class="controls"></div>')
                    .appendTo(controlgroup)
                    .append($('<input>')
                                .attr({'type' : 'text', 'name' : parameter.name})
                                .val(value));
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
    this.selectNamespace();
}
