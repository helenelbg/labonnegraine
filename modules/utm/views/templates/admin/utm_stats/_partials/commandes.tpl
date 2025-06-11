<div class="panel">
	<h3><i class="icon-cogs"></i> {l s='Commandes' d='Modules.utm'}</h3>
    <div class="table-responsive">
        <div class="btn-group">
            <button type="button" class="btn btn-default reset">Réinitialiser</button> <!-- targeted by the "filter_reset" option -->

            <!-- Split button -->
            <div class="btn-group">
            <button type="button" class="btn btn-default download">Exporter</button>
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                <span class="caret"></span>
                <span class="sr-only">Toggle Dropdown</span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><h5><strong>Options</strong></h5></li>
                <li>
                <label>Délimiteur: <input class="output-separator-input" type="text" size="2" value=","></label>
                <button type="button" class="output-separator btn btn-default btn-xs active" title="comma">,</button>
                <button type="button" class="output-separator btn btn-default btn-xs" title="semi-colon">;</button>
                <button type="button" class="output-separator btn btn-default btn-xs" title="tab">⇥</button>
                <button type="button" class="output-separator btn btn-default btn-xs" title="space">␣</button>
                <button type="button" class="output-separator btn btn-default btn-xs" title="output JSON">json</button>
                <button type="button" class="output-separator btn btn-default btn-xs" title="output Array (see note)">array</button>
                </li>
                <li class="divider"></li>
                <li>
                <label>Type de sortie:</label>
                <div class="btn-group output-download-popup" data-toggle="buttons" title="Download file or open in Popup window">
                    <label class="btn btn-default btn-sm active">
                    <input type="radio" name="delivery1" class="output-popup" checked=""> Web
                    </label>
                    <label class="btn btn-default btn-sm">
                    <input type="radio" name="delivery1" class="output-download"> Téléchargement
                    </label>
                </div>
                </li>
                <div style='display: none'>
                    <li>
                    <label>Include:</label>
                    <div class="btn-group output-filter-all" data-toggle="buttons" title="Output only filtered, visible, selected, selected+visible or all rows">
                        <label class="btn btn-default btn-sm active">
                        <input type="radio" name="getrows1" class="output-filter" checked="checked"> Filtered
                        </label>
                        <label class="btn btn-default btn-sm">
                        <input type="radio" name="getrows1" class="output-visible"> Visible
                        </label>
                        <label class="btn btn-default btn-sm">
                        <input type="radio" name="getrows1" class="output-selected"> Selected
                        </label>
                        <label class="btn btn-default btn-sm">
                        <input type="radio" name="getrows1" class="output-sel-vis"> Sel+Vis
                        </label>
                        <label class="btn btn-default btn-sm">
                        <input type="radio" name="getrows1" class="output-all"> All
                        </label>
                    </div>
                    </li>
                    <li>
                    <button class="output-header btn btn-default btn-sm active" title="Include table header">Header</button>
                    <button class="output-footer btn btn-default btn-sm active" title="Include table footer">Footer</button>
                    </li>
                </div>
                <li class="divider"></li>
                <li>
                <label>Remplacer les apostrophes: <input class="output-replacequotes" type="text" size="2" value="'"></label>
                <button type="button" class="output-quotes btn btn-default btn-xs active" title="single quote">'</button>
                <button type="button" class="output-quotes btn btn-default btn-xs" title="left double quote">“</button>
                <button type="button" class="output-quotes btn btn-default btn-xs" title="escaped quote">\"</button>
                </li>
                <div style='display: none'>
                    <li><label title="Remove extra white space from each cell">Trim spaces: <input class="output-trim" type="checkbox" checked=""></label></li>
                    <li><label title="Include HTML from cells in output">Include HTML: <input class="output-html" type="checkbox"></label></li>
                    <li><label title="Wrap all values in quotes">Wrap in Quotes: <input class="output-wrap" type="checkbox"></label></li>
                </div>
                <li class="divider"></li>
                <li><label title="Choose a download filename">Nom du fichier: <input class="output-filename" type="text" size="15" value="mytable.csv"></label></li>
            </ul>
            </div>
        </div>
        <table class="table table-bordered table-striped">
            <thead class="thead-light">
                <tr>
                    <th>{l s='id_order' d='Modules.utm'}</th>
                    <th>{l s='mail' d='Modules.utm'}</th>
                    <th>{l s='nom' d='Modules.utm'}</th>
                    <th>{l s='prenom' d='Modules.utm'}</th>
                    <th>{l s='sexe' d='Modules.utm'}</th>
                    <th>{l s='pays' d='Modules.utm'}</th>
                    <th>{l s='age' d='Modules.utm'}</th>
                    <th class="filter-select">{l s='groupe' d='Modules.utm'}</th>
                    <th>{l s='utm_source' d='Modules.utm'}</th>
                    <th>{l s='utm_medium' d='Modules.utm'}</th>
                    <th>{l s='utm_campaign' d='Modules.utm'}</th>
                    <th>{l s='total' d='Modules.utm'}</th>
                    <th class="filter-select">{l s='paiement' d='Modules.utm'}</th>
                    <th class="filter-select">{l s='etat' d='Modules.utm'}</th>
                    <th>{l s='date' d='Modules.utm'}</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th colspan="15" class="ts-pager">
                        <div class="form-inline">
                            <div class="btn-group btn-group-sm mx-1" role="group">
                                <button type="button" class="btn btn-secondary first" title="first">⇤</button>
                                <button type="button" class="btn btn-secondary prev" title="previous">←</button>
                            </div>
                            <span class="pagedisplay"></span>
                            <div class="btn-group btn-group-sm mx-1" role="group">
                                <button type="button" class="btn btn-secondary next" title="next">→</button>
                                <button type="button" class="btn btn-secondary last" title="last">⇥</button>
                            </div>
                            <select class="form-control-sm custom-select px-1 pagesize" title="Select page size">
                                <option value="10">10</option>
                                <option selected="selected" value="50">50</option>
                                <option value="100">100</option>
                                <option value="300">300</option>
                                <option value="1000">1000</option>
                            </select>
                            <select class="form-control-sm custom-select px-4 mx-1 pagenum" title="Select page number"></select>
                        </div>
                    </th>
                </tr>
            </tfoot>
            <tbody>
                {foreach from=$data item=line}
                    <tr class="product-line-row">
                        <td>{if $line.id_order eq ""}--{else}{$line.id_order}{/if}</td>
                        <td>{if $line.email eq ""}--{else}{$line.email}{/if}</td>
                        <td>{if $line.lastname eq ""}--{else}{$line.lastname}{/if}</td>
                        <td>{if $line.firstname eq ""}--{else}{$line.firstname}{/if}</td>
                        <td>{if $line.sexe eq ""}--{else}{$line.sexe}{/if}</td>
                        <td>{if $line.pays eq ""}--{else}{$line.pays}{/if}</td>
                        <td>{if $line.age eq ""}--{else}{$line.age|string_format:"%d"}{/if}</td>
                        <td>{if $line.group eq ""}--{else}{$line.group}{/if}</td>
                        <td>{if $line.utm_source eq ""}--{else}{$line.utm_source}{/if}</td>
                        <td>{if $line.utm_medium eq ""}--{else}{$line.utm_medium}{/if}</td>
                        <td>{if $line.utm_campaign eq ""}--{else}{$line.utm_campaign}{/if}</td>
                        <td>{if $line.total_paid_tax_incl eq ""}--{else}{$line.total_paid_tax_incl|string_format:"%.2f"}€{/if}</td>
                        <td>{if $line.payment eq ""}--{else}{$line.payment}{/if}</td>
                        <td>{if $line.etat eq ""}--{else}{$line.etat}{/if}</td>
                        <td>{if $line.date_add eq ""}--{else}{$line.date_add|date_format:"%d/%m/%Y %T"}{/if}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div> 

