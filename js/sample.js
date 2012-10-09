$(document).ready(function () {
    var bing = [
        [new Date(2012, 2, 5).getTime(), 9],
        [new Date(2012, 2, 5).getTime(), 8],
        [new Date(2012, 2, 6).getTime(), 5],
        [new Date(2012, 2, 7).getTime(), 4],
        [new Date(2012, 2, 8).getTime(), 9],
        [new Date(2012, 2, 9).getTime(), 4],
        [new Date(2012, 2, 10).getTime(), 5],
        [new Date(2012, 2, 11).getTime(), 5]
    ];
    var goog = [
        [new Date(2012, 2, 5).getTime(), 3],
        [new Date(2012, 2, 5).getTime(), 3],
        [new Date(2012, 2, 6).getTime(), 3],
        [new Date(2012, 2, 7).getTime(), 3],
        [new Date(2012, 2, 8).getTime(), 3],
        [new Date(2012, 2, 9).getTime(), 3],
        [new Date(2012, 2, 10).getTime(), 3],
        [new Date(2012, 2, 11).getTime(), 3]
    ];

    var formatTick = function (v, obj) {
        if (v == 0) {
            return v;
        } else if (parseInt(v) % 10 === 0) {
            return "Page " + parseInt(v) / 10;
        } else {
            return v;
        }
    }

    var plot = $.plot($("#graph"),
        [
            { data:goog, label:"Google"},
            { data:bing, label:"Bing" }
        ],
        {
            series:{
                lines:{ show:true },
                points:{ show:false }
            },
            legend:{position:"sw"},
            colors:["#F2583E", "#77BED2"],
            grid:{ hoverable:true, clickable:true },
            yaxis:{ transform:function (v) {
                return -v;
            },
                inverseTransform:function (v) {
                    return -v;
                },
                tickDecimals:0,
                tickFormatter:formatTick,
                min:0,
                autoscaleMargin:0.05
            },
            xaxis:{ mode:"time"}
        });

    function showTooltip(x, y, contents) {
        $('<div id="tooltip">' + contents + '</div>').css({
            position:'absolute',
            display:'none',
            top:y + 5,
            left:x + 5,
            border:'1px solid #fdd',
            padding:'2px',
            'background-color':'#fee',
            opacity:0.80
        }).appendTo("body").fadeIn(200);
    }

    var previousPoint = null;
    $("#graph").bind("plothover", function (event, pos, item) {
        $("#x").text(pos.x.toFixed(2));
        $("#y").text(pos.y.toFixed(2));

        if (item) {
            if (previousPoint != item.dataIndex) {
                previousPoint = item.dataIndex;

                $("#tooltip").remove();
                var x = item.datapoint[0].toFixed(2),
                    y = item.datapoint[1].toFixed(2);

                showTooltip(item.pageX, item.pageY,
                    item.series.label + " rank of " + Math.round(y));
            }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;
        }
    });

    $("#graph").bind("plotclick", function (event, pos, item) {
        if (item) {
            $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
            plot.highlight(item.series, item.datapoint);
        }
    });
});
