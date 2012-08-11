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
 * @constructor
 */
function JsonClient(serverurl, options)
{
    this.serverurl = serverurl;
    this.options = options || {};

    var self = this;
    /**
     * Sendet einen oder mehrere Json Requests zum Json Server
     * @param JsonRequest jsonrequest
     * @param object options
     */
    this.send = function (jsonrequest, options) {
        var options = options || {};
        var requesturl = self.serverurl;
        if ($.isArray(jsonrequest)) {
            requesturl += 'multijsonrpc2.php';
        } else {
            requesturl += 'jsonrpc2.php';
        }
        $.ajax($.extend({
            url : requesturl,
            type: 'POST',
            dataType : 'json',
            data : JSON.stringify(jsonrequest)
        }, self.options, options));
    }

    var self = this;
    /**
     * Sendet einen Request zur Abfrage der SMD des Json Servers
     * @param object options
     */
    this.smd = function (options) {
        var options = options || {};
        $.ajax($.extend({
            url : self.serverurl + 'jsonrpc2.php',
            dataType : 'json'
        }, self.options, options));
    }
}
