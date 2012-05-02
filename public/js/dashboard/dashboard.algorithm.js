/*global $*/
/*global document*/
/*global midas*/

$(document).ready(function () {
    'use strict';
    var init = { title : 'Results Over the Past Seven Days',
                 axesDefaults: { tickRenderer: $.jqplot.CanvasAxisTickRenderer,
                                 tickOptions: { angle: -30, fontSize: '10pt' }
                               },
                 axes : { xaxis : {renderer : $.jqplot.DateAxisRenderer}},
                 series : [{ lineWidth : 4, markerOptions : {style : 'square'}}]
               };
    $.jqplot('chartdiv', midas.openscience.resultsets, init);
});
