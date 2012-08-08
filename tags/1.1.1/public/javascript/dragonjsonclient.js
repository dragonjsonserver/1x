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
 */
function DragonJsonClient(jsonclient)
{
	var applicationname = 'DragonJsonClient';
	var applicationversion = 'v1.1.1';

    $('#applicationname').html(applicationname);
    $('#applicationversion').html(applicationversion);
    $('#applicationcopyright').html('© DragonProjects 2012');

    this.jsonclient = jsonclient;
    this.namespaces = {};
    this.data = {};

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
    };

    var self = this;
    /**
     * Selektiert einen anderen Namespace und baut die GUI entsprechend um
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
    };

    var self = this;
    /**
     * Selektiert eine andere Methode und baut die GUI entsprechend um
     */
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
