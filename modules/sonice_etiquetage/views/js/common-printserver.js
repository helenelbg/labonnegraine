/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2016 Common-PrintServer
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * -----------------------------------------------------------------------------
 *
 * @package   Common-PrintServer
 * @author    Alexandre D.
 * @copyright Copyright (c) 2016 Alexandre D. [a.k.a. debuss-a]
 * @license   MIT Licence
 */

var CommonPrintServer = (function () {

    var cps = {

        fn: {

            version: '1.0.0',

            callMethod: function(method, param, callback) {
              console.log('LBG1');
                $.ajax({
                    type: CommonPrintServer.getAjaxTypeFromMethod(method),
                    url: CommonPrintServer.getWebServiceUrl() + method + CommonPrintServer.getFormatedParamsURL(method, param),
                    dataType: 'json',
                    data: param || [],
                    success: function (data) {
                        typeof(callback) == 'function' && callback(data);
                    },
                    error: function (data) {
                        typeof(callback) == 'function' && callback(data);
                    }
                });
            }

        },

        /*
         * Utils
         */
        getWebServiceUrl: function () {
            var protocol = 'http:';
            var port = 4567;

              console.log('LBG2');
            return protocol + '//localhost:' + port + '/';
        },

        getFormatedParamsURL: function (method, param) {
          console.log('LBG3');
            return (method == 'setPrinter' && param !== '')  ? ('/' + encodeURI(param)) : '';
        },

        getAjaxTypeFromMethod: function(method) {
          console.log('LBG4');
            return (method == 'printFileByURL' || method == 'printRaw') ? 'POST' : 'GET';
        },

        /*
         * Functions
         */
        getPrinters: function (callback) {
          console.log('LBG5');
            this.fn.callMethod('getPrinters', null, callback);
        },

        getPrinter: function (callback) {
          console.log('LBG6');
            this.fn.callMethod('getPrinter', null, callback);
        },

        setPrinter: function (printer_name, callback) {

            console.log('LBG7');
            this.fn.callMethod('setPrinter', printer_name, callback);
        },

        printFileByURL: function (file_url, callback) {
          console.log('LBG8');
            this.fn.callMethod('printFileByURL', file_url, callback);
        }

    };
      console.log('LBG9');

    return cps;

}());
