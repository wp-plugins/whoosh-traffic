<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script type="application/x-javascript">
function getID(obj) {
    return $(obj).attr('href').substr(1);
}

$(document).ready(function() {
    $('.submitdelete').click(function() {
        if (!confirm( 'You are about to delete this pair \n  \'Cancel\' to stop, \'OK\' to delete.' ) ) {
            return false;
        }
        
        var ID = getID(this);
        
        var data = {
            action: '<?php echo $this->plugin_id;?>_delete_pair',
            pair_id: ID
        };
        
        $.post(ajaxurl, data, function (resp) {
            if (!resp['errors']) {
                $('<div id="message" class="updated below-h2"><p>Pair Deleted</p></div>').insertBefore('#poststuff');
            } else {
                $('<div id="message" class="updated below-h2"><p>'+resp['message']+'</p></div>').insertBefore('#poststuff');
            }
        });
    });
});
</script>
<div class="wrap">
  <br class="clear"/>
  <div id="poststuff" class="metabox-holder">
      <div class="inside">
        <table class="wp-list-table widefat fixed" cellspacing="0">
          <thead>
            <tr>
              <th scope="col" class="manage-column">URL</th>
              <th scope="col" class="manage-column">Keyword</th>
              <th scope="col" class="manage-column">Country</th>
              <th scope="col" class="manage-column">Language</th>
              <th scope="col" class="manage-column">Google Property</th>
              <th scope="col" class="manage-column">Rank</th>
              <th scope="col" class="manage-column">Change</th>
            </tr>
          </thead>
          <tbody>
            <tr class="alternate" id="row-<?php echo $this->pair['id']; ?>">
              <td>
                <strong class="<?php echo $this->plugin_id ?>_details"><?php echo $this->pair['url']; ?></strong>
                <div class="row-actions">
                  <!--<span class="inline"><a href="#<?php echo $this->pair['id']; ?>" class="editinline" title="Edit this item inline">Edit</a> | </span>-->
                  <span class="inline hide-if-no-js trash"><a class="submitdelete" title="Delete this item" href="#<?php echo $this->pair['id']; ?>">Delete</a></span>
                </div>
              </td>
              <td><?php echo $this->pair['keyword']; ?></td>
              <td><img alt="<?php echo $this->settings['country']; ?>" src="<?PHP echo $this->static;?>/images/countries/<?php echo strtolower($this->settings['country']); ?>.png"</td>
              <td><?php echo $this->settings['lang']; ?></td>
              <td><?php echo $this->settings['tld']; ?></td>
              <td align="center">
                <strong><?php echo '0'; ?></strong>
              </td>
              <td align="center">
                <strong><?php echo '0'; ?></strong>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