{literal}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/extras/jquery.tablesorter.pager.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/jquery.tablesorter.widgets.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/widgets/widget-filter-formatter-jui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/js/widgets/widget-output.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tipsy/1.0.3/jquery.tipsy.min.js" integrity="sha512-eVZ32+tSCynyovm4XjueCZMl/S4bDZeOlo26LqkHplwQ99NPMG6Q37WED09zlsDa0cB4C2D1M8a34RlmnOTMBQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.3/css/theme.bootstrap_4.min.css" />

    <style>
        .tablesorter-pager .btn-group-sm .btn {
            font-size: 1.2em;
        }

        .table tr td{
            padding: 10px 7px !important; 
        }
    </style>

    <script>
        $(function() {

            $('.table').tablesorter({
                //theme: 'bootstrap',
                dateFormat : "dd/mm/yy",
                checkboxClass : 'checked', // default setting
                widthFixed : true,
                headerTemplate : '{content} {icon}',
                widgets: ["zebra", "columns", "filter", "uitheme", "output"],
                widgetOptions : {
                    zebra : ["even", "odd"],
                    columns: [ "primary", "secondary", "tertiary" ],
                    filter_reset : ".reset",
                    filter_formatter : {
                        'th:contains("date")' : function($cell, indx) {
                            console.log($cell);
                            console.log(indx);
                            return $.tablesorter.filterFormatter.uiDatepicker( $cell, indx, {
                                changeMonth : true,
                                changeYear : true,
                                dateFormat: "dd/mm/yy"
                            });
                        },
                    },
                    filter_placeholder : {
                        from : 'Du...',
                        to   : 'Au...'
                    },

                    output_separator     : ',',         // ',' 'json', 'array' or separator (e.g. ';')
                    output_ignoreColumns : [0],         // columns to ignore [0, 1,... ] (zero-based index)
                    output_hiddenColumns : false,       // include hidden columns in the output
                    output_includeFooter : true,        // include footer rows in the output
                    output_includeHeader : true,        // include header rows in the output
                    output_headerRows    : false,       // output all header rows (if multiple rows)
                    output_dataAttrib    : 'data-name', // data-attribute containing alternate cell text
                    output_delivery      : 'p',         // (p)opup, (d)ownload
                    output_saveRows      : 'f',         // (a)ll, (v)isible, (f)iltered, jQuery filter selector (string only) or filter function
                    output_duplicateSpans: true,        // duplicate output data in tbody colspan/rowspan
                    output_replaceQuote  : '\u201c;',   // change quote to left double quote
                    output_includeHTML   : true,        // output includes all cell HTML (except the header cells)
                    output_trimSpaces    : false,       // remove extra white-space characters from beginning & end
                    output_wrapQuotes    : false,       // wrap every cell output in quotes
                    output_popupStyle    : 'width=580,height=310',
                    output_saveFileName  : 'mytable.csv',
                    // callback executed after the content of the table has been processed
                    output_formatContent : function(config, widgetOptions, data) {
                        // data.isHeader (boolean) = true if processing a header cell
                        // data.$cell = jQuery object of the cell currently being processed
                        // data.content = processed cell content (spaces trimmed, quotes added/replaced, etc)
                        // data.columnIndex = column in which the cell is contained
                        // data.parsed = cell content parsed by the associated column parser
                        return data.content;
                    },
                    // callback executed when processing completes
                    output_callback      : function(config, data, url) {
                        // return false to stop delivery & do something else with the data
                        // return true OR modified data (v2.25.1) to continue download/output
                        return true;
                    },
                    // callbackJSON used when outputting JSON & any header cells has a colspan - unique names required
                    output_callbackJSON  : function($cell, txt, cellIndex) {
                        return txt + '(' + cellIndex + ')';
                    },
                    // the need to modify this for Excel no longer exists
                    output_encoding      : 'data:application/octet-stream;charset=utf8,',
                    // override internal save file code and use an external plugin such as
                    // https://github.com/eligrey/FileSaver.js
                    output_savePlugin    : null /* function(config, widgetOptions, data) {
                        var blob = new Blob([data], {type: widgetOptions.output_encoding});
                        saveAs(blob, widgetOptions.output_saveFileName);
                    } */
                }
            }).tablesorterPager({
                container: $('.ts-pager'),
                output: '{startRow} - {endRow} / {filteredRows} ({totalRows})',
                size: 50,
                cssGoto  : ".pagenum",
                removeRows: false
            });

            // set up download buttons for two table groups
            var demos = ['.panel'];

            $.each(demos, function(groupIndex) {
                var $this = $(demos[groupIndex]);

                $this.find('.dropdown-toggle').click(function(e) {
                // this is needed because clicking inside the dropdown will close
                // the menu with only bootstrap controlling it.
                $this.find('.dropdown-menu').toggle();
                return false;
                });
                // make separator & replace quotes buttons update the value
                $this.find('.output-separator').click(function() {
                $this.find('.output-separator').removeClass('active');
                var txt = $(this).addClass('active').html();
                $this.find('.output-separator-input').val( txt );
                $this.find('.output-filename').val(function(i, v) {
                    // change filename extension based on separator
                    var filetype = (txt === 'json' || txt === 'array') ? 'js' :
                    txt === ',' ? 'csv' : 'txt';
                    return v.replace(/\.\w+$/, '.' + filetype);
                });
                return false;
                });
                $this.find('.output-quotes').click(function() {
                $this.find('.output-quotes').removeClass('active');
                $this.find('.output-replacequotes').val( $(this).addClass('active').text() );
                return false;
                });
                // header/footer toggle buttons
                $this.find('.output-header, .output-footer').click(function() {
                $(this).toggleClass('active');
                });
                // clicking the download button; all you really need is to
                // trigger an "output" event on the table
                $this.find('.download').click(function() {
                var typ,
                    $table = $this.find('table'),
                    wo = $table[0].config.widgetOptions,
                    val = $this.find('.output-filter-all :checked').attr('class');
                wo.output_saveRows     = val === 'output-filter' ? 'f' :
                    val === 'output-visible' ? 'v' :
                    // checked class name, see table.config.checkboxClass
                    val === 'output-selected' ? '.checked' :
                    val === 'output-sel-vis' ? '.checked:visible' :
                    'a';
                val = $this.find('.output-download-popup :checked').attr('class');
                wo.output_delivery     = val === 'output-download' ? 'd' : 'p';
                wo.output_separator    = $this.find('.output-separator-input').val();
                wo.output_replaceQuote = $this.find('.output-replacequotes').val();
                wo.output_trimSpaces   = $this.find('.output-trim').is(':checked');
                wo.output_includeHTML  = $this.find('.output-html').is(':checked');
                wo.output_wrapQuotes   = $this.find('.output-wrap').is(':checked');
                wo.output_saveFileName = $this.find('.output-filename').val();

                // first example buttons, second has radio buttons
                if (groupIndex === 0) {
                    wo.output_includeHeader = $this.find('button.output-header').is(".active");
                } else {
                    wo.output_includeHeader = !$this.find('.output-no-header').is(':checked');
                    wo.output_headerRows = $this.find('.output-headers').is(':checked');
                }
                // footer not included in second example
                wo.output_includeFooter = $this.find('.output-footer').is(".active");

                $table.trigger('outputTable');
                return false;
                });

                // add tooltip
                $this.find('.dropdown-menu [title]').tipsy({ gravity: 's' });

            });
            $(".tablesorter-filter-row [data-column='14'] label").css("display", "none");
        });
    </script>
{/literal}