<div class="row">
  <div class="col-sm-2">
    <div class="panel" id="utmSideBar">
      <h3><i class="icon-cogs"></i> {l s='Liste des sources' d='Modules.utm'}</h3>
      <div class="itemUtmSideBar active">Global</div>
      {foreach from=$listSource item=source}
        <div class="itemUtmSideBar">{$source['utm_source']}</div>
      {/foreach}
    </div> 
  </div>
  <div class="col-sm-10">
    <div class="panel" id="utmFilter">
      <h3><i class="icon-cogs"></i> {l s='Filtres' d='Modules.utm'}</h3>
      <form action="{$controllerUrl}" method="post">  
        <input type="hidden" name="page_fragment" value="{$dataPost}">
        <div class="row">
          <div class="col-md-6">
            <div class="row">
              <div class="col-xs-4">
                <div class="input-group">
                  <label class="input-group-addon">Du</label>
                  <input type="date" name="utmFrom" value="{$utmFrom}" class="form-control">
                </div>
              </div>
              <div class="col-xs-4">
                <div class="input-group">
                  <label class="input-group-addon">au</label>
                  <input type="date" name="utmTo" value="{$utmTo}" class="form-control">
                </div>
              </div>
              <div class="col-xs-4">
                <button type="submit" class="btn btn-default"><i class="icon-save"></i> Enregistrer</button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div> 
    <div class="panel" id="utmContent">
      <h3><i class="icon-cogs"></i> {l s='Global' d='Modules.utm'}</h3>
      <span class="utm_cleaner" style="clear: both">&nbsp;</span>
    </div> 
  </div>
</div>

{literal}
  <style>
    .itemUtmSideBar{
      width: 100%;
      padding: 10px 0;
      text-align: center;
      text-transform: uppercase;
      cursor: pointer;
    }

    .itemUtmSideBar:hover, .itemUtmSideBar.active{
      background-color: #f1f4f2;
    }
  </style>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <script type="text/javascript">
    google.charts.load('current', {
       'packages': ['geochart', "corechart"],
       'mapsApiKey': 'AIzaSyAeeVLk6GLqQxESblNBOT1yqmEnhfQi8SA'
      });

    function createChart(item, options, data, type){
      google.charts.setOnLoadCallback(function(){
        if(type === "pie"){
          var chart = new google.visualization.PieChart(document.getElementById(item));
        }else if(type === "geo"){
          var chart = new google.visualization.GeoChart(document.getElementById('chart_div'));
        }

        chart.draw(google.visualization.arrayToDataTable(data), options);
      });
    }

    var data = {/literal}{$dataChart}{literal};
    console.log(data);
    var i = 0;
    data.forEach(function(elem){
        let height = (elem["col"] == "2") ? 500 : 300;
        let html = "<div id='utm_chart_"+i+"' class='utm_chart' style='width: "+(parseInt(100) / parseInt(elem["col"]))+"%; height: "+height+"px; float: left'></div>";

        $("#utmContent .utm_cleaner").before(html);

        createChart("utm_chart_"+i, JSON.parse(elem["options"]), elem["data"], elem["type"]);

        i++;
    });
        
    $(document).on("click", ".itemUtmSideBar", function(){
        $("#utmContent h3").text($(this).text());
        $(".itemUtmSideBar.active").removeClass("active");
        $(this).addClass("active");

        let utm_source = $(this).text();
        let utmFrom = $('[name="utmFrom"]').val();
        let utmTo = $('[name="utmTo"]').val();

        $.ajax({
            url: "/modules/utm/ajax/stats.php",
            type: 'POST',
            data: {
              utm_source: utm_source,
              utmFrom: utmFrom,
              utmTo: utmTo
            },
            dataType: 'json',
            success: function (data) {
              console.log(data);
              $(".utm_chart").remove();
              var elements = data["data"];
              var i = 0;
              elements.forEach(function(elem){
                  let height = (elem["col"] <= "2") ? 500 : 300;
                  let html = "<div id='utm_chart_"+i+"' class='utm_chart' style='width: "+(parseInt(100) / parseInt(elem["col"]))+"%; height: "+height+"px; float: left'></div>";

                  $("#utmContent .utm_cleaner").before(html);

                  createChart("utm_chart_"+i, JSON.parse(elem["options"]), elem["data"], elem["type"]);

                  i++;
              });
            },
            error: function () {
                console.log("Probleme interne");
                
            }
        }); 
    });

  </script>
{/literal}