<?PHP if($this->timeline) { ?>
      <form id="side_panel">
		<section><div id="legend"></div></section>
		<section>
			<div id="renderer_form" class="toggler">
				<input type="radio" name="renderer" id="area" value="area">
				<label for="area">area</label>
				<input type="radio" name="renderer" id="bar" value="bar">
				<label for="bar">bar</label>
				<input type="radio" name="renderer" id="line" value="line" checked>
				<label for="line">line</label>
				<input type="radio" name="renderer" id="scatter" value="scatterplot">
				<label for="scatter">scatter</label>
			</div>
		</section>
		<section>
			<div id="offset_form">
				<label for="stack">
					<input type="radio" name="offset" id="stack" value="zero">
					<span>stack</span>
				</label>
				<label for="stream">
					<input type="radio" name="offset" id="stream" value="wiggle">
					<span>stream</span>
				</label>
				<label for="pct">
					<input type="radio" name="offset" id="pct" value="expand">
					<span>pct</span>
				</label>
				<label for="value">
					<input type="radio" name="offset" id="value" value="value" checked>
					<span>value</span>
				</label>
			</div>
			<div id="interpolation_form">
				<label for="cardinal">
					<input type="radio" name="interpolation" id="cardinal" value="cardinal">
					<span>cardinal</span>
				</label>
				<label for="linear">
					<input type="radio" name="interpolation" id="linear" value="linear" checked>
					<span>linear</span>
				</label>
				<label for="step">
					<input type="radio" name="interpolation" id="step" value="step-after">
					<span>step</span>
				</label>
			</div>
		</section>
		<section>
		</section>
	</form>

	<div id="chart_container">
		<div id="chart"></div>
		<div id="timeline"></div>
		<div id="slider"></div>
	</div>
    </div>
</div>

<script src="<?PHP echo $this->static;?>/js/rickshaw/vendor/d3.min.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/vendor/d3.layout.min.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Class.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Compat.ClassList.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Renderer.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Renderer.Area.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Renderer.Line.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Renderer.Bar.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Renderer.ScatterPlot.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.RangeSlider.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.HoverDetail.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Annotate.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Legend.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Axis.Time.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Behavior.Series.Toggle.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Behavior.Series.Order.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Behavior.Series.Highlight.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Smoother.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Unstacker.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Fixtures.Time.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Fixtures.Number.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Fixtures.RandomData.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Fixtures.Color.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Color.Palette.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Graph.Axis.Y.js"></script>
<script src="<?PHP echo $this->static;?>/js/rickshaw/src/js/Rickshaw.Series.js"></script>
<script src="<?PHP echo $this->static;?>/js/extensions.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js"></script>

<script type="application/x-javascript">

<?PHP

if($this->timeline)
{

    function cmp($a, $b)
    {
        if ($a['timestamp'] == $b['timestamp']) {
            return 0;
        }
        return ($a['timestamp'] < $b['timestamp']) ? -1 : 1;
    }
    
    usort($this->timeline, 'cmp');
    
    $google_timeline = array();
    
    foreach($this->timeline as $r)
    {
        $google_timeline[] = "{x: ".$r['timestamp'].", y: " . $r['rank'] . "}";
    }
    
    $google_timeline = implode(', ', $google_timeline);
?>
var google = [<?PHP echo $google_timeline; ?>];
<?PHP
} else {
?>
var google = [];
<?PHP }?>
var series = [];

if(google.length > 0) {
    series.push({
            color: "#F2583E",
            data: google,
            name: 'Google'
    });
}

series = new Rickshaw.Series(series);

Rickshaw.Series.zeroFill(series);
    
// Get section width
var section_width = $("div#poststuff").width();

var graph = new Rickshaw.Graph( {
	element: document.getElementById("chart"),
	width: section_width - 270,
	height: 500,
    invert: true, // Invert our graph
    min: -1,      // Set to -1 so we can actually see the 0
    interpolation: 'linear',
	renderer: 'line',
	stroke: true,
    series: series
} );

graph.render();

var slider = new Rickshaw.Graph.RangeSlider( {
	graph: graph,
	element: $('#slider')
} );

var hoverDetail = new Rickshaw.Graph.HoverDetail( {
	graph: graph,
	formatter: function(series, x, y) {
		var swatch = '<span class="detail_swatch" style="padding: 2px; background-color: ' + series.color + '">';
		var content = swatch + series.name + ": " + parseInt(y) + '</span>';
		return content;
	}
} );

var legend = new Rickshaw.Graph.Legend( {
	graph: graph,
	element: document.getElementById('legend')

} );

/* var shelving = new Rickshaw.Graph.Behavior.Series.Toggle( { */
/* 	graph: graph, */
/* 	legend: legend */
/* } ); */

var order = new Rickshaw.Graph.Behavior.Series.Order( {
	graph: graph,
	legend: legend
} );

var highlighter = new Rickshaw.Graph.Behavior.Series.Highlight( {
	graph: graph,
	legend: legend
} );

var smoother = new Rickshaw.Graph.Smoother( {
	graph: graph,
	element: $('#smoother')
} );

var ticksTreatment = 'glow';

var xAxis = new Rickshaw.Graph.Axis.Time( {
	graph: graph,
	ticksTreatment: ticksTreatment
} );

xAxis.render();

var formatTick = function (v) {
    if (v==0) {
        return v;
    } else if (parseInt(v) % 10 === 0) {
        return "Page " + parseInt(v) / 10;
    } else {
        return v;
    }
};

var yAxis = new Rickshaw.Graph.Axis.Y( {
	graph: graph,
	tickFormat: formatTick,
	ticksTreatment: ticksTreatment
} );

yAxis.render();


var controls = new RenderControls( {
	element: document.querySelector('form'),
	graph: graph
} );

</script>
<?PHP
}
?>