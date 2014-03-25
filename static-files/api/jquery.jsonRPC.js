/**
 * This is jquery plugin from Spotman`s kohana-simple-api
 * See more at https://github.com/spotman/kohana-simple-api
 *
 * UMD definition from https://github.com/umdjs/umd/blob/master/jqueryPlugin.js
 * Uses AMD or browser globals to create a jQuery plugin.
 * It does not try to register in a CommonJS environment since
 * jQuery is not likely to run in those environments.
 * See jqueryPluginCommonJs.js for that version.
 */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    /**
     * Thanks to JSON-RPC Tool
     * c. 2012 CJ Holmes - Free for all use provided attribution is given.
     * @link http://github.com/slashingweapon/jsonTools
     * @param url string
     * @param resource string
     * @param method string
     * @returns {ajax}
     */
    $.fn.jsonRPC = function(url, resource, method)
    {
        var JSONRPCError = function (obj) {
            jQuery.extend(this, obj);
            this.toString = function() { return this.message; }
        };

        // Keep a static counter, so we can give a unique ID to each request.  This technique
        // even survives a function name change.
        arguments.callee.requestCount = arguments.callee.requestCount || 0;
        arguments.callee.requestCount++;

        var params = [];

        // Take the extra parameters and push them onto the params array.
        for (var idx=3; idx<arguments.length; idx++)
            params.push(arguments[idx]);

        // If first parameter is object, then we use it as hash with named parameters
        if ( params.length > 0 && typeof(params[0]) == "object" )
        {
            params = params[0];
        }

        var request = {
            jsonrpc: "2.0",
            id: arguments.callee.requestCount,
            method: resource + "." + method
        };

        if ( params && params.length )
        {
            request.params = params;
        }

        var jqXHR = jQuery.ajax({
            type:'post',
            url: url,
            contentType: 'application/json',
            data: JSON.stringify(request),
            dataType: 'json_rpc_response',
            converters: {
                "text json_rpc_response": function(textValue) {
                    var retval = jQuery.parseJSON(textValue);

                    if (typeof(retval) == 'object') {
                        if (typeof(retval.error) == 'object') {
                            retval = new JSONRPCError(retval.error);
                            throw retval;
                        } else if (typeof(retval.result) != 'undefined') {
                            retval = retval.result;
                        } else
                            throw "Invalid JSON response";
                    } else
                        throw "Invalid JSON response";

                    return retval;
                }
            }
        });

        jqXHR.jsonRequestObject = request;

        return jqXHR;
    };

    return $.fn.jsonRPC;
}));
