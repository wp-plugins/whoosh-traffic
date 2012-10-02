
jQuery(function ($) {
    /**
     * Get ID from a#href
     * @param obj
     * return str
     */
    function getID(obj) {
        return $(obj).attr('href').substr(1);
    }

    /* Edit pairs */
    $('.'+plugin_id+'_edit > a').click(function () {
        var ID = getID(this);
        $('#row-'+ID).hide();
        $('#edit-row-'+ID).show();
        return false;
    });
    /* Cancel Edit pairs */
    $('.'+plugin_id+'_edit_cancel > a').click(function () {
        var ID = getID(this);
        $('#row-'+ID).show();
        $('#edit-row-'+ID).hide();
        return false;
    });

    /* Cancel Edit pairs */
    $('.'+plugin_id+'_edit_submit > a').click(function () {
        var ID = getID(this);

        var data = {
            action: plugin_id+'_update_pair',
            pair_id: ID
        };
        $('#edit-row-'+ID).find('input').each(function () {
            data[$(this).attr('name')] = $(this).val();
        });

        var waiting = $(this).parent().parent().find('.waiting');
        waiting.show();

        $.post(ajaxurl, data, function (resp) {
            if (resp) {
                var msg = '<div id="message" class="updated below-h2"><p>Pair updated</p></div>';
                $(msg).insertBefore('#poststuff');
                waiting.hide();

                setTimeout(function () { location.href = location.href; }, 1000);
            }
        });


        return false;
    });

    /* View pairs */
    $('.'+plugin_id+'_details > a').click(function () {
        var ID = getID(this);
        if (!$(this).hasClass('hide_me')) {
            $(this).addClass('hide_me');
            $('#details-row-'+ID).css({'position':'relative'});
        } else {
            $(this).removeClass('hide_me');
            $('#details-row-'+ID).css({'position':'absolute'});
        }

        return false;
    });
    /* Hide details */
    $('.'+plugin_id+'_details_cancel > a').click(function () {
        var ID = getID(this);

        $('#details-row-'+ID).css({'position':'absolute'});

        return false;
    });

    /* Delete pairs */
    $('.'+plugin_id+'_delete > a').click(function () {

        if (!confirm( 'You are about to delete this pair \n  \'Cancel\' to stop, \'OK\' to delete.' ) ) {
            return false;
        }

        var ID = getID(this);

        var tbody = $('#row-'+ID).parent();
        $('#row-'+ID).remove();
        $('#edit-row-'+ID).remove();
        $('#details-row-'+ID).remove();

        if ($(tbody).find('tr').length == 0) {
            $(tbody).parent().parent().parent().remove();
        }

        var data = {
            action: plugin_id+'_delete_pair',
            pair_id: ID
        };

        $.post(ajaxurl, data, function (resp) {
            if (resp) {
                var msg = '<div id="message" class="updated below-h2"><p>Item deleted</p></div>';
                $(msg).insertBefore('#poststuff');
            }
        });
        return false;
    });



    /** FLOT **/
    var formatTick = function (v, obj) {
        if (v == 0) {
            return v;
        } else if (parseInt(v) % 10 === 0) {
            return "Page " + parseInt(v) / 10;
        } else {
            return v;
        }
    }
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



    $('div[id^="flot-"]').ready(function () {
        // Render
        $('div[id^="flot-"]').each(function () {
            // Get ID
            var ID = $(this).attr('id').replace('flot-', '');
            // Draw
            var plot = $.plot($("#"+$(this).attr('id')),
                [
                    { data:SE[ID].goog, label:"Google"},
                    { data:SE[ID].bing, label:"Bing" }
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
            $("#"+$(this).attr('id')).bind("plothover", function (event, pos, item) {
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

            $("#"+$(this).attr('id')).bind("plotclick", function (event, pos, item) {
                if (item) {
                    $("#clickdata").text("You clicked point " + item.dataIndex + " in " + item.series.label + ".");
                    plot.highlight(item.series, item.datapoint);
                }
            });
        });

    });
});
